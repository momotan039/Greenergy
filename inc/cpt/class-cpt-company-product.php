<?php

/**
 * CPT: Company Product (منتجات الشركات)
 *
 * Each product: title, thumbnail, short description, variants (استطاعة + تقنية).
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_CPT_Company_Product
{
    const POST_TYPE = 'company_product';

    const META_VARIANTS = 'product_variants';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_meta']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::POST_TYPE, [$this, 'save_variants'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_variant_media']);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('منتجات الشركات', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('منتج', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('منتجات الشركات', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة منتج', 'greenergy'),
            'add_new_item'       => __('إضافة منتج جديد', 'greenergy'),
            'edit_item'          => __('تعديل المنتج', 'greenergy'),
            'new_item'           => __('منتج جديد', 'greenergy'),
            'view_item'          => __('عرض المنتج', 'greenergy'),
            'search_items'       => __('بحث المنتجات', 'greenergy'),
            'not_found'          => __('لا يوجد منتجات', 'greenergy'),
            'not_found_in_trash' => __('لا يوجد منتجات في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع المنتجات', 'greenergy'),
        ];

        $args = [
            'labels'              => $labels,
            'public'               => true,
            'publicly_queryable'   => true,
            'show_ui'              => true,
            'show_in_menu'         => true,
            'query_var'            => true,
            'rewrite'              => ['slug' => 'company-product', 'with_front' => false],
            'capability_type'      => 'post',
            'has_archive'          => false,
            'hierarchical'         => false,
            'menu_position'        => 10,
            'menu_icon'            => 'dashicons-cart',
            'supports'             => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'        => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_meta()
    {
        register_post_meta(self::POST_TYPE, self::META_VARIANTS, [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '[]',
            'description'  => __('JSON array of variants: [{ "feature": "تقنية", "capacity": "استطاعة" }]', 'greenergy'),
            'sanitize_callback' => function ($value) {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? wp_json_encode($decoded, JSON_UNESCAPED_UNICODE) : '[]';
                }
                return '[]';
            },
        ]);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'company_product_variants',
            __('خيارات المنتج (استطاعة / تقنية)', 'greenergy'),
            [$this, 'render_variants_meta_box'],
            self::POST_TYPE,
            'normal'
        );
    }

    public function render_variants_meta_box($post)
    {
        wp_nonce_field('company_product_variants_nonce', 'company_product_variants_nonce');
        $raw   = get_post_meta($post->ID, self::META_VARIANTS, true);
        $items = [];
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $items = $decoded;
            }
        }
?>
        <p class="description"><?php esc_html_e('أضف خيارات متعددة (مثلاً استطاعات أو تقنيات مختلفة) ويمكنك ربط ملف قابل للتحميل بكل خيار.', 'greenergy'); ?></p>
        <div id="company-product-variants">
            <?php foreach ($items as $i => $item) : ?>
                <?php
                $file_id = isset($item['file_id']) ? absint($item['file_id']) : 0;
                $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
                $file_name = $file_id ? basename(get_attached_file($file_id)) : '';
                ?>
                <div class="variant-row" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:center;">
                    <input type="text" name="product_variant_feature[]" value="<?php echo esc_attr($item['feature'] ?? ''); ?>"
                        placeholder="<?php esc_attr_e('تقنية / الميزة', 'greenergy'); ?>" class="regular-text" style="min-width:140px;" />
                    <input type="text" name="product_variant_capacity[]" value="<?php echo esc_attr($item['capacity'] ?? ''); ?>"
                        placeholder="<?php esc_attr_e('استطاعة', 'greenergy'); ?>" style="width:120px;" />
                    <span class="variant-file-wrap" style="display:inline-flex;align-items:center;gap:6px;">
                        <input type="hidden" name="product_variant_file_id[]" value="<?php echo esc_attr($file_id); ?>" class="variant-file-id" />
                        <button type="button" class="button variant-upload-btn"><?php echo $file_id ? esc_html__('تغيير الملف', 'greenergy') : esc_html__('إرفاق ملف', 'greenergy'); ?></button>
                        <span class="variant-file-name" style="font-size:12px;color:#50575e;"><?php echo $file_name ? esc_html($file_name) : '—'; ?></span>
                    </span>
                    <button type="button" class="button remove-variant"><?php esc_html_e('حذف', 'greenergy'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <p>
            <button type="button" class="button" id="add-product-variant"><?php esc_html_e('إضافة خيار', 'greenergy'); ?></button>
        </p>
        <script>
        (function() {
            var container = document.getElementById('company-product-variants');
            if (!container) return;
            var rowHtml = '<div class="variant-row" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:center;">' +
                '<input type="text" name="product_variant_feature[]" placeholder="<?php echo esc_js(__('تقنية / الميزة', 'greenergy')); ?>" class="regular-text" style="min-width:140px;" />' +
                '<input type="text" name="product_variant_capacity[]" placeholder="<?php echo esc_js(__('استطاعة', 'greenergy')); ?>" style="width:120px;" />' +
                '<span class="variant-file-wrap" style="display:inline-flex;align-items:center;gap:6px;">' +
                '<input type="hidden" name="product_variant_file_id[]" value="0" class="variant-file-id" />' +
                '<button type="button" class="button variant-upload-btn"><?php echo esc_js(__('إرفاق ملف', 'greenergy')); ?></button>' +
                '<span class="variant-file-name" style="font-size:12px;color:#50575e;">—</span></span>' +
                '<button type="button" class="button remove-variant"><?php echo esc_js(__('حذف', 'greenergy')); ?></button></div>';
            document.getElementById('add-product-variant').onclick = function() {
                var r = document.createElement('div');
                r.className = 'variant-row';
                r.innerHTML = rowHtml;
                r.querySelector('.remove-variant').onclick = function() { r.remove(); };
                bindVariantRow(r);
                container.appendChild(r);
            };
            function bindVariantRow(row) {
                var wrap = row.querySelector('.variant-file-wrap');
                if (!wrap) return;
                var fileIdInput = wrap.querySelector('.variant-file-id');
                var fileNameSpan = wrap.querySelector('.variant-file-name');
                var btn = wrap.querySelector('.variant-upload-btn');
                if (!btn || !fileIdInput) return;
                var frame = null;
                btn.onclick = function(e) {
                    e.preventDefault();
                    if (typeof wp === 'undefined' || !wp.media) return;
                    if (frame) {
                        frame.open();
                        return;
                    }
                    frame = wp.media({
                        title: '<?php echo esc_js(__('اختر ملفاً', 'greenergy')); ?>',
                        button: { text: '<?php echo esc_js(__('استخدم هذا الملف', 'greenergy')); ?>' },
                        multiple: false
                    });
                    frame.on('select', function() {
                        var att = frame.state().get('selection').first().toJSON();
                        if (att && att.id) {
                            fileIdInput.value = att.id;
                            fileNameSpan.textContent = att.filename || att.title || String(att.id);
                            btn.textContent = '<?php echo esc_js(__('تغيير الملف', 'greenergy')); ?>';
                        }
                    });
                    frame.open();
                };
            }
            container.querySelectorAll('.variant-row').forEach(function(row) { bindVariantRow(row); });
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variant')) e.target.closest('.variant-row').remove();
            });
        })();
        </script>
<?php
    }

    public function enqueue_variant_media($hook)
    {
        $screen = get_current_screen();
        if (! $screen || $screen->post_type !== self::POST_TYPE || $screen->base !== 'post') {
            return;
        }
        wp_enqueue_media();
    }

    public function save_variants($post_id, $post)
    {
        if (
            ! isset($_POST['company_product_variants_nonce']) ||
            ! wp_verify_nonce($_POST['company_product_variants_nonce'], 'company_product_variants_nonce')
        ) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }
        $features  = isset($_POST['product_variant_feature']) ? (array) $_POST['product_variant_feature'] : [];
        $capacities = isset($_POST['product_variant_capacity']) ? (array) $_POST['product_variant_capacity'] : [];
        $file_ids   = isset($_POST['product_variant_file_id']) ? (array) $_POST['product_variant_file_id'] : [];
        $variants   = [];
        foreach ($features as $i => $feature) {
            $cap    = isset($capacities[$i]) ? $capacities[$i] : '';
            $feature = is_string($feature) ? trim($feature) : '';
            $file_id = isset($file_ids[$i]) ? absint($file_ids[$i]) : 0;
            if ($feature !== '' || $cap !== '' || $file_id > 0) {
                $variants[] = [
                    'feature'  => $feature,
                    'capacity' => trim((string) $cap),
                    'file_id'  => $file_id,
                ];
            }
        }
        update_post_meta($post_id, self::META_VARIANTS, wp_json_encode($variants, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Get variants for a product post (decoded array).
     *
     * @param int $post_id
     * @return array
     */
    public static function get_variants($post_id)
    {
        $raw = get_post_meta($post_id, self::META_VARIANTS, true);
        if (! is_string($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
