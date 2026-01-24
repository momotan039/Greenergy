<?php
/**
 * Redux Section: Advanced
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Advanced', 'greenergy' ),
    'id'     => 'advanced',
    'icon'   => 'el el-cogs',
    'fields' => [
        [
            'id'       => 'custom_css',
            'type'     => 'ace_editor',
            'title'    => __( 'Custom CSS', 'greenergy' ),
            'subtitle' => __( 'Add custom CSS. Use Tailwind utilities in templates instead when possible.', 'greenergy' ),
            'mode'     => 'css',
            'theme'    => 'monokai',
        ],
        [
            'id'       => 'custom_js_header',
            'type'     => 'ace_editor',
            'title'    => __( 'Header Scripts', 'greenergy' ),
            'subtitle' => __( 'Scripts added before </head>. Use sparingly - affects performance.', 'greenergy' ),
            'mode'     => 'javascript',
            'theme'    => 'monokai',
        ],
        [
            'id'       => 'custom_js_footer',
            'type'     => 'ace_editor',
            'title'    => __( 'Footer Scripts', 'greenergy' ),
            'subtitle' => __( 'Scripts added before </body>. Preferred location for tracking codes.', 'greenergy' ),
            'mode'     => 'javascript',
            'theme'    => 'monokai',
        ],
        [
            'id'       => 'google_analytics',
            'type'     => 'text',
            'title'    => __( 'Google Analytics ID', 'greenergy' ),
            'subtitle' => __( 'GA4 Measurement ID (G-XXXXXXX)', 'greenergy' ),
        ],
        [
            'id'       => 'gtm_id',
            'type'     => 'text',
            'title'    => __( 'Google Tag Manager ID', 'greenergy' ),
            'subtitle' => __( 'GTM Container ID (GTM-XXXXXX)', 'greenergy' ),
        ],
    ],
] );
