<?php

/**
 * ACF Fields for Posts (standard post type)
 *
 * Mirrors the pattern of class-acf-courses.php + news ACF fields.
 * Fields:
 *  General Tab:
 *    - post_description  (textarea) — card excerpt / short desc
 *    - _news_view_count  (number)   — same manual-view key used by Greenergy_Post_Views
 *
 *  Writer Tab:
 *    - show_writer       (true_false)
 *    - writer_name       (text)
 *    - writer_title      (text)       — job title / specialty
 *    - writer_bio        (textarea)
 *    - writer_image      (image → url)
 *    - writer_link       (url)
 *    - writer_post_count (number)     — how many posts this writer has done
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group([
        'key'   => 'group_post_details',
        'title' => 'تفاصيل المقال',
        'fields' => [

            // ── Tab: General ──────────────────────────────────────────────────
            [
                'key'       => 'field_post_general_tab',
                'label'     => 'بيانات عامة',
                'type'      => 'tab',
                'placement' => 'top',
                'endpoint'  => 0,
            ],
            [
                'key'         => 'field_post_description',
                'label'       => 'وصف مختصر للمقال',
                'name'        => 'post_description',
                'type'        => 'textarea',
                'required'    => 0,
                'rows'        => 3,
                'placeholder' => 'وصف قصير يظهر في بطاقة المقال',
                'instructions' => 'يظهر هذا الوصف في بطاقة المقال (الأرشيف). إذا تُرك فارغاً سيُستخدم المقتطف التلقائي.',
            ],
            [
                'key'           => 'field_post_view_count',
                'label'         => 'عدد المشاهدات (يدوي)',
                'name'          => '_news_view_count',   // same key as news — Greenergy_Post_Views reads this
                'type'          => 'number',
                'required'      => 0,
                'default_value' => '',
                'min'           => 0,
                'max'           => 999999999,
                'step'          => 1,
                'prepend'       => 'مشاهدة',
                'append'        => 'مرة',
                'placeholder'   => 'أدخل عدد المشاهدات',
                'instructions'  => 'أدخل عدد المشاهدات يدوياً. سيتم تحديثه تلقائياً عند كل زيارة.',
            ],

            // ── Tab: Writer ───────────────────────────────────────────────────
            [
                'key'       => 'field_post_writer_tab',
                'label'     => 'بيانات الكاتب',
                'type'      => 'tab',
                'placement' => 'top',
                'endpoint'  => 0,
            ],
            [
                'key'           => 'field_post_show_writer',
                'label'         => 'إظهار قسم الكاتب؟',
                'name'          => 'show_writer',
                'type'          => 'true_false',
                'instructions'  => 'قم بتفعيل هذا الخيار لعرض بيانات الكاتب',
                'default_value' => 1,
                'ui'            => 1,
                'ui_on_text'    => 'نعم',
                'ui_off_text'   => 'لا',
            ],
            [
                'key'               => 'field_post_writer_name',
                'label'             => 'اسم الكاتب',
                'name'              => 'writer_name',
                'type'              => 'text',
                'required'          => 0,
                'placeholder'       => 'مثال: أ. محمد الأحمدي',
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
            [
                'key'               => 'field_post_writer_title',
                'label'             => 'التخصص / المسمى الوظيفي',
                'name'              => 'writer_title',
                'type'              => 'text',
                'required'          => 0,
                'placeholder'       => 'مثال: خبير في الطاقة المتجددة',
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
            [
                'key'               => 'field_post_writer_bio',
                'label'             => 'نبذة عن الكاتب',
                'name'              => 'writer_bio',
                'type'              => 'textarea',
                'required'          => 0,
                'rows'              => 3,
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
            [
                'key'               => 'field_post_writer_image',
                'label'             => 'صورة الكاتب',
                'name'              => 'writer_image',
                'type'              => 'image',
                'required'          => 0,
                'return_format'     => 'url',
                'preview_size'      => 'thumbnail',
                'library'           => 'all',
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
            [
                'key'               => 'field_post_writer_link',
                'label'             => 'رابط صفحة الكاتب',
                'name'              => 'writer_link',
                'type'              => 'url',
                'required'          => 0,
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
            [
                'key'               => 'field_post_writer_post_count',
                'label'             => 'عدد المقالات المنشورة',
                'name'              => 'writer_post_count',
                'type'              => 'number',
                'required'          => 0,
                'min'               => 0,
                'placeholder'       => 'مثال: 42',
                'instructions'      => 'عدد المقالات التي نشرها هذا الكاتب (يمكن إدخاله يدوياً)',
                'prepend'           => '',
                'append'            => 'مقال',
                'wrapper'           => ['width' => '50'],
                'conditional_logic' => [[['field' => 'field_post_show_writer', 'operator' => '==', 'value' => '1']]],
            ],
        ],
        'location' => [
            [[
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'post',
            ]],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => '',
    ]);

endif;
