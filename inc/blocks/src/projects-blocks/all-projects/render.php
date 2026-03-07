<?php

/**
 * All Projects Block — dynamic list with filter and pagination (no full page reload).
 * Uses GET: s_proj, type, country, sort. Query via greenergy_projects_query().
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

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
    'title'               => 'جميع المشاريع',
    'description'         => 'اكتشف أبرز المشاريع في مجال الطاقة المتجددة والاستدامة',
    'perPage'             => 15,
    'visibleTypeIds'      => [],
    'visibleCountryIds'   => [],
]);

$title    = (string) ($attrs['title'] ?? 'جميع المشاريع');
$desc     = (string) ($attrs['description'] ?? '');
$per_page = max(1, min(24, (int) ($attrs['perPage'] ?? 15)));

$list_query = function_exists('greenergy_projects_query') ? greenergy_projects_query([
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
]) : new WP_Query([
    'post_type'      => 'projects',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
]);

$total_pages  = $list_query->max_num_pages;
$current_page = max(1, $list_query->get('paged'));
$found_posts  = $list_query->found_posts;

$block_id_attr = 'greenergy-all-projects-' . substr(uniqid(), -8);
$archive_url   = function_exists('get_post_type_archive_link') ? get_post_type_archive_link('projects') : home_url('/projects/');
$archive_url   = $archive_url ?: home_url('/');

$current_type    = isset($_GET['type']) ? absint($_GET['type']) : 0;
$current_country = isset($_GET['country']) ? absint($_GET['country']) : 0;
$current_sort    = isset($_GET['sort']) ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'latest';
$current_search  = isset($_GET['s_proj']) ? sanitize_text_field(wp_unslash($_GET['s_proj'])) : '';

// Countries (project_location parent = 0)
$countries = get_terms([
    'taxonomy'   => 'project_location',
    'hide_empty' => false,
    'parent'     => 0,
]);
if (empty($countries) || is_wp_error($countries)) {
    $countries = [];
}

$visible_type_ids    = isset($attrs['visibleTypeIds']) && is_array($attrs['visibleTypeIds']) ? array_filter(array_map('absint', $attrs['visibleTypeIds'])) : [];
$visible_country_ids = isset($attrs['visibleCountryIds']) && is_array($attrs['visibleCountryIds']) ? array_filter(array_map('absint', $attrs['visibleCountryIds'])) : [];

$type_args = ['taxonomy' => 'project_type', 'hide_empty' => false, 'parent' => 0];
if (! empty($visible_type_ids)) {
    $type_args['include'] = $visible_type_ids;
}
$project_types = get_terms($type_args);
if (empty($project_types) || is_wp_error($project_types)) {
    $project_types = [];
}
if (! empty($visible_type_ids)) {
    usort($project_types, function ($a, $b) use ($visible_type_ids) {
        $pos_a = array_search((int) $a->term_id, $visible_type_ids, true);
        $pos_b = array_search((int) $b->term_id, $visible_type_ids, true);
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        return $pos_a - $pos_b;
    });
}

$country_filter = $countries;
if (! empty($visible_country_ids)) {
    $country_filter = array_filter($countries, function ($t) use ($visible_country_ids) {
        return in_array((int) $t->term_id, $visible_country_ids, true);
    });
    usort($country_filter, function ($a, $b) use ($visible_country_ids) {
        $pos_a = array_search((int) $a->term_id, $visible_country_ids, true);
        $pos_b = array_search((int) $b->term_id, $visible_country_ids, true);
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        return $pos_a - $pos_b;
    });
}

$type_label = __('نوع المشروع', 'greenergy');
foreach ($project_types as $t) {
    if ((int) $t->term_id === $current_type) {
        $type_label = $t->name;
        break;
    }
}
$country_label = __('كل الدول', 'greenergy');
foreach ($country_filter as $c) {
    if ((int) $c->term_id === $current_country) {
        $country_label = $c->name;
        break;
    }
}
$sort_label = $current_sort === 'oldest' ? __('الأقدم', 'greenergy') : ($current_sort === 'popular' ? __('الأكثر مشاهدة', 'greenergy') : __('الأحدث', 'greenergy'));
?>
<form method="get" action="<?php echo esc_url($archive_url); ?>" class="contents js-all-projects-filter-form" id="js-all-projects-filter-form" data-block-id="<?php echo esc_attr($block_id_attr); ?>">
    <div class="grid max-md:grid-cols-2 sm:grid-cols-[1fr_auto_auto_auto] gap-2 sm:gap-3 mt-8 w-full max-w-full overflow-hidden">

        <!-- Search -->
        <div class="js-projects-filter-search-wrap relative col-span-2 sm:col-span-1 min-w-0">
            <div class="group w-full h-12 px-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-green-500 transition-all bg-white">
                <svg class="w-6 h-6 flex-shrink-0 text-stone-400 group-hover:text-green-600 transition-colors" aria-hidden="true">
                    <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
                <input type="text" name="s_proj" id="js-projects-filter-search-input"
                    placeholder="<?php echo esc_attr__('ابحث عن مشروع ...', 'greenergy'); ?>"
                    value="<?php echo esc_attr($current_search); ?>"
                    autocomplete="off"
                    class="flex-1 min-w-0 h-6 text-right text-stone-500 text-sm bg-transparent border-none outline-none focus:ring-0">
            </div>
        </div>

        <!-- Type (تصنيف) -->
        <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all min-w-0 sm:min-w-[130px] flex-shrink-0">
            <div class="js-all-projects-filter-label text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate min-w-0"><?php echo esc_html($type_label); ?></div>
            <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
            <select name="type" class="js-all-projects-filter absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                <option value=""><?php esc_html_e('الكل', 'greenergy'); ?></option>
                <?php foreach ($project_types as $term) : ?>
                    <option value="<?php echo esc_attr((int) $term->term_id); ?>" <?php selected($current_type, (int) $term->term_id); ?>><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Country (دولة) -->
        <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all min-w-0 sm:min-w-[130px] flex-shrink-0">
            <div class="js-all-projects-filter-label text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate min-w-0"><?php echo esc_html($country_label); ?></div>
            <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
            <select name="country" class="js-all-projects-filter absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                <option value=""><?php esc_html_e('كل الدول', 'greenergy'); ?></option>
                <?php foreach ($country_filter as $c) : ?>
                    <option value="<?php echo esc_attr((int) $c->term_id); ?>" <?php selected($current_country, (int) $c->term_id); ?>><?php echo esc_html($c->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Sort (فلترة حسب) -->
        <div class="group relative h-12 px-3 max-sm:col-span-full  md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all min-w-0 sm:min-w-[140px] flex-shrink-0">
            <div class="js-all-projects-filter-label text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate min-w-0"><?php echo esc_html($sort_label); ?></div>
            <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
            <select name="sort" class="js-all-projects-filter absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                <option value="latest" <?php selected($current_sort, 'latest'); ?>><?php esc_html_e('الأحدث', 'greenergy'); ?></option>
                <option value="oldest" <?php selected($current_sort, 'oldest'); ?>><?php esc_html_e('الأقدم', 'greenergy'); ?></option>
                <option value="popular" <?php selected($current_sort, 'popular'); ?>><?php esc_html_e('الأكثر مشاهدة', 'greenergy'); ?></option>
            </select>
        </div>
    </div>
</form>

<section class="flex flex-col items-center gap-8">
    <header class="text-center space-y-3">
        <div class="inline-flex items-center title-badge"><?php echo esc_html($title); ?></div>
        <p class="text-stone-500 text-base"><?php echo esc_html($desc); ?></p>
    </header>
</section>

<section class="w-full mx-auto flex flex-col gap-6 js-all-projects-block" id="<?php echo esc_attr($block_id_attr); ?>"
    data-per-page="<?php echo esc_attr((string) $per_page); ?>"
    data-block-id="<?php echo esc_attr($block_id_attr); ?>"
    data-type="<?php echo esc_attr($current_type); ?>"
    data-country="<?php echo esc_attr($current_country); ?>"
    data-sort="<?php echo esc_attr($current_sort); ?>"
    data-s-proj="<?php echo esc_attr($current_search); ?>"
    data-total-pages="<?php echo esc_attr((string) $total_pages); ?>">

    <div class="relative js-ajax-grid" data-loader-text="<?php echo esc_attr(__('جاري جلب المشاريع', 'greenergy')); ?>">
        <div class="ajax-loader" aria-hidden="true">
            <div class="ajax-loader-container">
                <div class="ajax-loader-spinner-wrapper">
                    <div class="ajax-loader-spinner"></div>
                    <div class="ajax-loader-logo">
                        <i class="fas fa-leaf text-2xl"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="ajax-loader-text"><?php echo esc_html(__('جاري جلب المشاريع', 'greenergy')); ?></div>
                    <div class="ajax-loader-dots">
                        <div class="ajax-loader-dot"></div>
                        <div class="ajax-loader-dot"></div>
                        <div class="ajax-loader-dot"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $from = $found_posts > 0 ? (($current_page - 1) * $per_page) + 1 : 0;
        $to   = $found_posts > 0 ? min($current_page * $per_page, $found_posts) : 0;
        $count_text = $found_posts > 0
            ? sprintf(/* translators: 1: from number, 2: to number, 3: total count */__('عرض %1$s - %2$s من %3$s مشاريع', 'greenergy'), number_format_i18n($from), number_format_i18n($to), number_format_i18n($found_posts))
            : __('0 مشروع', 'greenergy');
        ?>
        <p class="js-all-projects-count text-neutral-500 text-sm text-center mb-4" aria-live="polite"><?php echo esc_html($count_text); ?></p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 js-all-projects-grid">
            <?php
            if ($list_query->have_posts()) {
                while ($list_query->have_posts()) {
                    $list_query->the_post();
                    get_template_part('templates/components/project-card', null, ['post_id' => get_the_ID(), 'is_featured' => false]);
                }
                wp_reset_postdata();
            } else {
                echo '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا توجد مشاريع تطابق المعايير.', 'greenergy') . '</p>';
            }
            ?>
        </div>
    </div>

    <div class="js-all-projects-pagination-wrap mt-6">
        <?php if ($total_pages > 1) : ?>
            <nav class="greenergy-pagination greenergy-all-projects-pagination flex justify-center items-center gap-2 flex-wrap" aria-label="<?php esc_attr_e('تنقل المشاريع', 'greenergy'); ?>">
                <?php if ($current_page > 1) : ?>
                    <button type="button" class="js-all-projects-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page - 1); ?>" aria-label="<?php esc_attr_e('الصفحة السابقة', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg></button>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <?php $active = $i === $current_page; ?>
                    <button type="button" class="js-all-projects-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm <?php echo $active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500'; ?>" data-page="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></button>
                <?php endfor; ?>
                <?php if ($current_page < $total_pages) : ?>
                    <button type="button" class="js-all-projects-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page + 1); ?>" aria-label="<?php esc_attr_e('الصفحة التالية', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg></button>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>