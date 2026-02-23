<?php

/**
 * Related Courses Block Template.
 *
 * - Desktop (≥ 768px): Swiper draggable slider
 * - Mobile (< 768px):  Vertical stacked list
 *
 * @param   array $attributes Block attributes.
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = wp_parse_args($attributes ?? [], [
    'postsCount' => 3,
]);

$posts_count = max(1, intval($attributes['postsCount']));
$post_id     = get_the_ID();

// ─── Build query: same course_category, exclude current course ─────────────
$course_terms = get_the_terms($post_id, 'course_category');

if ($course_terms && ! is_wp_error($course_terms)) {
    $term_ids = wp_list_pluck($course_terms, 'term_id');
    $args     = [
        'post_type'      => 'courses',
        'posts_per_page' => $posts_count,
        'post__not_in'   => [$post_id],
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'course_category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ],
        ],
    ];
} else {
    $args = [
        'post_type'      => 'courses',
        'posts_per_page' => $posts_count,
        'post__not_in'   => [$post_id],
        'post_status'    => 'publish',
    ];
}

$related_query = new WP_Query($args);

if (! $related_query->have_posts()) :
?>
    <div class="mt-16">
        <div class="text-center mb-10">
            <div class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full text-base font-bold mb-4 shadow-sm">
                دورات مماثلة
            </div>
            <h2 class="text-neutral-950 text-3xl font-bold leading-tight">اكتشف الدورات المشابهة</h2>
        </div>
        <div class="flex flex-wrap gap-4 justify-center">
            <p class="text-gray-500">لا توجد دورات مماثلة حالياً.</p>
        </div>
    </div>
<?php
    wp_reset_postdata();
    return;
endif;

// Collect courses into array (single query)
$courses_data = [];
while ($related_query->have_posts()) {
    $related_query->the_post();
    $courses_data[] = get_post();
}
wp_reset_postdata();

// Build Swiper config for desktop slider
$swiper_id     = 'related-courses-swiper-' . $post_id;
$swiper_config = [
    'slidesPerView'  => 1.15,
    'spaceBetween'   => 16,
    'grabCursor'     => true,
    'loop'           => false,
    'watchOverflow'  => false,
    'centeredSlides' => false,
    'pagination'     => ['clickable' => true],
    'breakpoints'    => [
        '640'  => ['slidesPerView' => 1.8, 'spaceBetween' => 20],
        '1024' => ['slidesPerView' => 2.4, 'spaceBetween' => 24],
        '1280' => ['slidesPerView' => 3,   'spaceBetween' => 28],
    ],
];
?>

<style>
    /* Force full-width inside the related-courses slider */
    .related-courses-slider .swiper-slide>div {
        width: 100% !important;
    }
</style>

<div class="mt-16">
    <!-- Header -->
    <div class="text-center mb-10">
        <div class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full text-base font-bold mb-4 shadow-sm">
            دورات مماثلة
        </div>
        <h2 class="text-neutral-950 text-3xl font-bold leading-tight">اكتشف الدورات المشابهة</h2>
    </div>

    <!-- ── Desktop: Swiper Slider (≥ 768px) ──────────────────────────── -->
    <div class="hidden md:block">
        <div id="<?php echo esc_attr($swiper_id); ?>"
            class="related-courses-slider swiper swiper-container js-swiper-init pb-12"
            data-swiper-config="<?php echo esc_attr(json_encode($swiper_config)); ?>">
            <div class="swiper-wrapper">
                <?php foreach ($courses_data as $course_post) :
                    setup_postdata($GLOBALS['post'] = $course_post);
                ?>
                    <div class="swiper-slide">
                        <?php get_template_part('templates/components/course-card', null, ['post' => $course_post]); ?>
                    </div>
                <?php endforeach;
                wp_reset_postdata(); ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination !relative mt-6"></div>
        </div>
    </div>

    <!-- ── Mobile: Vertical stack (< 768px) ──────────────────────────── -->
    <div class="flex flex-col gap-6 md:hidden">
        <?php foreach ($courses_data as $course_post) :
            setup_postdata($GLOBALS['post'] = $course_post);
        ?>
            <div class="w-full">
                <?php get_template_part('templates/components/course-card', null, ['post' => $course_post]); ?>
            </div>
        <?php endforeach;
        wp_reset_postdata(); ?>
    </div>
</div>