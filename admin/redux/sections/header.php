<?php
/**
 * Redux Section: Header
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Header', 'greenergy' ),
    'id'     => 'header',
    'icon'   => 'el el-website',
    'fields' => [
        [
            'id'       => 'header_style',
            'type'     => 'select',
            'title'    => __( 'Header Style', 'greenergy' ),
            'options'  => [
                'default'     => __( 'Default', 'greenergy' ),
                'transparent' => __( 'Transparent', 'greenergy' ),
                'minimal'     => __( 'Minimal', 'greenergy' ),
            ],
            'default'  => 'default',
        ],
        [
            'id'       => 'sticky_header',
            'type'     => 'switch',
            'title'    => __( 'Sticky Header', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'header_search',
            'type'     => 'switch',
            'title'    => __( 'Show Search', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'header_cta_text',
            'type'     => 'text',
            'title'    => __( 'CTA Button Text', 'greenergy' ),
            'default'  => __( 'Subscribe', 'greenergy' ),
        ],
        [
            'id'       => 'header_cta_url',
            'type'     => 'text',
            'title'    => __( 'CTA Button URL', 'greenergy' ),
            'default'  => '#',
        ],
        [
            'id'       => 'mobile_menu_breakpoint',
            'type'     => 'select',
            'title'    => __( 'Mobile Menu Breakpoint', 'greenergy' ),
            'options'  => [
                'lg' => __( 'Large (1024px)', 'greenergy' ),
                'md' => __( 'Medium (768px)', 'greenergy' ),
            ],
            'default'  => 'lg',
        ],
    ],
] );
