<?php
/**
 * Redux Section: General
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'General Settings', 'greenergy' ),
    'id'     => 'general',
    'icon'   => 'el el-home',
    'fields' => [
        [
            'id'       => 'logo',
            'type'     => 'media',
            'title'    => __( 'Logo', 'greenergy' ),
            'subtitle' => __( 'Upload your logo (recommended: SVG or PNG with transparency)', 'greenergy' ),
        ],
        [
            'id'       => 'logo_dark',
            'type'     => 'media',
            'title'    => __( 'Dark Mode Logo', 'greenergy' ),
            'subtitle' => __( 'Logo for dark backgrounds', 'greenergy' ),
        ],
        [
            'id'       => 'favicon',
            'type'     => 'media',
            'title'    => __( 'Favicon', 'greenergy' ),
            'subtitle' => __( 'Upload your favicon (recommended: 32x32 PNG)', 'greenergy' ),
        ],
        [
            'id'       => 'preloader',
            'type'     => 'switch',
            'title'    => __( 'Enable Preloader', 'greenergy' ),
            'default'  => false,
            'subtitle' => __( 'Show loading animation on page load', 'greenergy' ),
        ],
        [
            'id'       => 'back_to_top',
            'type'     => 'switch',
            'title'    => __( 'Back to Top Button', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'dark_mode',
            'type'     => 'switch',
            'title'    => __( 'Enable Dark Mode Toggle', 'greenergy' ),
            'default'  => true,
            'subtitle' => __( 'Allow users to switch between light and dark mode', 'greenergy' ),
        ],
    ],
] );
