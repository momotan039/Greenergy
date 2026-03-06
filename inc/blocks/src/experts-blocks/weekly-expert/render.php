<?php

/**
 * Weekly Expert Block — render
 *
 * Source: 'db' = choose expert from DB (experts CPT); 'manual' = enter name, role, quote, etc. manually.
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attrs = wp_parse_args($attributes ?? [], [
    'source'        => 'manual',
    'selectedExpert' => [],
    'badgeText'     => __('خبير الشهر', 'greenergy'),
    'name'          => '',
    'imageId'       => 0,
    'imageUrl'      => '',
    'role'          => '',
    'quote'         => '',
    'workFor'       => '',
    'profileUrl'    => '',
    'phone'         => '',
    'website'       => '',
    'twitter'       => '',
    'instagram'     => '',
    'facebook'      => '',
    'linkedin'      => '',
]);

$source   = ($attrs['source'] === 'db') ? 'db' : 'manual';
$expert_id = 0;

if ($source === 'db' && ! empty($attrs['selectedExpert'])) {
    $sel = $attrs['selectedExpert'];
    $first = is_array($sel) ? ($sel[0] ?? null) : $sel;
    if ($first !== null) {
        if (is_array($first)) {
            $expert_id = absint($first['id'] ?? $first['ID'] ?? 0);
        } elseif (is_object($first)) {
            $expert_id = absint($first->id ?? $first->ID ?? 0);
        } else {
            $expert_id = is_numeric($first) ? absint($first) : 0;
        }
    }
}

$name        = '';
$image_url   = 'https://placehold.co/126x126';
$role        = '';
$quote       = '';
$work_for      = '';
$work_for_url  = '';
$profile_url   = '#';
$phone       = '';
$website     = '';
$twitter     = '';
$instagram   = '';
$facebook    = '';
$linkedin    = '';

if ($source === 'db' && $expert_id) {
    $post = get_post($expert_id);
    if ($post && $post->post_type === 'experts' && $post->post_status === 'publish') {
        $name        = get_the_title($expert_id);
        $thumb       = get_the_post_thumbnail_url($expert_id, 'medium');
        $image_url   = $thumb ?: 'https://placehold.co/126x126';
        $role        = function_exists('get_field') ? (string) get_field('expert_role', $expert_id) : '';
        if ($role === '' && get_the_excerpt($expert_id)) {
            $role = get_the_excerpt($expert_id);
        }
        $quote       = function_exists('get_field') ? (string) get_field('expert_quote', $expert_id) : '';
        $wf          = function_exists('greenergy_expert_work_for_display') ? greenergy_expert_work_for_display($expert_id) : ['label' => '', 'url' => ''];
        $work_for    = isset($wf['label']) ? trim((string) $wf['label']) : '';
        $work_for_url = isset($wf['url']) ? trim((string) $wf['url']) : '';
        $profile_url = function_exists('get_field') ? trim((string) get_field('expert_profile_url', $expert_id)) : '';
        if ($profile_url === '') {
            $profile_url = get_permalink($expert_id) ?: '#';
        }
        $phone    = (string) get_post_meta($expert_id, 'expert_phone', true);
        $website  = (string) get_post_meta($expert_id, 'expert_website', true);
        $twitter  = (string) get_post_meta($expert_id, 'expert_twitter', true);
        $instagram = (string) get_post_meta($expert_id, 'expert_instagram', true);
        $facebook  = (string) get_post_meta($expert_id, 'expert_facebook', true);
        $linkedin  = (string) get_post_meta($expert_id, 'expert_linkedin', true);
    }
} else {
    $name        = (string) $attrs['name'];
    $role        = (string) $attrs['role'];
    $quote       = (string) $attrs['quote'];
    $work_for    = (string) $attrs['workFor'];
    $profile_url = esc_url_raw($attrs['profileUrl']) ?: '#';
    $phone       = (string) $attrs['phone'];
    $website     = (string) $attrs['website'];
    $twitter     = (string) $attrs['twitter'];
    $instagram   = (string) $attrs['instagram'];
    $facebook    = (string) $attrs['facebook'];
    $linkedin    = (string) $attrs['linkedin'];
    if (! empty($attrs['imageUrl'])) {
        $image_url = esc_url_raw($attrs['imageUrl']);
    } elseif (! empty($attrs['imageId'])) {
        $image_url = wp_get_attachment_image_url((int) $attrs['imageId'], 'medium') ?: 'https://placehold.co/126x126';
    }
}

$badge_text = (string) $attrs['badgeText'];
$has_content = $name !== '' || $role !== '' || $quote !== '';
$has_social = $website !== '' || $twitter !== '' || $instagram !== '' || $facebook !== '' || $linkedin !== '';
?>

<article class="weekly-expert-block hover:scale-105 transition-all duration-300 md:w-[652px] relative mx-auto p-6 bg-white rounded-[44px] shadow-[0px_4px_13.4px_0px_rgba(247,188,6,0.60)] outline outline-1 outline-yellow-500 flex flex-col items-center gap-3" dir="rtl">
    <div class="self-stretch flex flex-col justify-start items-center gap-3">
        <img class="w-32 h-32 absolute -top-8 mx-auto rounded-[32px] outline outline-4 outline-yellow-500 object-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($name ?: 'Expert'); ?>" />

        <div class="self-stretch flex justify-between items-start mt-20">
            <div class="flex flex-col justify-center gap-3">
                <h2 class="text-neutral-950 text-xl font-medium"><?php echo esc_html($name ?: '—'); ?></h2>
                <?php if ($role !== '') : ?>
                    <p class="text-stone-500 text-base font-normal"><?php echo esc_html($role); ?></p>
                <?php endif; ?>
            </div>
            <div class="h-9 px-8 bg-gradient-to-b from-yellow-500 to-yellow-600 rounded-lg flex justify-center items-center">
                <span class="text-white text-base font-normal"><?php echo esc_html($badge_text); ?></span>
            </div>
        </div>

        <?php if ($has_content) : ?>
            <div class="self-stretch flex flex-col items-center gap-4">
                <div class="self-stretch flex flex-col items-center gap-3">
                    <?php if ($quote !== '') : ?>
                        <blockquote class="self-stretch break-words overflow-hidden text-right text-stone-500 text-base font-normal max-md:text-sm">«<?php echo esc_html($quote); ?>»</blockquote>
                    <?php endif; ?>
                    <?php if ($work_for !== '') : ?>
                        <div class="self-stretch flex items-center gap-3">
                            <span class="text-stone-500 text-base font-normal"><?php esc_html_e('يعمل لدى :', 'greenergy'); ?></span>
                            <a href="<?php echo esc_url($work_for_url); ?>" class="text-green-700 text-sm font-medium"><?php echo esc_html($work_for); ?></a>
                        </div>
                    <?php endif; ?>

                    <div class="self-stretch flex items-start gap-3">
                        <a href="<?php echo esc_url($profile_url); ?>" class="flex-1">
                            <div class="flex-1 h-9 p-2.5 rounded-lg outline outline-1 outline-gray-700 hover:outline-green-700 flex justify-center items-center">
                                <span class="text-neutral-800 text-sm font-normal leading-5"><?php esc_html_e('عرض الملف', 'greenergy'); ?></span>
                            </div>
                        </a>
                        <?php if ($phone !== '') : ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="flex-1 h-9 p-2.5 bg-green-700 rounded-lg flex justify-center items-center gap-2.5">
                                <i class="text-lg rounded-md fa-solid fa-phone text-white"></i>
                                <span class="text-white text-sm font-normal leading-5"><?php esc_html_e('تواصل مع الخبير', 'greenergy'); ?></span>
                            </a>
                        <?php else : ?>
                            <div class="flex-1 h-9 p-2.5 bg-gray-300 rounded-lg flex justify-center items-center gap-2.5 cursor-not-allowed">
                                <i class="text-lg rounded-md fa-solid fa-phone text-white"></i>
                                <span class="text-white text-sm font-normal leading-5"><?php esc_html_e('تواصل مع الخبير', 'greenergy'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($has_social) : ?>
                        <nav class="max-md:w-full w-96 px-2 py-2 bg-gradient-to-b from-sky-500 to-blue-700 rounded-[32px] flex justify-evenly items-center">
                            <?php if ($website !== '') : ?><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('الموقع', 'greenergy'); ?>"><i class="text-xl rounded-md fa-solid fa-link text-white"></i></a><?php endif; ?>
                            <?php if ($linkedin !== '') : ?><a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="text-xl rounded-md fa-brands fa-linkedin text-white"></i></a><?php endif; ?>
                            <?php if ($facebook !== '') : ?><a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="text-xl rounded-md fa-brands fa-facebook text-white"></i></a><?php endif; ?>
                            <?php if ($instagram !== '') : ?><a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="text-xl rounded-md fa-brands fa-instagram text-white"></i></a><?php endif; ?>
                            <?php if ($twitter !== '') : ?><a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="text-xl rounded-md fa-brands fa-x-twitter text-white"></i></a><?php endif; ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        <?php else : ?>
            <p class="text-stone-500 text-sm py-4"><?php esc_html_e('اختر خبيراً من القاعدة أو أضف البيانات يدوياً من إعدادات الكتلة.', 'greenergy'); ?></p>
        <?php endif; ?>
    </div>
</article>