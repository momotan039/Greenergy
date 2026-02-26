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
        add_action('save_post_jobs', [$this, 'initialize_sort_key'], 10, 3);

        // Admin columns for News
        add_filter('manage_news_posts_columns', [$this, 'add_news_views_columns']);
        add_action('manage_news_posts_custom_column', [$this, 'render_news_views_columns'], 10, 2);
        add_filter('manage_edit-news_sortable_columns', [$this, 'make_news_views_columns_sortable']);

        // Admin columns for Jobs
        add_filter('manage_jobs_posts_columns', [$this, 'add_news_views_columns']);
        add_action('manage_jobs_posts_custom_column', [$this, 'render_news_views_columns'], 10, 2);
        add_filter('manage_edit-jobs_sortable_columns', [$this, 'make_news_views_columns_sortable']);

        // Admin columns for standard Posts
        add_filter('manage_post_posts_columns', [$this, 'add_news_views_columns']);
        add_action('manage_post_posts_custom_column', [$this, 'render_news_views_columns'], 10, 2);
        add_filter('manage_edit-post_sortable_columns', [$this, 'make_news_views_columns_sortable']);

        add_action('pre_get_posts', [$this, 'sort_news_views_columns']);
    }

    /**
     * Track a post view if it's a single news or post page.
     * Includes basic bot detection and cookie-based spam protection.
     *
     * @return void
     */
    public function track_view_action(): void
    {
        if (! is_singular(['news', 'post', 'jobs'])) {
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
        $manual_views = (int) get_post_meta($post_id, self::MANUAL_VIEWS_KEY, true);
        if ($manual_views > 0) {
            $manual_views++;
            update_post_meta($post_id, self::MANUAL_VIEWS_KEY, $manual_views);
        }
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

        if (! in_array($post->post_type, ['news', 'post', 'jobs'], true)) {
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

        $manual = is_numeric($manual_raw) ? (int) $manual_raw : 0;

        // total = manual base + real increments
        $total_sort_value = $manual + $real;

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
        $real_raw   = get_post_meta($post_id, self::REAL_VIEWS_KEY, true);

        $manual = is_numeric($manual_raw) ? (int) $manual_raw : 0;
        $real   = is_numeric($real_raw) ? (int) $real_raw : 0;

        return $manual > 0 ? self::format_number($manual) : self::format_number($real);
    }



    private static function format_number($num): string
    {
        $num = (float) $num;

        if ($num >= 1_000_000) {
            return round($num / 1_000_000, 1) . 'M';
        }

        if ($num >= 1_000) {
            return round($num / 1_000, 1) . 'K';
        }

        return (string) $num;
    }



    /**
     * Add custom columns to the news list.
     */
    public function add_news_views_columns(array $columns): array
    {
        $new_columns = [];
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key === 'title') {
                $new_columns['real_views'] = __('المشاهدات الحقيقية', 'greenergy');
                $new_columns['manual_views'] = __('المشاهدات اليدوية', 'greenergy');
            }
        }
        return $new_columns;
    }

    /**
     * Render content for the custom news columns.
     */
    public function render_news_views_columns(string $column, int $post_id): void
    {
        switch ($column) {
            case 'real_views':
                $real = get_post_meta($post_id, self::REAL_VIEWS_KEY, true);
                echo '<strong>' . esc_html(number_format_i18n((int) $real)) . '</strong>';
                break;

            case 'manual_views':
                $manual = get_post_meta($post_id, self::MANUAL_VIEWS_KEY, true);
                echo esc_html(is_numeric($manual) ? number_format_i18n((int) $manual) : '-');
                break;
        }
    }

    /**
     * Make the news view columns sortable.
     */
    public function make_news_views_columns_sortable(array $columns): array
    {
        $columns['real_views']   = 'real_views';
        $columns['manual_views'] = 'manual_views';
        return $columns;
    }

    /**
     * Handle sorting logic for news view columns.
     */
    public function sort_news_views_columns($query): void
    {
        if (! is_admin() || ! $query->is_main_query() || ! in_array($query->get('post_type'), ['news', 'jobs', 'post'])) {
            return;
        }

        $orderby = $query->get('orderby');

        switch ($orderby) {
            case 'real_views':
                $query->set('meta_key', self::REAL_VIEWS_KEY);
                $query->set('orderby', 'meta_value_num');
                break;
            case 'manual_views':
                $query->set('meta_key', self::MANUAL_VIEWS_KEY);
                $query->set('orderby', 'meta_value_num');
                break;
        }
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
