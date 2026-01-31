<?php
/**
 * Jobs Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'badgeText'   => 'الوظائف',
    'description' => 'كن على اطلاع دائم على آخر التطورات في عالم الطاقة المتجددة، مع لمحة سريعة عن أكثر المواضيع التي يتحدث عنها الجميع.',
    'buttonText'  => 'عرض جميع الوظائف',
    'imageId'     => 0,
    'imageUrl'    => '',
] );

$bg_image_url = $attributes['imageUrl'];
if ( ! empty( $attributes['imageId'] ) ) {
    $lib_url = wp_get_attachment_image_url( $attributes['imageId'], 'full' );
    if ( $lib_url ) {
        $bg_image_url = $lib_url;
    }
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'bg-[#F9FAFB] py-8 lg:py-16 px-4 relative overflow-hidden',
] );

// Dynamic Data Fetching: Get jobs from CPT
$args = [
    'post_type'      => 'jobs',
    'posts_per_page' => 10,
    'status'         => 'publish',
];
$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    $all_jobs = [];
    while ( $query->have_posts() ) {
        $query->the_post();
        $all_jobs[] = [
            'title'    => get_the_title(),
            'company'  => get_post_meta( get_the_ID(), 'company', true ) ?: 'Greenergy Corperation',
            'location' => get_post_meta( get_the_ID(), 'location', true ) ?: 'الرياض',
            'users'    => get_post_meta( get_the_ID(), 'users', true ) ?: '0',
            'date'     => get_the_date('d/m'),
            'tags'     => get_the_terms( get_the_ID(), 'job_category' ) ? wp_list_pluck( get_the_terms( get_the_ID(), 'job_category' ), 'name' ) : ['دوام كامل'],
            'image'    => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ];
    }
    wp_reset_postdata();

    // Distribute jobs
    $jobs = array_slice($all_jobs, 0, 3);
    $sidebar_jobs = array_slice($all_jobs, 3, 2);
    $golden_jobs = array_slice($all_jobs, 5, 3);
} else {
    // Fallback jobs
    $jobs = [
        [
            'title' => 'مطور مشاريع رياح',
            'company' => 'مجموعة الطاقة النظيفة للابتكار',
            'location' => 'الرياض, السعودية',
            'users' => '450',
            'date' => '16/08/2025',
            'tags' => ['استشارات', 'عن بعد'],
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
        [
            'title' => 'مطور مشاريع رياح',
            'company' => 'مجموعة الطاقة النظيفة للابتكار',
            'location' => 'الرياض, السعودية',
            'users' => '450',
            'date' => '16/08/2025',
            'tags' => ['استشارات', 'عن بعد'],
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
        [
            'title' => 'مطور مشاريع رياح',
            'company' => 'مجموعة الطاقة النظيفة للابتكار',
            'location' => 'الرياض, السعودية',
            'users' => '450',
            'date' => '16/08/2025',
            'tags' => ['استشارات', 'عن بعد'],
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
    ];

    $sidebar_jobs = [
        [
            'title' => 'مهندس طاقة شمسية',
            'company' => 'شركة حلول الطاقة المتكاملة',
            'location' => 'الرياض',
            'users' => '450',
            'date' => '16/08/2025',
            'tags' => ['عن بعد'],
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
        [
            'title' => 'مهندس طاقة شمسية',
            'company' => 'شركة حلول الطاقة المتكاملة',
            'location' => 'الرياض',
            'users' => '450',
            'date' => '16/08/2025',
            'tags' => ['استشارات'],
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
    ];

    $golden_jobs = [
        [
            'title' => 'مهندس طاقة شمسية',
            'company' => 'حلول الطاقة المتكاملة',
            'location' => 'الرياض',
            'users' => '450',
            'date' => '16/08',
            'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=100&h=100&auto=format&fit=crop',
        ],
        [
            'title' => 'فني توربينات رياح',
            'company' => 'مجموعة الطاقة النظيفة',
            'location' => 'نيوم',
            'users' => '120',
            'date' => '16/08',
            'image' => 'https://images.unsplash.com/photo-1559302995-f0a1bc1548e6?w=100&h=100&auto=format&fit=crop',
        ],
        [
            'title' => 'محلل بيانات طاقة',
            'company' => 'بترو غرين',
            'location' => 'الخبر',
            'users' => '340',
            'date' => '16/08',
            'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=100&h=100&auto=format&fit=crop',
        ],
    ];
}
?>
<style>
    .scroll-mask {
        filter: blur(3px);
        mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
        -webkit-mask-image: linear-gradient(to bottom, transparent, black 15%, black 85%, transparent);
        overflow: hidden;
    }
    .scroll-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        animation: scroll-vertical 12s linear infinite;
    }
    .scroll-container:hover {
        animation-play-state: paused;
    }
    @keyframes scroll-vertical {
        0% { transform: translateY(0); }
        100% { transform: translateY(-50%); }
    }
    .glass-job-card {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 1.25rem;
        padding: 0.75rem 0.75rem;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
</style>

<section <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_image_url ) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url( $bg_image_url ); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Header -->
        <div class="text-center mb-8 md:mb-12" data-aos="fade-down" data-aos-duration="1000">
            <div class="inline-block bg-[#E6F6EC] text-[#229924] px-6 py-2 rounded-full text-base md:text-lg font-bold mb-4">
                <?php echo esc_html( $attributes['badgeText'] ); ?>
            </div>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-lg leading-relaxed font-semibold px-4">
                <?php echo esc_html( $attributes['description'] ); ?>
            </p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-start">
            <!-- Sidebar (Left Column in RTL) -->
            <div class="lg:col-span-4 flex flex-col gap-4 md:gap-6 order-2 lg:order-2" data-aos="fade-left" data-aos-delay="200">
                <?php 
                    $delay = 400;
                    foreach ( $sidebar_jobs as $job ) : 
                        $delay += 100;
                ?>
                    <div class="bg-white rounded-3xl p-4 md:p-5 shadow-soft hover:shadow-2xl hover:shadow-[#D9A520]/10 hover:-translate-y-1 transition-all duration-500 flex items-center gap-4 border border-transparent hover:border-[#D9A520]/10 group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full border border-gray-100 p-1 flex-shrink-0 bg-white shadow-sm overflow-hidden group-hover:scale-110 transition-transform duration-500">
                            <img src="<?php echo esc_url( $job['image'] ); ?>" class="w-full h-full object-cover rounded-full" alt="Logo">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h3 class="font-black text-xs md:text-sm text-gray-900 truncate"><?php echo esc_html( $job['title'] ); ?></h3>
                                <div class="text-gray-400 text-[8px] md:text-[9px] font-bold flex items-center gap-1 flex-shrink-0" dir="ltr">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo esc_html( $job['date'] ); ?></span>
                                </div>
                            </div>
                            <p class="text-gray-500 text-[9px] md:text-[10px] mb-2 truncate"><?php echo esc_html( $job['company'] ); ?></p>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2  text-[8px] md:text-[9px]  overflow-x-auto no-scrollbar">
                                    <span class="flex items-center gap-0.5 whitespace-nowrap"><i class="fas fa-map-marker-alt"></i> <?php echo esc_html( $job['location'] ); ?></span>
                                    <span class="flex items-center gap-0.5 whitespace-nowrap"><i class="fas fa-users"></i> <?php echo esc_html( $job['users'] ); ?></span>
                                    <?php foreach ( $job['tags'] as $tag ) : ?>
                                        <span class="rounded-[123px] outline outline-1 outline-offset-[-1px] outline-zinc-200  bg-[#F9FAFB] px-1.5 py-0.5 rounded text-[7px] text-gray-500 border border-gray-100 whitespace-nowrap"><?php echo esc_html( $tag ); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <button class="bg-[#D9A520] text-white py-1.5 px-3 md:px-4 rounded-lg text-[9px] md:text-[10px] hover:bg-[#B78B17] hover:scale-105 transition-all flex-shrink-0 shadow-md">
                                     <span>تقدم الآن</span>
                                    <i class="fas fa-arrow-left text-[8px] md:text-[9px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Golden Card -->
                <div class="rounded-3xl p-6 shadow-lg relative overflow-hidden group h-[320px] md:h-[380px]">
                <div class="absolute w-full h-full bg-gradient-to-l from-yellow-50/25 via-yellow-500/50 to-amber-50/25 "></div>    
                <div class="absolute inset-0 z-0 bg-white/10  opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center">
                        <h3 class="text-2xl md:text-3xl font-black  text-black mb-2 drop-shadow-sm px-6 pb-2 bg-gradient-to-l from-yellow-500/80 to-amber-300/80 rounded-3xl">وظيفة ذهبية</h3>
                        <p class="text-gray-700 text-sm md:text-base mb-6">اكتشف احدث الوضائف المميزة</p>
                        <div class="scroll-mask w-full h-full absolute">
                            <div class="scroll-container px-2">
                                <?php 
                                $all_golden = array_merge($golden_jobs, $golden_jobs); // Duplicate for seamless scroll
                                foreach ( $all_golden as $job ) : 
                                ?>
                                    <div class="glass-job-card flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-white/40 p-1 flex-shrink-0 border border-white/20">
                                            <img src="<?php echo esc_url( $job['image'] ); ?>" class="w-full h-full object-cover rounded-full" alt="Logo">
                                        </div>
                                        <div class="flex-1 min-w-0 text-right">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <h4 class="font-black text-[10px] md:text-xs text-gray-900 truncate"><?php echo esc_html( $job['title'] ); ?></h4>
                                                <span class="text-[7px] md:text-[8px] text-gray-800 opacity-60 font-bold" dir="ltr"><?php echo esc_html( $job['date'] ); ?></span>
                                            </div>
                                            <p class="text-[8px] md:text-[9px] text-gray-700 font-bold mb-1 truncate"><?php echo esc_html( $job['company'] ); ?></p>
                                            <div class="flex items-center gap-2 text-[7px] md:text-[8px] text-gray-800 font-black">
                                                <span><i class="fas fa-map-marker-alt"></i> <?php echo esc_html( $job['location'] ); ?></span>
                                                <span><i class="fas fa-users"></i> <?php echo esc_html( $job['users'] ); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button class="bg-gradient-to-br from-amber-400 to-amber-400 text-white py-3 md:py-4 px-10 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-amber-500/30 transition-all transform hover:scale-110 flex items-center gap-3 text-sm md:text-base group-hover:from-amber-500 group-hover:to-amber-500">
                            <span>اكتشف الكل</span>
                            <i class="fas fa-arrow-left text-xs md:text-sm group-hover:-translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content (Right Column in RTL) -->
            <div class="lg:col-span-8 flex flex-col gap-4 md:gap-5 order-1 lg:order-1" data-aos="fade-right" data-aos-delay="200">
                <?php 
                    $delay = 400;
                    foreach ( $jobs as $job ) : 
                        $delay += 150;
                ?>
                    <div class="bg-white rounded-3xl p-4 md:p-8 shadow-soft hover:shadow-2xl hover:shadow-[#D9A520]/10 hover:-translate-y-1 transition-all duration-500 flex items-center gap-4 md:gap-8 border border-transparent hover:border-[#D9A520]/20 group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full border border-gray-100 p-2 flex-shrink-0 bg-white shadow-sm overflow-hidden group-hover:scale-110 transition-transform duration-500">
                            <img src="<?php echo esc_url( $job['image'] ); ?>" class="w-full h-full object-cover rounded-full" alt="Logo">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-black text-sm md:text-base text-gray-900 truncate"><?php echo esc_html( $job['title'] ); ?></h3>
                                <div class="text-gray-400 text-[8px] md:text-xs font-bold flex items-center gap-2 flex-shrink-0" dir="ltr">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo esc_html( $job['date'] ); ?></span>
                                </div>
                            </div>
                            <p class="text-gray-500 text-[10px] md:text-sm mb-4 md:mb-6 truncate"><?php echo esc_html( $job['company'] ); ?></p>
                            <div class="flex flex-wrap items-center gap-3 md:gap-6">
                                <div class="flex items-center gap-2  text-[9px] md:text-sm">
                                    <i class="fas fa-map-marker-alt text-[10px] md:text-xs"></i>
                                    <span class="responsive-text-xs"><?php echo esc_html( $job['location'] ); ?></span>
                                </div>
                                <div class="flex items-center gap-2  text-[9px] md:text-sm">
                                    <i class="fas fa-users text-[10px] md:text-xs"></i>
                                    <span class="responsive-text-xs"><?php echo esc_html( $job['users'] ); ?></span>
                                </div>
                                <div class="flex gap-1 md:gap-2">
                                    <?php foreach ( $job['tags'] as $tag ) : ?>
                                        <span class="rounded-[123px] outline outline-1 outline-offset-[-1px] outline-zinc-200 bg-[#F9FAFB] border border-gray-100 px-2 py-0.5 md:px-4 md:py-1.5 rounded-lg md:rounded-xl text-[8px] md:text-xs font-black"><?php echo esc_html( $tag ); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <button class="bg-[#D9A520] text-white py-1.5 md:py-3 px-4 md:px-10 rounded-xl md:rounded-2xl hover:bg-[#B78B17] hover:scale-105 transition-all transform hover:translate-x-[-4px] flex items-center gap-2 md:gap-3 mr-auto shadow-md text-[10px] md:text-base">
                                    <span>تقدم الآن</span>
                                    <i class="fas fa-arrow-left text-[10px] md:text-sm group-hover:-translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Footer Button -->
        <div class="text-center mt-12 pb-12">
            <a href="#" class="inline-flex items-center gap-4 bg-white border border-gray-200 text-gray-800 px-8 md:px-10 py-3 md:py-4 rounded-2xl font-black hover:shadow-xl transition-all hover:border-[#D9A520]/40 group text-sm md:text-base">
                <span><?php echo esc_html( $attributes['buttonText'] ); ?></span>
                <i class="fas fa-arrow-left text-xs md:text-sm group-hover:translate-x-[-4px] transition-transform"></i>
            </a>
        </div>
    </div>
</section>
