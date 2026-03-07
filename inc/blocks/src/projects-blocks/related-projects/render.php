<?php

/**
 * Related Projects Block — dynamic by same project_type and project_tag.
 * Priority: 1) same type AND same tag(s), 2) same type, 3) same tag(s).
 * Layout: same as featured-projects (title, description, Swiper, project-card).
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$block_attrs = (isset($block) && $block instanceof WP_Block)
    ? array_merge((array) ($block->attributes ?? []), (array) ($block->parsed_block['attrs'] ?? []))
    : [];
$attrs = wp_parse_args($attributes ?? [], wp_parse_args($block_attrs, [
    'title'     => 'مشاريع ذات صلة',
    'description' => 'اكتشف مشاريع أخرى من نفس التصنيف أو الوسوم.',
    'maxCount'  => 6,
]));

$title     = (string) ($attrs['title'] ?? 'مشاريع ذات صلة');
$desc      = (string) ($attrs['description'] ?? '');
$max_count = max(1, min(24, (int) ($attrs['maxCount'] ?? 6)));

$current_post_id = isset($block->context['postId']) ? (int) $block->context['postId'] : get_the_ID();
if (! $current_post_id || get_post_type($current_post_id) !== 'projects') {
    $current_post_id = 0;
}

$type_term_ids = [];
$tag_term_ids  = [];
if ($current_post_id) {
    $type_terms = get_the_terms($current_post_id, 'project_type');
    if ($type_terms && ! is_wp_error($type_terms)) {
        $type_term_ids = array_map('intval', wp_list_pluck($type_terms, 'term_id'));
    }
    $tag_terms = get_the_terms($current_post_id, 'project_tag');
    if ($tag_terms && ! is_wp_error($tag_terms)) {
        $tag_term_ids = array_map('intval', wp_list_pluck($tag_terms, 'term_id'));
    }
}

$project_ids = [];
$exclude_ids = [$current_post_id];

// Tier 1: same type AND at least one same tag
if (! empty($type_term_ids) && ! empty($tag_term_ids)) {
    $tier1 = new WP_Query([
        'post_type'      => 'projects',
        'post_status'    => 'publish',
        'posts_per_page' => $max_count,
        'post__not_in'   => $exclude_ids,
        'fields'         => 'ids',
        'tax_query'      => [
            'relation' => 'AND',
            [
                'taxonomy' => 'project_type',
                'field'    => 'term_id',
                'terms'    => $type_term_ids,
            ],
            [
                'taxonomy' => 'project_tag',
                'field'    => 'term_id',
                'terms'    => $tag_term_ids,
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($tier1->have_posts()) {
        $project_ids = $tier1->posts;
        $exclude_ids = array_merge($exclude_ids, $project_ids);
    }
}

// Tier 2: same type only (أولوية التصنيف)
if (count($project_ids) < $max_count && ! empty($type_term_ids)) {
    $need = $max_count - count($project_ids);
    $tier2 = new WP_Query([
        'post_type'      => 'projects',
        'post_status'    => 'publish',
        'posts_per_page' => $need,
        'post__not_in'   => $exclude_ids,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'project_type',
                'field'    => 'term_id',
                'terms'    => $type_term_ids,
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($tier2->have_posts()) {
        $project_ids = array_merge($project_ids, $tier2->posts);
        $exclude_ids = array_merge($exclude_ids, $tier2->posts);
    }
}

// Tier 3: same tag(s) only (ثم الهاشتاغ)
if (count($project_ids) < $max_count && ! empty($tag_term_ids)) {
    $need = $max_count - count($project_ids);
    $tier3 = new WP_Query([
        'post_type'      => 'projects',
        'post_status'    => 'publish',
        'posts_per_page' => $need,
        'post__not_in'   => $exclude_ids,
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'project_tag',
                'field'    => 'term_id',
                'terms'    => $tag_term_ids,
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($tier3->have_posts()) {
        $project_ids = array_merge($project_ids, $tier3->posts);
    }
}

$project_ids = array_slice(array_unique(array_filter($project_ids)), 0, $max_count);

$swiper_config = wp_json_encode([
    'slidesPerView'  => 1.3,
    'spaceBetween'   => 16,
    'loop'           => count($project_ids) > 3,
    'watchOverflow'  => true,
    'autoplay'       => count($project_ids) > 1 ? ['delay' => 5000, 'disableOnInteraction' => false] : false,
    'pagination'     => ['clickable' => true],
    'breakpoints'    => [
        '640'  => ['slidesPerView' => 2, 'spaceBetween' => 20],
        '1024' => ['slidesPerView' => 3, 'spaceBetween' => 24],
    ],
]);
?>
<section class="space-y-6 flex flex-col items-center w-full">
    <header class="text-center my-8">
        <div class="inline-flex items-center title-badge">
            <?php echo esc_html($title); ?>
        </div>
        <?php if ($desc !== '') : ?>
            <p class="text-stone-500 text-base"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
    </header>

    <?php if (! empty($project_ids)) : ?>
        <div class="swiper swiper-container js-swiper-init overflow-hidden w-full pb-16"
            data-swiper-config="<?php echo esc_attr($swiper_config); ?>">
            <div class="swiper-wrapper items-stretch">
                <?php foreach ($project_ids as $pid) : ?>
                    <div class="swiper-slide h-auto">
                        <?php get_template_part('templates/components/project-card', null, ['post_id' => $pid, 'is_featured' => true]); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination !relative mt-8"></div>
        </div>
    <?php else : ?>
        <p class="text-stone-500 text-sm text-center py-8">
            <?php esc_html_e('لا توجد مشاريع ذات صلة لعرضها.', 'greenergy'); ?>
        </p>
    <?php endif; ?>
</section>