<?php
/**
 * Redux Section: Blog
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Blog / Archive', 'greenergy' ),
    'id'     => 'blog',
    'icon'   => 'el el-edit',
    'fields' => [
        [
            'id'       => 'archive_layout',
            'type'     => 'image_select',
            'title'    => __( 'Archive Layout', 'greenergy' ),
            'options'  => [
                'grid' => [
                    'alt' => __( 'Grid', 'greenergy' ),
                    'img' => ReduxFramework::$_url . 'assets/img/2cl.png',
                ],
                'list' => [
                    'alt' => __( 'List', 'greenergy' ),
                    'img' => ReduxFramework::$_url . 'assets/img/1col.png',
                ],
            ],
            'default'  => 'grid',
        ],
        [
            'id'       => 'archive_columns',
            'type'     => 'select',
            'title'    => __( 'Grid Columns', 'greenergy' ),
            'options'  => [
                '2' => __( '2 Columns', 'greenergy' ),
                '3' => __( '3 Columns', 'greenergy' ),
                '4' => __( '4 Columns', 'greenergy' ),
            ],
            'default'  => '3',
            'required' => [ 'archive_layout', '=', 'grid' ],
        ],
        [
            'id'       => 'show_sidebar',
            'type'     => 'switch',
            'title'    => __( 'Show Sidebar', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'posts_per_page',
            'type'     => 'slider',
            'title'    => __( 'Posts Per Page', 'greenergy' ),
            'default'  => 12,
            'min'      => 6,
            'max'      => 24,
            'step'     => 3,
        ],
        [
            'id'       => 'show_excerpt',
            'type'     => 'switch',
            'title'    => __( 'Show Excerpt', 'greenergy' ),
            'default'  => true,
        ],
        [
            'id'       => 'excerpt_length',
            'type'     => 'slider',
            'title'    => __( 'Excerpt Length (words)', 'greenergy' ),
            'default'  => 20,
            'min'      => 10,
            'max'      => 50,
            'required' => [ 'show_excerpt', '=', true ],
        ],
    ],
] );
