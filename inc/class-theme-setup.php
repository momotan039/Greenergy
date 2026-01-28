<?php
/**
 * Theme Setup Class
 *
 * Handles theme supports, menus, sidebars, and image sizes.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Theme_Setup
 */
class Greenergy_Theme_Setup {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'after_setup_theme', [ $this, 'setup' ] );
        add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
        add_action( 'init', [ $this, 'register_menus' ] );
        add_action( 'init', [ $this, 'register_patterns' ] );
    }

    /**
     * Theme setup
     */
    public function setup() {
        // Load text domain
        load_theme_textdomain( 'greenergy', GREENERGY_DIR . '/languages' );

        // Theme supports
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ] );
        add_theme_support( 'custom-logo', [
            'height'      => 80,
            'width'       => 250,
            'flex-height' => true,
            'flex-width'  => true,
        ] );
        add_theme_support( 'custom-background' );
        add_theme_support( 'customize-selective-refresh-widgets' );
        
        // Block editor supports
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );
        add_editor_style( 'assets/css/dist/editor.min.css' );

        // Add support for editor color palette (synced with Tailwind)
        add_theme_support( 'editor-color-palette', [
            [
                'name'  => __( 'Primary', 'greenergy' ),
                'slug'  => 'primary',
                'color' => '#10B981',
            ],
            [
                'name'  => __( 'Secondary', 'greenergy' ),
                'slug'  => 'secondary',
                'color' => '#1F2937',
            ],
            [
                'name'  => __( 'Accent', 'greenergy' ),
                'slug'  => 'accent',
                'color' => '#F59E0B',
            ],
            [
                'name'  => __( 'White', 'greenergy' ),
                'slug'  => 'white',
                'color' => '#FFFFFF',
            ],
            [
                'name'  => __( 'Light Gray', 'greenergy' ),
                'slug'  => 'light-gray',
                'color' => '#F3F4F6',
            ],
        ] );

        // Custom image sizes for performance
        $this->register_image_sizes();
    }

    /**
     * Register custom image sizes
     */
    private function register_image_sizes() {
        // Card thumbnails (optimized for cards)
        add_image_size( 'card-thumbnail', 400, 250, true );
        add_image_size( 'card-thumbnail-2x', 800, 500, true );
        
        // Featured image (hero)
        add_image_size( 'hero', 1200, 514, true ); // 21:9 ratio
        add_image_size( 'hero-2x', 2400, 1028, true );
        
        // Article content
        add_image_size( 'article', 800, 450, true );
        
        // Small squares for compact views
        add_image_size( 'square-sm', 80, 80, true );
        add_image_size( 'square-md', 150, 150, true );
    }

    /**
     * Register navigation menus
     */
    public function register_menus() {
        register_nav_menus( [
            'primary'   => __( 'Primary Menu', 'greenergy' ),
            'footer'    => __( 'Footer Menu', 'greenergy' ),
            'mobile'    => __( 'Mobile Menu', 'greenergy' ),
            'social'    => __( 'Social Links', 'greenergy' ),
        ] );
    }

    /**
     * Register widget areas (sidebars)
     */
    public function register_sidebars() {
        // Main sidebar
        register_sidebar( [
            'name'          => __( 'Main Sidebar', 'greenergy' ),
            'id'            => 'sidebar-main',
            'description'   => __( 'Widgets in this area appear in the main sidebar.', 'greenergy' ),
            'before_widget' => '<section id="%1$s" class="widget mb-6 p-5 bg-white dark:bg-secondary-800 rounded-xl shadow-card %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title text-lg font-bold mb-4 pb-2 border-b border-secondary-100 dark:border-secondary-700">',
            'after_title'   => '</h3>',
        ] );

        // Footer widgets (4 columns)
        for ( $i = 1; $i <= 4; $i++ ) {
            register_sidebar( [
                'name'          => sprintf( __( 'Footer Column %d', 'greenergy' ), $i ),
                'id'            => 'footer-' . $i,
                'description'   => sprintf( __( 'Footer column %d widgets.', 'greenergy' ), $i ),
                'before_widget' => '<div id="%1$s" class="widget mb-6 %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h4 class="widget-title text-base font-bold text-white mb-4">',
                'after_title'   => '</h4>',
            ] );
        }
    }

    /**
     * Register Block Patterns
     */
    public function register_patterns() {
        register_block_pattern_category(
            'greenergy',
            array( 'label' => __( 'Greenergy', 'greenergy' ) )
        );

        if ( file_exists( GREENERGY_INC_DIR . '/patterns/homepage-content.php' ) ) {
            $pattern_data = require GREENERGY_INC_DIR . '/patterns/homepage-content.php';
            register_block_pattern(
                'greenergy/homepage-content',
                $pattern_data
            );
        }
    }
}
