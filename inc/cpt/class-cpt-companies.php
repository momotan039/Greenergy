<?php

/**
 * Custom Post Type: Companies (دليل الشركات)
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_CPT_Companies
{
    const POST_TYPE = 'companies';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('init', [$this, 'register_post_meta']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_verified_meta'], 10, 2);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_views_and_card_meta'], 10, 2);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('الشركات', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('شركة', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('دليل الشركات', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة جديد', 'greenergy'),
            'add_new_item'       => __('إضافة شركة جديدة', 'greenergy'),
            'edit_item'          => __('تعديل الشركة', 'greenergy'),
            'new_item'           => __('شركة جديدة', 'greenergy'),
            'view_item'          => __('عرض الشركة', 'greenergy'),
            'search_items'       => __('بحث في الشركات', 'greenergy'),
            'not_found'          => __('لم يتم العثور على شركات', 'greenergy'),
            'not_found_in_trash' => __('لا توجد شركات في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع الشركات', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'companies', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 9,
            'menu_icon'          => 'dashicons-building',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
            'show_in_rest'       => true,
            /*
             * Default block template injected when admin creates a new company.
             * Sections appear in the editor in this order.
             */
            'template'          => [
                ['greenergy/company-overview'],
                ['greenergy/company-about'],
                ['greenergy/company-team'],
                ['greenergy/company-projects'],
                ['greenergy/company-products'],
                ['greenergy/company-gallery'],
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_taxonomy()
    {
        register_taxonomy('company_category', [self::POST_TYPE], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x('تصنيفات الشركات', 'الاسم العام للتصنيف', 'greenergy'),
                'singular_name' => _x('تصنيف الشركة', 'الاسم المفرد للتصنيف', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'company-category'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy('company_tag', [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('وسوم الشركات', 'الاسم العام للوسوم', 'greenergy'),
                'singular_name' => _x('وسم الشركة', 'الاسم المفرد للوسم', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'company-tag'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy('company_location', [self::POST_TYPE], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x('المواقع', 'الاسم العام للمواقع', 'greenergy'),
                'singular_name' => _x('موقع', 'الاسم المفرد للموقع', 'greenergy'),
                'search_items'  => __('بحث في المواقع', 'greenergy'),
                'all_items'     => __('جميع المواقع', 'greenergy'),
                'parent_item'   => __('الدولة', 'greenergy'),
                'parent_item_colon' => __('الدولة:', 'greenergy'),
                'edit_item'     => __('تعديل الموقع', 'greenergy'),
                'update_item'   => __('تحديث الموقع', 'greenergy'),
                'add_new_item'  => __('إضافة موقع جديد', 'greenergy'),
                'new_item_name' => __('اسم الموقع الجديد', 'greenergy'),
                'menu_name'     => __('المواقع', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'company-location'],
            'show_in_rest'      => true,
        ]);

        // نوع الشركة: عادية، موثوقة، ذهبية، فضية، ماسية
        register_taxonomy('company_type', [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('نوع الشركة', 'الاسم العام', 'greenergy'),
                'singular_name' => _x('نوع الشركة', 'الاسم المفرد', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'company-type'],
            'show_in_rest'      => true,
        ]);

        $this->ensure_company_type_terms();
    }

    /**
     * Register company_verified post meta for verified-company toggle.
     * show_in_rest enables block editor to read/write it.
     */
    public function register_post_meta()
    {
        register_post_meta(self::POST_TYPE, 'company_verified', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'boolean',
            'default'           => false,
            'sanitize_callback' => function ($v) {
                return (bool) $v;
            },
        ]);

        register_post_meta(self::POST_TYPE, 'company_card_description', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ]);
    }

    /**
     * Add meta box for verified company toggle (classic editor / sidebar).
     */
    public function add_meta_boxes()
    {
        add_meta_box(
            'company_verified',
            __('شركة موثوقة', 'greenergy'),
            [$this, 'render_verified_meta_box'],
            self::POST_TYPE,
            'side'
        );
        add_meta_box(
            'company_views',
            __('المشاهدات', 'greenergy'),
            [$this, 'render_views_meta_box'],
            self::POST_TYPE,
            'side'
        );
        add_meta_box(
            'company_card_description',
            __('وصف البطاقة (قائمة الشركات)', 'greenergy'),
            [$this, 'render_card_description_meta_box'],
            self::POST_TYPE,
            'normal'
        );
    }

    public function render_verified_meta_box($post)
    {
        wp_nonce_field('company_verified_nonce', 'company_verified_nonce');
        $verified = (bool) get_post_meta($post->ID, 'company_verified', true);
        ?>
        <p>
            <label>
                <input type="checkbox" name="company_verified" value="1" <?php checked($verified); ?> />
                <?php esc_html_e('شركة موثوقة', 'greenergy'); ?>
            </label>
        </p>
        <?php
    }

    public function save_verified_meta($post_id, $post)
    {
        if (! isset($_POST['company_verified_nonce']) || ! wp_verify_nonce($_POST['company_verified_nonce'], 'company_verified_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }
        $verified = isset($_POST['company_verified']) && $_POST['company_verified'] === '1';
        update_post_meta($post_id, 'company_verified', $verified ? 1 : 0);
    }

    /**
     * Meta key for manual views (must match Greenergy_Post_Views::MANUAL_VIEWS_KEY for sync).
     */
    private static function manual_views_key()
    {
        return class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::MANUAL_VIEWS_KEY : '_news_view_count';
    }

    public function render_views_meta_box($post)
    {
        $key = self::manual_views_key();
        wp_nonce_field('company_views_nonce', 'company_views_nonce');
        $manual = (int) get_post_meta($post->ID, $key, true);
        $real_key = class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::REAL_VIEWS_KEY : '_real_views';
        $real = (int) get_post_meta($post->ID, $real_key, true);
        ?>
        <p>
            <label for="company_manual_views"><?php esc_html_e('المشاهدات اليدوية', 'greenergy'); ?></label>
            <input type="number" id="company_manual_views" name="company_manual_views" value="<?php echo esc_attr($manual); ?>" min="0" step="1" class="widefat" />
        </p>
        <p class="description">
            <?php echo esc_html__('المشاهدات الفعلية:', 'greenergy'); ?> <strong><?php echo esc_html(number_format_i18n($real)); ?></strong>
        </p>
        <?php
    }

    public function render_card_description_meta_box($post)
    {
        wp_nonce_field('company_card_desc_nonce', 'company_card_desc_nonce');
        $desc = get_post_meta($post->ID, 'company_card_description', true);
        ?>
        <p>
            <label for="company_card_description"><?php esc_html_e('نص يظهر في بطاقة الشركة في قائمة الشركات (اختياري). إن تركت فارغاً يُستخدم الملخص أو مقتطف من المحتوى.', 'greenergy'); ?></label>
        </p>
        <textarea id="company_card_description" name="company_card_description" rows="3" class="widefat"><?php echo esc_textarea($desc); ?></textarea>
        <?php
    }

    public function save_views_and_card_meta($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }
        $key = self::manual_views_key();
        if (isset($_POST['company_views_nonce']) && wp_verify_nonce($_POST['company_views_nonce'], 'company_views_nonce')) {
            $manual = isset($_POST['company_manual_views']) ? max(0, (int) $_POST['company_manual_views']) : 0;
            update_post_meta($post_id, $key, $manual);
        }
        if (isset($_POST['company_card_desc_nonce']) && wp_verify_nonce($_POST['company_card_desc_nonce'], 'company_card_desc_nonce')) {
            $desc = isset($_POST['company_card_description']) ? sanitize_textarea_field($_POST['company_card_description']) : '';
            update_post_meta($post_id, 'company_card_description', $desc);
        }
    }

    /**
     * Ensure the 5 default company type terms exist (عادية، موثوقة، ذهبية، فضية، ماسية).
     */
    private function ensure_company_type_terms()
    {
        $types = [
            'normal'   => 'عادية',
            'trusted'  => 'موثوقة',
            'gold'     => 'ذهبية',
            'silver'   => 'فضية',
            'diamond'  => 'ماسية',
        ];
        foreach ($types as $slug => $name) {
            if (get_term_by('slug', $slug, 'company_type') === false) {
                wp_insert_term($name, 'company_type', ['slug' => $slug]);
            }
        }
    }
}
