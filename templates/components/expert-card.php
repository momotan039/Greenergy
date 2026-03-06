<?php

/**
 * Expert card component — used in All Experts block and single expert context.
 * All fields always visible; missing data shows locked/placeholder state.
 */
if (! defined('ABSPATH')) {
    exit;
}

$post_id = isset($args['post_id']) ? absint($args['post_id']) : get_the_ID();
if (! $post_id) return;

$post = get_post($post_id);
if (! $post || $post->post_type !== 'experts') return;

$name   = get_the_title($post_id);
$avatar = get_the_post_thumbnail_url($post_id, 'thumbnail');
if (empty($avatar)) {
    $avatar = 'https://placehold.co/72x72';
}

$role = get_field('expert_role', $post_id);
if ($role === null || $role === false) {
    $role = get_the_excerpt($post_id) ?: '';
}
$role = is_string($role) ? trim($role) : '';

$quote = get_field('expert_quote', $post_id);
$quote = is_string($quote) ? trim($quote) : '';

$work_for = trim((string) get_field('expert_work_for', $post_id));
if ($work_for === '') {
    $linked_org = get_field('expert_linked_organization', $post_id);
    if ($linked_org && is_object($linked_org) && isset($linked_org->post_title)) {
        $work_for = $linked_org->post_title;
    }
}
if ($work_for === '') {
    $linked_company = get_field('expert_linked_company', $post_id);
    if ($linked_company && is_object($linked_company) && isset($linked_company->post_title)) {
        $work_for = $linked_company->post_title;
    }
}

$profile_url = trim((string) get_field('expert_profile_url', $post_id));
if ($profile_url === '') {
    $profile_url = get_permalink($post_id) ?: '#';
}

$phone     = trim((string) get_post_meta($post_id, 'expert_phone',     true));
$website   = trim((string) get_post_meta($post_id, 'expert_website',   true));
$twitter   = trim((string) get_post_meta($post_id, 'expert_twitter',   true));
$instagram = trim((string) get_post_meta($post_id, 'expert_instagram', true));
$facebook  = trim((string) get_post_meta($post_id, 'expert_facebook',  true));
$linkedin  = trim((string) get_post_meta($post_id, 'expert_linkedin',  true));

$any_social = $website || $twitter || $instagram || $facebook || $linkedin;
?>
<article class="rounded-2xl card-hover relative  p-4 max-md:p-3 shadow-lg outline outline-1 outline-green-200 flex flex-col items-center gap-3 relative h-[340px]">
    <a href="<?php echo esc_url($profile_url); ?>" class="absolute inset-0 z-10 w-full h-full"></a>
    <!-- Avatar -->
    <img
        src="<?php echo esc_url($avatar); ?>"
        alt="<?php echo esc_attr($name); ?>"
        class="w-16 h-16 rounded-2xl outline outline-4 outline-sky-500 absolute top-[-20px] mx-auto object-cover">

    <!-- Name + Role -->
    <div class="text-center mt-8 shrink-0 w-full">
        <h3 class="text-xl font-medium text-neutral-950 max-md:text-lg line-clamp-1">
            <?php echo esc_html($name); ?>
        </h3>

        <?php if ($role !== '') : ?>
            <p class="text-stone-500 text-sm line-clamp-1 mt-0.5"><?php echo esc_html($role); ?></p>
        <?php else : ?>
            <p class="flex items-center justify-center gap-1.5 mt-0.5 select-none">
                <i class="fa-solid fa-lock text-stone-300 text-[9px]"></i>
                <span class="inline-block w-28 h-2.5 rounded-full bg-stone-100 border border-stone-200"></span>
            </p>
        <?php endif; ?>
    </div>

    <!-- Quote — always 2 lines tall -->
    <div class="shrink-0 w-full flex flex-col items-center justify-center gap-1.5" style="min-height:2.6rem">
        <?php if ($quote !== '') : ?>
            <blockquote class="text-center text-xs text-stone-500 line-clamp-2 px-1 leading-relaxed w-full">
                «<?php echo esc_html($quote); ?>»
            </blockquote>
        <?php else : ?>
            <div class="flex flex-col items-center gap-1.5 w-full px-2 select-none">
                <span class="flex items-center gap-1.5 w-full justify-center">
                    <i class="fa-solid fa-lock text-stone-300 text-[9px] shrink-0"></i>
                    <span class="block h-2.5 rounded-full bg-stone-100 border border-stone-200 w-4/5"></span>
                </span>
                <span class="block h-2.5 rounded-full bg-stone-100 border border-stone-200 w-3/5"></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Work-for — always rendered -->
    <div class="shrink-0 flex gap-2 text-sm h-6 items-center w-full justify-center">
        <?php if ($work_for !== '') : ?>
            <span class="text-stone-500"><?php esc_html_e('يعمل لدى :', 'greenergy'); ?></span>
            <span class="text-green-700 font-medium line-clamp-1"><?php echo esc_html($work_for); ?></span>
        <?php else : ?>
            <span class="flex items-center gap-1.5 text-stone-400 text-xs select-none">
                <i class="fa-solid fa-lock text-[9px]"></i>
                <span class="text-stone-400"><?php esc_html_e('يعمل لدى :', 'greenergy'); ?></span>
                <span class="inline-block w-20 h-2.5 rounded-full bg-stone-100 border border-stone-200"></span>
            </span>
        <?php endif; ?>
    </div>

    <!-- Divider -->
    <hr class="w-full border-stone-100 shrink-0 my-0">

    <!-- Actions -->
    <div class="flex w-full gap-4 shrink-0">
        <a href="<?php echo esc_url($profile_url); ?>"
            class="flex-1 h-9 flex items-center justify-center border rounded-lg text-sm hover:bg-stone-50 transition-colors">
            <?php esc_html_e('عرض الملف', 'greenergy'); ?>
        </a>

        <?php if ($phone !== '') : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"
                class="w-9 h-9 bg-green-700 hover:bg-green-800 rounded-lg flex items-center justify-center transition-colors"
                aria-label="<?php esc_attr_e('اتصال', 'greenergy'); ?>">
                <i class="fa-solid fa-phone text-white text-sm"></i>
            </a>
        <?php else : ?>
            <span class="w-9 h-9 bg-stone-200 rounded-lg flex items-center justify-center cursor-not-allowed relative select-none"
                title="<?php esc_attr_e('رقم غير متاح', 'greenergy'); ?>">
                <i class="fa-solid fa-phone text-stone-400 text-sm"></i>
                <i class="fa-solid fa-lock text-[8px] text-stone-400 absolute bottom-1.5 right-1.5"></i>
            </span>
        <?php endif; ?>
    </div>

    <!-- Social bar — always rendered -->
    <div class="w-full shrink-0 mt-auto px-4 py-1.5 rounded-full flex justify-between items-center
        <?php echo $any_social ? 'bg-gradient-to-b from-sky-500 to-blue-700' : 'bg-stone-200 border border-stone-200'; ?>">

        <?php
        $socials = [
            ['url' => $website,   'icon' => 'fa-solid fa-link',           'label' => 'الموقع'],
            ['url' => $twitter,   'icon' => 'fa-brands fa-x-twitter',      'label' => 'X'],
            ['url' => $instagram, 'icon' => 'fa-brands fa-instagram',      'label' => 'انستغرام'],
            ['url' => $facebook,  'icon' => 'fa-brands fa-facebook-f',     'label' => 'فيسبوك'],
            ['url' => $linkedin,  'icon' => 'fa-brands fa-linkedin-in',    'label' => 'لينكد إن'],
        ];
        foreach ($socials as $s) :
            if ($s['url'] !== '') : ?>
                <a href="<?php echo esc_url($s['url']); ?>"
                    target="_blank" rel="noopener noreferrer"
                    aria-label="<?php echo esc_attr($s['label']); ?>"
                    class="hover:scale-110 transition-transform">
                    <i class="<?php echo esc_attr($s['icon']); ?> text-white text-xs"></i>
                </a>
            <?php else : ?>
                <span class="relative inline-flex items-center justify-center select-none" aria-hidden="true">
                    <i class="<?php echo esc_attr($s['icon']); ?> text-xs <?php echo $any_social ? 'text-white opacity-30' : 'text-stone-400 opacity-50'; ?>"></i>
                    <i class="fa-solid fa-lock text-[7px] absolute -bottom-1 -right-1 <?php echo $any_social ? 'text-white opacity-40' : 'text-stone-400'; ?>"></i>
                </span>
        <?php endif;
        endforeach; ?>

    </div>

</article>