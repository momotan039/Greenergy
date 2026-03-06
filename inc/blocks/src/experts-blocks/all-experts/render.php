<?php

/**
 * All Experts Block — dynamic list with search, location filter, and pagination.
 * No featured logic. Uses GET: location, cat, s_exp. Query via greenergy_experts_query().
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
    'title'                 => 'جميع الخبراء',
    'description'           => 'اكتشف الخبراء والمتخصصين في مجالات الطاقة والمجتمع',
    'perPage'               => 9,
    'visibleLocationIds'     => [],
    'visibleCategoryIds'    => [],
]);

$title    = (string) ($attrs['title'] ?? 'جميع الخبراء');
$desc     = (string) ($attrs['description'] ?? '');
$per_page = max(1, min(24, (int) ($attrs['perPage'] ?? 9)));

$list_query = function_exists('greenergy_experts_query') ? greenergy_experts_query([
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
]) : new WP_Query([
    'post_type'      => 'experts',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => max(1, get_query_var('paged', 1)),
]);

$total_pages  = $list_query->max_num_pages;
$current_page = max(1, $list_query->get('paged'));
$found_posts  = $list_query->found_posts;

$block_id_attr = 'greenergy-all-experts-' . substr(uniqid(), -8);

$experts_archive_url = function_exists('get_post_type_archive_link') ? get_post_type_archive_link('experts') : home_url('/experts/');
$experts_archive_url = $experts_archive_url ?: home_url('/');

$current_location = isset($_GET['location']) ? absint($_GET['location']) : 0;
$current_cat      = isset($_GET['cat']) ? absint($_GET['cat']) : 0;
$current_search   = isset($_GET['s_exp']) ? sanitize_text_field(wp_unslash($_GET['s_exp'])) : '';

$visible_location_ids = isset($attrs['visibleLocationIds']) && is_array($attrs['visibleLocationIds'])
    ? array_filter(array_map('absint', $attrs['visibleLocationIds']))
    : [];

$location_args = [
    'taxonomy'   => 'expert_location',
    'hide_empty' => false,
    'parent'     => 0,
];
if (! empty($visible_location_ids)) {
    $location_args['include'] = $visible_location_ids;
}
$locations = get_terms($location_args);
if (empty($locations) || is_wp_error($locations)) {
    $locations = [];
}
if (! empty($visible_location_ids)) {
    usort($locations, function ($a, $b) use ($visible_location_ids) {
        $pos_a = array_search((int) $a->term_id, $visible_location_ids, true);
        $pos_b = array_search((int) $b->term_id, $visible_location_ids, true);
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        return $pos_a - $pos_b;
    });
}

$location_label = __('كل المواقع', 'greenergy');
foreach ($locations as $term) {
    if ((int) $term->term_id === $current_location) {
        $location_label = $term->name;
        break;
    }
}

$visible_category_ids = isset($attrs['visibleCategoryIds']) && is_array($attrs['visibleCategoryIds'])
    ? array_filter(array_map('absint', $attrs['visibleCategoryIds']))
    : [];
$category_args = [
    'taxonomy'   => 'expert_category',
    'hide_empty' => false,
    'parent'     => 0,
];
if (! empty($visible_category_ids)) {
    $category_args['include'] = $visible_category_ids;
}
$categories = get_terms($category_args);
if (empty($categories) || is_wp_error($categories)) {
    $categories = [];
}
if (! empty($visible_category_ids)) {
    usort($categories, function ($a, $b) use ($visible_category_ids) {
        $pos_a = array_search((int) $a->term_id, $visible_category_ids, true);
        $pos_b = array_search((int) $b->term_id, $visible_category_ids, true);
        if ($pos_a === false) $pos_a = 999;
        if ($pos_b === false) $pos_b = 999;
        return $pos_a - $pos_b;
    });
}
$cat_label = __('الكل', 'greenergy');
foreach ($categories as $c) {
    if ((int) $c->term_id === $current_cat) {
        $cat_label = $c->name;
        break;
    }
}
?>
<form method="get" action="<?php echo esc_url($experts_archive_url); ?>" class="contents js-all-experts-filter-form" id="js-all-experts-filter-form" data-block-id="<?php echo esc_attr($block_id_attr); ?>">
    <div class="grid grid-cols-2 sm:grid-cols-[1fr_auto_auto] gap-2 sm:gap-3 mt-8 w-full max-w-full">

        <!-- Search -->
        <div class="js-exp-filter-search-wrap relative col-span-2 sm:col-span-1 min-w-0">
            <div class="group w-full h-12 px-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-green-500 transition-all bg-white">
                <svg class="w-6 h-6 flex-shrink-0 text-stone-400 group-hover:text-green-600 transition-colors" aria-hidden="true">
                    <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
                <input type="text" name="s_exp" id="js-exp-filter-search-input"
                    placeholder="<?php echo esc_attr__('اسم خبير، تخصص، جهة عمل، أو مصطلح تقني ...', 'greenergy'); ?>"
                    title="<?php echo esc_attr__('البحث الحر بالكلمات المفتاحية: اسم خبير، تخصص، جهة عمل، أو مصطلح تقني', 'greenergy'); ?>"
                    value="<?php echo esc_attr($current_search); ?>"
                    autocomplete="off" role="combobox"
                    aria-autocomplete="list" aria-controls="js-exp-filter-suggestions" aria-expanded="false"
                    class="flex-1 min-w-0 h-6 text-right text-stone-500 text-sm bg-transparent border-none outline-none focus:ring-0">
            </div>
            <ul id="js-exp-filter-suggestions" class="js-exp-filter-suggestions absolute top-full right-0 left-0 z-20 mt-1 py-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto hidden" role="listbox"></ul>
        </div>

        <!-- Location -->
        <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-2 cursor-pointer transition-all min-w-0 sm:min-w-[130px] flex-shrink-0">
            <div class="js-all-experts-filter-label text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate min-w-0"><?php echo esc_html($location_label); ?></div>
            <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
            <select name="location" class="js-all-experts-filter absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                <option value=""><?php esc_html_e('كل المواقع', 'greenergy'); ?></option>
                <?php foreach ($locations as $term) : ?>
                    <option value="<?php echo esc_attr((int) $term->term_id); ?>" <?php selected($current_location, (int) $term->term_id); ?>>
                        <?php echo esc_html($term->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Category -->
        <div class="group relative h-12 px-3 md:px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 focus-within:outline-2 focus-within:outline-green-500 flex justify-between items-center gap-3 cursor-pointer transition-all min-w-0 sm:min-w-[140px] flex-shrink-0">
            <div class="js-all-experts-filter-label text-right text-neutral-950 text-sm font-normal leading-6 pointer-events-none truncate min-w-0"><?php echo esc_html($cat_label); ?></div>
            <svg class="w-5 h-5 text-stone-400 group-hover:text-green-600 transition-colors pointer-events-none flex-shrink-0" aria-hidden="true">
                <use href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
            <select name="cat" class="js-all-experts-filter absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 focus:outline-none focus:ring-0">
                <option value="" <?php selected($current_cat, 0); ?>><?php esc_html_e('الكل', 'greenergy'); ?></option>
                <?php foreach ($categories as $term) : ?>
                    <option value="<?php echo esc_attr((int) $term->term_id); ?>" <?php selected($current_cat, (int) $term->term_id); ?>>
                        <?php echo esc_html($term->name); ?>
                    </option>
                <?php endforeach; ?>
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

<section class="w-full mx-auto flex flex-col gap-6 js-all-experts-block" id="<?php echo esc_attr($block_id_attr); ?>"
    data-per-page="<?php echo esc_attr((string) $per_page); ?>"
    data-block-id="<?php echo esc_attr($block_id_attr); ?>"
    data-location="<?php echo esc_attr($current_location); ?>"
    data-cat="<?php echo esc_attr($current_cat); ?>"
    data-s-exp="<?php echo esc_attr($current_search); ?>"
    data-total-pages="<?php echo esc_attr((string) $total_pages); ?>">

    <div class="relative js-ajax-grid" data-loader-text="<?php echo esc_attr(__('جاري جلب الخبراء', 'greenergy')); ?>">
        <div class="ajax-loader" aria-hidden="true">
            <div class="ajax-loader-container">
                <div class="ajax-loader-spinner-wrapper">
                    <div class="ajax-loader-spinner"></div>
                    <div class="ajax-loader-logo">
                        <i class="fas fa-leaf text-2xl"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="ajax-loader-text"><?php echo esc_html(__('جاري جلب الخبراء', 'greenergy')); ?></div>
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
            ? sprintf(/* translators: 1: from number, 2: to number, 3: total count */__('عرض %1$s - %2$s من %3$s خبير', 'greenergy'), number_format_i18n($from), number_format_i18n($to), number_format_i18n($found_posts))
            : __('0 خبير', 'greenergy');
        ?>
        <p class="js-all-experts-count text-neutral-500 text-sm text-center mb-4" aria-live="polite"><?php echo esc_html($count_text); ?></p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 gap-y-12 js-all-experts-grid">
            <?php
            if ($list_query->have_posts()) {
                while ($list_query->have_posts()) {
                    $list_query->the_post();
                    get_template_part('templates/components/expert-card', null, ['post_id' => get_the_ID()]);
                }
                wp_reset_postdata();
            } else {
                echo '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا يوجد خبراء يطابقون المعايير.', 'greenergy') . '</p>';
            }
            ?>
        </div>
    </div>

    <div class="js-all-experts-pagination-wrap mt-6">
        <?php if ($total_pages > 1) : ?>
            <nav class="greenergy-pagination greenergy-all-experts-pagination flex justify-center items-center gap-2 flex-wrap" aria-label="<?php esc_attr_e('تنقل الخبراء', 'greenergy'); ?>">
                <?php if ($current_page > 1) : ?>
                    <button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page - 1); ?>" aria-label="<?php esc_attr_e('الصفحة السابقة', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg></button>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <?php $active = $i === $current_page; ?>
                    <button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm <?php echo $active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500'; ?>" data-page="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></button>
                <?php endfor; ?>
                <?php if ($current_page < $total_pages) : ?>
                    <button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page + 1); ?>" aria-label="<?php esc_attr_e('الصفحة التالية', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg></button>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>