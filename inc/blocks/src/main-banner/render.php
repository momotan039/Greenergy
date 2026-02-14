<?php

/**
 * Main Banner Block Template
 *
 * @package Greenergy
 */

$attributes = wp_parse_args($attributes ?? [], [
    'title'             => 'اكتشف مستقبل الطاقة المتجددة',
    'subtitle'          => 'الاخبار',
    'backgroundImage'   => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
    'backgroundImageId' => 0,
    'bannerType'        => 'image',
    'videoUrl'          => '',
    'showTitle'         => true,
    'showSubtitle'      => true,
]);

$title         = $attributes['title'];
$subtitle      = $attributes['subtitle'];
$banner_type   = $attributes['bannerType'];
$show_title    = $attributes['showTitle'];
$show_subtitle = $attributes['showSubtitle'];
$bg_image      = $attributes['backgroundImage'];
$bg_video      = $attributes['videoUrl'];

// Check for background image from ID
if (!empty($attributes['backgroundImageId'])) {
    $img_url = wp_get_attachment_image_url($attributes['backgroundImageId'], 'full');
    if ($img_url) {
        $bg_image = $img_url;
    }
}

// Dynamic Logic for Archives/Single/Special Pages
if (is_singular() || is_archive() || is_search()) {
    // If used on a page and title is default, try to get current title
    if ($title === 'اكتشف مستقبل الطاقة المتجددة') {
        if (is_singular()) {
            $title = get_the_title();
        } elseif (is_archive()) {
            $title = get_the_archive_title();
        } elseif (is_search()) {
            $title = sprintf(__('نتائج البحث عن: %s', 'greenergy'), get_search_query());
        }
    }

    // Fallback to post thumbnail if no custom image set
    if ($banner_type === 'image' && empty($bg_image) && has_post_thumbnail()) {
        $bg_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }
}

// Global Theme Settings override (optional, keep for backward compatibility if needed)
$news_settings = get_option('greenergy_news_settings', []);
if (!empty($news_settings) && (is_singular('news') || is_post_type_archive('news') || is_page('news'))) {
    // Only override if specified in global settings
    if (!empty($news_settings['bannerTitle'])) $title = $news_settings['bannerTitle'];
    if (!empty($news_settings['bannerSubtitle'])) $subtitle = $news_settings['bannerSubtitle'];
    if (!empty($news_settings['bannerImage'])) $bg_image = $news_settings['bannerImage'];
    if (isset($news_settings['bannerType']) && $news_settings['bannerType'] === 'video') {
        $banner_type = 'video';
        $bg_video = $news_settings['bannerVideo'] ?? '';
    }
}

$wrapper_style = '';
if ($banner_type === 'image' && !empty($bg_image)) {
    $wrapper_style = 'background-image: url(' . esc_url($bg_image) . ');';
} else {
    // For video or if no image, ensure relative positioning
    $wrapper_style = 'position: relative; overflow: hidden;';
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'bg-cover bg-center rounded-3xl inline-flex justify-between items-center relative overflow-hidden min-h-[500px] max-md:min-h-[324px]',
    'style' => $wrapper_style,
]);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ($banner_type === 'video' && !empty($bg_video)) : ?>
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover">
            <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
        </video>
        <!-- Overlay for legibility -->
        <div class="absolute top-0 left-0 w-full h-full bg-black/40"></div>
    <?php elseif ($banner_type === 'video' && !empty($bg_image)) : ?>
        <!-- Fallback image if video is selected but URL is empty -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url(<?php echo esc_url($bg_image); ?>);"></div>
        <div class="absolute inset-0 bg-black/30"></div>
    <?php endif; ?>
    <div class="relative z-10 flex justify-center overflow-hidden pt-28 px-4 md:px-28 w-full">
        <div class="flex-1 h-44 inline-flex flex-col justify-start items-center gap-4">
            <?php if ($show_subtitle) : ?>
                <div class="w-64 h-8 p-2.5 bg-white/20 rounded-[44px] backdrop-blur-[2px] inline-flex justify-center items-center gap-2.5">
                    <div class="h-7 text-center justify-start text-white text-sm font-normal leading-5">
                        <?php echo esc_html($subtitle); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($show_title) : ?>
                <div class="flex flex-col justify-center items-center gap-3">
                    <div class="text-center justify-start text-white text-xl font-bold lg:text-5xl">
                        <?php echo esc_html($title); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>