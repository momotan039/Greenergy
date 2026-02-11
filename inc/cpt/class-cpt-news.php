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
            'name'                  => _x('News', 'Post type general name', 'greenergy'),
            'singular_name'         => _x('News', 'Post type singular name', 'greenergy'),
            'menu_name'             => _x('News', 'Admin Menu text', 'greenergy'),
            'add_new'               => __('Add New', 'greenergy'),
            'add_new_item'          => __('Add New News', 'greenergy'),
            'edit_item'             => __('Edit News', 'greenergy'),
            'new_item'              => __('New News', 'greenergy'),
            'view_item'             => __('View News', 'greenergy'),
            'search_items'          => __('Search News', 'greenergy'),
            'not_found'             => __('No news found', 'greenergy'),
            'not_found_in_trash'    => __('No news found in Trash', 'greenergy'),
            'all_items'             => __('All News', 'greenergy'),
            'archives'              => __('News Archives', 'greenergy'),
            'featured_image'        => __('Featured Image', 'greenergy'),
            'set_featured_image'    => __('Set featured image', 'greenergy'),
            'remove_featured_image' => __('Remove featured image', 'greenergy'),
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
            'name'              => _x('News Categories', 'taxonomy general name', 'greenergy'),
            'singular_name'     => _x('News Category', 'taxonomy singular name', 'greenergy'),
            'search_items'      => __('Search Categories', 'greenergy'),
            'all_items'         => __('All Categories', 'greenergy'),
            'parent_item'       => __('Parent Category', 'greenergy'),
            'parent_item_colon' => __('Parent Category:', 'greenergy'),
            'edit_item'         => __('Edit Category', 'greenergy'),
            'update_item'       => __('Update Category', 'greenergy'),
            'add_new_item'      => __('Add New Category', 'greenergy'),
            'new_item_name'     => __('New Category Name', 'greenergy'),
            'menu_name'         => __('Categories', 'greenergy'),
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
            'name'                       => _x('News Labels', 'taxonomy general name', 'greenergy'),
            'singular_name'              => _x('News Label', 'taxonomy singular name', 'greenergy'),
            'search_items'               => __('Search Labels', 'greenergy'),
            'popular_items'              => __('Popular Labels', 'greenergy'),
            'all_items'                  => __('All Labels', 'greenergy'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Label', 'greenergy'),
            'update_item'                => __('Update Label', 'greenergy'),
            'add_new_item'               => __('Add New Label', 'greenergy'),
            'new_item_name'              => __('New Label Name', 'greenergy'),
            'separate_items_with_commas' => __('Separate labels with commas', 'greenergy'),
            'add_or_remove_items'        => __('Add or remove labels', 'greenergy'),
            'choose_from_most_used'      => __('Choose from the most used labels', 'greenergy'),
            'not_found'                  => __('No labels found.', 'greenergy'),
            'menu_name'                  => __('Labels', 'greenergy'),
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
                        'default_value' => 1,
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
