<?php

/**
 * ACF Fields for Experts CPT
 *
 * Required for expert-card: role, quote, work_for (manual) or linked org/company, profile link.
 * Phone and social links are edited in the Company Overview block on the expert post and synced to post meta for the card.
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) :

    acf_add_local_field_group([
        'key'                   => 'group_expert_card',
        'title'                 => __('بيانات بطاقة الخبير', 'greenergy'),
        'fields'                => [
            [
                'key'           => 'field_expert_role',
                'label'         => __('المسمى الوظيفي / الدور', 'greenergy'),
                'name'          => 'expert_role',
                'type'          => 'text',
                'instructions'  => __('مثال: مهندس طاقة رياح. يُستخدم في البطاقة تحت الاسم.', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_quote',
                'label'         => __('اقتباس', 'greenergy'),
                'name'          => 'expert_quote',
                'type'          => 'textarea',
                'rows'          => 2,
                'instructions'  => __('اقتباس قصير يظهر في البطاقة.', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_work_for',
                'label'         => __('يعمل لدى (نص يدوي)', 'greenergy'),
                'name'          => 'expert_work_for',
                'type'          => 'text',
                'instructions'  => __('إذا تركته فارغاً وربطت منظمة أو شركة أدناه، سيُستخدم اسمها تلقائياً.', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_linked_organization',
                'label'         => __('المنظمة المرتبطة', 'greenergy'),
                'name'          => 'expert_linked_organization',
                'type'          => 'post_object',
                'post_type'     => ['organizations'],
                'return_format' => 'object',
                'allow_null'    => 1,
                'multiple'      => 0,
                'instructions'  => __('اختياري. يُستخدم لـ "يعمل لدى" إذا لم يُملأ النص اليدوي.', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_linked_company',
                'label'         => __('الشركة المرتبطة', 'greenergy'),
                'name'          => 'expert_linked_company',
                'type'          => 'post_object',
                'post_type'     => ['companies'],
                'return_format' => 'object',
                'allow_null'    => 1,
                'multiple'      => 0,
                'instructions'  => __('اختياري. يُستخدم لـ "يعمل لدى" إذا لم يُملأ النص اليدوي أو المنظمة.', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_profile_url',
                'label'         => __('رابط الملف الشخصي', 'greenergy'),
                'name'          => 'expert_profile_url',
                'type'          => 'url',
                'instructions'  => __('رابط "عرض الملف". اتركه فارغاً لاستخدام رابط صفحة الخبير.', 'greenergy'),
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'experts',
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
