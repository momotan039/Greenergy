<?php

/**
 * Footer Widgets Block Template.
 *
 * @package Greenergy
 */

// Use block attributes with fallback to theme options or defaults
$logo_url = !empty($attributes['logoUrl']) ? $attributes['logoUrl'] : greenergy_option('footer_logo', get_template_directory_uri() . '/assets/images/logo.png');
$description = !empty($attributes['description']) ? $attributes['description'] : greenergy_option('footer_description', __('منصة Greenergy الرائدة في مجال الطاقة المتجددة والاستدامة، نقدم المحتوى التعليمي والفرص الوظيفية ودليل الخبراء في قطاع الطاقة النظيفة.', 'greenergy'));

if (!function_exists('greenergy_render_footer_column')) {
    /**
     * Helper to render a footer column
     */
    function greenergy_render_footer_column($index, $attributes)
    {
        $sidebar_id = 'footer-' . $index;
        $title = $attributes['col' . $index . 'Title'] ?? '';
        $links = !empty($attributes['col' . $index . 'Links']) ? $attributes['col' . $index . 'Links'] : [];

        $default_titles = [
            1 => __('عن الشركة', 'greenergy'),
            2 => __('خدماتنا', 'greenergy'),
            3 => __('أنواع الطاقة', 'greenergy'),
            4 => __('الموارد', 'greenergy'),
        ];

        $default_links = [
            1 => [
                ['label' => __('من نحن', 'greenergy'), 'url' => '#'],
                ['label' => __('فريق العمل', 'greenergy'), 'url' => '#'],
                ['label' => __('الشركان', 'greenergy'), 'url' => '#'],
                ['label' => __('تواصل معنا', 'greenergy'), 'url' => '#'],
            ],
            2 => [
                ['label' => __('الأخبار والمقالات', 'greenergy'), 'url' => '#'],
                ['label' => __('الدورات التدريبية', 'greenergy'), 'url' => '#'],
                ['label' => __('فرص العمل', 'greenergy'), 'url' => '#'],
                ['label' => __('دليل الشركات', 'greenergy'), 'url' => '#'],
            ],
            3 => [
                ['label' => __('الطاقة الشمسية', 'greenergy'), 'url' => '#'],
                ['label' => __('طاقة الرياح', 'greenergy'), 'url' => '#'],
                ['label' => __('الطاقة المائية', 'greenergy'), 'url' => '#'],
                ['label' => __('الطاقة الحيوية', 'greenergy'), 'url' => '#'],
            ],
            4 => [
                ['label' => __('مكتبة الوسائط', 'greenergy'), 'url' => '#'],
                ['label' => __('المقالات', 'greenergy'), 'url' => '#'],
                ['label' => __('الدراسات', 'greenergy'), 'url' => '#'],
                ['label' => __('الأسئلة الشائعة', 'greenergy'), 'url' => '#'],
            ],
        ];

        $display_title = !empty($title) ? $title : ($default_titles[$index] ?? '');

        // Priority 1: Block Links
        if (!empty($links)) {
?>
            <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php echo esc_html($display_title); ?></h4>
            <ul class="space-y-3 font-bold text-green-100/60 text-sm list-none p-0 m-0">
                <?php foreach ($links as $link) : ?>
                    <li><a href="<?php echo esc_url($link['url'] ?? '#'); ?>" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php echo esc_html($link['label'] ?? ''); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php
        }
        // Priority 2: Sidebar
        elseif (is_active_sidebar($sidebar_id)) {
            dynamic_sidebar($sidebar_id);
        }
        // Priority 3: Fallback Defaults
        else {
            $links_fallback = $default_links[$index] ?? [];
        ?>
            <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php echo esc_html($display_title); ?></h4>
            <ul class="space-y-3 font-bold text-green-100/60 text-sm list-none p-0 m-0">
                <?php if (!empty($links_fallback)) : ?>
                    <?php foreach ($links_fallback as $link) : ?>
                        <li><a href="<?php echo esc_url($link['url'] ?? '#'); ?>" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php echo esc_html($link['label'] ?? ''); ?></a></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li class="opacity-50 italic"><?php _e('لا توجد روابط مضافة.', 'greenergy'); ?></li>
                <?php endif; ?>
            </ul>
<?php
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-6 relative z-10">
    <div class="grid grid-cols-2 lg:grid-cols-12 gap-x-6 gap-y-10 lg:gap-8 border-b border-white/10 pb-12">
        <!-- Col 1 -->
        <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-1 lg:order-1" data-aos="fade-up" data-aos-delay="100">
            <?php greenergy_render_footer_column(1, $attributes); ?>
        </div>

        <!-- Col 2 -->
        <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-2 lg:order-2" data-aos="fade-up" data-aos-delay="200">
            <?php greenergy_render_footer_column(2, $attributes); ?>
        </div>

        <!-- Center: Logo Block -->
        <div class="col-span-2 lg:col-span-4 flex flex-col items-center text-center order-0 lg:order-3 lg:mx-auto" data-aos="zoom-in" data-aos-delay="300">
            <div class="flex flex-col items-center hover:scale-105 transition-transform duration-500 cursor-pointer mb-6">
                <div class="!w-[134px] !h-[138px] mb-4">
                    <img class="mix-blend-darken transition-transform duration-300 hover:scale-105 w-full h-full object-contain" src="<?php echo esc_url($logo_url); ?>" alt="Footer Logo" />
                </div>
            </div>
            <p class="text-white text-base font-normal leading-loose max-w-xs px-2 opacity-80 hover:opacity-100 transition-opacity">
                <?php echo esc_html($description); ?>
            </p>
        </div>

        <!-- Col 3 -->
        <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-3 lg:order-4" data-aos="fade-up" data-aos-delay="400">
            <?php greenergy_render_footer_column(3, $attributes); ?>
        </div>

        <!-- Col 4 -->
        <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-4 lg:order-5" data-aos="fade-up" data-aos-delay="500">
            <?php greenergy_render_footer_column(4, $attributes); ?>
        </div>
    </div>
</div>