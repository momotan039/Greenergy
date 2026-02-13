<?php

/**
 * News List Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @package Greenergy
 */

$attributes = wp_parse_args($attributes ?? [], [
    'count'           => 5,
    'offset'          => 0,
    'title'           => 'آخر الأخبار',
    'queryCategories' => [],
]);

// Pagination and Offset handling
$current_page = max(1, get_query_var('paged'));
$ppp = (int) $attributes['count'];
$initial_offset = (int) $attributes['offset'];

// Query arguments
$args = [
    'post_type'      => 'news',
    'posts_per_page' => $ppp,
    'post_status'    => 'publish',
    'order'          => 'DESC',
    'orderby'        => 'date',
];

// Special handling for offset + pagination
if ($current_page > 1 || $initial_offset > 0) {
    $args['offset'] = $initial_offset + (($current_page - 1) * $ppp);
}


// Apply category filter
$tax_query = [];

// Block attribute filter
if (!empty($attributes['queryCategories'])) {
    $tax_query[] = [
        'taxonomy' => 'news_category',
        'field'    => 'term_id',
        'terms'    => $attributes['queryCategories'],
        'operator' => 'IN',
    ];
}

// URL parameter filter (override or merge? usually override for archive pages)
if (!empty($_GET['news_cat'])) {
    $tax_query = [[
        'taxonomy' => 'news_category',
        'field'    => 'slug',
        'terms'    => rawurldecode(sanitize_text_field($_GET['news_cat'])),
    ]];
    $args['offset'] = 0;
}

if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// Apply sorting
if (!empty($_GET['sort'])) {
    switch (sanitize_text_field($_GET['sort'])) {
        case 'oldest':
            $args['order']   = 'ASC';
            $args['orderby'] = 'date';
            break;
        case 'popular':
            $args['meta_key'] = '_total_views_sort';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        default: // latest
            $args['order']   = 'DESC';
            $args['orderby'] = 'date';
            break;
    }
}

$query = new WP_Query($args);

// Fix max_num_pages when using offset
if ($initial_offset > 0) {
    $found_posts = $query->found_posts;
    $effective_total = max(0, $found_posts - $initial_offset);
    $query->max_num_pages = ceil($effective_total / $ppp);
}


// Prepare news items
$news_items = [];
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $terms = get_the_terms(get_the_ID(), 'news_category');

        $news_items[] = [
            'title'     => get_the_title(),
            'excerpt'   => get_the_excerpt(),
            'date'      => get_the_date('d/m/Y'),
            'views'     => Greenergy_Post_Views::get_views(get_the_ID()),
            'image'     => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/800X800',
            'permalink' => get_permalink(),
            'cat'       => $terms && !is_wp_error($terms) ? $terms[0]->name : '',
        ];
    }
    wp_reset_postdata();
}

// Fallback news (Only if specifically needed, but we'll prioritize actual DB content)
if (empty($news_items) && defined('WP_DEBUG') && WP_DEBUG) {
    $fallback_news = [
        [
            'title'   => 'السعودية تُدشن أكبر مشروع للطاقة الشمسية في المنطقة بقدرة ٢٠٠٠ ميجاواط',
            'excerpt' => 'في خطوة رائدة نحو تحقيق رؤية ٢٠٣٠، أعلنت السعودية عن تدشين مشروع ضخم للطاقة الشمسية...',
            'date'    => '08/08/2025',
            'views'   => '9,870',
            'cat'     => 'الطاقة_الشمسية',
        ],
        [
            'title'   => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
            'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
            'date'    => '07/08/2025',
            'views'   => '5,430',
            'cat'     => 'بيئة',
        ],
        [
            'title'   => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
            'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة وتحقيق الاستدامة البيئية',
            'date'    => '06/08/2025',
            'views'   => '8,120',
            'cat'     => 'طاقة_متجددة',
        ],
    ];

    $default_image = get_template_directory_uri() . '/assets/images/new-2.jpg';
    $news_items = array_map(function ($item) use ($default_image) {
        return array_merge($item, [
            'image'     => $default_image,
            'permalink' => '#',
        ]);
    }, array_slice(array_merge($fallback_news, $fallback_news, $fallback_news), 0, 10));
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'self-stretch flex flex-col gap-6', // Increased gap
]);



?>
<div <?php echo $wrapper_attributes; ?>>

    <?php if (!empty($news_items)) : ?>
        <div class="flex flex-col gap-4">
            <?php foreach ($news_items as $item) {
                // render_news_card($item); // Deprecated inline function
                greenergy_get_template('templates/components/news-card', null, ['item' => $item]);
            } ?>
        </div>
    <?php else : ?>
        <div class="p-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <p class="text-neutral-500"><?php _e('عذراً، لا توجد أخبار متاحة حالياً.', 'greenergy'); ?></p>
        </div>
    <?php endif; ?>
    <!-- Pagination for page -->
    <div class="greenergy-block-pagination"
        data-query-args="<?php echo esc_attr(json_encode($args)); ?>"
        data-block-id="<?php echo esc_attr($attributes['blockId'] ?? uniqid('news-list-')); ?>">
        <?php
        $current_page = max(1, get_query_var('paged'));
        echo greenergy_get_pagination_html($query, $current_page);
        ?>
    </div>
</div>