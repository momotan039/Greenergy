<?php

/**
 * Related Posts Block — render.php
 *
 * Renders related posts using the same visual layout as the Featured Posts block
 * (`inc/blocks/src/posts/featured-posts/render.php`), but the query is driven
 * by the current post's categories / tags or by a manual selection.
 *
 * Attributes:
 *  - title               (string) Section title.
 *  - subtitle            (string) Section subtitle.
 *  - selectionMode       (string) 'auto' | 'manual'.
 *  - relatedCount        (int)    Number of posts to show (default 4).
 *  - relatedSelectedPosts(array)  Manually selected posts (objects or IDs).
 *  - isEnabled           (bool)   Toggle to show / hide the block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

// Merge attributes with defaults.
$attributes = wp_parse_args($attributes ?? [], [
    'title'               => 'مقالات ذات صلة',
    'subtitle'            => 'اكتشف مقالات أخرى مرتبطة بنفس الموضوع.',
    'selectionMode'       => 'auto',
    'relatedCount'        => 4,
    'relatedSelectedPosts'=> [],
    'isEnabled'           => true,
]);

// Allow editor toggle to completely hide the block.
if (empty($attributes['isEnabled'])) {
    return;
}

$title                 = $attributes['title'];
$subtitle              = $attributes['subtitle'];
$selection_mode        = $attributes['selectionMode'];
$related_count         = max(1, intval($attributes['relatedCount']));
$related_selected_posts= $attributes['relatedSelectedPosts'];
$current_post_id       = get_the_ID();

// ── Build related posts query ─────────────────────────────────────────────────
$related_args = [
    'post_type'      => 'post',
    'posts_per_page' => $related_count,
    'post_status'    => 'publish',
    'post__not_in'   => [$current_post_id],
];

if ($selection_mode === 'manual' && ! empty($related_selected_posts)) {
    // Manual mode: honor editor-selected posts, preserving order.
    $ids = array_map(
        static function ($item) {
            if (is_array($item)) {
                return isset($item['id']) ? (int) $item['id'] : null;
            }
            return (int) $item;
        },
        $related_selected_posts
    );

    $ids = array_values(array_filter(array_unique($ids)));

    if (! empty($ids)) {
        // Manual mode: show exactly the posts selected in the editor,
        // in the same order, without excluding the current post.
        $related_args['post__in']       = $ids;
        $related_args['orderby']        = 'post__in';
        $related_args['posts_per_page'] = -1; // all selected posts
        unset($related_args['post__not_in']);
    }
} else {
    // Auto mode: posts sharing categories or tags with the current post.
    $tax_query = ['relation' => 'OR'];
    $has_tax   = false;

    $cat_terms = get_the_terms($current_post_id, 'category');
    if ($cat_terms && ! is_wp_error($cat_terms)) {
        $cat_ids     = wp_list_pluck($cat_terms, 'term_id');
        if (! empty($cat_ids)) {
            $tax_query[] = [
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat_ids,
            ];
            $has_tax = true;
        }
    }

    $tag_terms = get_the_terms($current_post_id, 'post_tag');
    if ($tag_terms && ! is_wp_error($tag_terms)) {
        $tag_ids    = wp_list_pluck($tag_terms, 'term_id');
        if (! empty($tag_ids)) {
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tag_ids,
            ];
            $has_tax = true;
        }
    }

    if ($has_tax) {
        $related_args['tax_query'] = $tax_query;
    }

    // Fallback ordering: latest posts.
    $related_args['orderby'] = 'date';
    $related_args['order']   = 'DESC';
}

$related_query = new WP_Query($related_args);

if (! $related_query->have_posts()) {
    return;
}

// ── Swiper Settings (same as Featured Posts) ─────────────────────────────────
$post_count = $related_query->post_count;

$swiper_settings = [
    'slidesPerView'  => 1.1,
    'spaceBetween'   => 16,
    'centeredSlides' => false,
    'loop'           => $post_count > 4, // Only loop if we have more than the maximum visible slides (4)
    'watchOverflow'  => true,
    'autoplay'       => $post_count > 1 ? [
        'delay' => 5000,
        'disableOnInteraction' => false,
    ] : false,
    'pagination'     => [
        'clickable' => true,
    ],
    'breakpoints'    => [
        '480'  => [
            'slidesPerView' => 2,
            'spaceBetween'  => 24,
        ],
        '1024' => [
            'slidesPerView' => 3,
            'spaceBetween'  => 32,
        ],
        '1280' => [
            'slidesPerView' => 4,
            'spaceBetween'  => 32,
        ],
    ],
];
?>

<!-- Section header -->
<div class="flex flex-col items-center gap-3 self-stretch mb-6">
    <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
               flex items-center justify-center
               text-green-700 text-2xl font-medium leading-5">
        <?php echo esc_html($title); ?>
    </h2>
    <?php if (! empty($subtitle)) : ?>
        <p class="text-stone-500 text-base font-medium leading-[1] mt-2 mb-4">
            <?php echo esc_html($subtitle); ?>
        </p>
    <?php endif; ?>
</div>

<!-- Swiper Slider -->
<div class="swiper swiper-container js-swiper-init pb-16"
    data-swiper-config="<?php echo esc_attr(json_encode($swiper_settings)); ?>"
    data-aos="fade-up" data-aos-duration="1000">

    <div class="swiper-wrapper">
        <?php
        while ($related_query->have_posts()) :
            $related_query->the_post();
            ?>
            <div class="swiper-slide h-auto">
                <?php get_template_part('templates/components/post-card', null, ['wrapper_class' => 'w-full h-full']); ?>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>

    <!-- Pagination -->
    <div class="swiper-pagination !relative mt-8"></div>
</div>

