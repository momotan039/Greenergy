<?php
/**
 * Redux Section: Footer
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Footer', 'greenergy' ),
    'id'     => 'footer',
    'icon'   => 'el el-website-alt',
    'fields' => [
        [
            'id'       => 'footer_style',
            'type'     => 'select',
            'title'    => __( 'Footer Style', 'greenergy' ),
            'options'  => [
                'default' => __( 'Default (4 Columns)', 'greenergy' ),
                'minimal' => __( 'Minimal', 'greenergy' ),
            ],
            'default'  => 'default',
        ],
        [
            'id'       => 'footer_logo',
            'type'     => 'media',
            'title'    => __( 'Footer Logo', 'greenergy' ),
        ],
        [
            'id'       => 'footer_description',
            'type'     => 'textarea',
            'title'    => __( 'Footer Description', 'greenergy' ),
            'default'  => __( 'Your trusted source for green energy news and resources.', 'greenergy' ),
        ],
        [
            'id'       => 'copyright_text',
            'type'     => 'text',
            'title'    => __( 'Copyright Text', 'greenergy' ),
            'default'  => '© {year} Greenergy. All rights reserved.',
            'subtitle' => __( 'Use {year} for dynamic year', 'greenergy' ),
        ],
        [
            'id'       => 'footer_newsletter',
            'type'     => 'switch',
            'title'    => __( 'Show Newsletter Signup', 'greenergy' ),
            'default'  => true,
        ],
    ],
] );
