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
if (! defined('ABSPATH')) {
    exit;
}

file_put_contents('C:/xampp/htdocs/greenergy/wp-content/themes/greenergy_theme/test-log.log', "PHP Execution Success: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

/**
 * Theme Constants
 */
define('GREENERGY_VERSION', '1.0.0');
define('GREENERGY_DIR', get_template_directory());
define('GREENERGY_URI', get_template_directory_uri());
define('GREENERGY_ASSETS_DIR', GREENERGY_DIR . '/assets');
define('GREENERGY_ASSETS_URI', GREENERGY_URI . '/assets');
define('GREENERGY_INC_DIR', GREENERGY_DIR . '/inc');

/**
 * Autoloader for theme classes
 *
 * @param string $class_name The class name to load.
 */
function greenergy_autoloader($class_name)
{
    // Only load our classes
    if (strpos($class_name, 'Greenergy_') !== 0) {
        return;
    }

    // Convert class name to file path
    $class_file = str_replace('Greenergy_', '', $class_name);
    $class_file = strtolower(str_replace('_', '-', $class_file));

    // Define possible paths
    $paths = [
        GREENERGY_INC_DIR . '/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/cpt/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/taxonomies/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/blocks/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/widgets/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/rest-api/class-' . $class_file . '.php',
        GREENERGY_INC_DIR . '/admin/class-' . $class_file . '.php', // New Admin Path
        GREENERGY_DIR . '/admin/class-' . $class_file . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}
spl_autoload_register('greenergy_autoloader');

/**
 * Load helper functions
 */
require_once GREENERGY_INC_DIR . '/helpers.php';
require_once GREENERGY_INC_DIR . '/stats-functions.php';

/**
 * Initialize theme classes
 */
function greenergy_init()
{
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
    // new Greenergy_CPT_Articles();
    new Greenergy_CPT_Jobs();
    // new Greenergy_CPT_Courses();
    // new Greenergy_CPT_Directory();
    // new Greenergy_CPT_Stories();
    // new Greenergy_CPT_Stats();

    // Gutenberg Blocks
    new Greenergy_Blocks_Loader();

    // Admin functionality
    if (is_admin()) {
        Greenergy_Admin_Panel::get_instance();
    }

    // REST API (Initialize always or checking is_admin/rest request context - usually always for routes)
    Greenergy_Admin_REST::get_instance();

    // AJAX functionality
    Greenergy_Ajax::get_instance();

    // ACF Fields for Jobs
    require_once GREENERGY_INC_DIR . '/class-acf-jobs.php';

    // Post Views System
    Greenergy_Post_Views::get_instance();

    // Flush rewrite rules on theme activation or when requested (temp fix for 404)
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'greenergy_init', 5);

/**
 * Get theme option
 *
 * @param string $option_name The option key to retrieve.
 * @param mixed  $default     Default value if option not found.
 * @return mixed The option value.
 */
function greenergy_option($option_name, $default = '')
{
    // Try to get from new settings first (if migrated)
    $new_settings = get_option('greenergy_settings', []);

    if (isset($new_settings[$option_name])) {
        return $new_settings[$option_name];
    }

    // Fallback to legacy Redux options
    $options = get_option('greenergy_options', []);

    if (isset($options[$option_name])) {
        return $options[$option_name];
    }

    return $default;
}
