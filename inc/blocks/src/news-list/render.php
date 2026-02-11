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

// Query arguments
$args = [
    'post_type'      => 'news',
    'posts_per_page' => (int) $attributes['count'],
    'offset'         => (int) $attributes['offset'],
    'post_status'    => 'publish',
    'order'          => 'DESC',
    'orderby'        => 'date',
];

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
        'terms'    => sanitize_text_field($_GET['news_cat']),
    ]];
    $args['offset'] = 0;
}

if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// Apply sorting
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'oldest':
            $args['order'] = 'ASC';
            break;
        case 'popular':
            $args['meta_key'] = 'views';
            $args['orderby'] = 'meta_value_num';
            unset($args['order']);
            break;
    }
}

$query = new WP_Query($args);

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
            'views'     => get_post_meta(get_the_ID(), 'views', true) ?: '0',
            'image'     => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg',
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

// Reusable news card function
if (!function_exists('render_news_card')) {
    function render_news_card($item)
    {
?>
        <div class="relative group  w-full rounded-2xl inline-flex justify-start items-center gap-6 overflow-hidden max-sm:flex-col bg-white border border-gray-100 hover:shadow-xl transition-all duration-300">
            <a href="<?php echo esc_url($item['permalink']); ?>" class="absolute inset-0 z-10 w-full h-full" aria-label="<?php echo esc_attr($item['title']); ?>"></a>

            <div class="h-44 w-44 max-sm:w-full max-sm:h-60 shrink-0 bg-cover bg-center"
                style="background-image: url('<?php echo esc_url($item['image']); ?>');"
                role="img"
                aria-label="<?php echo esc_attr($item['title']); ?>">
            </div>

            <div class="flex-1 self-stretch pl-6 max-sm:px-4 py-4 inline-flex flex-col justify-between items-end">
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                        <time datetime="<?php echo esc_attr($item['date']); ?>" class="text-neutral-500 text-xs font-normal">
                            <?php echo esc_html($item['date']); ?>
                        </time>
                        <?php if ($item['cat']) : ?>
                            <span class="text-primary text-xs font-bold px-3 py-1 bg-primary/10 rounded-full">
                                #<?php echo esc_html($item['cat']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-right text-neutral-900 text-lg font-bold leading-tight line-clamp-2 transition-colors group-hover:text-primary">
                        <?php echo esc_html($item['title']); ?>
                    </h3>

                    <p class="self-stretch text-right text-neutral-500 text-sm font-normal line-clamp-2">
                        <?php echo esc_html($item['excerpt']); ?>
                    </p>
                </div>

                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-4 pt-4 border-t border-gray-50">
                    <span class="text-primary text-sm font-bold flex items-center gap-2">
                        إقرأ المزيد
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                    <div class="flex justify-start items-center gap-1.5 text-neutral-400 text-sm ">
                        <i class="far fa-eye text-xs"></i>
                        <span><?php echo esc_html($item['views']); ?></span>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if (!empty($attributes['title'])) : ?>
        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mb-2">
            <h2 class="text-right text-neutral-900 text-2xl font-bold"><?php echo esc_html($attributes['title']); ?></h2>
            <div class="h-1 flex-1 bg-gray-100 rounded-full mr-6"></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($news_items)) : ?>
        <div class="flex flex-col gap-4">
            <?php foreach ($news_items as $item) {
                render_news_card($item);
            } ?>
        </div>
    <?php else : ?>
        <div class="p-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <p class="text-neutral-500"><?php _e('عذراً، لا توجد أخبار متاحة حالياً.', 'greenergy'); ?></p>
        </div>
    <?php endif; ?>
    <!-- Pagination for page -->
    <?php
    get_template_part('templates/components/pagination');
    ?>
</div>