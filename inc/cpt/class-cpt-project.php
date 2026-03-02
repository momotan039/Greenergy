<?php

/**
 * CPT: Project (المشاريع)
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Greenergy_CPT_Project
 */
class Greenergy_CPT_Project
{
    const POST_TYPE = 'project';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
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
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'       => true,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}
