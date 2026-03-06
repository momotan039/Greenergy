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
                'instructions'  => __('إن وُجد نص هنا يُعرض دائماً في البطاقة والملخص. إذا تركته فارغاً يُستخدم اسم الجهة التي أضفت الخبير فيها من صفحة الشركة/المنظمة (قسم فريق العمل).', 'greenergy'),
            ],
            [
                'key'           => 'field_expert_primary_entity',
                'label'         => __('الجهة الظاهرة في البطاقة والملخص', 'greenergy'),
                'name'          => 'expert_primary_entity',
                'type'          => 'post_object',
                'post_type'     => ['organizations', 'companies'],
                'return_format' => 'object',
                'allow_null'    => 1,
                'multiple'      => 0,
                'instructions'  => __('يُملأ تلقائياً من صفحات الشركات/المنظمات التي أضفت فيها الخبير في قسم "فريق العمل". عند ظهور الخبير في أكثر من جهة، اختر هنا أي جهة تظهر في البطاقة وملخص صفحته.', 'greenergy'),
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

    /**
     * عرض جهات الخبير المرتبطة فقط في حقل "الجهة الظاهرة" (تسهيل الاختيار).
     */
    add_filter('acf/fields/post_object/query/name=expert_primary_entity', 'greenergy_expert_primary_entity_query_only_linked', 10, 3);

endif;

/**
 * Limit expert_primary_entity choices to entities linked to this expert (from company-team).
 *
 * @param array $args   WP_Query args.
 * @param array $field  ACF field array.
 * @param int   $post_id Current post ID (expert).
 * @return array
 */
function greenergy_expert_primary_entity_query_only_linked($args, $field, $post_id)
{
    if (! $post_id || get_post_type($post_id) !== 'experts') {
        return $args;
    }
    $ids = get_post_meta($post_id, 'expert_linked_entity_ids', true);
    if (! is_array($ids)) {
        $ids = [];
    }
    $ids = array_filter(array_map('absint', $ids));
    if (empty($ids)) {
        $args['post__in'] = [0];
        return $args;
    }
    $args['post__in'] = $ids;
    $args['orderby'] = 'post__in';
    return $args;
}
