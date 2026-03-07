<?php

/**
 * Organizations Filter Block.
 * Filters: Parent Category Tabs | Country (organization_location parents) | Sort | Search (s_org).
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'w-full p-3 bg-white rounded-2xl flex flex-col gap-3',
    'dir'   => 'rtl',
]);

$raw_attrs = $attributes ?? [];
if (isset($block) && $block instanceof WP_Block && ! empty($block->attributes)) {
    $raw_attrs = array_merge($raw_attrs, (array) $block->attributes);
}
if (isset($block) && $block instanceof WP_Block && ! empty($block->parsed_block['attrs'])) {
    $raw_attrs = array_merge($raw_attrs, (array) $block->parsed_block['attrs']);
}
$visible_ids = isset($raw_attrs['visibleCategoryIds']) && is_array($raw_attrs['visibleCategoryIds'])
    ? array_filter(array_map('absint', $raw_attrs['visibleCategoryIds']))
    : (isset($raw_attrs['visible_category_ids']) && is_array($raw_attrs['visible_category_ids'])
        ? array_filter(array_map('absint', $raw_attrs['visible_category_ids']))
        : []);

$current_cat     = isset($_GET['cat']) ? absint($_GET['cat']) : 0;
$current_country = isset($_GET['country']) ? absint($_GET['country']) : 0;
$current_sort    = isset($_GET['sort']) ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'latest';
$current_search  = isset($_GET['s_org']) ? sanitize_text_field(wp_unslash($_GET['s_org'])) : '';

$allowed_sort = ['latest', 'oldest', 'popular', 'alpha'];
if (! in_array($current_sort, $allowed_sort, true)) {
    $current_sort = 'latest';
}

global $wp;
$current_path = isset($wp->request) ? $wp->request : '';
$organizations_archive_url = function_exists('get_post_type_archive_link') ? get_post_type_archive_link('organizations') : '';
if (! $organizations_archive_url) {
    $organizations_archive_url = home_url($current_path ? '/' . ltrim($current_path, '/') : '/');
}

$categories = get_terms([
    'taxonomy'   => 'organization_category',
    'hide_empty' => false,
    'parent'     => 0,
]);
if (empty($categories) || is_wp_error($categories)) {
    $categories = [];
}
if (! empty($visible_ids)) {
    $categories = array_filter($categories, function ($term) use ($visible_ids) {
        return in_array((int) $term->term_id, $visible_ids, true);
    });
    $categories = array_values($categories);
}

$countries = get_terms([
    'taxonomy'   => 'organization_location',
    'hide_empty' => false,
    'parent'     => 0,
]);
if (empty($countries) || is_wp_error($countries)) {
    $countries = [];
}

$country_ids = wp_list_pluck($countries, 'term_id');
if ($current_country > 0 && ! in_array($current_country, $country_ids, true)) {
    $current_country = 0;
}

if (! function_exists('greenergy_organization_filter_url')) {
    function greenergy_organization_filter_url(array $new_args, $base_url = '')
    {
        $archive = function_exists('get_post_type_archive_link') ? get_post_type_archive_link('organizations') : '';
        $base = $base_url ? $base_url : ($archive ?: home_url('/organizations/'));
        $current = array_filter([
            'cat'     => isset($_GET['cat']) ? absint($_GET['cat']) : 0,
            'country' => isset($_GET['country']) ? absint($_GET['country']) : 0,
            'sort'    => isset($_GET['sort']) ? sanitize_text_field(wp_unslash($_GET['sort'])) : '',
            's_org'   => isset($_GET['s_org']) ? sanitize_text_field(wp_unslash($_GET['s_org'])) : '',
        ]);
        $merged = array_merge($current, $new_args);
        $merged = array_filter($merged);
        return add_query_arg($merged, $base);
    }
}

$sort_label = match ($current_sort) {
    'popular' => __('الاكثر مشاهدة', 'greenergy'),
    'oldest'  => __('الاقدم', 'greenergy'),
    'alpha'   => __('أبجدي', 'greenergy'),
    default  => __('الاحدث', 'greenergy'),
};

$sel = array_filter((array) $countries, fn($c) => (int) $c->term_id === $current_country);
$country_label = $sel ? esc_html(reset($sel)->name) : __('كل الدول', 'greenergy');
?>

<div <?php echo $wrapper_attributes; ?>>
    <form method="get" action="<?php echo esc_url($organizations_archive_url); ?>">
        <input type="hidden" name="cat" value="<?php echo $current_cat > 0 ? (int) $current_cat : ''; ?>" />
        <div class="flex flex-col gap-2">
            <div class="flex justify-center md:mb-0">
                <div class="js-org-filter-search-wrap relative w-full md:max-w-xl">
                    <div class="group w-full h-11 px-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-green-500 transition-all">
                        <svg class="w-6 h-6 inline self-center text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                            <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                        </svg>
                        <input type="text" name="s_org" id="js-org-filter-search-input"
                            value="<?php echo esc_attr($current_search); ?>"
                            placeholder="<?php echo esc_attr__('ابحث عن منظمة ...', 'greenergy'); ?>"
                            autocomplete="off"
                            class="js-org-filter-search-input flex-1 h-6 text-right text-stone-500 text-sm font-normal leading-4 bg-transparent border-none outline-none focus:ring-0"
                            aria-autocomplete="list"
                            aria-controls="js-org-filter-suggestions"
                            aria-expanded="false"
                            role="combobox">
                        <?php if ($current_search !== '') : ?>
                            <a href="<?php echo esc_url(greenergy_organization_filter_url(['s_org' => ''], $organizations_archive_url)); ?>" class="text-stone-300 hover:text-red-500 transition-colors flex-shrink-0">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <ul id="js-org-filter-suggestions" class="js-org-filter-suggestions absolute top-full right-0 left-0 z-20 mt-1 py-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto hidden" role="listbox"></ul>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <div style="scrollbar-width:none"
                    class="overflow-x-auto w-full md:w-2/3 bg-green-200 h-12 p-1.5 rounded-xl flex items-center gap-2 flex-shrink-0 order-2 md:order-1">
                    <div class="px-1 inline-flex items-center gap-2">
                        <?php
                        $all_active = empty($current_cat);
                        $all_tab_class = $all_active
                            ? 'h-9 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center whitespace-nowrap'
                            : 'h-9 px-4 rounded-lg flex items-center whitespace-nowrap text-neutral-950 hover:bg-gray-100 transition-colors';
                        ?>
                        <a href="<?php echo esc_url(greenergy_organization_filter_url(['cat' => ''], $organizations_archive_url)); ?>" class="<?php echo esc_attr($all_tab_class); ?>">
                            <span class="text-sm leading-6 <?php echo $all_active ? 'text-white' : 'text-neutral-950'; ?>"><?php echo esc_html__('جميع المنظمات', 'greenergy'); ?></span>
                        </a>
                        <?php foreach ($categories as $cat) :
                            $is_active = (int) $cat->term_id === $current_cat;
                            $tab_class = $is_active
                                ? 'h-9 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center whitespace-nowrap'
                                : 'h-9 px-4 rounded-lg flex items-center whitespace-nowrap text-neutral-950 hover:bg-gray-100 transition-colors';
                        ?>
                            <a href="<?php echo esc_url(greenergy_organization_filter_url(['cat' => (int) $cat->term_id], $organizations_archive_url)); ?>" class="<?php echo esc_attr($tab_class); ?>">
                                <span class="min-w-max text-sm leading-6 <?php echo $is_active ? 'text-white' : 'text-neutral-950'; ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all flex-1 min-w-[120px] order-3">
                    <div class="text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate"><?php echo $country_label; ?></div>
                    <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                        <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                    <select name="country" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                        <option value=""><?php echo esc_html__('كل الدول', 'greenergy'); ?></option>
                        <?php foreach ($countries as $country) : ?>
                            <option value="<?php echo esc_attr((int) $country->term_id); ?>" <?php selected($current_country, (int) $country->term_id); ?>>
                                <?php echo esc_html($country->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-3 cursor-pointer transition-all min-w-[140px] md:min-w-[150px] flex-1 order-1 md:order-3">
                    <div class="text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none"><?php echo $sort_label; ?></div>
                    <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                        <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                    <select name="sort" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                        <option value="latest" <?php selected($current_sort, 'latest'); ?>><?php echo esc_html__('الاحدث', 'greenergy'); ?></option>
                        <option value="oldest" <?php selected($current_sort, 'oldest'); ?>><?php echo esc_html__('الاقدم', 'greenergy'); ?></option>
                        <option value="popular" <?php selected($current_sort, 'popular'); ?>><?php echo esc_html__('الاكثر مشاهدة', 'greenergy'); ?></option>
                        <option value="alpha" <?php selected($current_sort, 'alpha'); ?>><?php echo esc_html__('أبجدي', 'greenergy'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <noscript>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg"><?php echo esc_html__('بحث', 'greenergy'); ?></button>
        </noscript>
    </form>
</div>
