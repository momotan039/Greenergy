<?php
/**
 * Greenergy Theme Functions and Definitions
 *
 * This file bootstraps the theme by loading all required classes and helpers.
 * Individual functionality is separated into dedicated class files in /inc/.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Constants
 */
define( 'GREENERGY_VERSION', '1.0.0' );
define( 'GREENERGY_DIR', get_template_directory() );
define( 'GREENERGY_URI', get_template_directory_uri() );
define( 'GREENERGY_ASSETS_DIR', GREENERGY_DIR . '/assets' );
define( 'GREENERGY_ASSETS_URI', GREENERGY_URI . '/assets' );
define( 'GREENERGY_INC_DIR', GREENERGY_DIR . '/inc' );

/**
 * Autoloader for theme classes
 *
 * @param string $class_name The class name to load.
 */
function greenergy_autoloader( $class_name ) {
    // Only load our classes
    if ( strpos( $class_name, 'Greenergy_' ) !== 0 ) {
        return;
    }

    // Convert class name to file path
    $class_file = str_replace( 'Greenergy_', '', $class_name );
    $class_file = strtolower( str_replace( '_', '-', $class_file ) );
    
    // Define possible paths
    $paths = [
        GREENERGY_INC_DIR . '/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/cpt/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/taxonomies/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/blocks/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/widgets/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/rest-api/class-' . $class_file . '.php',
        GREENERGY_DIR . '/admin/class-' . $class_file . '.php',
    ];

    foreach ( $paths as $path ) {
        if ( file_exists( $path ) ) {
            require_once $path;
            return;
        }
    }
}
spl_autoload_register( 'greenergy_autoloader' );

/**
 * Load helper functions
 */
require_once GREENERGY_INC_DIR . '/helpers.php';

/**
 * Initialize theme classes
 */
function greenergy_init() {
    // Core theme setup
    new Greenergy_Theme_Setup();
    
    // Asset management
    new Greenergy_Assets();
    
    // Performance optimizations
    new Greenergy_Performance();
    
    // SEO helpers
    new Greenergy_SEO();
    
    // Custom Post Types
    new Greenergy_CPT_News();
    new Greenergy_CPT_Articles();
    new Greenergy_CPT_Jobs();
    new Greenergy_CPT_Courses();
    new Greenergy_CPT_Directory();
    
    // Gutenberg Blocks
    new Greenergy_Blocks_Loader();
    
    // Admin functionality
    if ( is_admin() ) {
        new Greenergy_Admin_Init();
    }
}
add_action( 'after_setup_theme', 'greenergy_init', 5 );

/**
 * Redux Framework Configuration
 * Only load if Redux is active
 */
function greenergy_load_redux() {
    if ( class_exists( 'Redux' ) ) {
        require_once GREENERGY_DIR . '/admin/redux/redux-config.php';
    }
}
add_action( 'init', 'greenergy_load_redux' );

/**
 * Get theme option from Redux
 *
 * @param string $option_name The option key to retrieve.
 * @param mixed  $default     Default value if option not found.
 * @return mixed The option value.
 */
function greenergy_option( $option_name, $default = '' ) {
    global $greenergy_options;
    
    if ( isset( $greenergy_options[ $option_name ] ) ) {
        return $greenergy_options[ $option_name ];
    }
    
    return $default;
}
