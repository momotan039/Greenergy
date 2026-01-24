<?php
/**
 * Redux Framework Configuration
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Redux' ) ) {
    return;
}

// Theme Options Name
$opt_name = 'greenergy_options';

// Redux Configuration Arguments
$args = [
    'opt_name'             => $opt_name,
    'display_name'         => __( 'Greenergy Theme', 'greenergy' ),
    'display_version'      => GREENERGY_VERSION,
    'menu_type'            => 'submenu',
    'allow_sub_menu'       => true,
    'menu_title'           => __( 'Theme Options', 'greenergy' ),
    'page_title'           => __( 'Greenergy Theme Options', 'greenergy' ),
    'google_api_key'       => '',
    'google_update_weekly' => false,
    'async_typography'     => true,
    'admin_bar'            => true,
    'admin_bar_icon'       => 'dashicons-admin-generic',
    'admin_bar_priority'   => 50,
    'global_variable'      => 'greenergy_options',
    'dev_mode'             => false, // CRITICAL: Always false in production
    'update_notice'        => false,
    'customizer'           => true,
    'page_priority'        => null,
    'page_parent'          => 'themes.php',
    'page_permissions'     => 'manage_options',
    'menu_icon'            => '',
    'last_tab'             => '',
    'page_icon'            => 'icon-themes',
    'page_slug'            => 'greenergy-options',
    'save_defaults'        => true,
    'default_show'         => false,
    'default_mark'         => '',
    'show_import_export'   => true,
    'transient_time'       => 60 * MINUTE_IN_SECONDS,
    'output'               => true,
    'output_tag'           => true,
    'footer_credit'        => '',
    'database'             => 'options',
    'use_cdn'              => true,
    'ajax_save'            => true,
    'hide_reset'           => false,

    // Hints
    'hints'                => [
        'icon'          => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color'    => 'lightgray',
        'icon_size'     => 'normal',
        'tip_style'     => [
            'color'   => 'light',
            'shadow'  => true,
            'rounded' => false,
            'style'   => '',
        ],
        'tip_position'  => [
            'my' => 'top left',
            'at' => 'bottom right',
        ],
        'tip_effect'    => [
            'show' => [
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'mouseover',
            ],
            'hide' => [
                'effect'   => 'slide',
                'duration' => '500',
                'event'    => 'click mouseleave',
            ],
        ],
    ],
];

Redux::set_args( $opt_name, $args );

// Load section files
$sections_dir = GREENERGY_DIR . '/admin/redux/sections';

$sections = [
    'general',
    'header',
    'footer',
    'typography',
    'blog',
    'social',
    'performance',
    'seo',
    'advanced',
];

foreach ( $sections as $section ) {
    $section_file = $sections_dir . '/' . $section . '.php';
    if ( file_exists( $section_file ) ) {
        require_once $section_file;
    }
}
