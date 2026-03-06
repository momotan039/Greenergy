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

        add_action('wp_ajax_greenergy_company_products_page', [$this, 'company_products_page']);
        add_action('wp_ajax_nopriv_greenergy_company_products_page', [$this, 'company_products_page']);

        add_action('wp_ajax_greenergy_companies_page', [$this, 'companies_page']);
        add_action('wp_ajax_nopriv_greenergy_companies_page', [$this, 'companies_page']);

        add_action('wp_ajax_greenergy_organizations_page', [$this, 'organizations_page']);
        add_action('wp_ajax_nopriv_greenergy_organizations_page', [$this, 'organizations_page']);

        add_action('wp_ajax_greenergy_experts_page', [$this, 'experts_page']);
        add_action('wp_ajax_nopriv_greenergy_experts_page', [$this, 'experts_page']);

        add_action('wp_ajax_greenergy_experts_search_suggest', [$this, 'experts_search_suggest']);
        add_action('wp_ajax_nopriv_greenergy_experts_search_suggest', [$this, 'experts_search_suggest']);

        add_action('wp_ajax_greenergy_companies_search_suggest', [$this, 'companies_search_suggest']);
        add_action('wp_ajax_nopriv_greenergy_companies_search_suggest', [$this, 'companies_search_suggest']);
    }

    /**
     * Return company name suggestions for smart search (autocomplete).
     */
    public function companies_search_suggest()
    {
        $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
        $term = trim($term);
        if (strlen($term) < 2) {
            wp_send_json_success([]);
        }

        global $wpdb;
        $like = '%' . $wpdb->esc_like($term) . '%';
        $limit = 10;
        $post_type = 'companies';

        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_title LIKE %s ORDER BY post_title ASC LIMIT %d",
            $post_type,
            $like,
            $limit
        ));

        $results = [];
        foreach (array_filter(array_map('absint', $ids)) as $post_id) {
            $results[] = [
                'id'    => $post_id,
                'title' => get_the_title($post_id),
            ];
        }

        wp_send_json_success($results);
    }

    /**
     * Return expert name suggestions for search autocomplete (all-experts block).
     */
    public function experts_search_suggest()
    {
        $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';
        $term = trim($term);
        if (strlen($term) < 2) {
            wp_send_json_success([]);
        }

        global $wpdb;
        $like = '%' . $wpdb->esc_like($term) . '%';
        $limit = 10;
        $post_type = 'experts';

        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_title LIKE %s ORDER BY post_title ASC LIMIT %d",
            $post_type,
            $like,
            $limit
        ));

        $results = [];
        foreach (array_filter(array_map('absint', $ids)) as $post_id) {
            $results[] = [
                'id'    => $post_id,
                'title' => get_the_title($post_id),
            ];
        }

        wp_send_json_success($results);
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

    /**
     * Company products block: return one page of product cards HTML + pagination.
     */
    public function company_products_page()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }
        $product_ids = isset($_POST['product_ids']) ? wp_parse_id_list($_POST['product_ids']) : [];
        $product_ids = array_filter(array_map('absint', $product_ids));
        $page        = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $per_page    = isset($_POST['per_page']) ? max(1, min(24, absint($_POST['per_page']))) : 8;
        $block_id    = isset($_POST['block_id']) ? sanitize_text_field($_POST['block_id']) : '';

        $total     = count($product_ids);
        $total_pages = $total <= 0 ? 0 : (int) ceil($total / $per_page);
        $page      = min($page, max(1, $total_pages));
        $offset    = ($page - 1) * $per_page;
        $page_ids  = array_slice($product_ids, $offset, $per_page);

        $content = '';
        if (! empty($page_ids)) {
            $query = new WP_Query([
                'post_type'      => 'company_product',
                'post__in'       => $page_ids,
                'orderby'        => 'post__in',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            ]);
            if ($query->have_posts()) {
                ob_start();
                while ($query->have_posts()) {
                    $query->the_post();
                    get_template_part('templates/components/company-product-card', null, [
                        'post_id'       => get_the_ID(),
                        'block_id_attr' => $block_id,
                    ]);
                }
                $content = ob_get_clean();
            }
            wp_reset_postdata();
        } else {
            $content = '<p class="col-span-full text-neutral-500 text-right text-sm">' . esc_html__('لا توجد منتجات في هذه الصفحة.', 'greenergy') . '</p>';
        }

        $pagination_html = '';
        if ($total_pages > 1) {
            $pagination_html .= '<nav class="greenergy-pagination greenergy-company-products-pagination mt-6 flex justify-center items-center gap-2 flex-wrap" aria-label="' . esc_attr__('تنقل المنتجات', 'greenergy') . '">';
            if ($page > 1) {
                $pagination_html .= '<button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($page - 1) . '" aria-label="' . esc_attr__('الصفحة السابقة', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $page;
                $pagination_html .= '<button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm ' . ($active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500') . '" data-page="' . $i . '">' . $i . '</button>';
            }
            if ($page < $total_pages) {
                $pagination_html .= '<button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($page + 1) . '" aria-label="' . esc_attr__('الصفحة التالية', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>';
            }
            $pagination_html .= '</nav>';
        }

        wp_send_json_success([
            'content'    => $content,
            'pagination' => $pagination_html,
        ]);
    }

    /**
     * All-companies block: return one page of company cards HTML + pagination (filter-aware).
     */
    public function companies_page()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }
        $page        = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $per_page    = isset($_POST['per_page']) ? max(1, min(24, absint($_POST['per_page']))) : 9;
        $featured_ids = isset($_POST['featured_ids']) ? array_filter(array_map('absint', wp_parse_id_list($_POST['featured_ids']))) : [];

        // Temporarily set GET so greenergy_companies_query_args() uses the same filters (cat/country as term IDs)
        $_GET['cat']      = isset($_POST['cat']) ? absint($_POST['cat']) : 0;
        $_GET['country']  = isset($_POST['country']) ? absint($_POST['country']) : 0;
        $_GET['sort']    = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'latest';
        $_GET['s_company'] = isset($_POST['s_company']) ? sanitize_text_field($_POST['s_company']) : '';

        // Search by name first, then by description if no results
        $query = function_exists('greenergy_companies_query') ? greenergy_companies_query([
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post__not_in'   => $featured_ids,
        ]) : new WP_Query([
            'post_type'      => 'companies',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post__not_in'   => $featured_ids,
        ]);
        $total_pages = $query->max_num_pages;
        $current_page = max(1, $page);

        $content = '';
        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('templates/components/company-card', null, ['post_id' => get_the_ID(), 'is_featured' => false]);
            }
            $content = ob_get_clean();
            wp_reset_postdata();
        } else {
            $content = '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا توجد شركات تطابق المعايير.', 'greenergy') . '</p>';
        }

        $pagination_html = '';
        if ($total_pages > 1) {
            $pagination_html .= '<nav class="greenergy-pagination greenergy-all-companies-pagination mt-6 flex justify-center items-center gap-2 flex-wrap" aria-label="' . esc_attr__('تنقل الشركات', 'greenergy') . '">';
            if ($current_page > 1) {
                $pagination_html .= '<button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page - 1) . '" aria-label="' . esc_attr__('الصفحة السابقة', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $current_page;
                $pagination_html .= '<button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm ' . ($active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500') . '" data-page="' . $i . '">' . $i . '</button>';
            }
            if ($current_page < $total_pages) {
                $pagination_html .= '<button type="button" class="js-all-companies-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page + 1) . '" aria-label="' . esc_attr__('الصفحة التالية', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>';
            }
            $pagination_html .= '</nav>';
        }

        wp_send_json_success([
            'content'    => $content,
            'pagination' => $pagination_html,
        ]);
    }

    /**
     * All-organizations block: return one page of organization cards HTML + pagination (filter-aware, no refresh).
     */
    public function organizations_page()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }
        $page         = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $per_page     = isset($_POST['per_page']) ? max(1, min(24, absint($_POST['per_page']))) : 9;
        $featured_ids = isset($_POST['featured_ids']) ? array_filter(array_map('absint', wp_parse_id_list($_POST['featured_ids']))) : [];

        $_GET['cat']     = isset($_POST['cat']) ? absint($_POST['cat']) : 0;
        $_GET['country'] = isset($_POST['country']) ? absint($_POST['country']) : 0;
        $_GET['sort']    = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'latest';
        $_GET['s_org']   = isset($_POST['s_org']) ? sanitize_text_field($_POST['s_org']) : '';

        $query = function_exists('greenergy_organizations_query') ? greenergy_organizations_query([
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post__not_in'   => $featured_ids,
        ]) : new WP_Query([
            'post_type'      => 'organizations',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post__not_in'   => $featured_ids,
        ]);
        $total_pages  = $query->max_num_pages;
        $current_page = max(1, $page);
        $found_posts  = $query->found_posts;

        $content = '';
        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('templates/components/org-card', null, ['post_id' => get_the_ID(), 'is_featured' => false]);
            }
            $content = ob_get_clean();
            wp_reset_postdata();
        } else {
            $content = '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا توجد منظمات تطابق المعايير.', 'greenergy') . '</p>';
        }

        $from       = $found_posts > 0 ? (($current_page - 1) * $per_page) + 1 : 0;
        $to         = $found_posts > 0 ? min($current_page * $per_page, $found_posts) : 0;
        $count_text = $found_posts > 0
            ? sprintf(/* translators: 1: from number, 2: to number, 3: total count */ __('عرض %1$s - %2$s من %3$s منظمة', 'greenergy'), number_format_i18n($from), number_format_i18n($to), number_format_i18n($found_posts))
            : __('0 منظمة', 'greenergy');
        $count_html = '<p class="js-all-orgs-count text-neutral-500 text-sm text-center mb-4" aria-live="polite">' . esc_html($count_text) . '</p>';

        $pagination_html = '';
        if ($total_pages > 1) {
            $pagination_html .= '<nav class="greenergy-pagination greenergy-all-orgs-pagination mt-6 flex justify-center items-center gap-2 flex-wrap" aria-label="' . esc_attr__('تنقل المنظمات', 'greenergy') . '">';
            if ($current_page > 1) {
                $pagination_html .= '<button type="button" class="js-all-orgs-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page - 1) . '" aria-label="' . esc_attr__('الصفحة السابقة', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $current_page;
                $pagination_html .= '<button type="button" class="js-all-orgs-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm ' . ($active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500') . '" data-page="' . $i . '">' . $i . '</button>';
            }
            if ($current_page < $total_pages) {
                $pagination_html .= '<button type="button" class="js-all-orgs-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page + 1) . '" aria-label="' . esc_attr__('الصفحة التالية', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>';
            }
            $pagination_html .= '</nav>';
        }

        wp_send_json_success([
            'content'        => $content,
            'pagination'     => $pagination_html,
            'count_html'     => $count_html,
            'found_posts'    => $found_posts,
            'total_pages'    => $total_pages,
            'current_page'   => $current_page,
        ]);
    }

    /**
     * All-experts block: return one page of expert cards HTML + pagination (filter-aware, no refresh).
     */
    public function experts_page()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'greenergy_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }
        $page     = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $per_page = isset($_POST['per_page']) ? max(1, min(24, absint($_POST['per_page']))) : 9;

        $_GET['location'] = isset($_POST['location']) ? absint($_POST['location']) : 0;
        $_GET['cat']     = isset($_POST['cat']) ? absint($_POST['cat']) : 0;
        $_GET['s_exp']   = isset($_POST['s_exp']) ? sanitize_text_field($_POST['s_exp']) : '';

        $query = function_exists('greenergy_experts_query') ? greenergy_experts_query([
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ]) : new WP_Query([
            'post_type'      => 'experts',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ]);
        $total_pages  = $query->max_num_pages;
        $current_page = max(1, $page);
        $found_posts  = $query->found_posts;

        $content = '';
        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('templates/components/expert-card', null, ['post_id' => get_the_ID()]);
            }
            $content = ob_get_clean();
            wp_reset_postdata();
        } else {
            $content = '<p class="col-span-full text-neutral-500 text-center text-sm">' . esc_html__('لا يوجد خبراء يطابقون المعايير.', 'greenergy') . '</p>';
        }

        $from       = $found_posts > 0 ? (($current_page - 1) * $per_page) + 1 : 0;
        $to         = $found_posts > 0 ? min($current_page * $per_page, $found_posts) : 0;
        $count_text = $found_posts > 0
            ? sprintf(/* translators: 1: from number, 2: to number, 3: total count */ __('عرض %1$s - %2$s من %3$s خبير', 'greenergy'), number_format_i18n($from), number_format_i18n($to), number_format_i18n($found_posts))
            : __('0 خبير', 'greenergy');
        $count_html = '<p class="js-all-experts-count text-neutral-500 text-sm text-center mb-4" aria-live="polite">' . esc_html($count_text) . '</p>';

        $pagination_html = '';
        if ($total_pages > 1) {
            $pagination_html .= '<nav class="greenergy-pagination greenergy-all-experts-pagination mt-6 flex justify-center items-center gap-2 flex-wrap" aria-label="' . esc_attr__('تنقل الخبراء', 'greenergy') . '">';
            if ($current_page > 1) {
                $pagination_html .= '<button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page - 1) . '" aria-label="' . esc_attr__('الصفحة السابقة', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $current_page;
                $pagination_html .= '<button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm ' . ($active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500') . '" data-page="' . $i . '">' . $i . '</button>';
            }
            if ($current_page < $total_pages) {
                $pagination_html .= '<button type="button" class="js-all-experts-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="' . ($current_page + 1) . '" aria-label="' . esc_attr__('الصفحة التالية', 'greenergy') . '"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>';
            }
            $pagination_html .= '</nav>';
        }

        wp_send_json_success([
            'content'        => $content,
            'pagination'     => $pagination_html,
            'count_html'     => $count_html,
            'found_posts'    => $found_posts,
            'total_pages'    => $total_pages,
            'current_page'   => $current_page,
        ]);
    }
}

// Initialize
Greenergy_Ajax::get_instance();
