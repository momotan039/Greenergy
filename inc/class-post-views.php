<?php

/**
 * Post Views System
 *
 * Handles view counting, synchronization, and formatted retrieval for news and posts.
 * Focuses on a unified sorting mechanism that prioritizes manual overrides.
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
     * Singleton instance.
     *
     * @var Greenergy_Post_Views|null
     */
    private static ?Greenergy_Post_Views $instance = null;

    /**
     * Meta key for manual view overrides.
     */
    public const MANUAL_VIEWS_KEY = '_news_view_count';

    /**
     * Meta key for real tracked views.
     */
    public const REAL_VIEWS_KEY = '_real_views';

    /**
     * Meta key used for unified sorting (consolidated value).
     */
    public const TOTAL_VIEWS_KEY = '_total_views_sort';

    /**
     * Cookie name for basic view tracking protection.
     */
    private const TRACKING_COOKIE_PREFIX = 'greenergy_viewed_';

    /**
     * Get the singleton instance.
     *
     * @return self
     */
    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor: Register WordPress hooks.
     */
    private function __construct()
    {
        // View tracking
        add_action('template_redirect', [$this, 'track_view_action']);

        // Data synchronization hooks
        add_action('updated_post_meta', [$this, 'sync_views_on_meta_update'], 10, 4);
        add_action('added_post_meta', [$this, 'sync_views_on_meta_update'], 10, 4);

        // Ensure new posts have the sort key initialized
        add_action('save_post_news', [$this, 'initialize_sort_key'], 10, 3);
        add_action('save_post_post', [$this, 'initialize_sort_key'], 10, 3);
    }

    /**
     * Track a post view if it's a single news or post page.
     * Includes basic bot detection and cookie-based spam protection.
     *
     * @return void
     */
    public function track_view_action(): void
    {
        if (! is_singular(['news', 'post'])) {
            return;
        }

        $post_id = get_the_ID();
        if (! $post_id) {
            return;
        }

        // Skip bots to keep stats clean
        if ($this->is_bot()) {
            return;
        }

        // Basic cookie protection
        $cookie_name = self::TRACKING_COOKIE_PREFIX . $post_id;
        if (isset($_COOKIE[$cookie_name])) {
            return;
        }

        $this->increment_real_views($post_id);

        // Set tracking cookie (valid for 1 hour)
        setcookie($cookie_name, '1', time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
    }

    /**
     * Increment the real view count for a specific post.
     *
     * @param int $post_id
     * @return void
     */
    private function increment_real_views(int $post_id): void
    {
        $real_views = (int) get_post_meta($post_id, self::REAL_VIEWS_KEY, true);
        $real_views++;

        update_post_meta($post_id, self::REAL_VIEWS_KEY, $real_views);

        // Sync to unified sort key immediately
        $this->update_unified_sort_key($post_id);
    }

    /**
     * Sync the unified sort key when manual or real meta keys are updated.
     *
     * @param int    $meta_id
     * @param int    $post_id
     * @param string $meta_key
     * @param mixed  $meta_value
     * @return void
     */
    public function sync_views_on_meta_update(int $meta_id, int $post_id, string $meta_key, $meta_value): void
    {
        if (self::MANUAL_VIEWS_KEY === $meta_key || self::REAL_VIEWS_KEY === $meta_key) {
            $this->update_unified_sort_key($post_id);
        }
    }

    /**
     * Initialize the sort key for newly created posts.
     *
     * @param int     $post_id
     * @param WP_Post $post
     * @param bool    $update
     * @return void
     */
    public function initialize_sort_key(int $post_id, $post, bool $update): void
    {
        if ($update) {
            return; // Only for new posts
        }

        if (! in_array($post->post_type, ['news', 'post'], true)) {
            return;
        }

        $this->update_unified_sort_key($post_id);
    }

    /**
     * Update the unified sort key based on the hierarchy: Manual > Real.
     *
     * @param int $post_id
     * @return void
     */
    private function update_unified_sort_key(int $post_id): void
    {
        $manual_raw = get_post_meta($post_id, self::MANUAL_VIEWS_KEY, true);
        $real       = (int) get_post_meta($post_id, self::REAL_VIEWS_KEY, true);

        // If manual value exists (even 0), treat it as override
        if ($manual_raw !== '' && $manual_raw !== null) {
            $total_sort_value = (int) $manual_raw;
        } else {
            $total_sort_value = $real;
        }

        update_post_meta($post_id, self::TOTAL_VIEWS_KEY, $total_sort_value);
    }


    /**
     * Get the displayable view count for a post.
     * Prioritizes manual count if available.
     *
     * @param int $post_id Optional. Defaults to current post ID.
     * @return string Formatted view count (e.g., 1,500).
     */
    public static function get_views(int $post_id = 0): string
    {
        if ($post_id === 0) {
            $post_id = get_the_ID();
        }

        if (! $post_id) {
            return '0';
        }

        $manual_raw = get_post_meta($post_id, self::MANUAL_VIEWS_KEY, true);

        // نحافظ على الرقم كما هو من ACF بدون تحويل int
        if ($manual_raw !== '' && $manual_raw !== null && is_numeric($manual_raw)) {
            return $manual_raw;
        }

        $real_raw = get_post_meta($post_id, self::REAL_VIEWS_KEY, true);
        $real = is_numeric($real_raw) ? $real_raw : 0;

        return $real;
    }


    /**
     * Identify common search engine bots.
     *
     * @return bool True if a bot is detected.
     */
    private function is_bot(): bool
    {
        if (! isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $bot_signatures = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'sogou',
            'exabot',
            'facebot',
            'ia_archiver'
        ];

        foreach ($bot_signatures as $bot) {
            if (str_contains($user_agent, $bot)) {
                return true;
            }
        }

        return false;
    }
}
