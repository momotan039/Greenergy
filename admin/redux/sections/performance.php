<?php
/**
 * Redux Section: Performance
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Performance', 'greenergy' ),
    'id'     => 'performance',
    'icon'   => 'el el-dashboard',
    'fields' => [
        [
            'id'       => 'perf_info',
            'type'     => 'info',
            'style'    => 'success',
            'title'    => __( 'Performance First', 'greenergy' ),
            'desc'     => __( 'This theme is built for 95+ PageSpeed scores. Core optimizations are built-in and cannot be disabled.', 'greenergy' ),
        ],
        [
            'id'       => 'lazy_load_images',
            'type'     => 'switch',
            'title'    => __( 'Lazy Load Images', 'greenergy' ),
            'default'  => true,
            'subtitle' => __( 'Native lazy loading for images below the fold', 'greenergy' ),
        ],
        [
            'id'       => 'lazy_load_iframes',
            'type'     => 'switch',
            'title'    => __( 'Lazy Load Iframes', 'greenergy' ),
            'default'  => true,
            'subtitle' => __( 'Videos, maps, and embeds load on scroll', 'greenergy' ),
        ],
        [
            'id'       => 'preload_fonts',
            'type'     => 'switch',
            'title'    => __( 'Preload Primary Fonts', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'remove_query_strings',
            'type'     => 'switch',
            'title'    => __( 'Remove Query Strings', 'greenergy' ),
            'default'  => false,
            'subtitle' => __( 'Remove ?ver= from static resources', 'greenergy' ),
        ],
        [
            'id'       => 'disable_emojis',
            'type'     => 'switch',
            'title'    => __( 'Disable WordPress Emojis', 'greenergy' ),
            'default'  => true,
            'subtitle' => __( 'Removes emoji scripts and styles', 'greenergy' ),
        ],
    ],
] );
