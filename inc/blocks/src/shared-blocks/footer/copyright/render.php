<?php

/**
 * Footer Copyright Block Template.
 *
 * @package Greenergy
 */

$copyright_text = !empty($attributes['copyrightText']) ? $attributes['copyrightText'] : greenergy_option('copyright_text', __('كل الحقوق محفوظة لدى © Greenergy {year}', 'greenergy'));
$copyright_text = str_replace('{year}', date('Y'), $copyright_text);

$follow_us_text = !empty($attributes['followUsText']) ? $attributes['followUsText'] : __('تابعنا', 'greenergy');
$social_links = !empty($attributes['socialLinks']) ? $attributes['socialLinks'] : [
    ['platform' => 'Facebook', 'url' => '#', 'icon' => 'facebook.svg', 'iconType' => 'system'],
    ['platform' => 'Youtube', 'url' => '#', 'icon' => 'youtube.svg', 'iconType' => 'system'],
    ['platform' => 'LinkedIn', 'url' => '#', 'icon' => 'ic16-linkedin.svg', 'iconType' => 'system'],
    ['platform' => 'Twitter', 'url' => '#', 'icon' => 'twitter_icon.svg', 'iconType' => 'system'],
];
?>

<div class="max-w-7xl mx-auto px-6 relative z-10">
    <div class="flex flex-col md:flex-row items-center justify-between pt-2 gap-6 md:gap-0 mt-8 md:mt-0 pb-8">
        <!-- Copyright -->
        <div class="text-white text-base order-last md:order-first">
            <?php echo esc_html($copyright_text); ?>
        </div>

        <!-- Social Icons -->
        <?php
        $icon_size = isset($attributes['iconSize']) ? (int)$attributes['iconSize'] : 40;
        $icon_gap  = isset($attributes['iconGap']) ? (int)$attributes['iconGap'] : 20;
        ?>
        <div class="flex items-center gap-6 order-first md:order-last flex-wrap justify-center md:justify-end">
            <span class="text-base text-white font-medium whitespace-nowrap"><?php echo esc_html($follow_us_text); ?></span>
            <div class="flex items-center flex-wrap justify-center" style="gap: <?php echo esc_attr($icon_gap); ?>px;">
                <?php
                if (!empty($social_links) && is_array($social_links)) :
                    foreach ($social_links as $social) :
                        $platform = strtolower($social['platform'] ?? '');
                        $url      = $social['url'] ?? '';
                        $icon     = $social['icon'] ?? '';
                        $icon_type = $social['iconType'] ?? 'system';

                        if (empty($url)) continue;

                        // Default SVG mapping for system icons
                        $system_icon_path = '';
                        if ($icon_type === 'system' && !empty($icon)) {
                            $path_bold = get_template_directory() . '/assets/images/vuesax/bold/' . $icon;
                            $path_root = get_template_directory() . '/assets/images/' . $icon;

                            if (file_exists($path_bold)) {
                                $system_icon_path = get_template_directory_uri() . '/assets/images/vuesax/bold/' . $icon;
                            } elseif (file_exists($path_root)) {
                                $system_icon_path = get_template_directory_uri() . '/assets/images/' . $icon;
                            }
                        }

                        // FontAwesome fallback mapping
                        $fa_icon = 'fas fa-link';
                        if (strpos($platform, 'twitter') !== false || strpos($platform, 'x') !== false) $fa_icon = 'fab fa-twitter';
                        elseif (strpos($platform, 'facebook') !== false) $fa_icon = 'fab fa-facebook-f';
                        elseif (strpos($platform, 'instagram') !== false) $fa_icon = 'fab fa-instagram';
                        elseif (strpos($platform, 'linkedin') !== false) $fa_icon = 'fab fa-linkedin-in';
                        elseif (strpos($platform, 'youtube') !== false) $fa_icon = 'fab fa-youtube';
                ?>
                        <div class="social-icon-wrapper flex-shrink-0">
                            <a href="<?php echo esc_url($url); ?>"
                                class="rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D9A520] hover:text-white transition-all duration-300 text-white hover:scale-110 shadow-lg"
                                style="width: <?php echo esc_attr($icon_size); ?>px; height: <?php echo esc_attr($icon_size); ?>px;"
                                title="<?php echo esc_attr($social['platform'] ?? ''); ?>"
                                target="_blank" rel="noopener noreferrer">
                                <?php if ($icon_type === 'system' && !empty($system_icon_path)) : ?>
                                    <img src="<?php echo esc_url($system_icon_path); ?>" alt="<?php echo esc_attr($social['platform']); ?>"
                                        style="width: <?php echo esc_attr($icon_size * 0.45); ?>px; height: <?php echo esc_attr($icon_size * 0.45); ?>px;"
                                        class="object-contain filter brightness-0 invert">
                                <?php elseif ($icon_type === 'image' && !empty($social['iconImage'])) : ?>
                                    <img src="<?php echo esc_url($social['iconImage']); ?>" alt="<?php echo esc_attr($social['platform']); ?>"
                                        style="width: <?php echo esc_attr($icon_size * 0.45); ?>px; height: <?php echo esc_attr($icon_size * 0.45); ?>px;"
                                        class="object-contain filter invert">
                                <?php else : ?>
                                    <i class="<?php echo esc_attr($fa_icon); ?>" style="font-size: <?php echo esc_attr($icon_size * 0.45); ?>px;"></i>
                                <?php endif; ?>
                            </a>
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</div>