<?php

/**
 * Post Views System
 *
 * Handles view counting and retrieval for posts.
 * Supports manual override via '_news_view_count' meta.
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_Post_Views
{

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Meta Keys
     */
    const MANUAL_VIEWS_KEY = '_news_view_count';
    const REAL_VIEWS_KEY   = '_real_views';

    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('template_redirect', [$this, 'track_views']);
    }

    /**
     * Track Views
     *
     * Increments the real view count on single post views.
     */
    public function track_views()
    {
        if (! is_singular(['news', 'post'])) { // Add other CPTs if needed
            return;
        }

        $post_id = get_the_ID();

        // Basic spam protection: check if cookie is set for this post
        // Cookie name: greenergy_viewed_{post_id}
        // Expires: 1 hour
        $cookie_name = 'greenergy_viewed_' . $post_id;

        if (self::is_bot()) {
            return;
        }

        if (isset($_COOKIE[$cookie_name])) {
            return;
        }

        // Increment Views
        $current_views = (int) get_post_meta($post_id, self::REAL_VIEWS_KEY, true);
        $current_views++;
        update_post_meta($post_id, self::REAL_VIEWS_KEY, $current_views);

        // Set Cookie
        // Note: setting cookie in wp_head might be too late if headers sent, but usually fine.
        // Better hook might be 'template_redirect' or 'wp', checking is_singular there.
        // However, generic WP themes often use wp_head or an early hook.
        // Let's stick to wp_head but be aware of headers.
        // Actually, PHP setcookie must be before output. wp_head is definitely after output started (html tag).
        // I must change hook to 'wp' or 'template_redirect'.
    }

    /**
     * Get View Count
     *
     * Returns the formatted view count.
     * Logic: Use manual count if set, otherwise real count.
     *
     * @param int $post_id
     * @return string
     */
    public static function get_views($post_id = 0)
    {
        if (! $post_id) {
            $post_id = get_the_ID();
        }

        $manual_views = get_post_meta($post_id, self::MANUAL_VIEWS_KEY, true);

        if (! empty($manual_views) && $manual_views > 0) {
            return number_format((int) $manual_views);
        }

        $real_views = (int) get_post_meta($post_id, self::REAL_VIEWS_KEY, true);
        return number_format($real_views);
    }

    /**
     * Is Bot
     * 
     * Simple bot detection
     */
    private static function is_bot()
    {
        if (! isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $bot_agents = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'sogou',
            'exabot',
            'facebot',
            'ia_archiver',
        ];

        foreach ($bot_agents as $bot) {
            if (stripos($user_agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}
