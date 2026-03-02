<?php

/**
 * All Companies Block — dynamic list with filter, featured row, and pagination (AJAX).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

// Gather attributes: callback param, block object, or parsed_block (template/saved content).
// Priority: parsed_block attrs (what's actually in saved content) > block->attributes > $attributes param.
$raw_attrs = [];
if (isset($block) && $block instanceof WP_Block) {
    if (! empty($block->parsed_block['attrs'])) {
        $raw_attrs = (array) $block->parsed_block['attrs'];
    }
    if (! empty($block->attributes)) {
        $raw_attrs = array_merge($raw_attrs, (array) $block->attributes);
    }
}
$raw_attrs = array_merge($attributes ?? [], $raw_attrs);

$attrs = wp_parse_args($raw_attrs, [
    'title'               => 'جميع الشركات',
    'description'         => 'اكتشف أهم الشركات والمشاريع والخبراء في مجال الطاقة المتجددة',
    'perPage'             => 9,
    'featuredCompanies'   => [],
    'featured_companies'  => [],
    'visibleCategoryIds'  => [],
]);

$title    = (string) ($attrs['title'] ?? '');
$desc     = (string) ($attrs['description'] ?? '');
$per_page = max(1, min(24, (int) ($attrs['perPage'] ?? 9)));

// featuredCompanies can be array of {id, title} or just ids; support both keys.
$featured = isset($attrs['featuredCompanies']) && is_array($attrs['featuredCompanies'])
    ? $attrs['featuredCompanies']
    : (isset($attrs['featured_companies']) && is_array($attrs['featured_companies']) ? $attrs['featured_companies'] : []);
$featured_ids = array_values(array_filter(array_map(function ($item) {
    if (is_array($item)) {
        $id = $item['id'] ?? $item['ID'] ?? null;
        return $id !== null ? (int) $id : null;
    }
    return is_numeric($item) ? (int) $item : null;
}, $featured)));

// Fallback: theme mod (so featured show when template is from file with no saved attrs).
if (empty($featured_ids)) {
    $saved_ids = get_theme_mod('greenergy_featured_company_ids', []);
    $featured_ids = is_array($saved_ids) ? array_filter(array_map('absint', $saved_ids)) : [];
}
// Sync: when we have featured from block attrs, save to theme mod so front keeps showing them even if template reverts to file.
if (! empty($featured_ids) && ! empty($featured)) {
    set_theme_mod('greenergy_featured_company_ids', $featured_ids);
}

// Featured companies (order preserved)
$featured_posts = [];
if (! empty($featured_ids)) {
    $q = new WP_Query([
        'post_type'      => 'companies',
        'post__in'       => $featured_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $featured_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}

// List query: search by name first, then by description if no results; same filters as filter-companies
$list_query = function_exists('greenergy_companies_query') ? greenergy_companies_query([
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
    'post__not_in'   => $featured_posts,
]) : new WP_Query([
    'post_type'      => 'companies',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
    'post__not_in'   => $featured_posts,
]);
$total_pages = $list_query->max_num_pages;
$current_page = max(1, $list_query->get('paged'));

$block_id_attr = 'greenergy-all-companies-' . substr(uniqid(), -8);

// Pass selected categories to filter so only these show in tabs (empty = all).
$visible_category_ids = isset($attrs['visibleCategoryIds']) && is_array($attrs['visibleCategoryIds'])
    ? array_filter(array_map('absint', $attrs['visibleCategoryIds']))
    : [];
$filter_block_attrs = ! empty($visible_category_ids) ? wp_json_encode(['visibleCategoryIds' => array_values($visible_category_ids)]) : '';
?>

<?php echo do_blocks('<!-- wp:greenergy/filter-companies ' . $filter_block_attrs . ' /-->'); ?>

<section class="w-full mx-auto flex flex-col gap-6 js-all-companies-block" id="<?php echo esc_attr($block_id_attr); ?>"
    data-per-page="<?php echo esc_attr((string) $per_page); ?>"
    data-block-id="<?php echo esc_attr($block_id_attr); ?>"
    data-cat="<?php echo esc_attr(isset($_GET['cat']) ? absint($_GET['cat']) : ''); ?>"
    data-country="<?php echo esc_attr(isset($_GET['country']) ? absint($_GET['country']) : ''); ?>"
    data-sort="<?php echo esc_attr(isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'latest'); ?>"
    data-s-company="<?php echo esc_attr(isset($_GET['s_company']) ? sanitize_text_field($_GET['s_company']) : ''); ?>"
    data-featured-ids="<?php echo esc_attr(implode(',', $featured_posts)); ?>"
    data-total-pages="<?php echo esc_attr((string) $total_pages); ?>">

    <header class="text-center flex flex-col gap-2">
        <div class="mx-auto px-8 py-2.5 bg-teal-50 rounded-3xl text-green-700 text-2xl font-medium"><?php echo esc_html($title); ?></div>
        <p class="text-stone-500 text-base"><?php echo esc_html($desc); ?></p>
    </header>

    <?php if (! empty($featured_posts)) : ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featured_posts as $pid) : ?>
                <?php get_template_part('templates/components/company-card', null, ['post_id' => $pid, 'is_featured' => true]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="relative js-ajax-grid" data-loader-text="<?php echo esc_attr(__('جاري جلب الشركات', 'greenergy')); ?>">
        <!-- Same loader as news-list / all-posts (premium AJAX loader) -->
        <div class="ajax-loader" aria-hidden="true">
            <div class="ajax-loader-container">
                <div class="ajax-loader-spinner-wrapper">
                    <div class="ajax-loader-spinner"></div>
                    <div class="ajax-loader-logo">
                        <i class="fas fa-leaf text-2xl"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="ajax-loader-text"><?php echo esc_html(__('جاري جلب الشركات', 'greenergy')); ?></div>
                    <div class="ajax-loader-dots">
                        <div class="ajax-loader-dot"></div>
                        <div class="ajax-loader-dot"></div>
                        <div class="ajax-loader-dot"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 js-all-companies-grid">
            <?php
            if ($list_query->have_posts()) {
                while ($list_query->have_posts()) {
                    $list_query->the_post();
                    get_template_part('templates/components/company-card', null, ['post_id' => get_the_ID(), 'is_featured' => false]);
                }
                wp_reset_postdata();
            } else {
                echo '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا توجد شركات تطابق المعايير.', 'greenergy') . '</p>';
            }
            ?>
        </div>
    </div>

    <?php if ($total_pages > 1) : ?>
        <nav class="greenergy-pagination greenergy-all-companies-pagination mt-6 flex justify-center items-center gap-2 flex-wrap" aria-label="<?php esc_attr_e('تنقل الشركات', 'greenergy'); ?>">
            <?php if ($current_page > 1) : ?>
                <button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page - 1); ?>" aria-label="<?php esc_attr_e('الصفحة السابقة', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg></button>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <?php $active = $i === $current_page; ?>
                <button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm <?php echo $active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500'; ?>" data-page="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></button>
            <?php endfor; ?>
            <?php if ($current_page < $total_pages) : ?>
                <button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page + 1); ?>" aria-label="<?php esc_attr_e('الصفحة التالية', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg></button>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>