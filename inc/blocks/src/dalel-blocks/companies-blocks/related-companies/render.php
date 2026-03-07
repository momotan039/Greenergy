<?php

/**
 * Related Companies Block — render.php
 *
 * Same layout as Related Posts: section title + subtitle, Swiper slider.
 * Each slide shows 3 company cards (slidesPerView: 3). Uses company-card.php.
 *
 * Attributes:
 *  - title                   (string) Section title.
 *  - subtitle                (string) Section subtitle.
 *  - selectionMode           (string) 'auto' | 'manual'.
 *  - relatedCount            (int)    Number of companies to show (default 6).
 *  - relatedSelectedCompanies(array)  Manually selected companies (objects or IDs).
 *  - isEnabled               (bool)   Toggle to show / hide the block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attributes = wp_parse_args($attributes ?? [], [
    'title'                    => 'شركات ذات صلة',
    'subtitle'                 => 'اكتشف شركات أخرى في نفس المجال.',
    'selectionMode'            => 'auto',
    'relatedCount'             => 6,
    'relatedSelectedCompanies' => [],
    'isEnabled'                => true,
]);

if (empty($attributes['isEnabled'])) {
    return;
}

$title                      = $attributes['title'];
$subtitle                   = $attributes['subtitle'];
$selection_mode             = $attributes['selectionMode'];
$related_count              = max(1, (int) $attributes['relatedCount']);
$related_selected_companies = $attributes['relatedSelectedCompanies'];
$current_post_id            = get_the_ID();

$related_args = [
    'post_type'      => 'companies',
    'posts_per_page' => $related_count,
    'post_status'    => 'publish',
    'post__not_in'   => $current_post_id ? [$current_post_id] : [],
];

if ($selection_mode === 'manual' && ! empty($related_selected_companies)) {
    $ids = array_map(
        static function ($item) {
            if (is_array($item)) {
                return isset($item['id']) ? (int) $item['id'] : null;
            }
            return (int) $item;
        },
        $related_selected_companies
    );
    $ids = array_values(array_filter(array_unique($ids)));
    if (! empty($ids)) {
        $related_args['post__in']       = $ids;
        $related_args['orderby']        = 'post__in';
        $related_args['posts_per_page'] = -1;
        unset($related_args['post__not_in']);
    }
} else {
    $tax_query = ['relation' => 'OR'];
    $has_tax   = false;
    $terms     = $current_post_id ? get_the_terms($current_post_id, 'company_category') : null;
    if ($terms && ! is_wp_error($terms)) {
        $term_ids = wp_list_pluck($terms, 'term_id');
        if (! empty($term_ids)) {
            $tax_query[] = [
                'taxonomy' => 'company_category',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ];
            $has_tax = true;
        }
    }
    if ($has_tax) {
        $related_args['tax_query'] = $tax_query;
    }
    $related_args['orderby'] = 'date';
    $related_args['order']   = 'DESC';
}

$related_query = new WP_Query($related_args);

if (! $related_query->have_posts()) {
    return;
}

$company_count = $related_query->post_count;

$swiper_settings = [
    'slidesPerView'  => 1.1,
    'spaceBetween'   => 16,
    'centeredSlides' => false,
    'loop'           => $company_count > 3,
    'watchOverflow'  => true,
    'autoplay'       => $company_count > 1 ? [
        'delay'                   => 5000,
        'disableOnInteraction'   => false,
    ] : false,
    'pagination'     => [
        'clickable' => true,
    ],
    'breakpoints'    => [
        '480'  => [
            'slidesPerView' => 2,
            'spaceBetween'  => 24,
        ],
        '768'  => [
            'slidesPerView' => 3,
            'spaceBetween'  => 24,
        ],
        '1024' => [
            'slidesPerView' => 3,
            'spaceBetween'  => 32,
        ],
    ],
];
?>

<!-- Section header (same layout as related-posts) -->
<div class="flex flex-col items-center gap-3 self-stretch mb-6">
    <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
               flex items-center justify-center
               text-green-700 text-2xl font-medium leading-5">
        <?php echo esc_html($title); ?>
    </h2>
    <?php if ($subtitle !== '') : ?>
        <p class="text-stone-500 text-base font-medium leading-[1] mt-2 mb-4">
            <?php echo esc_html($subtitle); ?>
        </p>
    <?php endif; ?>
</div>

<!-- Swiper: 3 items per slide (company cards) -->
<div class="swiper swiper-container js-swiper-init overflow-hidden w-full pb-16"
    data-swiper-config="<?php echo esc_attr(wp_json_encode($swiper_settings)); ?>"
    data-aos="fade-up" data-aos-duration="1000">

    <div class="swiper-wrapper items-stretch">
        <?php
        while ($related_query->have_posts()) :
            $related_query->the_post();
            $company_id = get_the_ID();
            $GLOBALS['post'] = get_post($company_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            setup_postdata($GLOBALS['post']);
        ?>
            <div class="swiper-slide h-auto">
                <?php get_template_part('templates/components/company-card', null, ['post_id' => $company_id, 'is_featured' => false]); ?>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>

    <div class="swiper-pagination !relative mt-8"></div>
</div>