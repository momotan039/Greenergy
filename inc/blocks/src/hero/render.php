<?php

/**
 * Render for Hero Block
 */
$attributes = isset($attributes) ? $attributes : [];

// Default attributes if not provided
$badgeText = $attributes['badgeText'] ?? 'مستقبل الطاقة النظيفة';
$headlineHighlight = $attributes['headlineHighlight'] ?? 'اكتشف';
$headlineMain = $attributes['headlineMain'] ?? 'عالم الطاقة المتجددة من دليل اللاعبين في السوق';
$description = $attributes['description'] ?? 'اكتشف دليل الطاقة المتجددة الشامل الذي غيّر مستقبل الملايين.';
$ctaText = $attributes['ctaText'] ?? 'المستقبل يبدأ من هنا';
$cta_url = $attributes['ctaUrl'] ?? '#';
$view_mode = $attributes['viewMode'] ?? 'static';
$stats = $attributes['stats'] ?? [
    ['icon' => 'fa-user-friends', 'value' => '1000+', 'label' => 'خبير ومختص'],
    ['icon' => 'fa-chart-line', 'value' => '500+', 'label' => 'شركة'],
    ['icon' => 'fa-globe', 'value' => '50+', 'label' => 'منظمة'],
    ['icon' => 'fa-building', 'value' => '11M+', 'label' => 'محطة و مشروع']
];
$featured_stat = $attributes['featuredStat'] ?? [
    'value' => '500+',
    'label' => 'زيارة و مهتمين'
];

$bg_image_url = $attributes['imageUrl'] ?? '';
if (! empty($attributes['imageId'])) {
    $lib_url = wp_get_attachment_image_url($attributes['imageId'], 'full');
    if ($lib_url) {
        $bg_image_url = $lib_url;
    }
}

// Fallback image if none specified
if (empty($bg_image_url)) {
    $bg_image_url = get_template_directory_uri() . '/assets/images/hero/image.png';
}
?>
<!-- Hero section -->
<div class="container w-full m-auto">
    <div class="flex flex-col h-auto items-center justify-between lg:flex-row lg:pb-20 w-full gap-4">
        <!-- Text Content -->
        <div class="flex-1 flex flex-col justify-start items-center lg:items-start gap-4 w-full">
            <!-- Badge -->
            <div class="px-12 h-8 p-2.5 bg-[#2FF7337A] rounded-[44px] backdrop-blur-[2px] inline-flex justify-center items-center gap-2.5" data-aos="fade-down" data-aos-delay="100">
                <div class="text-center justify-start text-neutral-950 text-sm font-normal leading-5">
                    <?php echo esc_html($badgeText); ?>
                </div>
            </div>

            <!-- Headlines -->
            <div class="self-stretch flex flex-col justify-start items-center lg:items-start gap-3">
                <div class="w-full lg:w-[610px] text-center lg:text-right justify-start" data-aos="fade-up" data-aos-delay="200">
                    <span class="text-green-700 text-3xl lg:text-4xl font-bold leading-[40px] lg:leading-[48.80px]"><?php echo esc_html($headlineHighlight); ?></span>
                    <span class="text-neutral-950 text-3xl lg:text-4xl font-bold leading-[40px] lg:leading-[48.80px]">
                        <?php echo esc_html($headlineMain); ?>
                    </span>
                </div>
                <div class="w-full lg:w-96 text-center lg:text-right justify-start text-green-700 text-base lg:text-lg font-medium leading-6" data-aos="fade-up" data-aos-delay="300">
                    <?php echo esc_html($description); ?>
                </div>

                <!-- CTA -->
                <a href="<?php echo esc_url($cta_url); ?>"
                    class="group h-12 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-3xl inline-flex justify-center items-center gap-3 overflow-hidden transition-all duration-500 shadow-lg hover:shadow-green-500/20"
                    data-aos="zoom-in" data-aos-delay="400">

                    <div class="overflow-hidden whitespace-nowrap animate-typing">
                        <div class="pl-2 text-white text-sm  border-l-2 border-transparent animate-blink">
                            <?php echo esc_html($ctaText); ?>
                        </div>
                    </div>

                    <svg class="flex-shrink-0 w-8 h-8 bg-white rounded-full animate-icon-zoom" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/arrow-up.svg"></use>
                    </svg>
                </a>

                <!-- Stats Row -->
                <div class="w-full pb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="py-2 max-sm:py-4 max-sm:gap-[.3rem] bg-lime-100 rounded-3xl flex flex-nowrap justify-center lg:justify-start items-center gap-2 overflow-x-auto">
                        <?php
                        // 1. First Stat
                        $s1 = $stats[0] ?? ['value' => '1000+', 'label' => 'خبير ومختص'];
                        ?>
                        <div class="flex-shrink-0">
                            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/user-octagon.svg"></use>
                                </svg>
                                <div class="flex flex-col justify-start items-center gap-2.5 max-sm:gap-1">
                                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                                        <span class="js-counter" data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $s1['value'])); ?>">0</span><?php echo esc_html(preg_replace('/[0-9]/', '', $s1['value'])); ?>
                                    </div>
                                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                                        <?php echo esc_html($s1['label']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // 2. Second Stat
                        $s2 = $stats[1] ?? ['value' => '500+', 'label' => 'شركة'];
                        ?>
                        <div class="flex-shrink-0">
                            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/status-up.svg"></use>
                                </svg>
                                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                                        <span class="js-counter" data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $s2['value'])); ?>">0</span><?php echo esc_html(preg_replace('/[0-9]/', '', $s2['value'])); ?>
                                    </div>
                                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                                        <?php echo esc_html($s2['label']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Featured Stat (The blue one) -->
                        <div class="flex-shrink-0">
                            <div class="w-32 h-28 max-sm:w-16 max-sm:h-26 gap-2 bg-gradient-to-b from-sky-500 to-blue-700 rounded-[32px] max-sm:rounded-2xl outline outline-[11px] max-sm:outline-8 outline-stone-50 inline-flex flex-col justify-center items-center hover:scale-110 transition-transform duration-300 shadow-xl">
                                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/eye.svg"></use>
                                </svg>
                                <div class="flex flex-col justify-start items-center gap-2 max-sm:gap-1">
                                    <div class="text-center justify-start text-white text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                                        <span class="js-counter" data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $featured_stat['value'])); ?>">0</span><?php echo esc_html(preg_replace('/[0-9]/', '', $featured_stat['value'])); ?>
                                    </div>
                                    <div class="w-28 max-sm:w-auto text-center text-white text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                                        <?php echo esc_html($featured_stat['label']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // 4. Fourth Stat
                        $s3 = $stats[2] ?? ['value' => '50+', 'label' => 'منظمة'];
                        ?>
                        <div class="flex-shrink-0">
                            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/global.svg"></use>
                                </svg>
                                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                                        <span class="js-counter" data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $s3['value'])); ?>">0</span><?php echo esc_html(preg_replace('/[0-9]/', '', $s3['value'])); ?>
                                    </div>
                                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                                        <?php echo esc_html($s3['label']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // 5. Fifth Stat
                        $s4 = $stats[3] ?? ['value' => '11M+', 'label' => 'محطة و مشروع'];
                        ?>
                        <div class="flex-shrink-0">
                            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/buliding.svg"></use>
                                </svg>
                                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                                        <span class="js-counter" data-target="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $s4['value'])); ?>">0</span><?php echo esc_html(preg_replace('/[0-9]/', '', $s4['value'])); ?>
                                    </div>
                                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight break-words">
                                        <?php echo esc_html($s4['label']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Image (Desktop Only) -->
        <div class="bg-center bg-cover bg-no-repeat flex-1 floating-animation gap-2.5 max-lg:hidden min-h-[450px] py-6 px-2 rounded-3xl"
            style="background-image: url('<?php echo esc_url($bg_image_url); ?>');"
            data-aos="zoom-in" data-aos-duration="1500">
        </div>
    </div>
</div>

<style>
    @keyframes floating {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-15px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .floating-animation {
        animation: floating 4s ease-in-out infinite;
    }

    @keyframes icon-zoom {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.15);
        }
    }

    .animate-icon-zoom {
        animation: icon-zoom 1s ease-in-out infinite;
        transform-origin: center;
    }

    @keyframes typing {
        from {
            max-width: 0;
        }

        to {
            max-width: 200px;
        }
    }

    .animate-typing {
        animation: typing 5s steps(40, end) forwards;
    }

    @keyframes blink {

        0%,
        100% {
            border-color: transparent;
        }

        50% {
            border-color: white;
        }
    }

    .animate-blink {
        animation: blink 1s step-end 0s 3 normal forwards;
        /* 5 iterations then stop */
    }
</style>