<?php
/**
 * Hero Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'badgeText'         => 'مستقبل الطاقة النظيفة',
    'headlineHighlight' => 'اكتشف',
    'headlineMain'      => 'عالم الطاقة المتجددة من دليل اللاعبين في السوق',
    'description'       => 'اكتشف دليل الطاقة المتجددة الشامل الذي غيّر مستقبل الملايين.',
    'ctaText'           => 'المستقبل يبدأ من هنا',
    'ctaUrl'            => '#',
    'imageUrl'          => '',
    'imageId'           => 0,
    'viewMode'          => 'static',
    'stats'             => [],
    'featuredStat'      => [ 'value' => '500+', 'label' => 'زيارة و مهتمين' ],
] );

$hero_image_url = $attributes['imageUrl'];
if ( ! empty( $attributes['imageId'] ) ) {
    $lib_url = wp_get_attachment_image_url( $attributes['imageId'], 'full' );
    if ( $lib_url ) {
        $hero_image_url = $lib_url;
    }
}
if ( empty( $hero_image_url ) ) {
    $hero_image_url = 'https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1200&auto=format&fit=crop';
}

// Stats Logic
$display_stats = $attributes['stats'];
$featured = $attributes['featuredStat'];

if ( $attributes['viewMode'] === 'real' ) {
    // Fetch actual counts if functions are available
    $news_count = function_exists('wp_count_posts') ? wp_count_posts('news')->publish : '0';
    $user_count = function_exists('count_users') ? count_users()['total_users'] : '0';
    
    $display_stats = [
        [ 'icon' => 'fa-building', 'value' => $news_count . '+', 'label' => 'مقال إخباري' ],
        [ 'icon' => 'fa-globe', 'value' => '15+', 'label' => 'دولة' ],
        [ 'icon' => 'fa-chart-line', 'value' => '200+', 'label' => 'شركة مسجلة' ],
        [ 'icon' => 'fa-user-friends', 'value' => $user_count . '+', 'label' => 'عضو' ],
    ];
    $featured = [ 'value' => 'real count', 'label' => 'زيارات حقيقية' ];
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'relative bg-white overflow-hidden py-8 lg:py-20 flex flex-col items-center',
] );

// Inline styles for the hero section
?>
<style>
    .discover-gradient {
        background: linear-gradient(to bottom, #348934 0%, #64B24D 46.635%, #ABEF74 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .custom-scrollbar::-webkit-scrollbar {
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<section <?php echo $wrapper_attributes; ?>>
    <div class="max-w-[1400px] mx-auto px-4 w-full">
        <!-- Hero Container -->
        <div class="grid lg:grid-cols-2 gap-12 items-center w-full">

            <!-- Content Side -->
            <div class="flex flex-col items-center lg:items-start text-center lg:text-right order-1" data-aos="fade-left" data-aos-duration="1000">
                <!-- Badge -->
                <div class="backdrop-blur-[2px] bg-[rgba(47,247,51,0.48)] px-8 py-2 rounded-full mb-6 border border-white/20">
                    <span class="text-[#0B0C0B] text-sm font-bold">
                        <?php echo esc_html( $attributes['badgeText'] ); ?>
                    </span>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl lg:text-6xl font-black text-[#0B0C0B] leading-snug mb-4 max-w-2xl">
                    <span class="discover-gradient"><?php echo esc_html( $attributes['headlineHighlight'] ); ?></span> 
                    <?php echo esc_html( $attributes['headlineMain'] ); ?>
                </h1>

                <!-- Subheading -->
                <p class="text-[#229924] text-lg lg:text-xl font-bold mb-10 opacity-90">
                    <?php echo esc_html( $attributes['description'] ); ?>
                </p>

                <!-- CTA Button -->
                <a href="<?php echo esc_url( $attributes['ctaUrl'] ); ?>" class="group relative flex items-center justify-between bg-[#5DB34A] hover:bg-[#4D7C0F] text-white px-10 py-4 rounded-full shadow-lg transition-all duration-300 transform hover:-translate-y-1 w-full max-w-[300px] lg:max-w-none lg:w-fit gap-6">
                    <span class="text-xl font-bold whitespace-nowrap"><?php echo esc_html( $attributes['ctaText'] ); ?></span>
                    <div class="bg-white rounded-full p-2 text-[#5DB34A] transform transition-transform group-hover:rotate-45">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7 7-7"></path>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Image Side -->
            <div class="hidden lg:block order-2" data-aos="fade-right" data-aos-duration="1200">
                <div class="relative rounded-[3rem] overflow-hidden shadow-2xl">
                    <img src="<?php echo esc_url( $hero_image_url ); ?>" alt="Hero Image" class="w-full h-[550px] object-cover">
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="w-full mt-16 bg-[#F7FEE7] rounded-[3rem] p-6 lg:p-12 border border-green-100/50 shadow-sm" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <div class="flex flex-wrap lg:flex-nowrap gap-10 lg:gap-4 items-center justify-center lg:justify-between">
                <?php if ( !empty($display_stats) ) : ?>
                    <!-- First 2 Stats -->
                    <?php for($i=0; $i<min(2, count($display_stats)); $i++): 
                        $val = $display_stats[$i]['value'];
                        preg_match('/(\d+)/', $val, $matches);
                        $number = $matches[1] ?? 0;
                        $suffix = str_replace($number, '', $val);
                    ?>
                    <div class="flex flex-col items-center min-w-[120px]" data-aos="zoom-in" data-aos-delay="<?php echo 400 + ($i * 100); ?>">
                        <div class="text-[#229924] text-3xl mb-2"><i class="fas <?php echo esc_attr($display_stats[$i]['icon']); ?>"></i></div>
                        <div class="text-2xl lg:text-3xl font-black text-[#229924]">
                            <span class="js-counter" data-target="<?php echo esc_attr($number); ?>">0</span><?php echo esc_html($suffix); ?>
                        </div>
                        <div class="text-sm font-bold text-[#656865]"><?php echo esc_html($display_stats[$i]['label']); ?></div>
                    </div>
                    <?php endfor; ?>
                <?php endif; ?>

                <!-- Featured Center Stat -->
                <?php 
                    $f_val = $featured['value'];
                    preg_match('/(\d+)/', $f_val, $matches);
                    $f_number = $matches[1] ?? 0;
                    $f_suffix = str_replace($f_number, '', $f_val);
                ?>
                <div class="w-full lg:w-fit flex justify-center" data-aos="zoom-in" data-aos-delay="600">
                    <div class="bg-gradient-to-b from-sky-500 to-blue-700 p-8 lg:px-20 lg:py-10 rounded-[3rem] shadow-2xl shadow-blue-300 relative overflow-hidden group border-4 border-white flex flex-col items-center gap-2 transform transition hover:scale-105 w-full max-w-[300px] lg:max-w-none">
                        <div class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center mb-1">
                            <div class="w-5 h-5 rounded-full bg-white animate-pulse"></div>
                        </div>
                        <div class="text-3xl lg:text-4xl font-black text-white italic">
                            <span class="js-counter" data-target="<?php echo esc_attr($f_number); ?>">0</span><?php echo esc_html($f_suffix); ?>
                        </div>
                        <div class="text-sm font-black text-white/90 whitespace-nowrap"><?php echo esc_html($featured['label']); ?></div>
                    </div>
                </div>

                <?php if ( count($display_stats) > 2 ) : ?>
                    <!-- Remaining Stats -->
                    <?php for($i=2; $i<count($display_stats); $i++): 
                        $val = $display_stats[$i]['value'];
                        preg_match('/(\d+)/', $val, $matches);
                        $number = $matches[1] ?? 0;
                        $suffix = str_replace($number, '', $val);
                    ?>
                    <div class="flex flex-col items-center min-w-[120px]">
                        <div class="text-[#229924] text-3xl mb-2"><i class="fas <?php echo esc_attr($display_stats[$i]['icon']); ?>"></i></div>
                        <div class="text-2xl lg:text-3xl font-black text-[#229924]">
                            <span class="js-counter" data-target="<?php echo esc_attr($number); ?>">0</span><?php echo esc_html($suffix); ?>
                        </div>
                        <div class="text-sm font-bold text-[#656865]"><?php echo esc_html($display_stats[$i]['label']); ?></div>
                    </div>
                    <?php endfor; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

