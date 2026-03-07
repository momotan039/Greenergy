<?php

/**
 * Stats Helper Functions for Main Banner
 *
 * @package Greenergy
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Count of countries (parent=0 terms only) in a location taxonomy.
 *
 * @param string $taxonomy Taxonomy name (e.g. project_location, company_location).
 * @return int
 */
function greenergy_count_countries_in_taxonomy($taxonomy)
{
    if (! taxonomy_exists($taxonomy)) {
        return 0;
    }
    $terms = get_terms(['taxonomy' => $taxonomy, 'parent' => 0, 'hide_empty' => true]);
    return (is_array($terms) && ! is_wp_error($terms)) ? count($terms) : 0;
}

/**
 * Get a single dynamic stat value based on source
 *
 * Data sources:
 * - projects_count: إجمالي عدد المشاريع
 * - projects_countries_count: عدد الدول المربوطة في المشاريع فقط (دول بدون مدن)
 * - platform_countries_count: عدد الدول الموجودة في المنصة (دول بدون مدن، فريدة عبر المشاريع/الشركات/المنظمات)
 *
 * @param string $source The data source key
 * @return int|string The numeric value
 */
function greenergy_get_dynamic_stat_value($source)
{
    switch ($source) {
        case 'news_count':
            return (int) wp_count_posts('news')->publish;

        case 'jobs_count':
            return (int) wp_count_posts('jobs')->publish;

        case 'gold_jobs_count':
            $query = new WP_Query([
                'post_type'      => 'jobs',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => [['key' => '_is_gold', 'value' => 'yes', 'compare' => '=']],
            ]);
            return (int) $query->found_posts;

        case 'pages_count':
            return (int) wp_count_posts('page')->publish;

        case 'projects_count':
            return post_type_exists('projects') ? (int) wp_count_posts('projects')->publish : 0;

        case 'projects_countries_count':
            return greenergy_count_countries_in_taxonomy('project_location');

        case 'platform_countries_count':
            $names = [];
            foreach (['project_location', 'company_location', 'organization_location'] as $tax) {
                if (! taxonomy_exists($tax)) {
                    continue;
                }
                $terms = get_terms(['taxonomy' => $tax, 'parent' => 0, 'hide_empty' => true]);
                if (is_array($terms) && ! is_wp_error($terms)) {
                    foreach ($terms as $t) {
                        $names[$t->name] = true;
                    }
                }
            }
            return count($names);

        default:
            return 0;
    }
}

/**
 * Whether the current request is in a "projects archive" context (for banner stats).
 * Uses main query, queried object, current block template, and URL path.
 *
 * @return bool
 */
function greenergy_is_projects_archive_context()
{
    if (! post_type_exists('projects')) {
        return false;
    }

    // 1. Main query (post type archive)
    if (is_post_type_archive('projects')) {
        return true;
    }

    // 2. Queried object (WP_Post_Type when on archive)
    $queried = get_queried_object();
    if ($queried instanceof WP_Post_Type && $queried->name === 'projects') {
        return true;
    }

    // 3. Global wp_query (in case conditional tags weren't set yet)
    global $wp_query;
    if ($wp_query && $wp_query->is_post_type_archive('projects')) {
        return true;
    }

    // 4. Block theme: current template is archive-projects (e.g. greenergy_theme//archive-projects)
    if (
        isset($GLOBALS['_wp_current_template_id']) && is_string($GLOBALS['_wp_current_template_id'])
        && strpos($GLOBALS['_wp_current_template_id'], 'archive-projects') !== false
    ) {
        return true;
    }

    // 5. URL path: current request path matches projects archive path or ends with archive slug
    $archive_link = get_post_type_archive_link('projects');
    if (! $archive_link || ! isset($_SERVER['REQUEST_URI'])) {
        return false;
    }
    $archive_path = trim(trim((string) wp_parse_url($archive_link, PHP_URL_PATH)), '/');
    $current_uri  = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
    $current_path = trim(trim((string) strtok($current_uri, '?'), '/'), '/');
    if ($archive_path !== '' && $current_path !== '') {
        if ($current_path === $archive_path || strpos($current_path, $archive_path . '/') === 0) {
            return true;
        }
        // Path ends with archive slug (e.g. /project or /projects for different permalink setups)
        $pt_obj = get_post_type_object('projects');
        $slug   = ($pt_obj && ! empty($pt_obj->rewrite['slug'])) ? (string) $pt_obj->rewrite['slug'] : 'project';
        if ($slug !== '' && (rtrim($current_path, '/') === $slug || preg_match('#/' . preg_quote($slug, '#') . '/?$#', '/' . $current_path))) {
            return true;
        }
    }

    return false;
}

/**
 * Default stats when banner has showStats but no custom stats.
 *
 * @param string|null $context Ignored; stats are chosen by page (projects archive → projects, else → jobs).
 * @return array List of stat items (value, label, icon, iconType, optional mode/dataSource).
 */
function greenergy_main_banner_default_stats($context = null)
{
    $use_projects = function_exists('greenergy_is_projects_archive_context') && greenergy_is_projects_archive_context();

    if ($use_projects) {
        $projects_count  = (int) wp_count_posts('projects')->publish;
        $countries_count = function_exists('greenergy_get_dynamic_stat_value')
            ? greenergy_get_dynamic_stat_value('projects_countries_count')
            : 0;
        return [
            ['value' => (string) $projects_count, 'label' => __('محطة', 'greenergy'), 'icon' => 'buildings-2.svg', 'iconType' => 'platform'],
            ['value' => '1000', 'label' => __('كمية الكربون المتوفرة', 'greenergy'), 'icon' => 'award.svg', 'iconType' => 'platform'],
            ['value' => (string) $countries_count, 'label' => __('دولة', 'greenergy'), 'icon' => 'global.svg', 'iconType' => 'platform'],
        ];
    }
    return [
        ['value' => '250', 'label' => __('وظيفة متاحة', 'greenergy'), 'icon' => 'clipboard-text.svg', 'iconType' => 'platform', 'mode' => 'dynamic', 'dataSource' => 'jobs_count'],
        ['value' => '300', 'label' => __('فرص ذهبية', 'greenergy'), 'icon' => 'profile-2user.svg', 'iconType' => 'platform', 'mode' => 'dynamic', 'dataSource' => 'gold_jobs_count'],
        ['value' => '+150', 'label' => __('شريك موثوق', 'greenergy'), 'icon' => 'medal.svg', 'iconType' => 'platform', 'mode' => 'manual', 'dataSource' => 'news_count'],
    ];
}

/**
 * Get dynamic stats from the platform (legacy/helper)
 *
 * @return array Array of stats objects {value, label, icon}
 */
function greenergy_get_dynamic_banner_stats()
{
    return [
        [
            'value' => '+' . greenergy_get_dynamic_stat_value('jobs_count'),
            'label' => __('وضيفة متاحة', 'greenergy'),
            'icon'  => 'clipboard-text.svg'
        ],
        [
            'value' => '+' . greenergy_get_dynamic_stat_value('gold_jobs_count'),
            'label' => __('فرصة ذهبية', 'greenergy'),
            'icon'  => 'profile-2user.svg'
        ],
        [
            'value' => '+' . greenergy_get_dynamic_stat_value('news_count'),
            'label' => __('خبر متجدد', 'greenergy'),
            'icon'  => 'medal.svg'
        ]
    ];
}
