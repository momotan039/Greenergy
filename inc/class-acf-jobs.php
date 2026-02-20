<?php

/**
 * ACF Fields for Jobs
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group(array(
        'key' => 'group_job_details',
        'title' => 'تفاصيل الوظيفة',
        'fields' => array(
            array(
                'key' => 'field_job_company',
                'label' => 'اسم الشركة',
                'name' => 'company_name',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_job_location',
                'label' => 'الموقع',
                'name' => 'location',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'مثال: الرياض، السعودية',
            ),
            array(
                'key' => 'field_job_type_acf',
                'label' => 'نوع الوظيفة',
                'name' => 'job_type_acf',
                'type' => 'select',
                'choices' => array(
                    'full-time' => 'دوام كامل',
                    'part-time' => 'دوام جزئي',
                    'contract' => 'عقد',
                    'remote' => 'عمل عن بعد',
                ),
                'default_value' => 'full-time',
                'required' => 0,
            ),
            array(
                'key' => 'field_job_card_description',
                'label' => 'وصف مختصر (للبطاقة)',
                'name' => 'job_card_description',
                'type' => 'textarea',
                'instructions' => 'يظهر في بطاقة الوظيفة بقائمة الوظائف (بحد أقصى سطرين)',
                'required' => 0,
                'rows' => 3,
                'placeholder' => 'أدخل وصفاً مشوقاً للوظيفة للظهور في القائمة...',
            ),
            array(
                'key' => 'field_job_is_gold',
                'label' => 'وظيفة ذهبية؟',
                'name' => 'is_gold_acf',
                'type' => 'true_false',
                'instructions' => 'عند التفعيل، ستظهر الوظيفة ببرواز ذهبي وتصميم مميز في القوائم',
                'required' => 0,
                'ui' => 1,
                'ui_on_text' => 'نعم',
                'ui_off_text' => 'لا',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_job_manual_views',
                'label' => 'عدد المشاهدات اليدوي',
                'name' => '_news_view_count',
                'type' => 'number',
                'instructions' => 'إذا تم إدخال قيمة هنا، فستظهر كعدد مشاهدات للوظيفة بدلاً من العدد الحقيقي',
                'required' => 0,
                'min' => 0,
                'placeholder' => 'مثال: 1500',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'jobs',
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
