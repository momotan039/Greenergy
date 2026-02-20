<?php

/**
 * CPT: Jobs (Arabic UI)
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_CPT_Jobs
{
    const POST_TYPE = 'jobs';

    public function __construct()
    {
        add_action('init', [$this, 'register']);
    }

    public function register()
    {
        $labels = [
            'name'               => _x('الوظائف', 'Post type general name', 'greenergy'),
            'singular_name'      => _x('وظيفة', 'Post type singular name', 'greenergy'),
            'menu_name'          => _x('الوظائف', 'Admin Menu text', 'greenergy'),
            'add_new'            => __('إضافة جديد', 'greenergy'),
            'add_new_item'       => __('إضافة وظيفة جديدة', 'greenergy'),
            'edit_item'          => __('تعديل الوظيفة', 'greenergy'),
            'new_item'           => __('وظيفة جديدة', 'greenergy'),
            'view_item'          => __('عرض الوظيفة', 'greenergy'),
            'search_items'       => __('بحث في الوظائف', 'greenergy'),
            'not_found'          => __('لا توجد وظائف', 'greenergy'),
            'not_found_in_trash' => __('لا توجد وظائف في سلة المهملات', 'greenergy'),
            'all_items'          => __('جميع الوظائف', 'greenergy'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,

            // Slug عربي (اختياري)
            'rewrite' => [
                'slug'       => 'وظائف',
                'with_front' => false
            ],

            'capability_type' => 'post',
            'has_archive'     => true,
            'hierarchical'    => false,
            'menu_position'   => 7,
            'menu_icon'       => 'dashicons-businessman',

            'supports' => [
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'revisions'
            ],

            'taxonomies'   => ['category', 'post_tag'],
            'show_in_rest' => true,

            'template' => [
                ['greenergy/job-section', ['sectionType' => 'paragraph'], [
                    ['core/heading', ['level' => 3, 'placeholder' => 'المسمى الوظيفي', 'content' => 'المسمى الوظيفي']],
                    ['core/paragraph', ['placeholder' => 'اكتب المسمى الوظيفي هنا...']]
                ]],
                ['greenergy/job-section', ['sectionType' => 'list', 'listStyle' => 'bullets', 'iconStrategy' => 'uniform'], [
                    ['core/heading', ['level' => 3, 'placeholder' => 'المسؤوليات الرئيسية', 'content' => 'المسؤوليات الرئيسية']],
                    ['core/list', ['placeholder' => 'أضف المسؤوليات هنا...']]
                ]],
                ['greenergy/job-section', ['sectionType' => 'list', 'listStyle' => 'icons', 'iconStrategy' => 'uniform', 'iconType' => 'check'], [
                    ['core/heading', ['level' => 3, 'placeholder' => 'المؤهلات والمتطلبات', 'content' => 'المؤهلات والمتطلبات']],
                    ['core/list', ['placeholder' => 'أضف المؤهلات هنا...']]
                ]],
                ['greenergy/job-section', ['sectionType' => 'list', 'listStyle' => 'icons', 'iconStrategy' => 'uniform', 'iconType' => 'gift'], [
                    ['core/heading', ['level' => 3, 'placeholder' => 'المزايا والامتيازات', 'content' => 'المزايا والامتيازات']],
                    ['core/list', ['placeholder' => 'أضف المزايا هنا...']]
                ]],
            ],

            'template_lock' => false,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}
