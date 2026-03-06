<?php

/**
 * Company Team Block — render
 * Dynamic block: manual team members and/or experts from CPT.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

// Post ID and company_type for color theme (gold, silver, diamond, trusted, normal).
$post_id = isset($block->context['postId']) ? (int) $block->context['postId'] : get_the_ID();
if (! $post_id && is_singular('companies')) {
    $post_id = get_queried_object_id();
}
if (! $post_id && is_singular('organizations')) {
    $post_id = get_queried_object_id();
}
$post_type = $post_id ? get_post_type($post_id) : '';
$type_terms = ($post_id && $post_type !== 'organizations') ? get_the_terms($post_id, 'company_type') : null;
$type_slug = ($type_terms && ! is_wp_error($type_terms) && ! empty($type_terms)) ? $type_terms[0]->slug : 'normal';
$type_slug = in_array($type_slug, ['normal', 'gold', 'silver', 'diamond', 'trusted'], true) ? $type_slug : 'normal';

// Border and ring classes + gradient style for social bar (gradient colors per company_type).
$type_border_class = [
    'normal'  => 'border-sky-500',
    'gold'    => 'border-amber-500',
    'silver'  => 'border-gray-400',
    'diamond' => 'border-sky-500',
    'trusted' => 'border-emerald-500',
][$type_slug];
$type_ring_class = [
    'normal'  => 'ring-sky-500',
    'gold'    => 'ring-amber-500',
    'silver'  => 'ring-gray-400',
    'diamond' => 'ring-sky-500',
    'trusted' => 'ring-emerald-500',
][$type_slug];
$type_gradient_style = [
    'normal'  => 'background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);',
    'gold'    => 'background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #b45309 100%);',
    'silver'  => 'background: linear-gradient(135deg, #9ca3af 0%, #4b5563 50%, #374151 100%);',
    'diamond' => 'background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);',
    'trusted' => 'background: linear-gradient(135deg, #34d399 0%, #059669 50%, #047857 100%);',
][$type_slug];

// Ensure we have attributes (from first param or from block object).
$raw_attrs = $attributes ?? [];
if (empty($raw_attrs) && isset($block) && $block instanceof WP_Block) {
    $raw_attrs = $block->attributes ?? [];
}
$attributes = wp_parse_args($raw_attrs, [
    'title'           => 'يعمل هنا',
    'teamMembers'     => [],
    'selectedExperts' => [],
]);

// Support both camelCase (from editor) and snake_case if ever passed.
$team_members = [];
if (! empty($attributes['teamMembers']) && is_array($attributes['teamMembers'])) {
    $team_members = $attributes['teamMembers'];
} elseif (! empty($attributes['team_members']) && is_array($attributes['team_members'])) {
    $team_members = $attributes['team_members'];
}

$title = isset($attributes['title']) ? (string) $attributes['title'] : 'يعمل هنا';
$title_company = 'يعمل هنا';
$title_org    = 'فريق المنظمة';
$post_type = $post_id ? get_post_type($post_id) : '';
if ($post_type === 'organizations' && ($title === '' || $title === $title_company)) {
    $title = $title_org;
} elseif ($title === '') {
    $title = $title_company;
}
$experts      = is_array($attributes['selectedExperts']) ? $attributes['selectedExperts'] : [];
$expert_ids   = array_values(array_filter(array_map(function ($item) {
    if (is_array($item) && isset($item['id'])) {
        return (int) $item['id'];
    }
    return is_numeric($item) ? (int) $item : null;
}, $experts)));
$experts_query = ! empty($expert_ids) ? new WP_Query([
    'post_type'      => 'experts',
    'post__in'       => $expert_ids,
    'orderby'        => 'post__in',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]) : null;

// Build flat list of team items: manual members first, then experts from CPT (if any).
$team_items = [];
if (! empty($team_members)) {
    foreach ($team_members as $member) {
        if (! is_array($member)) {
            continue;
        }
        $name   = trim((string) ($member['name'] ?? $member['title'] ?? ''));
        $role   = trim((string) ($member['role'] ?? $member['description'] ?? ''));
        $img_id = absint($member['imageId'] ?? $member['image_id'] ?? 0);
        $img_url = trim((string) ($member['imageUrl'] ?? $member['image_url'] ?? ''));
        if ($img_url === '' && $img_id > 0) {
            $img_url = wp_get_attachment_image_url($img_id, 'thumbnail') ?: '';
        }
        $link = trim((string) ($member['link'] ?? ''));
        if ($name === '' && $role === '' && $img_url === '') {
            continue;
        }
        $team_items[] = [
            'name'     => $name,
            'role'     => $role,
            'img_url'  => $img_url,
            'link'     => $link,
            'twitter'  => isset($member['twitter']) ? (string) $member['twitter'] : '',
            'instagram' => isset($member['instagram']) ? (string) $member['instagram'] : '',
            'facebook' => isset($member['facebook']) ? (string) $member['facebook'] : '',
            'linkedin' => isset($member['linkedin']) ? (string) $member['linkedin'] : '',
        ];
    }
}
if ($experts_query && $experts_query->have_posts()) {
    while ($experts_query->have_posts()) {
        $experts_query->the_post();
        $team_items[] = [
            'name'   => get_the_title(),
            'role'   => get_the_excerpt() ?: __('لا يوجد وصف للخبرة', 'greenergy'),
            'img_url' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
            'link'   => get_permalink(),
        ];
    }
    wp_reset_postdata();
}

$has_any = ! empty($team_items);
$item_count = count($team_items);

// 2 عناصر على الشاشات الصغيرة، 4 عناصر كاملة على الشاشات الكبيرة.
$swiper_settings = [
    'slidesPerView'  => 2,
    'spaceBetween'   => 16,
    'centeredSlides' => false,
    'loop'           => $item_count > 4,
    'watchOverflow'  => true,
    'grabCursor'     => true,
    'autoplay'       => $item_count > 1 ? [
        'delay'                   => 5000,
        'disableOnInteraction'     => false,
    ] : false,
    'pagination'     => [
        'clickable' => true,
    ],
    'breakpoints'    => [
        '640'  => [
            'slidesPerView' => 2,
            'spaceBetween'  => 20,
        ],
        '768'  => [
            'slidesPerView' => 4,
            'spaceBetween'  => 24,
        ],
    ],
];
?>
<div class="w-full bg-white p-4 rounded-lg shadow-lg outline outline-1 outline-gray-200 border border-zinc-100 flex flex-col gap-4">
    <h2 class="text-xl font-bold text-right"><?php echo esc_html($title); ?></h2>
    <?php if (! $has_any) : ?>
        <p class="text-neutral-500 text-right text-sm"><?php esc_html_e('لم تتم إضافة فريق بعد.', 'greenergy'); ?></p>
    <?php else : ?>
        <style>
            /* مساحة علوية لظهور صورة الآفاتار كاملة (top: -2.2rem) دون قص */
            .js-company-team-swiper .swiper-wrapper {
                padding-top: 3rem;
                align-items: stretch;
            }
        </style>
        <div class="swiper swiper-container js-swiper-init js-company-team-swiper overflow-hidden w-full pb-12"
            data-swiper-config="<?php echo esc_attr(wp_json_encode($swiper_settings)); ?>">
            <div class="swiper-wrapper items-stretch">
                <?php foreach ($team_items as $item) : ?>
                    <div class="swiper-slide h-auto">
                        <div class="bg-white relative rounded-2xl p-4 flex flex-col items-center gap-3 shadow-md border h-full box-border <?php echo esc_attr($type_border_class); ?>">
                            <?php if (! empty($item['img_url'])) : ?>
                                <img class="w-20 h-20 rounded-2xl object-cover ring-4 <?php echo esc_attr($type_ring_class); ?> absolute top-[-2.2rem]" src="<?php echo esc_url($item['img_url']); ?>" alt="<?php echo esc_attr($item['name']); ?>" />
                            <?php else : ?>
                                <div class="w-20 h-20 rounded-2xl ring-4 <?php echo esc_attr($type_ring_class); ?> absolute top-[-2.2rem] bg-neutral-200 flex items-center justify-center text-neutral-500 text-xs"><?php esc_html_e('صورة', 'greenergy'); ?></div>
                            <?php endif; ?>
                            <div class="text-center pt-12">
                                <div class="text-base font-medium text-neutral-950"><?php echo esc_html($item['name'] ?: '—'); ?></div>
                                <div class="text-xs text-stone-500"><?php echo esc_html($item['role'] ?: __('لا يوجد وصف', 'greenergy')); ?></div>
                            </div>
                            <?php if (! empty($item['link'])) : ?>
                                <a href="<?php echo esc_url($item['link']); ?>" class="w-full h-9 rounded-lg border border-zinc-200 text-sm text-neutral-800 hover:bg-zinc-50 transition flex items-center justify-center"><?php esc_html_e('عرض الملف', 'greenergy'); ?></a>
                            <?php else : ?>
                                <span class="w-full h-9 rounded-lg border border-zinc-200 text-sm text-neutral-400 flex items-center justify-center"><?php esc_html_e('عرض الملف', 'greenergy'); ?></span>
                            <?php endif; ?>
                            <div class="w-full px-4 py-1 rounded-full flex justify-between pb-[8px]" style="<?php echo esc_attr($type_gradient_style); ?>">
                                <?php if (! empty($item['link'])) : ?>
                                    <a href="<?php echo esc_url($item['link']); ?>" aria-label="رابط"><i class="text-white text-xs fa-solid fa-link"></i></a>
                                <?php else : ?>
                                    <span aria-hidden="true"><i class="text-white text-xs fa-solid fa-link"></i></span>
                                <?php endif; ?>

                                <?php if (! empty($item['twitter'])) : ?>
                                    <a href="<?php echo esc_url($item['twitter']); ?>" aria-label="X"><i class="text-white text-xs fa-brands fa-x-twitter"></i></a>
                                <?php else : ?>
                                    <span aria-hidden="true"><i class="text-white text-xs fa-brands fa-x-twitter"></i></span>
                                <?php endif; ?>

                                <?php if (! empty($item['instagram'])) : ?>
                                    <a href="<?php echo esc_url($item['instagram']); ?>" aria-label="انستغرام"><i class="text-white text-xs fa-brands fa-instagram"></i></a>
                                <?php else : ?>
                                    <span aria-hidden="true"><i class="text-white text-xs fa-brands fa-instagram"></i></span>
                                <?php endif; ?>

                                <?php if (! empty($item['facebook'])) : ?>
                                    <a href="<?php echo esc_url($item['facebook']); ?>" aria-label="فيسبوك"><i class="text-white text-xs fa-brands fa-facebook-f"></i></a>
                                <?php else : ?>
                                    <span aria-hidden="true"><i class="text-white text-xs fa-brands fa-facebook-f"></i></span>
                                <?php endif; ?>

                                <?php if (! empty($item['linkedin'])) : ?>
                                    <a href="<?php echo esc_url($item['linkedin']); ?>" aria-label="لينكد إن"><i class="text-white text-xs fa-brands fa-linkedin-in"></i></a>
                                <?php else : ?>
                                    <span aria-hidden="true"><i class="text-white text-xs fa-brands fa-linkedin-in"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination !relative mt-6"></div>
        </div>
    <?php endif; ?>
</div>