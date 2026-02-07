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
            'value' => '120+ جيجاوات',
            'desc'  => 'تم إنتاجها عالميًا في آخر 12 شهر',
            'icon'  => 'wind-power.png ',
        ],
        [
            'title' => 'إجمالي إنتاج طاقة الرياح',
            'value' => '95+ جيجاوات',
            'desc'  => 'عبر مزارع الرياح البرية والبحرية',
            'icon'  => 'energy.png  ',
        ],
        [
            'title' => 'انخفاض انبعاثات الكربون',
            'value' => '2.5+ مليون طن',
            'desc'  => 'تم تجنبها باستخدام مصادر الطاقة النظيفة',
            'icon'  => 'cardano-(ada).svg',
        ], 
        [
            'title' => 'عدد المشاريع الجديدة',
            'value' => '3,200+ مشروع',
            'desc'  => 'في مجال الطاقة المتجددة خلال العام الحالي',
            'icon'  => 'note-favorite.svg',
        ],
        [
            'title' => 'الاستثمارات العالمية',
            'value' => '500+ مليار دولار',
            'desc'  => 'تم ضخها في قطاع الطاقة النظيفة',
            'icon'  => 'status-up.svg',
        ],
        [
            'title' => 'عدد الدول المشاركة',
            'value' => '180+ دولة',
            'desc'  => 'تتبنى سياسات للطاقة المستدامة',
            'icon'  => 'global.svg',
        ],
    ];
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'my-12 relative w-full max-w-[1400px] mx-auto rounded-[3rem] overflow-hidden p-8 lg:p-16 min-h-[800px] flex flex-col items-center justify-center',
    'style' => 'background-image: url("' . get_template_directory_uri() . '/assets/images/home_stats.png"); background-size: cover; background-position: center; background-repeat: no-repeat;'
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
    <!-- Content -->
    <div class="relative z-10 w-full pt-10">
        <!-- Header -->
        <div class="text-center mb-2" data-aos="fade-down" data-aos-duration="1000">
            <h2 class="text-xl lg:text-4xl font-black text-white pb-12"><?php echo esc_html( $attributes['title'] ); ?></h2>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php 
                $delay = 0;
                foreach ( $stats as $stat ) : 
                    $delay += 100;
            ?>
                <div class="glass-card rounded-2xl p-8 max-sm:p-3 flex flex-col items-center text-center hover:bg-white/20 transition-all duration-500 group hover:-translate-y-2 hover:shadow-2xl hover:shadow-green-500/10 cursor-default" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="text-[#4ADE80] text-4xl group-hover:scale-110 transition duration-300">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/homepage/<?php echo esc_attr( $stat['icon'] ); ?>" alt="<?php echo esc_attr( $stat['title'] ); ?>" class="w-12 h-12 mb-6 group-hover:scale-110 transition duration-300">
                    </div>
                    <span class="text-white text-base mb-2"><?php echo esc_html( $stat['title'] ); ?></span>
                    <div class="text-xl lg:text-2xl font-black text-white mb-4">
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
                    <p class="text-white text-sm font-medium"><?php echo esc_html( $stat['desc'] ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
