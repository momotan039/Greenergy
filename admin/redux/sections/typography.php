<?php
/**
 * Redux Section: Typography
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Typography', 'greenergy' ),
    'id'     => 'typography',
    'icon'   => 'el el-font',
    'fields' => [
        [
            'id'       => 'typography_info',
            'type'     => 'info',
            'style'    => 'info',
            'title'    => __( 'Tailwind CSS Typography', 'greenergy' ),
            'desc'     => __( 'Primary typography is configured in tailwind.config.js. These settings provide additional overrides.', 'greenergy' ),
        ],
        [
            'id'       => 'body_font',
            'type'     => 'typography',
            'title'    => __( 'Body Font', 'greenergy' ),
            'google'   => true,
            'output'   => [ 'body' ],
            'default'  => [
                'font-family' => 'Inter',
                'font-weight' => '400',
            ],
        ],
        [
            'id'       => 'heading_font',
            'type'     => 'typography',
            'title'    => __( 'Heading Font', 'greenergy' ),
            'google'   => true,
            'output'   => [ 'h1, h2, h3, h4, h5, h6' ],
            'default'  => [
                'font-family' => 'Inter',
                'font-weight' => '700',
            ],
        ],
        [
            'id'       => 'arabic_font',
            'type'     => 'typography',
            'title'    => __( 'Arabic Font (RTL)', 'greenergy' ),
            'google'   => true,
            'default'  => [
                'font-family' => 'Noto Sans Arabic',
                'font-weight' => '400',
            ],
        ],
    ],
] );
