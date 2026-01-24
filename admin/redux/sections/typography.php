<?php
/**
 * Redux Section: Typography
 *
 * PERFORMANCE: Google Fonts disabled - using self-hosted fonts via Tailwind CSS.
 * This eliminates external HTTP requests for 95+ PageSpeed.
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
            'style'    => 'success',
            'title'    => __( 'Self-Hosted Fonts (Performance)', 'greenergy' ),
            'desc'     => __( 'Fonts are self-hosted via Tailwind CSS for optimal PageSpeed. No external Google Fonts requests. Configure fonts in tailwind.config.js and /assets/fonts/.', 'greenergy' ),
        ],
        [
            'id'       => 'font_display',
            'type'     => 'select',
            'title'    => __( 'Font Display Strategy', 'greenergy' ),
            'subtitle' => __( 'Controls how fonts render while loading', 'greenergy' ),
            'options'  => [
                'swap'     => __( 'Swap (Recommended - shows fallback, then swaps)', 'greenergy' ),
                'optional' => __( 'Optional (May skip custom fonts on slow connections)', 'greenergy' ),
                'block'    => __( 'Block (Brief invisible text - not recommended)', 'greenergy' ),
            ],
            'default'  => 'swap',
        ],
        [
            'id'       => 'enable_arabic_font',
            'type'     => 'switch',
            'title'    => __( 'Enable Arabic Font (RTL)', 'greenergy' ),
            'subtitle' => __( 'Loads Noto Sans Arabic for RTL pages', 'greenergy' ),
            'default'  => true,
        ],
    ],
] );
