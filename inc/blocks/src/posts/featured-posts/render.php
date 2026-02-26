<?php

/**
 * Featured Posts Block — render.php
 *
 * Renders featured posts using Swiper slider for a better user experience,
 * mimicking the design of the courses block.
 */


$is_enabled              = $attributes['featuredIsEnabled']      ?? ($attributes['isEnabled'] ?? true);
$featured_count          = $attributes['featuredCount']          ?? 8;
$featured_selected_posts = $attributes['featuredSelectedPosts']  ?? [];
$selection_mode          = $attributes['featuredSelectionMode']  ?? ($attributes['selectionMode'] ?? 'dynamic');
$order_by                = $attributes['featuredOrderBy']        ?? ($attributes['orderBy'] ?? 'latest');
$title                   = $attributes['featuredTitle']          ?? ($attributes['title'] ?? 'المقالات المميزة');
$subtitle                = $attributes['featuredSubtitle']       ?? ($attributes['subtitle'] ?? 'أبرز المقالات التعليمية في مجال الطاقة والاستدامة');

if (! $is_enabled) {
    return;
}

// ── Build featured query ──────────────────────────────────────────────────────
$featured_args = [
    'post_type'      => 'post',
    'posts_per_page' => $featured_count,
    'post_status'    => 'publish',
];

if ($selection_mode === 'manual' && !empty($featured_selected_posts)) {
    $f_ids = array_map(fn($p) => is_array($p) ? $p['id'] : $p, $featured_selected_posts);
    $featured_args['post__in']       = $f_ids;
    $featured_args['orderby']       = 'post__in';
    $featured_args['posts_per_page'] = count($f_ids); // Fix length-1 issue: show all selected
} else {
    // Dynamic Mode
    switch ($order_by) {
        case 'oldest':
            $featured_args['orderby'] = 'date';
            $featured_args['order']   = 'ASC';
            break;
        case 'popular':
            $featured_args['meta_query'] = [
                'relation' => 'OR',
                'views_clause' => [
                    'key'     => '_total_views_sort',
                    'type'    => 'NUMERIC',
                    'compare' => 'EXISTS',
                ],
                'not_exists_clause' => [
                    'key'     => '_total_views_sort',
                    'compare' => 'NOT EXISTS',
                ],
            ];
            $featured_args['orderby'] = [
                'views_clause' => 'DESC',
                'date'         => 'DESC',
            ];
            break;
        default:
            $featured_args['orderby'] = 'date';
            $featured_args['order']   = 'DESC';
    }
}

$featured_query = new WP_Query($featured_args);

if (!$featured_query->have_posts()) {
    return;
}

// ── Swiper Settings ───────────────────────────────────────────────────────────
$swiper_settings = [
    'slidesPerView' => 1.1,
    'spaceBetween' => 16,
    'centeredSlides' => false,
    'loop'           => $featured_count > 4, // Only loop if we have more than the maximum visible slides (4)
    'watchOverflow'  => true,
    'autoplay'       => $featured_count > 1 ? [
        'delay' => 5000,
        'disableOnInteraction' => false,
    ] : false,
    'pagination' => [
        'clickable' => true,
    ],
    'breakpoints' => [
        '480' => [
            'slidesPerView' => 2,
            'spaceBetween' => 24,
        ],
        '1024' => [
            'slidesPerView' => 3,
            'spaceBetween' => 32,
        ],
        '1280' => [
            'slidesPerView' => 4,
            'spaceBetween' => 32,
        ]
    ]
];
?>

<!-- Section header -->
<div class="flex flex-col items-center gap-3 self-stretch mb-6">
    <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
               flex items-center justify-center
               text-green-700 text-2xl font-medium leading-5">
        <?php echo esc_html($title); ?>
    </h2>
    <p class="text-stone-500 text-base font-medium leading-[1] mt-2 mb-4">
        <?php echo esc_html($subtitle); ?>
    </p>
</div>

<!-- Swiper Slider -->
<div class="swiper swiper-container js-swiper-init pb-16"
    data-swiper-config='<?php echo esc_attr(json_encode($swiper_settings)); ?>'
    data-aos="fade-up" data-aos-duration="1000">

    <div class="swiper-wrapper">
        <?php while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
            <div class="swiper-slide h-auto">
                <?php get_template_part('templates/components/post-card', null, ['wrapper_class' => 'w-full h-full']); ?>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>

    <!-- Pagination -->
    <div class="swiper-pagination !relative mt-8"></div>
</div>