<?php
/**
 * Admin Panel Class
 *
 * Registers the custom admin page and enqueues necessary Block Editor assets.
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Greenergy_Admin_Panel {

    /**
     * Instance of this class.
     *
     * @var object
     */
    private static $instance;

    /**
     * Initiator
     *
     * @return object initialized object of class.
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register top-level admin menu
     */
    public function register_admin_page() {
        add_menu_page(
            __( 'Theme Settings', 'greenergy' ),  // Page Title
            __( 'Greenergy', 'greenergy' ),       // Menu Title
            'manage_options',                     // Capability
            'greenergy-settings',                 // Menu Slug
            [ $this, 'render_admin_page' ],       // Callback
            'dashicons-admin-generic',           // Icon
            2                                     // Position
        );
    }

    /**
     * Register settings for the block editor
     */
    public function register_settings() {
        // This option stores the RAW block data for the editor state
        register_setting( 'greenergy_settings_group', 'greenergy_admin_blocks', [
            'type'         => 'string',
            'show_in_rest' => true,
            'default'      => '',
        ] );

        // This option stores the PARSED settings for easy frontend access
        register_setting( 'greenergy_settings_group', 'greenergy_settings', [
            'type'         => 'object',
            'show_in_rest' => [
                'schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'social_media' => [
                            'type'  => 'array',
                            'items' => [
                                'type'       => 'object',
                                'properties' => [
                                    'platform' => [ 'type' => 'string' ],
                                    'url'      => [ 'type' => 'string' ],
                                    'icon'     => [ 'type' => 'string' ], // URL or Class
                                    'iconId'   => [ 'type' => 'integer' ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'default'      => [],
        ] );
    }

    /**
     * Enqueue Block Editor Assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_greenergy-settings' !== $hook ) {
            return;
        }

        // Enable media picker for this page
        wp_enqueue_media();

        // Enqueue core block editor assets
        $asset_file = include( GREENERGY_ASSETS_DIR . '/js/dist/admin-panel.asset.php' );

        wp_enqueue_script(
            'greenergy-admin-panel',
            GREENERGY_ASSETS_URI . '/js/dist/admin-panel.js',
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );

        // Core styles
        wp_enqueue_style( 'wp-components' );
        wp_enqueue_style( 'wp-block-editor' );
        wp_enqueue_style( 'wp-editor' );

        // Admin CSS
        wp_enqueue_style(
            'greenergy-admin-css',
            GREENERGY_ASSETS_URI . '/css/admin.css',
            [ 'wp-components' ],
            GREENERGY_VERSION
        );

        // Localize script with necessary data
        wp_localize_script( 'greenergy-admin-panel', 'greenergySettings', [
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'root'     => esc_url_raw( rest_url() ),
            'settings' => get_option( 'greenergy_settings', [] ),
            'blocks'   => get_option( 'greenergy_admin_blocks', '' ),
        ] );
    }

    /**
     * Render the admin page container
     */
    public function render_admin_page() {
        ?>
        <div class="greenergy-admin-wrapper">
            <div id="greenergy-admin-app">
                <!-- React App will mount here -->
                <div style="padding: 50px; text-align: center;">
                    <span class="spinner is-active" style="float:none;"></span>
                    <?php esc_html_e( 'Loading Theme Settings...', 'greenergy' ); ?>
                </div>
            </div>
        </div>
        <?php
    }
}
