<?php
/**
 * Global Stats Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @package Greenergy
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'title'       => 'أرقام تتحدث عن مستقبل الطاقة',
    'description' => 'تعرف على أبرز إنجازات ومؤشرات قطاع الطاقة المتجددة حول العالم، في لمحة سريعة.',
] );

// Dynamic Data Fetching: Get stats from CPT
$args = [
    'post_type'      => 'stats',
    'posts_per_page' => 6,
    'status'         => 'publish',
];
$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    $stats = [];
    $icons = ['fa-solar-panel', 'fa-wind', 'fa-smog', 'fa-globe-americas', 'fa-chart-line', 'fa-calendar-check'];
    $i = 0;
    while ( $query->have_posts() ) {
        $query->the_post();
        $stats[] = [
            'title' => get_the_title(),
            'value' => get_the_excerpt(),
            'desc'  => get_the_content(),
            'icon'  => $icons[$i % count($icons)], // Rotate icons if not set in meta
        ];
        $i++;
    }
    wp_reset_postdata();
} else {
    $stats = [
        [
            'title' => 'إجمالي إنتاج الطاقة الشمسية',
            'value' => '+120 GW',
            'desc'  => 'تم إنتاجها عالمياً في آخر 12 شهر',
            'icon'  => 'fa-solar-panel',
        ],
        [
            'title' => 'إجمالي إنتاج طاقة الرياح',
            'value' => '+95 GW',
            'desc'  => 'عبر مزارع الرياح البرية والبحرية',
            'icon'  => 'fa-wind',
        ],
        [
            'title' => 'انخفاض انبعاثات الكربون',
            'value' => '+2.5 M Ton',
            'desc'  => 'تم تجنبها باستخدام مصادر الطاقة النظيفة',
            'icon'  => 'fa-smog',
        ],
        [
            'title' => 'عدد الدول المشاركة',
            'value' => '+180',
            'desc'  => 'تتبنى سياسات للطاقة المستدامة',
            'icon'  => 'fa-globe-americas',
        ],
        [
            'title' => 'الاستثمارات العالمية',
            'value' => '+$500 B',
            'desc'  => 'تم ضخها في قطاع الطاقة النظيفة',
            'icon'  => 'fa-chart-line',
        ],
        [
            'title' => 'عدد المشاريع الجديدة',
            'value' => '+3,200',
            'desc'  => 'في مجال الطاقة المتجددة خلال العام الحالي',
            'icon'  => 'fa-calendar-check',
        ],
    ];
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'my-12 relative w-full max-w-[1400px] mx-auto bg-[#022C22] rounded-[3rem] overflow-hidden p-8 lg:p-16 min-h-[800px] flex flex-col items-center justify-center',
] );
?>
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .bg-overlay {
        background: radial-gradient(circle at center, rgba(16, 185, 129, 0.15) 0%, rgba(2, 44, 34, 0.95) 100%);
    }
</style>

<div <?php echo $wrapper_attributes; ?>>
    <!-- Background Map & Overlay -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://upload.wikimedia.org/wikipedia/commons/8/80/World_map_-_low_resolution.svg')] bg-contain bg-center bg-no-repeat pointer-events-none"></div>
    <div class="absolute inset-0 bg-overlay pointer-events-none"></div>

    <!-- Content -->
    <div class="relative z-10 w-full">
        <!-- Header -->
        <div class="text-center mb-16" data-aos="fade-down" data-aos-duration="1000">
            <h2 class="text-3xl lg:text-5xl font-black text-white mb-4"><?php echo esc_html( $attributes['title'] ); ?></h2>
            <p class="text-white/60 text-sm font-medium"><?php echo esc_html( $attributes['description'] ); ?></p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php 
                $delay = 0;
                foreach ( $stats as $stat ) : 
                    $delay += 100;
            ?>
                <div class="glass-card rounded-2xl p-8 flex flex-col items-center text-center hover:bg-white/15 transition duration-300 group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="text-[#4ADE80] text-4xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas <?php echo esc_attr( $stat['icon'] ); ?>"></i>
                    </div>
                    <h3 class="text-white/80 font-bold text-lg mb-2"><?php echo esc_html( $stat['title'] ); ?></h3>
                    <div class="text-4xl lg:text-5xl font-black text-white mb-4" dir="ltr">
                        <?php 
                            $val = $stat['value'];
                            preg_match('/(\d+[,.]?\d*)/', $val, $matches);
                            $number_raw = $matches[1] ?? 0;
                            // Clean number for JS (remove commas)
                            $number = str_replace(',', '', $number_raw);
                            $suffix = str_replace($number_raw, '', $val);
                        ?>
                        <span class="js-counter" data-target="<?php echo esc_attr($number); ?>">0</span><?php echo esc_html($suffix); ?>
                    </div>
                    <p class="text-white/60 text-sm font-medium"><?php echo esc_html( $stat['desc'] ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
