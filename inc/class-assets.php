<?php
/**
 * Assets Management Class
 *
 * Handles CSS/JS enqueue with performance optimizations.
 * Implements Tailwind CSS loading with RTL support.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Assets
 */
class Greenergy_Assets {

    /**
     * CSS version for cache busting
     */
    private $css_version;

    /**
     * JS version for cache busting
     */
    private $js_version;

    /**
     * Constructor
     */
    public function __construct() {
        $this->css_version = GREENERGY_VERSION;
        $this->js_version  = GREENERGY_VERSION;

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 10 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
        add_action( 'wp_head', [ $this, 'inline_critical_css' ], 1 );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
        
        // Script loading optimization
        add_filter( 'script_loader_tag', [ $this, 'optimize_script_loading' ], 10, 3 );
        add_filter( 'style_loader_tag', [ $this, 'optimize_style_loading' ], 10, 4 );
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_styles() {
        $css_dir = GREENERGY_ASSETS_URI . '/css/dist';
        
        // Main Tailwind stylesheet
        wp_enqueue_style(
            'greenergy-main',
            $css_dir . '/main.min.css',
            [],
            $this->css_version
        );

        // RTL support - WordPress auto-loads RTL version
        wp_style_add_data( 'greenergy-main', 'rtl', 'replace' );
        wp_style_add_data( 'greenergy-main', 'suffix', '-rtl' );
    }

    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        $js_dir = GREENERGY_ASSETS_URI . '/js/dist';

        // Remove jQuery dependency for better performance
        // Only use if absolutely necessary for third-party plugins
        
        // Main theme script (deferred)
        wp_enqueue_script(
            'greenergy-main',
            $js_dir . '/main.min.js',
            [],
            $this->js_version,
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ]
        );

        // Localize script with theme data
        wp_localize_script( 'greenergy-main', 'greenergyData', [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'greenergy_nonce' ),
            'isRtl'     => is_rtl(),
            'themeUrl'  => GREENERGY_URI,
        ] );

        // Comment reply script only when needed
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
    }

    /**
     * Inline critical CSS for above-the-fold content
     */
    public function inline_critical_css() {
        $critical_file = GREENERGY_ASSETS_DIR . '/css/dist/critical.min.css';
        
        if ( file_exists( $critical_file ) ) {
            $critical_css = file_get_contents( $critical_file );
            echo '<style id="greenergy-critical-css">' . $critical_css . '</style>';
        }
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style(
            'greenergy-editor',
            GREENERGY_ASSETS_URI . '/css/dist/editor.min.css',
            [ 'wp-edit-blocks' ],
            $this->css_version
        );
    }

    /**
     * Optimize script loading (add defer/async attributes)
     *
     * @param string $tag    Script tag HTML.
     * @param string $handle Script handle.
     * @param string $src    Script source URL.
     * @return string Modified script tag.
     */
    public function optimize_script_loading( $tag, $handle, $src ) {
        // Skip if already has defer/async
        if ( strpos( $tag, 'defer' ) !== false || strpos( $tag, 'async' ) !== false ) {
            return $tag;
        }

        // List of scripts to defer
        $defer_scripts = [
            'greenergy-main',
            'comment-reply',
        ];

        if ( in_array( $handle, $defer_scripts, true ) ) {
            return str_replace( ' src', ' defer src', $tag );
        }

        return $tag;
    }

    /**
     * Optimize stylesheet loading
     *
     * @param string $html   Link tag HTML.
     * @param string $handle Style handle.
     * @param string $href   Style URL.
     * @param string $media  Media attribute.
     * @return string Modified link tag.
     */
    public function optimize_style_loading( $html, $handle, $href, $media ) {
        // Non-critical stylesheets loaded asynchronously
        $async_styles = [];

        if ( in_array( $handle, $async_styles, true ) ) {
            return str_replace(
                "media='all'",
                "media='print' onload=\"this.media='all'\"",
                $html
            );
        }

        return $html;
    }

    /**
     * Get asset URL with cache busting
     *
     * @param string $path Relative path to asset.
     * @return string Full asset URL with version.
     */
    public static function get_asset_url( $path ) {
        $file_path = GREENERGY_ASSETS_DIR . '/' . ltrim( $path, '/' );
        $version   = file_exists( $file_path ) ? filemtime( $file_path ) : GREENERGY_VERSION;
        
        return add_query_arg( 'v', $version, GREENERGY_ASSETS_URI . '/' . ltrim( $path, '/' ) );
    }
}
