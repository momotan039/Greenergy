<?php
/**
 * Admin Initialization
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Admin_Init
 */
class Greenergy_Admin_Init {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        wp_enqueue_style(
            'greenergy-admin',
            GREENERGY_ASSETS_URI . '/css/dist/admin.min.css',
            [],
            GREENERGY_VERSION
        );
    }

    /**
     * Add custom admin body class
     */
    public function admin_body_class( $classes ) {
        return $classes . ' greenergy-admin';
    }
}
