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

    public function __construct()
    {
        add_action('init', [$this, 'register']);
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
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}
