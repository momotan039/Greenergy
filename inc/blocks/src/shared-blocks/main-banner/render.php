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
    'stats'             => [],
]);

$title          = $attributes['title'];
$subtitle       = $attributes['subtitle'];
$banner_type    = $attributes['bannerType'];
$show_title     = $attributes['showTitle'];
$show_subtitle  = $attributes['showSubtitle'];
$bg_image       = $attributes['backgroundImage'];
$bg_video       = $attributes['videoUrl'];
$show_desc      = $attributes['showDesc'];
$desc           = $attributes['desc'];
$show_stats     = $attributes['showStats'] || $attributes['isJobsPage'];
$stats          = $attributes['stats'];
$overlay_opacity = ($attributes['overlayOpacity'] ?? 40) / 100;
$overlay_style  = "background-color: rgba(0, 0, 0, {$overlay_opacity});";

if ($show_stats) {
    if (empty($stats) && function_exists('greenergy_main_banner_default_stats')) {
        $stats = greenergy_main_banner_default_stats(null);
    }
    if (! empty($stats) && function_exists('greenergy_get_dynamic_stat_value')) {
        foreach ($stats as $key => $stat) {
            if (($stat['mode'] ?? '') === 'dynamic') {
                $source = $stat['dataSource'] ?? '';
                $stats[$key]['value'] = '+' . greenergy_get_dynamic_stat_value($source);
            }
        }
    }
}

// Check for background image from ID
if (!empty($attributes['backgroundImageId'])) {
    $img_url = wp_get_attachment_image_url($attributes['backgroundImageId'], 'full');
    if ($img_url) {
        $bg_image = $img_url;
    }
}

// Company banner: override from ACF when on single company
if (is_singular('companies') && function_exists('get_field')) {
    $company_id = get_queried_object_id();
    $acf_title    = get_field('company_banner_title', $company_id);
    $acf_subtitle = get_field('company_banner_subtitle', $company_id);
    $acf_image    = get_field('company_banner_image', $company_id);
    if ($acf_title !== '' && $acf_title !== null) {
        $title = $acf_title;
    } else {
        $title = get_the_title($company_id);
    }
    if ($acf_subtitle !== '' && $acf_subtitle !== null) {
        $subtitle = $acf_subtitle;
    } else {
        $subtitle = __('الشركة', 'greenergy');
    }
    if (! empty($acf_image)) {
        $bg_image = is_array($acf_image) ? ($acf_image['url'] ?? '') : $acf_image;
        if (empty($bg_image) && is_array($acf_image) && ! empty($acf_image['ID'])) {
            $bg_image = wp_get_attachment_image_url($acf_image['ID'], 'full');
        }
    } else {
        $fallback = get_the_post_thumbnail_url($company_id, 'full');
        if ($fallback) {
            $bg_image = $fallback;
        }
    }
}

$wrapper_style = '';
if ($banner_type === 'image' && !empty($bg_image)) {
    $wrapper_style = 'background-image: url(' . esc_url($bg_image) . ');';
} else {
    $wrapper_style = 'position: relative; overflow: hidden;';
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'overflow-hidden mt-0 bg-cover bg-center rounded-3xl inline-flex justify-between items-center relative max-md:min-h-[180px] md:h-[297px] md:p-0 py-2',
    'style' => $wrapper_style,
]);
?>
<section <?php echo $wrapper_attributes; ?>>

    <?php if ($banner_type === 'video' && !empty($bg_video)) : ?>

        <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
            <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
        </video>

    <?php elseif (!empty($bg_image)) : ?>

        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image:url('<?php echo esc_url($bg_image); ?>')"></div>

    <?php endif; ?>

    <?php if (!empty($overlay_style)) : ?>
        <div class="absolute inset-0 rounded-3xl" style="<?php echo esc_attr($overlay_style); ?>"></div>
    <?php endif; ?>


    <div class="relative z-10 flex justify-center w-full px-4 md:px-28">

        <div class="flex flex-col items-center gap-6 text-center">

            <?php if ($show_subtitle && !empty($subtitle)) : ?>
                <span class="px-4 py-2 text-sm text-white rounded-full bg-white/20 backdrop-blur">
                    <?php echo esc_html($subtitle); ?>
                </span>
            <?php endif; ?>


            <header class="space-y-3">

                <?php if ($show_title && !empty($title)) : ?>
                    <h1 class="text-xl font-bold text-white lg:text-5xl">
                        <?php echo esc_html($title); ?>
                    </h1>
                <?php endif; ?>

                <?php if ($show_desc && !empty($desc)) : ?>
                    <p class="text-base font-medium text-white">
                        <?php echo esc_html($desc); ?>
                    </p>
                <?php endif; ?>

            </header>


            <?php if ($show_stats && !empty($stats)) : ?>

                <div class="flex items-center gap-16 px-4 py-2 rounded-3xl bg-lime-100 max-md:gap-4">

                    <?php foreach ($stats as $stat) :

                        $val = $stat['value'] ?? '0';
                        $numeric_val = (int) preg_replace('/[^0-9]/', '', $val);
                        $icon_type = $stat['iconType'] ?? 'platform';
                    ?>

                        <div class="flex flex-col items-center gap-2 md:w-28">

                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-green-700 via-lime-600 to-lime-300">

                                <?php if ($icon_type === 'font-awesome' && !empty($stat['faIcon'])) : ?>

                                    <i class="<?php echo esc_attr($stat['faIcon']); ?> text-white"
                                        style="font-size:<?php echo (int)($stat['faIconSize'] ?? 12); ?>px"></i>

                                <?php elseif (!empty($stat['icon'])) : ?>

                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/vuesax/outline/' . $stat['icon']); ?>"
                                        alt="<?php echo esc_attr($stat['label']); ?>">

                                <?php endif; ?>

                            </div>


                            <div class="text-center">

                                <div class="flex items-center justify-center text-xl font-bold text-green-700">

                                    <span>+</span>

                                    <span
                                        class="js-counter"
                                        data-target="<?php echo esc_attr($numeric_val); ?>">
                                        0
                                    </span>

                                </div>

                                <div class="text-sm text-stone-500 whitespace-nowrap">
                                    <?php echo esc_html($stat['label']); ?>
                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>