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
    'showDesc'          => false,
    'desc'              => '',
    'showStats'         => false,
    'overlayOpacity'    => 40,
    'isJobsPage'        => false,
    'stats'             => []
]);

$title         = $attributes['title'];
$subtitle      = $attributes['subtitle'];
$banner_type   = $attributes['bannerType'];
$show_title    = $attributes['showTitle'];
$show_subtitle = $attributes['showSubtitle'];
$bg_image      = $attributes['backgroundImage'];
$bg_video      = $attributes['videoUrl'];
$show_desc     = $attributes['showDesc'];
$desc          = $attributes['desc'];
$show_stats    = $attributes['showStats'] || $attributes['isJobsPage'];
$stats         = $attributes['stats'];
$is_jobs_page  = $attributes['isJobsPage'];
$overlay_opacity = ($attributes['overlayOpacity'] ?? 40) / 100;
$overlay_style = "background-color: rgba(0, 0, 0, {$overlay_opacity});";

// Resolve dynamic values for each stat if needed
if ($show_stats && !empty($stats)) {
    foreach ($stats as $key => $stat) {
        $mode = $stat['mode'] ?? 'manual';
        if ($mode === 'dynamic') {
            $source = $stat['dataSource'] ?? '';
            $dynamic_value = greenergy_get_dynamic_stat_value($source);
            $stats[$key]['value'] = '+' . $dynamic_value;
        }
    }
}

// Fallback default stats if empty but showing stats (matching old jobs page defaults)
if ($show_stats && empty($stats)) {
    $stats = [
        [
            'value'      => '250',
            'label'      => __('وظيفة متاحة', 'greenergy'),
            'icon'       => 'clipboard-text.svg',
            'iconType'   => 'platform',
            'mode'       => 'dynamic',
            'dataSource' => 'jobs_count'
        ],
        [
            'value'      => '300',
            'label'      => __('فرص ذهبية', 'greenergy'),
            'icon'       => 'profile-2user.svg',
            'iconType'   => 'platform',
            'mode'       => 'dynamic',
            'dataSource' => 'gold_jobs_count'
        ],
        [
            'value'      => '+150',
            'label'      => __('شريك موثوق', 'greenergy'),
            'icon'       => 'medal.svg',
            'iconType'   => 'platform',
            'mode'       => 'manual',
            'dataSource' => 'news_count'
        ]
    ];
}

// Check for background image from ID
if (!empty($attributes['backgroundImageId'])) {
    $img_url = wp_get_attachment_image_url($attributes['backgroundImageId'], 'full');
    if ($img_url) {
        $bg_image = $img_url;
    }
}

// Global Theme Settings override
$news_settings = get_option('greenergy_news_settings', []);
if (!empty($news_settings) && (is_singular('news') || is_post_type_archive('news') || is_page('news'))) {
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
    $wrapper_style = 'position: relative; overflow: hidden;';
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'mt-0 bg-cover bg-center rounded-3xl inline-flex justify-between items-center relative h-[297px] max-md:min-h-[180px]',
    'style' => $wrapper_style,
]);
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ($banner_type === 'image') : ?>
        <div class="absolute rounded-3xl inset-0" style="<?php echo $overlay_style; ?>"></div>
    <?php elseif ($banner_type === 'video' && !empty($bg_video)) : ?>
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover">
            <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
        </video>
        <div class="absolute top-0 left-0 w-full h-full" style="<?php echo $overlay_style; ?>"></div>
    <?php elseif ($banner_type === 'video' && !empty($bg_image)) : ?>
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url(<?php echo esc_url($bg_image); ?>);"></div>
        <div class="absolute inset-0" style="<?php echo $overlay_style; ?>"></div>
    <?php endif; ?>

    <div class="relative z-10 flex justify-center px-4 md:px-28 w-full">
        <div class="flex-1 inline-flex flex-col justify-start items-center gap-6">

            <?php if ($show_subtitle && !empty($subtitle)) : ?>
                <div class="w-64 h-8 p-2.5 bg-white/20 rounded-[44px] backdrop-blur-[2px] inline-flex justify-center items-center gap-2.5">
                    <div class="h-7 text-center justify-start text-white text-sm font-normal leading-5">
                        <?php echo esc_html($subtitle); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-col justify-center items-center gap-3">
                <?php if ($show_title && !empty($title)) : ?>
                    <div class="text-center justify-start text-white text-xl font-bold lg:text-5xl">
                        <?php echo esc_html($title); ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_desc && !empty($desc)) : ?>
                    <div class="text-center justify-start text-white text-base font-medium leading-5">
                        <?php echo esc_html($desc); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($show_stats && !empty($stats)) : ?>
                <div class="px-4 max-sm:px-0 py-2 bg-lime-100 rounded-3xl inline-flex justify-center items-center gap-16 max-md:gap-4">
                    <?php foreach ($stats as $stat) : ?>
                        <div class="md:w-28 rounded-xl inline-flex flex-col justify-center items-center gap-1">
                            <?php if (($stat['iconType'] ?? 'platform') === 'font-awesome' && !empty($stat['faIcon'])) : ?>
                                <div class="w-8 h-8 bg-gradient-to-br from-green-700 via-lime-600 to-lime-300 rounded-[80px] flex flex-col justify-center items-center gap-2">
                                    <i class="<?php echo esc_attr($stat['faIcon']); ?> text-white" style="font-size: <?php echo (int)($stat['faIconSize'] ?? 12); ?>px;"></i>
                                </div>
                            <?php elseif (!empty($stat['icon'])) : ?>
                                <div class="w-8 h-8 bg-gradient-to-br from-green-700 via-lime-600 to-lime-300 rounded-[80px] flex flex-col justify-center items-center gap-2">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/<?php echo esc_attr($stat['icon']); ?>"
                                        alt="<?php echo esc_attr($stat['label']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="flex flex-col justify-center items-center gap-1">
                                <div class="text-center text-green-700 text-xl font-bold leading-6 flex items-center">
                                    <?php
                                    $val = $stat['value'] ?? '0';
                                    $numeric_val = (int) preg_replace('/[^0-9]/', '', $val);
                                    ?>
                                    <span>+</span>
                                    <span class="js-counter" data-target="<?php echo esc_attr($numeric_val); ?>">0</span>
                                </div>
                                <div class="w-24 text-center text-stone-500 text-sm font-normal leading-6"><?php echo esc_html($stat['label']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>