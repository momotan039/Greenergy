<?php

/**
 * Main Banner Block Template
 *
 * @package Greenergy
 */

$attributes = isset($attributes) ? $attributes : [];
$title      = isset($attributes['title']) ? $attributes['title'] : 'اكتشف مستقبل الطاقة المتجددة';
$subtitle   = isset($attributes['subtitle']) ? $attributes['subtitle'] : 'الاخبار';
$bg_image   = isset($attributes['backgroundImage']) ? $attributes['backgroundImage'] : 'https://images.unsplash.com/photo-1506744038136-46273834b3fb';

// Custom Logic for Single News or News Archive
if (is_singular('news') || is_post_type_archive('news') || is_page('news')) {
    // Get Global Theme Settings
    $news_settings = get_option('greenergy_news_settings', []);

    // Map settings to variables with defaults
    $banner_type     = isset($news_settings['bannerType']) ? $news_settings['bannerType'] : 'image';
    $global_image    = isset($news_settings['bannerImage']) ? $news_settings['bannerImage'] : '';
    $global_video    = isset($news_settings['bannerVideo']) ? $news_settings['bannerVideo'] : '';
    $global_title    = isset($news_settings['bannerTitle']) ? $news_settings['bannerTitle'] : '';
    $global_subtitle = isset($news_settings['bannerSubtitle']) ? $news_settings['bannerSubtitle'] : 'الأخبار';

    // Set Subtitle
    $subtitle = !empty($global_subtitle) ? $global_subtitle : $subtitle;

    $title = $global_title;

    // Visibility Settings
    $show_title    = isset($news_settings['showBannerTitle']) ? $news_settings['showBannerTitle'] : true;
    $show_subtitle = isset($news_settings['showBannerSubtitle']) ? $news_settings['showBannerSubtitle'] : true;

    // Set Background based on Banner Type
    if ($banner_type === 'video' && !empty($global_video)) {
        $bg_video = $global_video;
        $bg_image = ''; // Reset image if video is selected
    } elseif (!empty($global_image)) {
        $bg_image = $global_image;
    } else {
        // Fallback to Featured Image if available and no global image set
        if (has_post_thumbnail()) {
            $bg_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        }
    }
} else {
    // Default visibility for other pages (or if we were using attributes directly)
    $show_title = true;
    $show_subtitle = true;
}

$wrapper_style = '';
if (!empty($bg_image)) {
    $wrapper_style = 'background-image: url(' . esc_url($bg_image) . ');';
} else {
    // If video, ensuring relative positioning is crucial
    $wrapper_style = 'position: relative; overflow: hidden;';
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'bg-cover bg-center rounded-3xl inline-flex justify-between items-center relative overflow-hidden min-h-[500px] max-md:min-h-[324px]', // Added min-height to ensure visibility
    'style' => $wrapper_style,
]);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if (isset($bg_video) && !empty($bg_video)) : ?>
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover -z-10">
            <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
        </video>
        <!-- Overlay for legibility if needed -->
        <div class="absolute top-0 left-0 w-full h-full bg-black/30 -z-10"></div>
    <?php endif; ?>
    <div class="flex justify-center overflow-hidden pt-28 px-28 w-full">
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