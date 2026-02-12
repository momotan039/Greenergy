<?php

/**
 * CPT: News
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Greenergy_CPT_News
 */
class Greenergy_CPT_News
{

    /**
     * Post type slug
     */
    const POST_TYPE = 'news';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('init', [$this, 'register_meta']);
    }

    /**
     * Register custom post type
     */
    public function register()
    {
        $labels = [
            'name'                  => _x('الأخبار', 'Post type general name', 'greenergy'),
            'singular_name'         => _x('خبر', 'Post type singular name', 'greenergy'),
            'menu_name'             => _x('الأخبار', 'Admin Menu text', 'greenergy'),
            'add_new'               => __('إضافة جديد', 'greenergy'),
            'add_new_item'          => __('إضافة خبر جديد', 'greenergy'),
            'edit_item'             => __('تعديل الخبر', 'greenergy'),
            'new_item'              => __('خبر جديد', 'greenergy'),
            'view_item'             => __('عرض الخبر', 'greenergy'),
            'search_items'          => __('البحث في الأخبار', 'greenergy'),
            'not_found'             => __('لم يتم العثور على أخبار', 'greenergy'),
            'not_found_in_trash'    => __('لا توجد أخبار في سلة المهملات', 'greenergy'),
            'all_items'             => __('جميع الأخبار', 'greenergy'),
            'archives'              => __('أرشيف الأخبار', 'greenergy'),
            'featured_image'        => __('الصورة البارزة', 'greenergy'),
            'set_featured_image'    => __('تعيين صورة بارزة', 'greenergy'),
            'remove_featured_image' => __('إزالة الصورة البارزة', 'greenergy'),
        ];


        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'news', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-megaphone',
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
            'taxonomies'         => ['post_tag'], // Add standard tags support
            'show_in_rest'       => true, // Gutenberg support
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    /**
     * Register taxonomy
     */
    public function register_taxonomy()
    {
        // News Category
        $labels_cat = [
            'name'              => _x('تصنيفات الأخبار', 'taxonomy general name', 'greenergy'),
            'singular_name'     => _x('تصنيف خبر', 'taxonomy singular name', 'greenergy'),
            'search_items'      => __('البحث في التصنيفات', 'greenergy'),
            'all_items'         => __('جميع التصنيفات', 'greenergy'),
            'parent_item'       => __('التصنيف الأب', 'greenergy'),
            'parent_item_colon' => __('التصنيف الأب:', 'greenergy'),
            'edit_item'         => __('تعديل التصنيف', 'greenergy'),
            'update_item'       => __('تحديث التصنيف', 'greenergy'),
            'add_new_item'      => __('إضافة تصنيف جديد', 'greenergy'),
            'new_item_name'     => __('اسم التصنيف الجديد', 'greenergy'),
            'menu_name'         => __('التصنيفات', 'greenergy'),
        ];


        $args_cat = [
            'hierarchical'      => true,
            'labels'            => $labels_cat,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'news-category'],
            'show_in_rest'      => true,
        ];

        register_taxonomy('news_category', [self::POST_TYPE], $args_cat);

        // News Label (Urgent, Included, etc.)
        $labels_label = [
            'name'                       => _x('العلامات المميزة', 'taxonomy general name', 'greenergy'),
            'singular_name'              => _x('علامة مميزة', 'taxonomy singular name', 'greenergy'),
            'search_items'               => __('البحث في العلامات', 'greenergy'),
            'popular_items'              => __('العلامات الأكثر استخدامًا', 'greenergy'),
            'all_items'                  => __('جميع العلامات', 'greenergy'),
            'edit_item'                  => __('تعديل العلامة', 'greenergy'),
            'update_item'                => __('تحديث العلامة', 'greenergy'),
            'add_new_item'               => __('إضافة علامة جديدة', 'greenergy'),
            'new_item_name'              => __('اسم العلامة الجديدة', 'greenergy'),
            'separate_items_with_commas' => __('افصل العلامات بفواصل', 'greenergy'),
            'add_or_remove_items'        => __('إضافة أو إزالة علامات', 'greenergy'),
            'choose_from_most_used'      => __('اختر من العلامات الأكثر استخدامًا', 'greenergy'),
            'not_found'                  => __('لم يتم العثور على علامات.', 'greenergy'),
            'menu_name'                  => __('العلامات المميزة', 'greenergy'),
        ];

        $args_label = [
            'hierarchical'      => false,
            'labels'            => $labels_label,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'news-label'],
            'show_in_rest'      => true,
        ];

        register_taxonomy('news_label', [self::POST_TYPE], $args_label);
    }

    /**
     * Register Meta Fields & ACF Support
     */
    public function register_meta()
    {
        // Source Name
        register_post_meta(self::POST_TYPE, '_news_source_name', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

        // Source URL
        register_post_meta(self::POST_TYPE, '_news_source_url', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

        // Author URL
        register_post_meta(self::POST_TYPE, '_news_author_url', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

        // View Count (Read only mostly, but accessible)
        register_post_meta(self::POST_TYPE, '_news_view_count', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'integer',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        // ACF Integration
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_news_details',
                'title' => 'تفاصيل الخبر',
                'fields' => [


                    // Tab: Source Data
                    [
                        'key' => 'tab_source',
                        'label' => 'بيانات المصدر',
                        'type' => 'tab',
                    ],
                    [
                        'key' => 'field_news_source_name',
                        'label' => 'اسم المصدر',
                        'name' => '_news_source_name',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_news_source_url',
                        'label' => 'رابط المصدر',
                        'name' => '_news_source_url',
                        'type' => 'url',
                    ],
                    [
                        'key' => 'field_news_view_count',
                        'label' => 'عدد المشاهدات (يدوي)',
                        'name' => '_news_view_count',
                        'type' => 'number',
                        'default_value' => '',
                        'min' => 0,
                        'max' => 999999999,
                        'step' => 1,
                        'prepend' => 'مشاهدة',
                        'append' => 'مرة',
                        'placeholder' => 'أدخل عدد المشاهدات',
                        'instructions' => 'أدخل عدد المشاهدات يدوياً',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                    ],

                    // Tab: Author Data
                    [
                        'key' => 'tab_author',
                        'label' => 'بيانات الكاتب',
                        'type' => 'tab',
                    ],
                    [
                        'key' => 'field_news_show_author_box',
                        'label' => 'إظهار معلومات الكاتب',
                        'name' => '_news_show_author_box',
                        'type' => 'true_false',
                        'default_value' => 0,
                        'ui' => 1,
                        'ui_on_text' => 'نعم',
                        'ui_off_text' => 'لا',
                    ],
                    [
                        'key' => 'field_news_author_name',
                        'label' => 'اسم الكاتب',
                        'name' => '_news_author_name',
                        'type' => 'text',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_news_show_author_box',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_news_author_title',
                        'label' => 'المسمى الوظيفي للكاتب',
                        'name' => '_news_author_title',
                        'type' => 'text',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_news_show_author_box',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_news_author_image',
                        'label' => 'صورة الكاتب',
                        'name' => '_news_author_image_id',
                        'type' => 'image',
                        'return_format' => 'id',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_news_show_author_box',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_news_author_url',
                        'label' => 'رابط الكاتب',
                        'name' => '_news_author_url',
                        'type' => 'url',
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_news_show_author_box',
                                    'operator' => '==',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => self::POST_TYPE,
                        ],
                    ],
                ],
            ]);
        }
    }
}
