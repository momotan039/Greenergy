<?php
/**
 * News List Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @package Greenergy
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'count'  => 5,
    'offset' => 1,
] );

// Query arguments
$args = [
    'post_type'      => 'news',
    'posts_per_page' => $attributes['count'],
    'offset'         => $attributes['offset'],
    'post_status'    => 'publish',
    'order'          => 'DESC',
    'orderby'        => 'date',
];

// Apply category filter
if ( !empty($_GET['news_cat']) ) {
    $args['tax_query'] = [[
        'taxonomy' => 'news_category',
        'field'    => 'slug',
        'terms'    => sanitize_text_field($_GET['news_cat']),
    ]];
    $args['offset'] = 0; // Reset offset when filtering
}

// Apply sorting
if ( !empty($_GET['sort']) ) {
    switch ( $_GET['sort'] ) {
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
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
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

// Fallback news
if ( empty($news_items) ) {
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
    $news_items = array_map(function($item) use ($default_image) {
        return array_merge($item, [
            'image'     => $default_image,
            'permalink' => '#',
        ]);
    }, array_slice(array_merge($fallback_news, $fallback_news, $fallback_news), 0, 10));
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'self-stretch flex flex-col gap-2',
]);

// Reusable news card function
if (!function_exists('render_news_card')) {
    function render_news_card($item) {
        ?>
        <div class="relative hover:bg-green-200 shadow-lg outline outline-1 outline-gray-200 w-full rounded-lg inline-flex justify-start items-center gap-4 overflow-hidden max-sm:flex-col hover:shadow-md transition-shadow duration-300">
            <a href="<?php echo esc_url($item['permalink']); ?>" class="absolute inset-0 z-10 w-full h-full" aria-label="<?php echo esc_attr($item['title']); ?>"></a>
            
            <div class="max-sm:h-60 max-sm:w-full w-36 h-36 shrink-0 bg-cover bg-center rounded" 
                 style="background-image: url('<?php echo esc_url($item['image']); ?>');" 
                 role="img" 
                 aria-label="<?php echo esc_attr($item['title']); ?>">
            </div>
            
            <div class="flex-1 self-stretch pl-3 max-sm:pr-3 py-3 inline-flex flex-col justify-start items-end gap-1.5">
                <div class="self-stretch flex flex-col justify-start items-end gap-4 max-sm:gap-2">
                    <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                        <time datetime="<?php echo esc_attr($item['date']); ?>" class="text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                            <?php echo esc_html($item['date']); ?>
                        </time>
                        <h3 class="text-right justify-start text-neutral-950 text-sm font-medium leading-5 hover:text-green-700 transition-colors">
                            <?php echo esc_html($item['title']); ?>
                        </h3>
                    </div>
                    
                    <p class="self-stretch text-right justify-start text-stone-500 text-xs font-normal capitalize leading-6 line-clamp-2">
                        <?php echo esc_html($item['excerpt']); ?>
                    </p>
                    
                    <?php if ($item['cat']) : ?>
                        <div class="h-6 px-4 bg-green-100 rounded-full inline-flex justify-center items-center self-start">
                            <span class="text-right text-neutral-950 text-xs font-normal">
                                #<?php echo esc_html($item['cat']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto">
                    <span class="text-center text-green-700 text-sm font-normal underline leading-5 hover:text-green-900 relative z-20">
                        المزيد
                    </span>
                    <div class="flex justify-start items-center gap-1.5 text-neutral-950 text-sm">
                        <svg class="w-4 h-4" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye.svg"></use>
                        </svg>
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
    <?php foreach ($news_items as $item) {
        render_news_card($item);
    } ?>
    <!-- Pagination for page -->
    <?php
        get_template_part('templates/components/pagination');
    ?>
</div>