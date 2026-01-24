<?php
/**
 * Performance Optimization Class
 *
 * Implements Core Web Vitals optimizations for 95+ PageSpeed.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Performance
 */
class Greenergy_Performance {

    /**
     * Constructor
     */
    public function __construct() {
        // Resource hints
        add_action( 'wp_head', [ $this, 'add_resource_hints' ], 1 );
        
        // Remove bloat
        add_action( 'init', [ $this, 'remove_bloat' ] );
        
        // Optimize images
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'lazy_load_images' ], 10, 3 );
        add_filter( 'the_content', [ $this, 'add_lazy_loading_to_content' ] );
        
        // Optimize embeds
        add_filter( 'embed_oembed_html', [ $this, 'lazy_load_embeds' ], 10, 4 );
        
        // Disable emojis
        add_action( 'init', [ $this, 'disable_emojis' ] );
        
        // Optimize WP core
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_unnecessary' ], 100 );
        
        // DNS prefetch
        add_filter( 'wp_resource_hints', [ $this, 'resource_hints' ], 10, 2 );
        
        // Limit post revisions
        if ( ! defined( 'WP_POST_REVISIONS' ) ) {
            define( 'WP_POST_REVISIONS', 5 );
        }
    }

    /**
     * Add resource hints (preconnect, prefetch)
     */
    public function add_resource_hints() {
        // Preconnect to commonly used origins
        $preconnect = [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
        ];

        foreach ( $preconnect as $url ) {
            echo '<link rel="preconnect" href="' . esc_url( $url ) . '" crossorigin>' . "\n";
        }

        // DNS prefetch for third-party services
        $dns_prefetch = [
            '//www.googletagmanager.com',
            '//www.google-analytics.com',
        ];

        foreach ( $dns_prefetch as $url ) {
            echo '<link rel="dns-prefetch" href="' . esc_url( $url ) . '">' . "\n";
        }
    }

    /**
     * Remove unnecessary bloat
     */
    public function remove_bloat() {
        // Remove RSD link
        remove_action( 'wp_head', 'rsd_link' );
        
        // Remove Windows Live Writer
        remove_action( 'wp_head', 'wlwmanifest_link' );
        
        // Remove shortlink
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        
        // Remove adjacent posts links
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
        
        // Remove WP version
        remove_action( 'wp_head', 'wp_generator' );
        add_filter( 'the_generator', '__return_empty_string' );
        
        // Remove REST API link
        remove_action( 'wp_head', 'rest_output_link_wp_head' );
        
        // Remove oEmbed discovery links
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    }

    /**
     * Disable emoji scripts and styles
     */
    public function disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        
        // Remove from TinyMCE
        add_filter( 'tiny_mce_plugins', function( $plugins ) {
            return is_array( $plugins ) ? array_diff( $plugins, [ 'wpemoji' ] ) : [];
        } );
        
        // Remove DNS prefetch for emoji
        add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
            if ( 'dns-prefetch' === $relation_type ) {
                $urls = array_filter( $urls, function( $url ) {
                    return strpos( $url, 'https://s.w.org/images/core/emoji/' ) === false;
                } );
            }
            return $urls;
        }, 10, 2 );
    }

    /**
     * Add lazy loading to attachment images
     *
     * @param array   $attr       Image attributes.
     * @param WP_Post $attachment Attachment post object.
     * @param mixed   $size       Image size.
     * @return array Modified attributes.
     */
    public function lazy_load_images( $attr, $attachment, $size ) {
        // Skip if already has loading attribute
        if ( isset( $attr['loading'] ) ) {
            return $attr;
        }

        // Add native lazy loading
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
        
        return $attr;
    }

    /**
     * Add lazy loading to content images and iframes
     *
     * @param string $content Post content.
     * @return string Modified content.
     */
    public function add_lazy_loading_to_content( $content ) {
        // Skip if in admin or feed
        if ( is_admin() || is_feed() ) {
            return $content;
        }

        // Add loading="lazy" to images without it
        $content = preg_replace(
            '/<img((?!loading=)[^>]*)>/i',
            '<img$1 loading="lazy" decoding="async">',
            $content
        );

        // Add loading="lazy" to iframes
        $content = preg_replace(
            '/<iframe((?!loading=)[^>]*)>/i',
            '<iframe$1 loading="lazy">',
            $content
        );

        return $content;
    }

    /**
     * Lazy load embeds (YouTube, Vimeo, etc.)
     *
     * @param string $html    Embed HTML.
     * @param string $url     Embed URL.
     * @param array  $attr    Embed attributes.
     * @param int    $post_id Post ID.
     * @return string Modified embed HTML.
     */
    public function lazy_load_embeds( $html, $url, $attr, $post_id ) {
        // Add loading="lazy" to iframe embeds
        if ( strpos( $html, '<iframe' ) !== false ) {
            $html = str_replace( '<iframe', '<iframe loading="lazy"', $html );
        }
        
        return $html;
    }

    /**
     * Dequeue unnecessary scripts and styles
     */
    public function dequeue_unnecessary() {
        // Remove block library CSS if not using Gutenberg on frontend
        // Uncomment if you're not using native blocks
        // wp_dequeue_style( 'wp-block-library' );
        // wp_dequeue_style( 'wp-block-library-theme' );
        
        // Remove classic theme styles
        wp_dequeue_style( 'classic-theme-styles' );
        
        // Remove global styles if not needed
        // wp_dequeue_style( 'global-styles' );
    }

    /**
     * Add resource hints
     *
     * @param array  $hints          Resource hints.
     * @param string $relation_type  Relation type.
     * @return array Modified hints.
     */
    public function resource_hints( $hints, $relation_type ) {
        if ( 'preconnect' === $relation_type ) {
            $hints[] = [
                'href'        => 'https://fonts.googleapis.com',
                'crossorigin' => 'anonymous',
            ];
        }

        return $hints;
    }

    /**
     * Get optimized image HTML with srcset
     *
     * @param int    $attachment_id Attachment ID.
     * @param string $size          Image size.
     * @param array  $attr          Additional attributes.
     * @return string Image HTML.
     */
    public static function get_optimized_image( $attachment_id, $size = 'large', $attr = [] ) {
        $default_attr = [
            'loading'  => 'lazy',
            'decoding' => 'async',
        ];

        $attr = wp_parse_args( $attr, $default_attr );
        
        return wp_get_attachment_image( $attachment_id, $size, false, $attr );
    }
}
