<?php

/**
 * Company Filter Block Template.
 *
 * Filters: Parent Category Tabs | Country (company_location parents) | Sort
 * Direction: RTL — right to left layout
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content    - Block content.
 * @param   array $block      - Block instance.
 * @package YourTheme
 */

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'w-full p-3 bg-white rounded-2xl flex flex-col gap-3',
    'dir'   => 'rtl',
]);

// Block attributes: which categories to show (empty = all).
$raw_attrs = $attributes ?? [];
if (isset($block) && $block instanceof WP_Block && ! empty($block->attributes)) {
    $raw_attrs = array_merge($raw_attrs, $block->attributes);
}
if (isset($block) && $block instanceof WP_Block && ! empty($block->parsed_block['attrs'])) {
    $raw_attrs = array_merge($raw_attrs, $block->parsed_block['attrs']);
}
$visible_ids = isset($raw_attrs['visibleCategoryIds']) && is_array($raw_attrs['visibleCategoryIds'])
    ? array_filter(array_map('absint', $raw_attrs['visibleCategoryIds']))
    : (isset($raw_attrs['visible_category_ids']) && is_array($raw_attrs['visible_category_ids'])
        ? array_filter(array_map('absint', $raw_attrs['visible_category_ids']))
        : []);

// ── Current filter state from URL (cat and country are term IDs) ─────────────
$current_cat     = isset($_GET['cat'])      ? absint($_GET['cat'])      : 0;
$current_country = isset($_GET['country'])  ? absint($_GET['country'])  : 0;
$current_sort    = isset($_GET['sort'])     ? sanitize_text_field(wp_unslash($_GET['sort']))     : 'latest';
$current_search  = isset($_GET['s_company']) ? sanitize_text_field(wp_unslash($_GET['s_company'])) : '';

// Normalize sort: only allow known values so the select shows the correct option after reload.
$allowed_sort = ['latest', 'oldest', 'popular', 'alpha'];
if (! in_array($current_sort, $allowed_sort, true)) {
    $current_sort = 'latest';
}

// Base URL for filter links and form.
// Use the current page URL (without query args) so the filters always reload
// the same template (whether it's the companies archive or a normal page).
// This avoids sending users to a non-existent /companies/ URL that can render
// a blank page with only header/footer.
global $wp;
$current_path = isset($wp->request) ? $wp->request : '';
$companies_archive_url = home_url($current_path ? '/' . ltrim($current_path, '/') : '/');

// ── 1. Parent Categories (tabs) ───────────────────────────────────────────────
$categories = get_terms([
    'taxonomy'   => 'company_category',
    'hide_empty' => false,
    'parent'     => 0,
]);

if (empty($categories) || is_wp_error($categories)) {
    $categories = [
        (object) ['term_id' => 1, 'slug' => 'technology',  'name' => 'تكنولوجيا'],
        (object) ['term_id' => 2, 'slug' => 'energy',      'name' => 'طاقة'],
        (object) ['term_id' => 3, 'slug' => 'construction', 'name' => 'إنشاءات'],
        (object) ['term_id' => 4, 'slug' => 'finance',     'name' => 'مالية'],
    ];
}
// Admin: show only selected categories when visibleCategoryIds is set.
if (! empty($visible_ids)) {
    $categories = array_filter($categories, function ($term) use ($visible_ids) {
        return in_array((int) $term->term_id, $visible_ids, true);
    });
    $categories = array_values($categories);
}

// ── 2. Countries — parent terms of company_location ──────────────────────────
$countries = get_terms([
    'taxonomy'   => 'company_location',
    'hide_empty' => false,
    'parent'     => 0,   // parents only = countries
]);

if (empty($countries) || is_wp_error($countries)) {
    // Fallback for dev/preview
    $countries = [
        (object) ['term_id' => 1, 'slug' => 'saudi-arabia', 'name' => 'المملكة العربية السعودية'],
        (object) ['term_id' => 2, 'slug' => 'uae',          'name' => 'الإمارات'],
        (object) ['term_id' => 3, 'slug' => 'egypt',        'name' => 'مصر'],
        (object) ['term_id' => 4, 'slug' => 'jordan',       'name' => 'الأردن'],
    ];
}

// Normalize country: ensure URL ID exists in options so select shows correct value after reload.
$country_ids = wp_list_pluck($countries, 'term_id');
if ($current_country > 0 && ! in_array($current_country, $country_ids, true)) {
    $current_country = 0;
}

// ── Helper: build filter URL preserving current params (cat/country as term IDs) ─
if (! function_exists('greenergy_company_filter_url')) {
    function greenergy_company_filter_url(array $new_args, $base_url = '')
    {
        // Always point filter links to the companies archive (or /companies/ as a safe fallback).
        $archive = get_post_type_archive_link('companies');
        $base = $base_url ? $base_url : ($archive ?: home_url('/companies/'));
        $cat_val     = isset($_GET['cat'])     ? absint($_GET['cat'])     : 0;
        $country_val = isset($_GET['country']) ? absint($_GET['country']) : 0;
        $current = array_filter([
            'cat'        => $cat_val > 0 ? $cat_val : '',
            'country'    => $country_val > 0 ? $country_val : '',
            'sort'       => isset($_GET['sort'])      ? sanitize_text_field(wp_unslash($_GET['sort']))      : '',
            's_company'  => isset($_GET['s_company']) ? sanitize_text_field(wp_unslash($_GET['s_company'])) : '',
        ]);
        $merged = array_merge($current, $new_args);
        $merged = array_filter($merged);
        return add_query_arg($merged, $base);
    }
}

?>


<!-- ── Filter Bar ────────────────────────────────────────────────────────── -->
<div <?php echo $wrapper_attributes; ?>>
    <form method="get" action="<?php echo esc_url($companies_archive_url); ?>">
        <input type="hidden" name="cat" value="<?php echo $current_cat > 0 ? (int) $current_cat : ''; ?>" />
        <?php
        // Shared sort label helper
        $sort_label = match ($current_sort) {
            'popular' => 'الاكثر مشاهدة',
            'oldest'  => 'الاقدم',
            'alpha'   => 'أبجدي',
            default  => 'الاحدث',
        };

        // Shared country label helper (by term_id)
        $sel           = array_filter((array) $countries, fn($c) => (int) $c->term_id === $current_country);
        $country_label = $sel ? esc_html(reset($sel)->name) : 'كل الدول';
        ?>

        <!-- Single set of controls (no duplicate names) — layout via flex order -->
        <div class="flex flex-col gap-2">
            <!-- Row 1: Search with smart autocomplete -->
            <div class="flex justify-center md:mb-0">
                <div class="js-company-filter-search-wrap relative w-full md:max-w-xl">
                    <div class="group w-full h-11 px-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-green-500 transition-all">
                        <svg class="w-6 h-6 inline self-center text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                        </svg>
                        <input type="text" name="s_company" id="js-company-filter-search-input"
                            value="<?php echo esc_attr($current_search); ?>"
                            placeholder="ابحث عن شركة ..."
                            autocomplete="off"
                            class="js-company-filter-search-input flex-1 h-6 text-right text-stone-500 text-sm font-normal leading-4 bg-transparent border-none outline-none focus:ring-0"
                            aria-autocomplete="list"
                            aria-controls="js-company-filter-suggestions"
                            aria-expanded="false"
                            role="combobox">
                        <?php if ($current_search !== '') : ?>
                            <a href="<?php echo esc_url(greenergy_company_filter_url(['s_company' => ''], $companies_archive_url)); ?>" class="text-stone-300 hover:text-red-500 transition-colors flex-shrink-0">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <ul id="js-company-filter-suggestions" class="js-company-filter-suggestions absolute top-full right-0 left-0 z-20 mt-1 py-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto hidden" role="listbox"></ul>
                </div>
            </div>

            <!-- Row 2: Tabs | Country | Sort — flex wrap so sm gets [Sort][Tabs][Country] order -->
            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <!-- Tabs — 2/3 on md, full on sm; order 2 on md, 2 on sm -->
                <div style="scrollbar-width:none"
                    class="overflow-x-auto w-full md:w-2/3 bg-green-200 h-12 p-1.5 rounded-xl flex items-center gap-2 flex-shrink-0 order-2 md:order-1">
                    <div class="px-1 inline-flex items-center gap-2">
                        <?php
                        $all_active    = empty($current_cat);
                        $all_tab_class = $all_active
                            ? 'h-9 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center whitespace-nowrap'
                            : 'h-9 px-4 rounded-lg flex items-center whitespace-nowrap text-neutral-950 hover:bg-gray-100 transition-colors';
                        ?>
                        <a href="<?php echo esc_url(greenergy_company_filter_url(['cat' => ''], $companies_archive_url)); ?>" class="<?php echo esc_attr($all_tab_class); ?>">
                            <span class="text-sm leading-6 <?php echo $all_active ? 'text-white' : 'text-neutral-950'; ?>">جميع الشركات</span>
                        </a>
                        <?php foreach ($categories as $cat) :
                            $is_active = ((int) $cat->term_id === $current_cat);
                            $tab_class = $is_active
                                ? 'h-9 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center whitespace-nowrap'
                                : 'h-9 px-4 rounded-lg flex items-center whitespace-nowrap text-neutral-950 hover:bg-gray-100 transition-colors';
                        ?>
                            <a href="<?php echo esc_url(greenergy_company_filter_url(['cat' => (int) $cat->term_id], $companies_archive_url)); ?>" class="<?php echo esc_attr($tab_class); ?>">
                                <span class="min-w-max text-sm leading-6 <?php echo $is_active ? 'text-white' : 'text-neutral-950'; ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Country — one select only -->
                <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all flex-1 min-w-[120px] order-3">
                    <div class="text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate"><?php echo $country_label; ?></div>
                    <svg class="w-5 h-5 md:w-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                    <select name="country" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                        <option value="">كل الدول</option>
                        <?php foreach ($countries as $country) : ?>
                            <option value="<?php echo esc_attr((int) $country->term_id); ?>" <?php selected($current_country, (int) $country->term_id); ?>>
                                <?php echo esc_html($country->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort — one select only -->
                <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-3 cursor-pointer transition-all min-w-[140px] md:min-w-[150px] flex-1 order-1 md:order-3">
                    <div class="text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none"><?php echo $sort_label; ?></div>
                    <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                    <select name="sort" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                        <option value="latest" <?php selected($current_sort, 'latest'); ?>>الاحدث</option>
                        <option value="oldest" <?php selected($current_sort, 'oldest'); ?>>الاقدم</option>
                        <option value="popular" <?php selected($current_sort, 'popular'); ?>>الاكثر مشاهدة</option>
                        <option value="alpha" <?php selected($current_sort, 'alpha'); ?>>أبجدي</option>
                    </select>
                </div>
            </div>
        </div>

        <noscript>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg">بحث</button>
        </noscript>

    </form>
</div><!-- /Filter Bar -->