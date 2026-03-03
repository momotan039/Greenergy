<?php

/**
 * Custom Post Type: Organizations (دليل المنظمات)
 *
 * Integrates with Post Views system. Includes card description and manual views.
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_CPT_Organizations
{
    const POST_TYPE = 'organizations';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('init', [$this, 'register_post_meta']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_views_and_card_meta'], 10, 2);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('المنظمات', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('منظمة', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('دليل المنظمات', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة جديد', 'greenergy'),
            'add_new_item'       => __('إضافة منظمة جديدة', 'greenergy'),
            'edit_item'          => __('تعديل المنظمة', 'greenergy'),
            'new_item'           => __('منظمة جديدة', 'greenergy'),
            'view_item'          => __('عرض المنظمة', 'greenergy'),
            'search_items'       => __('بحث في المنظمات', 'greenergy'),
            'not_found'          => __('لم يتم العثور على منظمات', 'greenergy'),
            'not_found_in_trash' => __('لا توجد منظمات في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع المنظمات', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'organizations', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 10,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
            'show_in_rest'       => true,
            /*
             * Default block template when creating a new organization.
             * Same sections as companies except company-products. Order preserved.
             */
            'template'          => [
                ['greenergy/company-overview'],
                ['greenergy/company-about'],
                ['greenergy/company-team'],
                ['greenergy/company-projects'],
                ['greenergy/company-gallery'],
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_taxonomy()
    {
        register_taxonomy('organization_category', [self::POST_TYPE], [
            'hierarchical'      => false, // تصنيفات المنظمات: رئيسية فقط، لا تصنيفات فرعية
            'labels'            => [
                'name'          => _x('تصنيفات المنظمات', 'الاسم العام للتصنيف', 'greenergy'),
                'singular_name' => _x('تصنيف المنظمة', 'الاسم المفرد للتصنيف', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'organization-category'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy('organization_tag', [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('وسوم المنظمات', 'الاسم العام للوسوم', 'greenergy'),
                'singular_name' => _x('وسم المنظمة', 'الاسم المفرد للوسم', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'organization-tag'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy('organization_location', [self::POST_TYPE], [
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
            'rewrite'           => ['slug' => 'organization-location'],
            'show_in_rest'      => true,
        ]);
    }

    /**
     * Register post meta for card description (external listing).
     */
    public function register_post_meta()
    {
        register_post_meta(self::POST_TYPE, 'org_card_description', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ]);
    }

    /**
     * Meta key for manual views (must match Greenergy_Post_Views::MANUAL_VIEWS_KEY).
     */
    private static function manual_views_key()
    {
        return class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::MANUAL_VIEWS_KEY : '_news_view_count';
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'org_views',
            __('المشاهدات', 'greenergy'),
            [$this, 'render_views_meta_box'],
            self::POST_TYPE,
            'side'
        );
        add_meta_box(
            'org_card_description',
            __('وصف البطاقة (قائمة المنظمات)', 'greenergy'),
            [$this, 'render_card_description_meta_box'],
            self::POST_TYPE,
            'normal'
        );
    }

    public function render_views_meta_box($post)
    {
        $key = self::manual_views_key();
        wp_nonce_field('org_views_nonce', 'org_views_nonce');
        $manual = (int) get_post_meta($post->ID, $key, true);
        $real_key = class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::REAL_VIEWS_KEY : '_real_views';
        $real = (int) get_post_meta($post->ID, $real_key, true);
        ?>
        <p>
            <label for="org_manual_views"><?php esc_html_e('المشاهدات اليدوية', 'greenergy'); ?></label>
            <input type="number" id="org_manual_views" name="org_manual_views" value="<?php echo esc_attr($manual); ?>" min="0" step="1" class="widefat" />
        </p>
        <p class="description">
            <?php echo esc_html__('المشاهدات الفعلية:', 'greenergy'); ?> <strong><?php echo esc_html(number_format_i18n($real)); ?></strong>
        </p>
        <?php
    }

    public function render_card_description_meta_box($post)
    {
        wp_nonce_field('org_card_desc_nonce', 'org_card_desc_nonce');
        $desc = get_post_meta($post->ID, 'org_card_description', true);
        ?>
        <p>
            <label for="org_card_description"><?php esc_html_e('نص يظهر في بطاقة المنظمة في قائمة المنظمات (اختياري). إن تركت فارغاً يُستخدم الملخص أو مقتطف من المحتوى.', 'greenergy'); ?></label>
        </p>
        <textarea id="org_card_description" name="org_card_description" rows="3" class="widefat"><?php echo esc_textarea($desc); ?></textarea>
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
        if (isset($_POST['org_views_nonce']) && wp_verify_nonce($_POST['org_views_nonce'], 'org_views_nonce')) {
            $manual = isset($_POST['org_manual_views']) ? max(0, (int) $_POST['org_manual_views']) : 0;
            update_post_meta($post_id, $key, $manual);
        }
        if (isset($_POST['org_card_desc_nonce']) && wp_verify_nonce($_POST['org_card_desc_nonce'], 'org_card_desc_nonce')) {
            $desc = isset($_POST['org_card_description']) ? sanitize_textarea_field($_POST['org_card_description']) : '';
            update_post_meta($post_id, 'org_card_description', $desc);
        }
    }
}
