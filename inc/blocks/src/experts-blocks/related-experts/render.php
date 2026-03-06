<?php

/**
 * Related Experts Block — render
 *
 * Fetches experts with the same expert_category as the current expert.
 * Admin can set relatedCount. Swiper: 2 slides on small screens, 4 on large; pagination.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attrs = wp_parse_args($attributes ?? [], [
    'title'        => __('خبراء من نفس المجال', 'greenergy'),
    'relatedCount' => 8,
]);

$title         = $attrs['title'];
$related_count = max(1, min(20, (int) $attrs['relatedCount']));
$current_id    = get_the_ID();

if (! $current_id || get_post_type($current_id) !== 'experts') {
    return;
}

$term_ids = [];
$terms = get_the_terms($current_id, 'expert_category');
if ($terms && ! is_wp_error($terms)) {
    $term_ids = wp_list_pluck($terms, 'term_id');
}

$args = [
    'post_type'      => 'experts',
    'posts_per_page' => $related_count,
    'post_status'    => 'publish',
    'post__not_in'   => [$current_id],
];

if (! empty($term_ids)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'expert_category',
            'field'    => 'term_id',
            'terms'    => $term_ids,
        ],
    ];
}
$args['orderby'] = 'date';
$args['order']   = 'DESC';

$related_query = new WP_Query($args);

if (! $related_query->have_posts()) {
    return;
}

$swiper_id = 'related-experts-swiper-' . $current_id;
$swiper_config = [
    'slidesPerView'  => 2,
    'spaceBetween'   => 16,
    'loop'           => false,
    'watchOverflow'  => true,
    'grabCursor'     => true,
    'pagination'     => [
        'clickable' => true,
    ],
    'breakpoints'    => [
        '640'  => [
            'slidesPerView' => 2,
            'spaceBetween'  => 20,
        ],
        '1024' => [
            'slidesPerView' => 4,
            'spaceBetween'  => 24,
        ],
    ],
];
?>

<style>
.related-experts-block .swiper-slide {
    padding-top: 2rem;
    overflow: hidden;
    box-sizing: border-box;
}
</style>

<section class="related-experts-block py-16 px-4 font-sans overflow-hidden" dir="rtl">
    <div class="max-w-7xl mx-auto overflow-hidden">
        <div class="title-badge w-fit mx-auto mb-12"><?php echo esc_html($title); ?></div>

        <div class="swiper swiper-container js-swiper-init overflow-hidden w-full pb-12"
            id="<?php echo esc_attr($swiper_id); ?>"
            data-swiper-config="<?php echo esc_attr(wp_json_encode($swiper_config)); ?>">

            <div class="swiper-wrapper items-stretch">
                <?php
                while ($related_query->have_posts()) :
                    $related_query->the_post();
                    $expert_id = get_the_ID();
                ?>
                    <div class="swiper-slide h-auto">
                        <?php get_template_part('templates/components/expert-card', null, ['post_id' => $expert_id]); ?>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>

            <div class="swiper-pagination !relative mt-8"></div>
        </div>
    </div>
</section>