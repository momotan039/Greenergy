<?php

/**
 * نوع المنشور المخصص: الدورات (CPT: Courses)
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Greenergy_CPT_Courses
 */
class Greenergy_CPT_Courses
{

    const POST_TYPE = 'courses';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('init', [$this, 'register_taxonomy']);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('الدورات', 'اسم نوع المنشور العام', 'greenergy'),
            'singular_name'      => _x('دورة', 'اسم نوع المنشور المفرد', 'greenergy'),
            'menu_name'          => _x('الدورات', 'نص قائمة الإدارة', 'greenergy'),
            'add_new'            => __('أضف جديدًا', 'greenergy'),
            'add_new_item'       => __('إضافة دورة جديدة', 'greenergy'),
            'edit_item'          => __('تحرير الدورة', 'greenergy'),
            'new_item'           => __('دورة جديدة', 'greenergy'),
            'view_item'          => __('عرض الدورة', 'greenergy'),
            'search_items'       => __('البحث في الدورات', 'greenergy'),
            'not_found'          => __('لم يتم العثور على دورات', 'greenergy'),
            'not_found_in_trash' => __('لم يتم العثور على دورات في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع الدورات', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'courses', 'with_front' => false],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 8,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'       => true,
            'template'           => [
                ['greenergy/course-section', ['type' => 'paragraph', 'title' => 'نبذة عن الدورة']],
                ['greenergy/course-section', ['type' => 'list', 'title' => 'محاور الدورة']],
                ['greenergy/course-section', ['type' => 'list', 'title' => 'المؤهلات والمتطلبات']],
                ['greenergy/course-section', ['type' => 'target-audience', 'title' => 'الفئة المستهدفة']],
                ['greenergy/course-section', ['type' => 'list', 'title' => 'المخرجات التعليمية']],
            ],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_taxonomy()
    {
        register_taxonomy('course_category', [self::POST_TYPE], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x('تصنيفات الدورات', 'الاسم العام للتصنيف', 'greenergy'),
                'singular_name' => _x('تصنيف الدورة', 'الاسم المفرد للتصنيف', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'course-category'],
            'show_in_rest'      => true,
        ]);

        register_taxonomy('course_tag', [self::POST_TYPE], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x('وسوم الدورات', 'الاسم العام للوسوم', 'greenergy'),
                'singular_name' => _x('وسم الدورة', 'الاسم المفرد للوسم', 'greenergy'),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'course-tag'],
            'show_in_rest'      => true,
        ]);
    }
}
