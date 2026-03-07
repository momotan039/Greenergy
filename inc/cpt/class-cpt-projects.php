<?php

/**
 * CPT: Projects (المشاريع)
 * Includes: views, hierarchical location (country/city + flag), type taxonomy,
 * meta (map URL, established date, funding, capacity). Default template: overview, about, gallery.
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_CPT_Projects
{
    const POST_TYPE = 'projects';

    const TAX_LOCATION = 'project_location';
    const TAX_TYPE     = 'project_type';
    const TAX_TAG      = 'project_tag';

    const META_MAP_URL     = 'project_map_url';
    const META_ESTABLISHED = 'project_established_date';
    const META_FUNDING     = 'project_funding';
    const META_CAPACITY    = 'project_capacity';
    const META_COUNTRY_CODE = 'project_country_code';

    const TERM_META_COUNTRY_CODE = 'project_location_country_code';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('init', [$this, 'register_post_meta']);
        add_action('init', [$this, 'register_term_meta'], 20);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_views_meta'], 10, 2);
        add_action(self::TAX_LOCATION . '_add_form_fields', [$this, 'location_add_country_code_field']);
        add_action(self::TAX_LOCATION . '_edit_form_fields', [$this, 'location_edit_country_code_field']);
        add_action('created_term', [$this, 'save_location_country_code'], 10, 3);
        add_action('edit_term', [$this, 'save_location_country_code'], 10, 3);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('المشاريع', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('مشروع', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('المشاريع', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة جديد', 'greenergy'),
            'add_new_item'       => __('إضافة مشروع جديد', 'greenergy'),
            'edit_item'          => __('تعديل المشروع', 'greenergy'),
            'new_item'           => __('مشروع جديد', 'greenergy'),
            'view_item'          => __('عرض المشروع', 'greenergy'),
            'search_items'       => __('بحث المشاريع', 'greenergy'),
            'not_found'          => __('لا يوجد مشاريع', 'greenergy'),
            'not_found_in_trash' => __('لا يوجد مشاريع في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع المشاريع', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'project', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'      => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
            'show_in_rest'       => true,
            'template'          => [
                ['greenergy/company-about', ['title' => 'نبذة عن المشروع']],
                ['greenergy/company-gallery', ['title' => 'معرض الصور الخاص بالمشروع']],
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_taxonomies()
    {
        // دول ومدن هرمي: الأب = دولة، الابن = مدينة. علم الدولة يُخزن كـ country_code (ISO 2) في term meta.
        register_taxonomy(self::TAX_LOCATION, [self::POST_TYPE], [
            'hierarchical'      => true,
            'labels'            => [
                'name'              => _x('الموقع (دولة / مدينة)', 'greenergy'),
                'singular_name'     => _x('موقع', 'greenergy'),
                'search_items'      => __('بحث المواقع', 'greenergy'),
                'all_items'         => __('جميع المواقع', 'greenergy'),
                'parent_item'       => __('الدولة', 'greenergy'),
                'parent_item_colon' => __('الدولة:', 'greenergy'),
                'edit_item'         => __('تعديل الموقع', 'greenergy'),
                'update_item'       => __('تحديث الموقع', 'greenergy'),
                'add_new_item'      => __('إضافة موقع جديد', 'greenergy'),
                'new_item_name'     => __('اسم الموقع الجديد', 'greenergy'),
                'menu_name'         => __('الموقع', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'project-location'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy(self::TAX_TYPE, [self::POST_TYPE], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x('نوع المشروع', 'greenergy'),
                'singular_name' => _x('نوع المشروع', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'project-type'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy(self::TAX_TAG, [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('وسوم المشروع', 'greenergy'),
                'singular_name' => _x('وسم المشروع', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'project-tag'],
            'show_in_rest'      => true,
        ]);
    }

    public function register_post_meta()
    {
        $post_meta = [
            self::META_MAP_URL     => ['type' => 'string', 'sanitize' => 'esc_url_raw'],
            self::META_ESTABLISHED => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
            self::META_FUNDING     => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
            self::META_CAPACITY    => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
            self::META_COUNTRY_CODE => ['type' => 'string', 'sanitize' => 'sanitize_text_field'],
        ];
        foreach ($post_meta as $key => $opts) {
            register_post_meta(self::POST_TYPE, $key, [
                'type'          => $opts['type'],
                'single'        => true,
                'show_in_rest'  => true,
                'auth_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ]);
        }
    }

    public function register_term_meta()
    {
        if (! taxonomy_exists(self::TAX_LOCATION)) {
            return;
        }
        register_term_meta(self::TAX_LOCATION, self::TERM_META_COUNTRY_CODE, [
            'type'              => 'string',
            'description'       => __('كود الدولة ISO 3166-1 alpha-2 (مثل SA, EG) لعرض العلم.', 'greenergy'),
            'single'            => true,
            'sanitize_callback' => function ($v) {
                $v = is_string($v) ? strtoupper(substr(trim($v), 0, 2)) : '';
                return preg_match('/^[A-Z]{2}$/', $v) ? $v : '';
            },
            'show_in_rest'      => true,
            'auth_callback'     => function () {
                return current_user_can('edit_posts');
            },
        ]);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'project_views',
            __('المشاهدات', 'greenergy'),
            [$this, 'render_views_meta_box'],
            self::POST_TYPE,
            'side'
        );
        add_meta_box(
            'project_data',
            __('بيانات المشروع', 'greenergy'),
            [$this, 'render_project_data_meta_box'],
            self::POST_TYPE,
            'normal'
        );
        add_meta_box(
            'project_location_flag',
            __('كود الدولة (علم الموقع)', 'greenergy'),
            [$this, 'render_country_code_meta_box'],
            self::POST_TYPE,
            'side'
        );
    }

    private static function manual_views_key()
    {
        return class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::MANUAL_VIEWS_KEY : '_news_view_count';
    }

    public function render_views_meta_box($post)
    {
        $key = self::manual_views_key();
        wp_nonce_field('project_views_nonce', 'project_views_nonce');
        $manual = (int) get_post_meta($post->ID, $key, true);
        $real_key = class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::REAL_VIEWS_KEY : '_real_views';
        $real = (int) get_post_meta($post->ID, $real_key, true);
?>
        <p>
            <label for="project_manual_views"><?php esc_html_e('المشاهدات اليدوية', 'greenergy'); ?></label>
            <input type="number" id="project_manual_views" name="project_manual_views" value="<?php echo esc_attr($manual); ?>" min="0" step="1" class="widefat" />
        </p>
        <p class="description"><?php esc_html_e('المشاهدات الفعلية:', 'greenergy'); ?> <strong><?php echo esc_html(number_format_i18n($real)); ?></strong></p>
    <?php
    }

    public function render_project_data_meta_box($post)
    {
        wp_nonce_field('project_data_nonce', 'project_data_nonce');
        $map_url     = get_post_meta($post->ID, self::META_MAP_URL, true);
        $established = get_post_meta($post->ID, self::META_ESTABLISHED, true);
        $funding     = get_post_meta($post->ID, self::META_FUNDING, true);
        $capacity    = get_post_meta($post->ID, self::META_CAPACITY, true);
    ?>
        <p>
            <label for="project_map_url"><?php esc_html_e('رابط الموقع على خريطة Google', 'greenergy'); ?></label>
            <input type="url" id="project_map_url" name="project_map_url" value="<?php echo esc_attr($map_url); ?>" class="widefat" placeholder="https://www.google.com/maps/..." />
        </p>
        <p>
            <label for="project_established_date"><?php esc_html_e('تاريخ التأسيس', 'greenergy'); ?></label>
            <input type="date" id="project_established_date" name="project_established_date" value="<?php echo esc_attr($established); ?>" class="widefat" />
        </p>
        <p>
            <label for="project_funding"><?php esc_html_e('التمويل', 'greenergy'); ?></label>
            <input type="text" id="project_funding" name="project_funding" value="<?php echo esc_attr($funding); ?>" class="widefat" />
        </p>
        <p>
            <label for="project_capacity"><?php esc_html_e('القدرة المركبة', 'greenergy'); ?></label>
            <input type="text" id="project_capacity" name="project_capacity" value="<?php echo esc_attr($capacity); ?>" class="widefat" placeholder="مثال: 2 جيجاواط" />
        </p>
        <p class="description"><?php esc_html_e('النوع يُحدد من تصنيف "نوع المشروع" في الشريط الجانبي.', 'greenergy'); ?></p>
    <?php
    }

    /**
     * Meta box: كود الدولة لعرض العلم بجانب الموقع في صفحة المشروع.
     * يظهر في الشريط الجانبي بجانب اختيار تصنيف الموقع.
     */
    public function render_country_code_meta_box($post)
    {
        wp_nonce_field('project_country_code_nonce', 'project_country_code_nonce');
        $code = get_post_meta($post->ID, self::META_COUNTRY_CODE, true);
        ?>
        <p>
            <label for="project_country_code"><?php esc_html_e('كود الدولة (ISO)', 'greenergy'); ?></label>
            <input type="text" id="project_country_code" name="project_country_code" value="<?php echo esc_attr($code); ?>" maxlength="2" pattern="[A-Za-z]{2}" placeholder="SA" style="width:4em; text-transform:uppercase;" />
        </p>
        <p class="description"><?php esc_html_e('حرفان فقط (مثل SA, EG, AE) لعرض علم الدولة بجانب الموقع. يمكن أيضاً تعيينه من صفحة تعديل مصطلح الموقع.', 'greenergy'); ?></p>
        <?php
    }

    public function save_views_meta($post_id, $post)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }
        $key = self::manual_views_key();
        if (isset($_POST['project_views_nonce']) && wp_verify_nonce($_POST['project_views_nonce'], 'project_views_nonce')) {
            $manual = isset($_POST['project_manual_views']) ? max(0, (int) $_POST['project_manual_views']) : 0;
            update_post_meta($post_id, $key, $manual);
        }
        if (isset($_POST['project_data_nonce']) && wp_verify_nonce($_POST['project_data_nonce'], 'project_data_nonce')) {
            update_post_meta($post_id, self::META_MAP_URL, isset($_POST['project_map_url']) ? esc_url_raw($_POST['project_map_url']) : '');
            update_post_meta($post_id, self::META_ESTABLISHED, isset($_POST['project_established_date']) ? sanitize_text_field($_POST['project_established_date']) : '');
            update_post_meta($post_id, self::META_FUNDING, isset($_POST['project_funding']) ? sanitize_text_field($_POST['project_funding']) : '');
            update_post_meta($post_id, self::META_CAPACITY, isset($_POST['project_capacity']) ? sanitize_text_field($_POST['project_capacity']) : '');
        }
        if (isset($_POST['project_country_code_nonce']) && wp_verify_nonce($_POST['project_country_code_nonce'], 'project_country_code_nonce')) {
            $code = strtoupper(substr(trim(sanitize_text_field($_POST['project_country_code'] ?? '')), 0, 2));
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                update_post_meta($post_id, self::META_COUNTRY_CODE, $code);
            } else {
                delete_post_meta($post_id, self::META_COUNTRY_CODE);
            }
        }
    }

    public function location_add_country_code_field()
    {
    ?>
        <div class="form-field">
            <label for="project_location_country_code"><?php esc_html_e('كود الدولة (للدولة فقط)', 'greenergy'); ?></label>
            <input type="text" id="project_location_country_code" name="project_location_country_code" value="" maxlength="2" pattern="[A-Za-z]{2}" placeholder="SA" style="width:4em; text-transform:uppercase;" />
            <p class="description"><?php esc_html_e('حرفان فقط (ISO) لعرض علم الدولة، مثل: SA, EG, AE. اتركه فارغاً للمدن.', 'greenergy'); ?></p>
        </div>
    <?php
    }

    public function location_edit_country_code_field($term)
    {
        $code = get_term_meta($term->term_id, self::TERM_META_COUNTRY_CODE, true);
    ?>
        <tr class="form-field">
            <th><label for="project_location_country_code"><?php esc_html_e('كود الدولة (للدولة فقط)', 'greenergy'); ?></label></th>
            <td>
                <input type="text" id="project_location_country_code" name="project_location_country_code" value="<?php echo esc_attr($code); ?>" maxlength="2" pattern="[A-Za-z]{2}" style="width:4em; text-transform:uppercase;" />
                <p class="description"><?php esc_html_e('حرفان فقط (ISO) لعرض العلم، مثل: SA, EG, AE.', 'greenergy'); ?></p>
            </td>
        </tr>
<?php
    }

    public function save_location_country_code($term_id, $tt_id, $taxonomy)
    {
        if ($taxonomy !== self::TAX_LOCATION) {
            return;
        }
        if (! isset($_POST['project_location_country_code'])) {
            return;
        }
        $code = strtoupper(substr(trim(sanitize_text_field($_POST['project_location_country_code'])), 0, 2));
        if (preg_match('/^[A-Z]{2}$/', $code)) {
            update_term_meta($term_id, self::TERM_META_COUNTRY_CODE, $code);
        } else {
            delete_term_meta($term_id, self::TERM_META_COUNTRY_CODE);
        }
    }

    /**
     * Base URL for country flags (2-letter ISO). Lightweight CDN.
     *
     * @param string $country_code Two-letter ISO code.
     * @return string
     */
    public static function get_country_flag_url($country_code)
    {
        if (! is_string($country_code) || strlen($country_code) !== 2) {
            return '';
        }
        $code = strtolower($country_code);
        return 'https://flagcdn.com/w40/' . $code . '.png';
    }
}
