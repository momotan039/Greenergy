<?php

/**
 * CPT: Experts
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Greenergy_CPT_Experts
 */
class Greenergy_CPT_Experts
{

    const POST_TYPE = 'experts';

    const TAXONOMY_LOCATION = 'expert_location';

    const TAXONOMY_CATEGORY = 'expert_category';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('init', [$this, 'register_expert_experience_meta'], 20);
        add_action('save_post_' . self::POST_TYPE, [$this, 'sync_overview_block_to_meta'], 10, 3);
        add_action('rest_after_insert_' . self::POST_TYPE, [$this, 'sync_overview_block_to_meta_rest'], 10, 3);
    }

    /**
     * Expose expert_experience in REST so the block editor can hydrate the field from meta when needed.
     */
    public function register_expert_experience_meta()
    {
        if (! function_exists('register_post_meta')) {
            return;
        }
        register_post_meta(self::POST_TYPE, 'expert_experience', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'default'       => '',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);
    }

    /**
     * When an expert post is saved, parse content for company-overview block and save phone/social/experience to post meta
     * so that expert-card and company-overview render can read them.
     */
    public function sync_overview_block_to_meta($post_id, $post, $update)
    {
        if (! $post || $post->post_status === 'auto-draft' || wp_is_post_revision($post_id)) {
            return;
        }
        $this->sync_overview_attrs_to_meta($post_id, $post->post_content ?: '');
    }

    /**
     * Sync when expert is saved via REST (block editor).
     */
    public function sync_overview_block_to_meta_rest($post, $request, $creating)
    {
        $post_id = $post->ID;
        if (! $post_id || $post->post_status === 'auto-draft') {
            return;
        }
        $content = $post->post_content ?: '';
        $this->sync_overview_attrs_to_meta($post_id, $content);
    }

    /**
     * Parse post content for company-overview block and write attributes to post meta.
     * Each meta key can be filled from the first matching block attribute (camelCase or snake_case).
     */
    private function sync_overview_attrs_to_meta($post_id, $content)
    {
        if ($content === '') {
            return;
        }
        $blocks         = parse_blocks($content);
        $overview_attrs = $this->find_overview_block_attrs($blocks);
        if ($overview_attrs === null) {
            return;
        }
        $meta_to_attrs = [
            'expert_phone'     => ['phone'],
            'expert_website'   => ['website'],
            'expert_twitter'   => ['xLink'],
            'expert_instagram' => ['instagramLink'],
            'expert_facebook'  => ['facebookLink'],
            'expert_linkedin'  => ['linkedinLink'],
            'expert_experience' => ['expertExperience', 'expert_experience'],
        ];
        foreach ($meta_to_attrs as $meta_key => $attr_names) {
            $val = '';
            foreach ($attr_names as $attr_name) {
                if (isset($overview_attrs[$attr_name])) {
                    $val = trim((string) $overview_attrs[$attr_name]);
                    break;
                }
            }
            if ($val !== '') {
                update_post_meta($post_id, $meta_key, $val);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    /**
     * @param array $blocks
     * @return array|null Block attributes, or null if block not found
     */
    private function find_overview_block_attrs(array $blocks)
    {
        foreach ($blocks as $block) {
            if (isset($block['blockName']) && $block['blockName'] === 'greenergy/company-overview') {
                return isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [];
            }
            if (! empty($block['innerBlocks'])) {
                $found = $this->find_overview_block_attrs($block['innerBlocks']);
                if ($found !== null) {
                    return $found;
                }
            }
        }
        return null;
    }

    /**
     * Register expert_location and expert_category taxonomies.
     */
    public function register_taxonomies()
    {
        $location_labels = [
            'name'              => _x('مواقع الخبراء', 'taxonomy general name', 'greenergy'),
            'singular_name'     => _x('موقع الخبير', 'taxonomy singular name', 'greenergy'),
            'search_items'      => __('بحث المواقع', 'greenergy'),
            'all_items'         => __('جميع المواقع', 'greenergy'),
            'parent_item'       => __('الموقع الأب', 'greenergy'),
            'parent_item_colon' => __('الموقع الأب:', 'greenergy'),
            'edit_item'         => __('تعديل الموقع', 'greenergy'),
            'update_item'       => __('تحديث الموقع', 'greenergy'),
            'add_new_item'      => __('إضافة موقع جديد', 'greenergy'),
            'new_item_name'     => __('اسم الموقع الجديد', 'greenergy'),
            'menu_name'         => __('الموقع', 'greenergy'),
        ];
        register_taxonomy(self::TAXONOMY_LOCATION, self::POST_TYPE, [
            'labels'            => $location_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'expert-location'],
        ]);

        $category_labels = [
            'name'              => _x('تصنيفات الخبراء', 'taxonomy general name', 'greenergy'),
            'singular_name'     => _x('تصنيف الخبير', 'taxonomy singular name', 'greenergy'),
            'search_items'      => __('بحث التصنيفات', 'greenergy'),
            'all_items'         => __('جميع التصنيفات', 'greenergy'),
            'parent_item'       => __('التصنيف الأب', 'greenergy'),
            'parent_item_colon' => __('التصنيف الأب:', 'greenergy'),
            'edit_item'         => __('تعديل التصنيف', 'greenergy'),
            'update_item'       => __('تحديث التصنيف', 'greenergy'),
            'add_new_item'      => __('إضافة تصنيف جديد', 'greenergy'),
            'new_item_name'     => __('اسم التصنيف الجديد', 'greenergy'),
            'menu_name'         => __('التصنيف', 'greenergy'),
        ];
        register_taxonomy(self::TAXONOMY_CATEGORY, self::POST_TYPE, [
            'labels'            => $category_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'expert-category'],
        ]);

        register_taxonomy('expert_tag', [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('وسوم الخبراء', 'الاسم العام للوسوم', 'greenergy'),
                'singular_name' => _x('وسم الخبير', 'الاسم المفرد للوسوم', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'expert-tag'],
            'show_in_rest'      => true,
        ]);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('الخبراء', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('خبراء', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('الخبراء', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة جديد', 'greenergy'),
            'add_new_item'       => __('إضافة خبير جديد', 'greenergy'),
            'edit_item'          => __('تعديل الخبير', 'greenergy'),
            'new_item'           => __('خبراء جديد', 'greenergy'),
            'view_item'          => __('عرض الخبير', 'greenergy'),
            'search_items'       => __('بحث الخبراء', 'greenergy'),
            'not_found'          => __('لا يوجد خبراء', 'greenergy'),
            'not_found_in_trash' => __('لا يوجد خبراء في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع الخبراء', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'articles', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-welcome-write-blog',
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
            'show_in_rest'       => true,
            'template'          => [
                ['greenergy/company-overview'],
                ['greenergy/company-about'],
                ['greenergy/expert-places-job'],
                ['greenergy/expert-practical-experience'],
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}
