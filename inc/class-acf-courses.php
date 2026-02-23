<?php

/**
 * ACF Fields for Courses
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_course_details',
        'title' => 'تفاصيل الكورس',
        'fields' => array(
            array(
                'key' => 'field_course_general_tab',
                'label' => 'بيانات عامة',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_course_start_date',
                'label' => 'تاريخ البدء',
                'name' => 'course_start_date',
                'type' => 'date_picker',
                'required' => 0,
                'display_format' => 'd-m-Y',
                'return_format' => 'd-m-Y',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_course_duration_value',
                'label' => 'المدة',
                'name' => 'course_duration_value',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'مثال: 5 أيام / 20 ساعة',
            ),
            array(
                'key' => 'field_course_registered_count',
                'label' => 'عدد المسجلين',
                'name' => 'course_registered_count',
                'type' => 'number',
                'required' => 0,
                'placeholder' => 'مثال: 245',
            ),
            array(
                'key' => 'field_course_price_type',
                'label' => 'تكلفة الكورس',
                'name' => 'course_price_type',
                'type' => 'select',
                'choices' => array(
                    'free' => 'مجانية',
                    'paid' => 'مدفوعة',
                ),
                'default_value' => 'free',
                'required' => 0,
            ),
            array(
                'key' => 'field_show_trainer',
                'label' => 'إظهار قسم المدرب؟',
                'name' => 'show_trainer',
                'type' => 'true_false',
                'instructions' => 'قم بتفعيل هذا الخيار لعرض بيانات المدرب في صفحة الكورس',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_course_trainer_section',
                'label' => 'بيانات المدرب',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_course_trainer_name',
                'label' => 'اسم المدرب',
                'name' => 'trainer_name',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'د. سارة العنزي',
            ),
            array(
                'key' => 'field_course_trainer_title',
                'label' => 'المسمى الوظيفي / الخبرة',
                'name' => 'trainer_title',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'خبيرة في أنظمة الطاقة المتجددة',
            ),
            array(
                'key' => 'field_course_trainer_bio',
                'label' => 'نبذة عن المدرب',
                'name' => 'trainer_bio',
                'type' => 'textarea',
                'required' => 0,
                'rows' => 3,
            ),
            array(
                'key' => 'field_course_trainer_image',
                'label' => 'صورة المدرب',
                'name' => 'trainer_image',
                'type' => 'image',
                'required' => 0,
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ),
            array(
                'key' => 'field_course_trainer_link',
                'label' => 'رابط المدرب',
                'name' => 'trainer_link',
                'type' => 'url',
                'required' => 0,
            ),
            array(
                'key' => 'field_course_trainer_stat1_val',
                'label' => 'إحصائية 1 (القيمة)',
                'name' => 'trainer_stat1_val',
                'type' => 'text',
                'required' => 0,
                'placeholder' => '+50',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_course_trainer_stat1_lab',
                'label' => 'إحصائية 1 (التسمية)',
                'name' => 'trainer_stat1_lab',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'مشروع منفذ',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_course_trainer_stat2_val',
                'label' => 'إحصائية 2 (القيمة)',
                'name' => 'trainer_stat2_val',
                'type' => 'text',
                'required' => 0,
                'placeholder' => '+500',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_course_trainer_stat2_lab',
                'label' => 'إحصائية 2 (التسمية)',
                'name' => 'trainer_stat2_lab',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'متدرب',
                'wrapper' => array('width' => '50'),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'courses',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;
