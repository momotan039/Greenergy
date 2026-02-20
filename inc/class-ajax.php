<?php

/**
 * Generic AJAX Handler Class
 *
 * Handles standard AJAX load more/pagination requests.
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Greenergy_Ajax
{

    /**
     * Instance of the class.
     *
     * @var Greenergy_Ajax
     */
    private static $instance = null;

    /**
     * Get instance of the class.
     *
     * @return Greenergy_Ajax
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_greenergy_load_posts', [$this, 'load_posts']);
        add_action('wp_ajax_nopriv_greenergy_load_posts', [$this, 'load_posts']);

        add_action('wp_ajax_greenergy_filter_latest_news', [$this, 'filter_latest_news']);
        add_action('wp_ajax_nopriv_greenergy_filter_latest_news', [$this, 'filter_latest_news']);
    }

    /**
     * Handle load posts request.
     */
    /**
     * Handle load posts request.
     */
    public function load_posts()
    {
        // Verify Nonce
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        // Get Args
        $args = isset($_POST['query_args']) ? json_decode(stripslashes($_POST['query_args']), true) : [];
        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $template_part = isset($_POST['template_part']) ? sanitize_text_field($_POST['template_part']) : 'templates/components/news-card';

        if (! is_array($args)) {
            wp_send_json_error(['message' => 'Invalid query args']);
        }

        // Ensure status is publish
        $args['post_status'] = 'publish';
        $args['paged'] = $page;

        // Query
        $query = new WP_Query($args);

        $content = '';

        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();

                if (strpos($template_part, 'job-card') !== false) {
                    $is_gold = ($args['meta_key'] ?? '') === '_is_gold' || (get_post_meta(get_the_ID(), '_is_gold', true) === 'yes');
                    get_template_part($template_part, null, ['post' => get_post(), 'is_gold' => $is_gold]);
                } else {
                    // For news cards, we want to maintain the item array structure for compatibility
                    $terms = get_the_terms(get_the_ID(), 'news_category');
                    $item = [
                        'title'     => get_the_title(),
                        'excerpt'   => get_the_excerpt(),
                        'date'      => get_the_date('d/m/Y'),
                        'views'     => Greenergy_Post_Views::get_views(get_the_ID()),
                        'image'     => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/800X800',
                        'permalink' => get_permalink(),
                        'cat'       => $terms && !is_wp_error($terms) ? $terms[0]->name : '',
                    ];
                    greenergy_get_template($template_part, null, ['item' => $item]);
                }
            }
            $content = ob_get_clean();
        } else {
            ob_start();
?>
            <div class="p-12 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 w-full">
                <p class="text-neutral-500"><?php _e('عذراً، لا توجد نتائج متاحة حالياً.', 'greenergy'); ?></p>
            </div>
            <?php
            $content = ob_get_clean();
        }

        // Generate Pagination HTML using helper
        $pagination_html = greenergy_get_pagination_html($query, $page);

        wp_reset_postdata();

        wp_send_json_success([
            'content'    => $content,
            'pagination' => $pagination_html,
        ]);
    }

    /**
     * Filter latest news by category
     */
    public function filter_latest_news()
    {
        // Verify Nonce
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';
        $term_id  = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;

        $args = [
            'post_type'      => 'news',
            'posts_per_page' => 8,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if ($term_id > 0) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ],
            ];
        } elseif ($category !== 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
                    'field'    => 'slug',
                    'terms'    => rawurldecode($category),
                ],
            ];
        }

        $query = new WP_Query($args);
        $content = '';

        // Find news page robustly
        $news_page = get_page_by_path('الاخبار') ?: get_page_by_title('الاخبار');
        $view_all_url = $news_page ? get_permalink($news_page) : home_url('/news');

        if ($category !== 'all') {
            $view_all_url = add_query_arg('news_cat', get_term($term_id, 'news_category')->slug, $view_all_url);
        }

        if ($query->have_posts()) {
            ob_start();
            $index = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $thumbnail = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/800X800';
                $views = Greenergy_Post_Views::get_views($post_id);
                $date = get_the_date('d/m/Y');
                $excerpt = get_the_excerpt() ?: wp_trim_words(get_the_content(), 15);
            ?>
                <div class="swiper-slide h-auto group">
                    <div class="bg-white rounded-2xl overflow-hidden cursor-pointer group hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500 h-full border border-gray-100 lg:border-none">
                        <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10 w-full h-full"></a>
                        <div class="relative aspect-square overflow-hidden">
                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="px-2 py-4 text-right group-hover:bg-green-600 relative">
                            <div class="self-stretch inline-flex justify-end items-start gap-4 w-full">
                                <div class="group-hover:text-white flex-1 text-right justify-start text-neutral-800 text-sm leading-5 line-clamp-2">
                                    <?php the_title(); ?>
                                </div>
                                <svg class="w-6 h-4 inline" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
                                </svg>
                            </div>
                            <p class="group-hover:text-white text-gray-600 text-xs md:text-sm mb-3 line-clamp-2">
                                <?php echo esc_html($excerpt); ?>
                            </p>
                            <div class="flex items-center justify-between text-[10px] md:text-xs font-bold text-gray-500 border-t border-gray-100 pt-3 group-hover:border-white/20">
                                <div class="flex items-center gap-1">
                                    <i class="far fa-eye group-hover:text-white"></i>
                                    <span class="group-hover:text-white"><?php echo esc_html($views); ?></span>
                                </div>
                                <div dir="ltr" class="group-hover:text-white"><?php echo esc_html($date); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
                $index++;
            }
            $content = ob_get_clean();
        } else {
            $content = '<div class="p-12 text-center w-full bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                <p class="text-neutral-500">' . __('لا توجد أخبار متوفرة في هذا القسم.', 'greenergy') . '</p>
            </div>';
        }

        wp_reset_postdata();

        wp_send_json_success([
            'content'     => $content,
            'view_all_url' => $view_all_url,
        ]);
    }
}

// Initialize
Greenergy_Ajax::get_instance();
