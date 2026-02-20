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
 * Get a single dynamic stat value based on source
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

        default:
            return 0;
    }
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
