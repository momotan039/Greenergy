<?php

/**
 * ACF Fields for Companies
 *
 * Banner fields for single company page (title, subtitle, image).
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group([
        'key'                   => 'group_company_banner',
        'title'                 => __('لافتة صفحة الشركة', 'greenergy'),
        'fields'                => [
            [
                'key'         => 'field_company_banner_title',
                'label'       => __('عنوان اللافتة', 'greenergy'),
                'name'        => 'company_banner_title',
                'type'        => 'text',
                'instructions' => __('يظهر في أعلى صفحة الشركة. اتركه فارغاً لاستخدام عنوان الشركة.', 'greenergy'),
                'required'    => 0,
                'placeholder' => __('مثال: شركة طاقة الرياح المتقدمة', 'greenergy'),
            ],
            [
                'key'         => 'field_company_banner_subtitle',
                'label'       => __('نص فرعي للافتة', 'greenergy'),
                'name'        => 'company_banner_subtitle',
                'type'        => 'text',
                'instructions' => __('نص صغير يظهر فوق العنوان. اتركه فارغاً لاستخدام العنوان الفرعي الافتراضي.', 'greenergy'),
                'required'    => 0,
                'placeholder' => __('مثال: الشركة', 'greenergy'),
            ],
            [
                'key'         => 'field_company_banner_image',
                'label'       => __('صورة خلفية اللافتة', 'greenergy'),
                'name'        => 'company_banner_image',
                'type'        => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions' => __('صورة الخلفية للافتة. اتركه فارغاً لاستخدام الصورة الافتراضية.', 'greenergy'),
                'required'    => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'companies',
                ],
            ],
        ],
        'menu_order'   => 0,
        'position'     => 'normal',
        'style'        => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active'       => true,
    ]);

endif;
