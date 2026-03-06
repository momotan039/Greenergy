<?php

/**
 * Theme Helper Functions
 *
 * Utility functions used across the theme.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Get template part with data passing
 *
 * @param string $slug   Template slug.
 * @param string $name   Template name.
 * @param array  $args   Arguments to pass to template.
 */
function greenergy_get_template($slug, $name = '', $args = [])
{
    if (! empty($args) && is_array($args)) {
        extract($args);
    }

    $template = '';

    if ($name) {
        $template = locate_template(["{$slug}-{$name}.php", "{$slug}.php"]);
    } else {
        $template = locate_template(["{$slug}.php"]);
    }

    if ($template) {
        include $template;
    }
}

/**
 * Get post thumbnail with fallback
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @param array  $attr    Image attributes.
 * @return string Image HTML.
 */
function greenergy_get_thumbnail($post_id = null, $size = 'card-thumbnail', $attr = [])
{
    if (! $post_id) {
        $post_id = get_the_ID();
    }

    if (has_post_thumbnail($post_id)) {
        $default_attr = [
            'loading'  => 'lazy',
            'decoding' => 'async',
            'class'    => 'w-full h-full object-cover',
        ];
        $attr = wp_parse_args($attr, $default_attr);

        return get_the_post_thumbnail($post_id, $size, $attr);
    }

    // Return placeholder
    $placeholder = GREENERGY_ASSETS_URI . '/images/placeholders/default.svg';
    return '<img src="' . esc_url($placeholder) . '" alt="" class="w-full h-full object-cover" loading="lazy">';
}

/**
 * Get reading time estimate
 *
 * @param int $post_id Post ID.
 * @return int Minutes to read.
 */
function greenergy_reading_time($post_id = null)
{
    if (! $post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average 200 words per minute

    return max(1, $reading_time);
}

/**
 * Get all companies/organizations linked to an expert (from company-team blocks).
 * Populated automatically when expert is added in company-team on company/org page.
 *
 * @param int $post_id Expert post ID.
 * @return array List of items with 'id' and 'type' (organizations|companies).
 */
function greenergy_expert_get_linked_entities($post_id)
{
    $post_id = absint($post_id);
    if (! $post_id || get_post_type($post_id) !== 'experts') {
        return [];
    }
    $ids = get_post_meta($post_id, 'expert_linked_entity_ids', true);
    if (! is_array($ids)) {
        $ids = [];
    }
    $out = [];
    foreach (array_map('absint', $ids) as $id) {
        if ($id <= 0) {
            continue;
        }
        $post = get_post($id);
        if (! $post || ! in_array($post->post_type, ['organizations', 'companies'], true)) {
            continue;
        }
        $out[] = [
            'id'   => (int) $post->ID,
            'type' => $post->post_type,
        ];
    }
    return $out;
}

/**
 * Get the entity (company or organization) to display as "work for" in card and overview.
 * Uses expert_primary_entity (ACF) if set, otherwise first of linked entities (from company-team sync).
 *
 * @param int $post_id Expert post ID.
 * @return WP_Post|null
 */
function greenergy_expert_get_display_entity($post_id)
{
    $post_id = absint($post_id);
    if (! $post_id || get_post_type($post_id) !== 'experts') {
        return null;
    }
    if (function_exists('get_field')) {
        $primary = get_field('expert_primary_entity', $post_id);
        if ($primary && is_object($primary) && isset($primary->ID)) {
            $p = get_post($primary->ID);
            if ($p && in_array($p->post_type, ['organizations', 'companies'], true)) {
                return $p;
            }
        }
        if (is_numeric($primary) && $primary > 0) {
            $p = get_post((int) $primary);
            if ($p && in_array($p->post_type, ['organizations', 'companies'], true)) {
                return $p;
            }
        }
    }
    $linked = greenergy_expert_get_linked_entities($post_id);
    if (! empty($linked)) {
        $first = $linked[0];
        $id = isset($first['id']) ? (int) $first['id'] : 0;
        if ($id > 0) {
            $p = get_post($id);
            if ($p && in_array($p->post_type, ['organizations', 'companies'], true)) {
                return $p;
            }
        }
    }
    return null;
}

/**
 * Get "يعمل لدى" label and optional URL for an expert.
 * Manual text (expert_work_for) is used when not empty; otherwise linked entity title + permalink.
 *
 * @param int $post_id Expert post ID.
 * @return array{label: string, url: string}
 */
function greenergy_expert_work_for_display($post_id)
{
    $post_id = absint($post_id);
    $result  = ['label' => '', 'url' => ''];
    if (! $post_id || get_post_type($post_id) !== 'experts') {
        return $result;
    }
    $manual = function_exists('get_field') ? trim((string) get_field('expert_work_for', $post_id)) : '';
    if ($manual !== '') {
        $result['label'] = $manual;
        return $result;
    }
    $entity = greenergy_expert_get_display_entity($post_id);
    if ($entity && isset($entity->post_title)) {
        $result['label'] = $entity->post_title;
        $result['url']   = get_permalink($entity->ID) ?: '';
    }
    return $result;
}

/**
 * Get formatted date
 *
 * @param int    $post_id Post ID.
 * @param string $format  Date format.
 * @return string Formatted date.
 */
function greenergy_get_date($post_id = null, $format = '')
{
    if (! $post_id) {
        $post_id = get_the_ID();
    }

    if (! $format) {
        $format = get_option('date_format');
    }

    return get_the_date($format, $post_id);
}

/**
 * Get author info
 *
 * @param int $post_id Post ID.
 * @return array Author data.
 */
function greenergy_get_author($post_id = null)
{
    if (! $post_id) {
        $post_id = get_the_ID();
    }

    $author_id = get_post_field('post_author', $post_id);

    return [
        'id'     => $author_id,
        'name'   => get_the_author_meta('display_name', $author_id),
        'url'    => get_author_posts_url($author_id),
        'avatar' => get_avatar_url($author_id, ['size' => 80]),
        'bio'    => get_the_author_meta('description', $author_id),
    ];
}

/**
 * Truncate text
 *
 * @param string $text   Text to truncate.
 * @param int    $length Maximum length.
 * @param string $suffix Suffix to add.
 * @return string Truncated text.
 */
function greenergy_truncate($text, $length = 150, $suffix = '...')
{
    $text = wp_strip_all_tags($text);

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get SVG icon
 *
 * @param string $name   Icon name.
 * @param int    $width  Icon width.
 * @param int    $height Icon height.
 * @param string $class  Additional CSS class.
 * @return string SVG markup.
 */
function greenergy_icon($name, $width = 24, $height = 24, $class = '')
{
    $icon_path = GREENERGY_ASSETS_DIR . "/images/icons/{$name}.svg";

    if (! file_exists($icon_path)) {
        return '';
    }

    $svg = file_get_contents($icon_path);

    // Add dimensions and class
    $svg = preg_replace(
        '/<svg/',
        sprintf('<svg width="%d" height="%d" class="%s"', $width, $height, esc_attr($class)),
        $svg,
        1
    );

    return $svg;
}

/**
 * Get social share URLs
 *
 * @param int $post_id Post ID.
 * @return array Share URLs.
 */
function greenergy_share_urls($post_id = null)
{
    if (! $post_id) {
        $post_id = get_the_ID();
    }

    $url = urlencode(get_permalink($post_id));
    $title = urlencode(get_the_title($post_id));

    return [
        'facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$url}",
        'twitter'   => "https://twitter.com/intent/tweet?url={$url}&text={$title}",
        'linkedin'  => "https://www.linkedin.com/shareArticle?mini=true&url={$url}&title={$title}",
        'whatsapp'  => "https://wa.me/?text={$title}%20{$url}",
        'telegram'  => "https://t.me/share/url?url={$url}&text={$title}",
        'email'     => "mailto:?subject={$title}&body={$url}",
    ];
}

/**
 * Check if current page is blog
 *
 * @return bool
 */
function greenergy_is_blog()
{
    return (is_archive() || is_author() || is_category() || is_home() || is_tag())
        && 'post' === get_post_type();
}

/**
 * Pagination
 *
 * @param WP_Query $query Query object.
 */
function greenergy_pagination($query = null)
{
    if (! $query) {
        global $wp_query;
        $query = $wp_query;
    }

    $big = 999999999;

    $pages = paginate_links([
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $query->max_num_pages,
        'type'      => 'array',
        'prev_text' => greenergy_icon('arrow-left', 20, 20),
        'next_text' => greenergy_icon('arrow-right', 20, 20),
    ]);

    if ($pages) {
        echo '<nav class="pagination" aria-label="' . esc_attr__('Pagination', 'greenergy') . '">';
        foreach ($pages as $page) {
            // Add Tailwind classes
            $page = str_replace('page-numbers', 'pagination-link', $page);
            $page = str_replace('current', 'pagination-link active', $page);
            echo $page;
        }
        echo '</nav>';
    }
}

/**
 * Breadcrumbs
 */
function greenergy_breadcrumbs()
{
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'greenergy') . '">';
    echo '<span class="breadcrumbs-item">';
    echo '<a href="' . esc_url(home_url('/')) . '" class="breadcrumbs-link">' . esc_html__('Home', 'greenergy') . '</a>';
    echo '</span>';

    if (is_singular()) {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object($post_type);

        if ($post_type_obj && $post_type !== 'page') {
            echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
            echo '<span class="breadcrumbs-item">';
            echo '<a href="' . esc_url(get_post_type_archive_link($post_type)) . '" class="breadcrumbs-link">';
            echo esc_html($post_type_obj->labels->name);
            echo '</a>';
            echo '</span>';
        }

        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        echo esc_html(get_the_title());
        echo '</span>';
    } elseif (is_archive()) {
        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        echo esc_html(get_the_archive_title());
        echo '</span>';
    } elseif (is_search()) {
        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        /* translators: %s: search query */
        printf(esc_html__('Search: %s', 'greenergy'), get_search_query());
        echo '</span>';
    }

    echo '</nav>';
}

/**
 * Check if we're in development mode
 *
 * @return bool
 */

/**
 * Get unified pagination HTML for AJAX and standard requests
 *
 * @param WP_Query $query        Query object.
 * @param int      $current_page Current page number.
 * @return string Pagination HTML.
 */
function greenergy_get_pagination_html($query, $current_page = 1)
{
    $big = 999999999;
    $pagination_html = '';

    if ($query->max_num_pages <= 1) {
        return '';
    }

    $links = paginate_links([
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, $current_page),
        'total'     => $query->max_num_pages,
        'type'      => 'array',
        'prev_next' => false,
    ]);

    if ($links) {
        $pagination_html .= '<nav class="greenergy-pagination mt-8 flex justify-center items-center gap-2" aria-label="' . esc_attr__('Pagination', 'greenergy') . '">';

        // Prev
        if ($current_page > 1) {
            $prev_link = get_pagenum_link($current_page - 1);
            $pagination_html .= sprintf(
                '<a href="%s" class="js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all shadow-sm" data-page="%d" aria-label="%s">%s</a>',
                esc_url($prev_link),
                $current_page - 1,
                esc_attr__('Previous Page', 'greenergy'),
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
            );
        }

        foreach ($links as $link) {
            // Extract page number from the link text (more reliable than parsing URL)
            $link_text = strip_tags($link);
            $link_page = is_numeric($link_text) ? (int) $link_text : null;

            // Fallback for current page if it's not a number (unlikely given WP default, but safe)
            if (!$link_page && strpos($link, 'current') !== false) {
                $link_page = $current_page;
            }

            if ($link_page) {
                $is_active = $link_page == $current_page;

                // Base classes
                $classes = 'w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm ';

                if ($is_active) {
                    $classes .= 'bg-green-600 text-white font-semibold shadow-md border border-transparent';
                } else {
                    $classes .= 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 font-medium';
                }

                $url = get_pagenum_link($link_page);

                $pagination_html .= sprintf(
                    '<a href="%s" class="js-ajax-pagination-link %s" data-page="%d">%s</a>',
                    esc_url($url),
                    $classes,
                    $link_page,
                    $link_page
                );
            } else {
                // Dots
                $pagination_html .= '<span class="w-10 h-10 flex justify-center items-center text-gray-400">...</span>';
            }
        }

        // Next
        if ($current_page < $query->max_num_pages) {
            $next_link = get_pagenum_link($current_page + 1);
            $pagination_html .= sprintf(
                '<a href="%s" class="js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all shadow-sm" data-page="%d" aria-label="%s">%s</a>',
                esc_url($next_link),
                $current_page + 1,
                esc_attr__('Next Page', 'greenergy'),
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>'
            );
        }

        $pagination_html .= '</nav>';
    }

    return $pagination_html;
}

/**
 * Get company verification badge URL by company type (taxonomy) or company_verified meta.
 * Returns full URL to vuesax/bold/verify-{type}.svg or empty string if no badge.
 *
 * @param int $post_id Company post ID.
 * @return string URL to badge image or empty.
 */
function greenergy_company_verification_badge_url($post_id)
{
    $post_id = absint($post_id);
    if (! $post_id) {
        return '';
    }
    $assets_uri = defined('GREENERGY_ASSETS_URI') ? GREENERGY_ASSETS_URI : get_template_directory_uri() . '/assets';
    $base = $assets_uri . '/images/vuesax/bold/';

    // Taxonomy company_type: gold => verify-gold, trusted => verify-green, silver => verify-grey, diamond => verify-blue
    $terms = get_the_terms($post_id, 'company_type');
    if ($terms && ! is_wp_error($terms)) {
        $slug = $terms[0]->slug ?? '';
        $map = [
            'gold'    => 'verify-gold.svg',
            'trusted' => 'verify-green.svg',
            'silver'  => 'verify-grey.svg',
            'diamond' => 'verify-blue.svg',
        ];
        if (isset($map[ $slug ])) {
            return $base . $map[ $slug ];
        }
    }

    // Fallback: company_verified meta => gold
    if ((bool) get_post_meta($post_id, 'company_verified', true)) {
        return $base . 'verify-gold.svg';
    }

    return '';
}

/**
 * Restrict search to post_title only when query var greenergy_search_in === 'title' (companies).
 */
function greenergy_companies_search_title_only($search, $wp_query)
{
    if ($wp_query->get('greenergy_search_in') !== 'title') {
        return $search;
    }
    if ($wp_query->get('post_type') !== 'companies' || empty($wp_query->query_vars['s'])) {
        return $search;
    }
    global $wpdb;
    $terms = $wp_query->query_vars['search_terms'] ?? [];
    if (empty($terms)) {
        $s = $wp_query->query_vars['s'];
        $terms = array_filter(explode(' ', $s));
    }
    if (empty($terms)) {
        return $search;
    }
    $and = [];
    foreach ($terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $and[] = $wpdb->prepare("({$wpdb->posts}.post_title LIKE %s)", $like);
    }
    return ' AND (' . implode(' AND ', $and) . ') ';
}

add_filter('posts_search', 'greenergy_companies_search_title_only', 10, 2);

/**
 * Restrict search to post_title only when query var greenergy_search_in_org === 'title' (organizations).
 */
function greenergy_organizations_search_title_only($search, $wp_query)
{
    if ($wp_query->get('greenergy_search_in_org') !== 'title') {
        return $search;
    }
    if ($wp_query->get('post_type') !== 'organizations' || empty($wp_query->query_vars['s'])) {
        return $search;
    }
    global $wpdb;
    $terms = $wp_query->query_vars['search_terms'] ?? [];
    if (empty($terms)) {
        $s = $wp_query->query_vars['s'];
        $terms = array_filter(explode(' ', $s));
    }
    if (empty($terms)) {
        return $search;
    }
    $and = [];
    foreach ($terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $and[] = $wpdb->prepare("({$wpdb->posts}.post_title LIKE %s)", $like);
    }
    return ' AND (' . implode(' AND ', $and) . ') ';
}

add_filter('posts_search', 'greenergy_organizations_search_title_only', 10, 2);

/**
 * Build WP_Query args for companies list from current request (GET: cat, country, sort, s_company).
 *
 * @param array $override Override or add args (e.g. post__not_in, paged, posts_per_page).
 * @return array Query args for WP_Query.
 */
function greenergy_companies_query_args($override = [])
{
    $cat     = isset($_GET['cat'])     ? absint($_GET['cat'])     : 0;
    $country = isset($_GET['country']) ? absint($_GET['country']) : 0;
    $sort    = isset($_GET['sort'])    ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'latest';
    $search  = isset($_GET['s_company']) ? sanitize_text_field(wp_unslash($_GET['s_company'])) : '';

    $args = [
        'post_type'      => 'companies',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
    ];

    if ($search !== '') {
        $args['s'] = $search;
    }

    $tax_query = [];
    if ($cat > 0) {
        $tax_query[] = [
            'taxonomy' => 'company_category',
            'field'    => 'term_id',
            'terms'    => $cat,
        ];
    }
    if ($country > 0) {
        $country_term = get_term($country, 'company_location');
        if ($country_term && ! is_wp_error($country_term)) {
            $term_ids = [ (int) $country_term->term_id ];
            $children = get_terms([
                'taxonomy'   => 'company_location',
                'parent'     => $country_term->term_id,
                'fields'     => 'ids',
                'hide_empty' => false,
            ]);
            if (! empty($children)) {
                $term_ids = array_merge($term_ids, array_map('intval', $children));
            }
            $tax_query[] = [
                'taxonomy' => 'company_location',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ];
        }
    }
    if (! empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    switch ($sort) {
        case 'oldest':
            $args['orderby'] = 'date';
            $args['order']   = 'ASC';
            break;
        case 'popular':
            if (class_exists('Greenergy_Post_Views')) {
                $meta_key = Greenergy_Post_Views::TOTAL_VIEWS_KEY;
            } else {
                $meta_key = '_total_views_sort';
            }
            $args['meta_key']   = $meta_key;
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'DESC';
            $args['meta_query'] = [
                'relation' => 'OR',
                ['key' => $meta_key, 'compare' => 'EXISTS'],
                ['key' => $meta_key, 'compare' => 'NOT EXISTS', 'value' => ''],
            ];
            break;
        case 'alpha':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    return array_merge($args, $override);
}

/**
 * Build WP_Query args for organizations list from current request (GET: cat, country, sort, s_org).
 *
 * @param array $override Override or add args (e.g. post__not_in, paged, posts_per_page).
 * @return array Query args for WP_Query.
 */
function greenergy_organizations_query_args($override = [])
{
    $cat     = isset($_GET['cat'])     ? absint($_GET['cat'])     : 0;
    $country = isset($_GET['country']) ? absint($_GET['country']) : 0;
    $sort    = isset($_GET['sort'])    ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'latest';
    $search  = isset($_GET['s_org'])   ? sanitize_text_field(wp_unslash($_GET['s_org'])) : '';

    $args = [
        'post_type'      => 'organizations',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
    ];

    if ($search !== '') {
        $args['s'] = $search;
    }

    $tax_query = [];
    if ($cat > 0) {
        $tax_query[] = [
            'taxonomy' => 'organization_category',
            'field'    => 'term_id',
            'terms'    => $cat,
        ];
    }
    if ($country > 0) {
        $country_term = get_term($country, 'organization_location');
        if ($country_term && ! is_wp_error($country_term)) {
            $term_ids = [ (int) $country_term->term_id ];
            $children = get_terms([
                'taxonomy'   => 'organization_location',
                'parent'     => $country_term->term_id,
                'fields'     => 'ids',
                'hide_empty' => false,
            ]);
            if (! empty($children)) {
                $term_ids = array_merge($term_ids, array_map('intval', $children));
            }
            $tax_query[] = [
                'taxonomy' => 'organization_location',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ];
        }
    }
    if (! empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    switch ($sort) {
        case 'oldest':
            $args['orderby'] = 'date';
            $args['order']   = 'ASC';
            break;
        case 'popular':
            if (class_exists('Greenergy_Post_Views')) {
                $meta_key = Greenergy_Post_Views::TOTAL_VIEWS_KEY;
            } else {
                $meta_key = '_total_views_sort';
            }
            $args['meta_key']   = $meta_key;
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'DESC';
            $args['meta_query'] = [
                'relation' => 'OR',
                ['key' => $meta_key, 'compare' => 'EXISTS'],
                ['key' => $meta_key, 'compare' => 'NOT EXISTS', 'value' => ''],
            ];
            break;
        case 'alpha':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    return array_merge($args, $override);
}

/**
 * Run organizations list query with search order: by name (title) first, then by description (content) if no results.
 *
 * @param array $override Override or add args (e.g. post__not_in, paged, posts_per_page).
 * @return WP_Query
 */
function greenergy_organizations_query($override = [])
{
    $args = function_exists('greenergy_organizations_query_args') ? greenergy_organizations_query_args($override) : array_merge([
        'post_type'      => 'organizations',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
    ], $override);

    $search = isset($args['s']) ? trim($args['s']) : '';

    if ($search === '') {
        return new WP_Query($args);
    }

    $args_title = array_merge($args, [ 'greenergy_search_in_org' => 'title' ]);
    $query_title = new WP_Query($args_title);

    if ($query_title->found_posts > 0) {
        return $query_title;
    }

    unset($args['greenergy_search_in_org']);
    return new WP_Query($args);
}

/**
 * Restrict search to post_title only when query var greenergy_search_in_expert === 'title' (experts).
 */
function greenergy_experts_search_title_only($search, $wp_query)
{
    if ($wp_query->get('greenergy_search_in_expert') !== 'title') {
        return $search;
    }
    if ($wp_query->get('post_type') !== 'experts' || empty($wp_query->query_vars['s'])) {
        return $search;
    }
    global $wpdb;
    $terms = $wp_query->query_vars['search_terms'] ?? [];
    if (empty($terms)) {
        $s = $wp_query->query_vars['s'];
        $terms = array_filter(explode(' ', $s));
    }
    if (empty($terms)) {
        return $search;
    }
    $and = [];
    foreach ($terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $and[] = $wpdb->prepare("({$wpdb->posts}.post_title LIKE %s)", $like);
    }
    return ' AND (' . implode(' AND ', $and) . ') ';
}

add_filter('posts_search', 'greenergy_experts_search_title_only', 10, 2);

/**
 * Join postmeta for expert_role and expert_work_for when doing experts keyword search.
 */
function greenergy_experts_keyword_search_join($join, $wp_query)
{
    if ($wp_query->get('greenergy_experts_keyword_search') != 1 || $wp_query->get('post_type') !== 'experts' || empty($wp_query->get('s'))) {
        return $join;
    }
    global $wpdb;
    $join .= " LEFT JOIN {$wpdb->postmeta} AS pm_role ON pm_role.post_id = {$wpdb->posts}.ID AND pm_role.meta_key = 'expert_role' ";
    $join .= " LEFT JOIN {$wpdb->postmeta} AS pm_work ON pm_work.post_id = {$wpdb->posts}.ID AND pm_work.meta_key = 'expert_work_for' ";
    return $join;
}

add_filter('posts_join', 'greenergy_experts_keyword_search_join', 10, 2);

/**
 * Extend experts search to include expert_role and expert_work_for (تخصص، جهة عمل).
 */
function greenergy_experts_keyword_search_where($search, $wp_query)
{
    if ($wp_query->get('greenergy_experts_keyword_search') != 1 || $wp_query->get('post_type') !== 'experts' || empty($wp_query->get('s'))) {
        return $search;
    }
    global $wpdb;
    $terms = $wp_query->query_vars['search_terms'] ?? [];
    if (empty($terms)) {
        $s = $wp_query->get('s');
        $terms = array_filter(explode(' ', $s));
    }
    if (empty($terms)) {
        return $search;
    }
    $and_parts = [];
    foreach ($terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $and_parts[] = $wpdb->prepare('(pm_role.meta_value LIKE %s OR pm_work.meta_value LIKE %s)', $like, $like);
    }
    $search .= ' OR (' . implode(' AND ', $and_parts) . ')';
    return $search;
}

add_filter('posts_search', 'greenergy_experts_keyword_search_where', 11, 2);

/**
 * Avoid duplicate rows when expert matches via multiple meta rows.
 */
function greenergy_experts_keyword_search_distinct($distinct, $wp_query)
{
    if ($wp_query->get('greenergy_experts_keyword_search') != 1 || $wp_query->get('post_type') !== 'experts' || empty($wp_query->get('s'))) {
        return $distinct;
    }
    return 'DISTINCT';
}

add_filter('posts_distinct', 'greenergy_experts_keyword_search_distinct', 10, 2);

/**
 * Build WP_Query args for experts list from current request (GET: location, sort, s_exp).
 *
 * @param array $override Override or add args (e.g. paged, posts_per_page).
 * @return array Query args for WP_Query.
 */
function greenergy_experts_query_args($override = [])
{
    $location = isset($_GET['location']) ? absint($_GET['location']) : 0;
    $cat      = isset($_GET['cat']) ? absint($_GET['cat']) : 0;
    $search   = isset($_GET['s_exp']) ? sanitize_text_field(wp_unslash($_GET['s_exp'])) : '';

    $args = [
        'post_type'      => 'experts',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if ($search !== '') {
        $args['s'] = $search;
    }

    $tax_query = [];
    if ($location > 0) {
        $location_term = get_term($location, 'expert_location');
        if ($location_term && ! is_wp_error($location_term)) {
            $term_ids = [ (int) $location_term->term_id ];
            $children = get_terms([
                'taxonomy'   => 'expert_location',
                'parent'     => $location_term->term_id,
                'fields'     => 'ids',
                'hide_empty' => false,
            ]);
            if (! empty($children)) {
                $term_ids = array_merge($term_ids, array_map('intval', $children));
            }
            $tax_query[] = [
                'taxonomy' => 'expert_location',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ];
        }
    }
    if ($cat > 0) {
        $tax_query[] = [
            'taxonomy' => 'expert_category',
            'field'    => 'term_id',
            'terms'    => $cat,
        ];
    }
    if (! empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    return array_merge($args, $override);
}

/**
 * Run experts list query. Search is free keyword: name (title), excerpt, content, expert_role (تخصص), expert_work_for (جهة عمل).
 *
 * @param array $override Override or add args (e.g. paged, posts_per_page).
 * @return WP_Query
 */
function greenergy_experts_query($override = [])
{
    $args = function_exists('greenergy_experts_query_args') ? greenergy_experts_query_args($override) : array_merge([
        'post_type'      => 'experts',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
    ], $override);

    $search = isset($args['s']) ? trim($args['s']) : '';

    if ($search !== '') {
        $args['greenergy_experts_keyword_search'] = 1;
    }

    return new WP_Query($args);
}

/**
 * Run companies list query with search order: by name (title) first, then by description (content) if no results.
 *
 * @param array $override Override or add args (e.g. post__not_in, paged, posts_per_page).
 * @return WP_Query
 */
function greenergy_companies_query($override = [])
{
    $args = function_exists('greenergy_companies_query_args') ? greenergy_companies_query_args($override) : array_merge([
        'post_type'      => 'companies',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => 1,
    ], $override);

    $search = isset($args['s']) ? trim($args['s']) : '';

    if ($search === '') {
        return new WP_Query($args);
    }

    // 1) Search by name (post_title) only
    $args_title = array_merge($args, [ 'greenergy_search_in' => 'title' ]);
    $query_title = new WP_Query($args_title);

    if ($query_title->found_posts > 0) {
        return $query_title;
    }

    // 2) No results by name — search by description (post_content); default WP search includes title + content
    unset($args['greenergy_search_in']);
    return new WP_Query($args);
}
