<?php

/**
 * Featured News Block Template.
 */

$attributes = wp_parse_args($attributes ?? [], [
    'count'           => 1,
    'selectionMode'   => 'dynamic',
    'selectedPosts'   => [],
    'queryCategories' => [],
    'orderBy'         => 'latest',
]);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'w-full flex flex-col justify-start items-center gap-4',
]);

// Query logic
$args = [
    'post_type'      => 'news',
    'posts_per_page' => (int) $attributes['count'],
    'post_status'    => 'publish',
];

if ($attributes['selectionMode'] === 'manual' && !empty($attributes['selectedPosts'])) {
    $args['post__in'] = array_column($attributes['selectedPosts'], 'id');
    $args['orderby']  = 'post__in';
} else {
    // Dynamic Mode
    $tax_query = [];
    if (!empty($attributes['queryCategories'])) {
        $tax_query[] = [
            'taxonomy' => 'news_category',
            'field'    => 'term_id',
            'terms'    => $attributes['queryCategories'],
            'operator' => 'IN',
        ];
    }

    /* 
    // Removed URL category filtering for Featured News block to respect admin configuration.
    if (isset($_GET['news_cat']) && !empty($_GET['news_cat'])) {
        $tax_query[] = [
            'taxonomy' => 'news_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['news_cat']),
        ];
    }
    */

    if (!empty($tax_query)) {
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }

    // Apply Sorting
    $order_by = $attributes['orderBy'] ?? 'latest';
    if ($order_by === 'url_parameter' && isset($_GET['sort'])) {
        $order_by = sanitize_text_field($_GET['sort']);
    }

    switch ($order_by) {
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
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="w-full relative">
        <div class="swiper js-swiper-init w-full rounded-lg overflow-hidden" data-swiper-config='{"autoplay":false, "pagination": {"clickable": true}}'>
            <div class="swiper-wrapper">
                <?php
                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        $post_id = get_the_ID();
                        $views   = Greenergy_Post_Views::get_views($post_id);
                        $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                ?>
                        <div class="swiper-slide">
                            <div class="w-full h-64 sm:h-80 md:h-96 p-3 md:p-4 bg-center bg-cover flex flex-col justify-between items-center relative overflow-hidden group rounded-lg" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');">
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>

                                <div class="w-full inline-flex justify-start items-start gap-4 md:gap-2 relative z-10 transition-transform duration-500 group-hover:translate-x-2">
                                    <div class="h-7 md:h-8 px-2 bg-black/25 rounded-lg flex justify-center items-center gap-2 backdrop-blur-sm hover:bg-black/40 transition-colors cursor-default">
                                        <div class="text-center justify-start text-white text-xs md:text-sm font-medium leading-6">
                                            بواسطة: <?php the_author(); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full inline-flex justify-between items-start relative z-10">
                                    <div class="flex-1 inline-flex flex-col justify-start items-end gap-2 md:gap-4 w-full">
                                        <a href="<?php the_permalink(); ?>" class="w-full text-right justify-start text-white text-lg md:text-2xl font-medium capitalize leading-tight md:leading-snug hover:text-green-400 transition-colors">
                                            <?php the_title(); ?>
                                        </a>
                                        <div class="w-full text-right justify-start text-white text-sm md:text-base font-normal capitalize leading-5 md:leading-6 line-clamp-2">
                                            <?php echo esc_html(get_the_excerpt()); ?>
                                        </div>
                                        <div class="w-full inline-flex justify-between items-center flex-row-reverse mt-2">
                                            <a href="<?php the_permalink(); ?>" class="text-right justify-start text-white text-sm md:text-base font-medium underline capitalize leading-6 hover:text-green-400 transition-colors">
                                                المزيد
                                            </a>
                                            <div class="flex justify-start items-center gap-1.5">
                                                <div class="text-right justify-start text-white text-xs md:text-sm font-normal flex items-center gap-1">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4 inline" aria-hidden="true">
                                                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye_white.svg"></use>
                                                    </svg>
                                                    <?php echo esc_html(number_format_i18n((int) $views)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    // Fallback
                    ?>
                    <div class="swiper-slide">
                        <div class="w-full h-64 sm:h-80 md:h-96 bg-gray-100 flex items-center justify-center rounded-lg">
                            <p class="text-gray-400"><?php _e('لا توجد أخبار مميزة.', 'greenergy'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dots / Pagination -->
            <div class="swiper-pagination !static !mt-4 transition-all duration-300"></div>
        </div>
    </div>
</div>