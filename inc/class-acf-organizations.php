<?php

/**
 * ACF Fields for Organizations CPT
 *
 * Background image for featured/hero usage (e.g. featured-orgs block cards).
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group([
        'key'                   => 'group_organization_featured',
        'title'                 => __('صورة الخلفية للمنظمة', 'greenergy'),
        'fields'                => [
            [
                'key'           => 'field_organization_background_image',
                'label'         => __('صورة خلفية (بطاقة مميزة)', 'greenergy'),
                'name'          => 'organization_background_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => __('تُستخدم في أقسام "المنظمات المميزة" وغيرها. اتركه فارغاً لاستخدام صورة المنظمة الافتراضية.', 'greenergy'),
                'required'      => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'organizations',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ]);

endif;
