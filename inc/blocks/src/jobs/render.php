<?php
/**
 * Jobs Block Template.
 *
 * @param   array $attributes - Block attributes.
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

// Background image
$bg_image_url = $attributes['imageUrl'];
if ( !empty($attributes['imageId']) ) {
    $bg_image_url = wp_get_attachment_image_url( $attributes['imageId'], 'full' ) ?: $bg_image_url;
}

// Fetch jobs
$query = new WP_Query([
    'post_type'      => 'jobs',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
]);

$all_jobs = [];
if ( $query->have_posts() ) {
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
}

// Fallback jobs
if ( empty($all_jobs) ) {
    $all_jobs = array_fill(0, 8, [
        'title'    => 'مطور مشاريع رياح',
        'company'  => 'مجموعة الطاقة النظيفة للابتكار',
        'location' => 'الرياض, السعودية',
        'users'    => '450',
        'date'     => '16/08/2025',
        'tags'     => ['استشارات', 'عن بعد'],
        'image'    => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09',
    ]);
}

// Distribute jobs
$jobs_sections = [
    'main'     => array_slice($all_jobs, 0, 3),
    'sidebar'  => array_slice($all_jobs, 3, 2),
    'golden'   => array_slice($all_jobs, 5, 3),
];

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'bg-[#F9FAFB] py-8 lg:py-16 px-4 relative overflow-hidden',
]);

// Reusable job card function
function render_job_card($job, $size = 'main', $delay = 0) {
    $sizes = [
    'main' => [
        'card_padding' => 'p-4 md:p-8',
        'logo_size'    => 'w-[57px] h-[57px] md:w-[105px] md:h-[105px]',
        'title_size'   => 'text-sm md:text-base',
        'company_size' => 'text-[10px] md:text-sm',
        'info_size'    => 'text-[9px] md:text-sm',
        'icon_size'    => 'text-[10px] md:text-xs',
        'tag_padding'  => 'px-2 py-0.5 md:px-4 md:py-1.5',
        'tag_size'     => 'text-[8px] md:text-xs',
        'btn_padding'  => 'py-1.5 md:py-3 px-4 md:px-10',
        'btn_size'     => 'text-[10px] md:text-base',
        'btn_rounded'  => 'rounded-xl md:rounded-2xl',
        'gap'          => 'gap-4 md:gap-8',
    ],
    'sidebar' => [
        // تم توحيد الجزء الأول مع main وتخصيص md: للـ sidebar
        'card_padding' => 'p-4 md:p-5', 
        'logo_size'    => 'w-[57px] h-[57px] md:w-16 md:h-16',
        'title_size'   => 'text-sm md:text-sm', // لاحظ text-sm من الماين
        'company_size' => 'text-[10px] md:text-[10px]',
        'info_size'    => 'text-[9px] md:text-[9px]',
        'icon_size'    => 'text-[10px] md:text-[9px]',
        'tag_padding'  => 'px-2 py-0.5 md:px-1.5 md:py-0.5',
        'tag_size'     => 'text-[8px] md:text-[7px]',
        'btn_padding'  => 'py-1.5 px-4 md:px-3 md:py-1.5',
        'btn_size'     => 'text-[10px] md:text-[10px]',
        'btn_rounded'  => 'rounded-xl md:rounded-lg',
        'gap'          => 'gap-4 md:gap-4',
    ],
];
    
    $s = $sizes[$size];
    ?>
    <div class="bg-white rounded-3xl <?php echo $s['card_padding']; ?> shadow-lg outline outline-1 outline-gray-200 hover:-translate-y-1 transition-all duration-500 flex items-center <?php echo $s['gap']; ?> border border-transparent hover:border-[#D9A520]/20 group" 
         data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
        
        <div class="<?php echo $s['logo_size']; ?>   flex-shrink-0 bg-white shadow-sm overflow-hidden group-hover:scale-110 transition-transform duration-500 rounded-xl">
            <img src="<?php echo esc_url($job['image']); ?>" class="w-full h-full object-cover" alt="<?php echo esc_attr($job['company']); ?>">
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between <?php echo $size === 'main' ? 'mb-1' : 'mb-0.5'; ?>">
                <h3 class="font-black <?php echo $s['title_size']; ?> text-gray-900 truncate">
                    <?php echo esc_html($job['title']); ?>
                </h3>
                <div class="text-gray-400 <?php echo $s['icon_size']; ?> font-bold flex items-center gap-<?php echo $size === 'main' ? '2' : '1'; ?> flex-shrink-0" dir="ltr">
                    <span><?php echo esc_html($job['date']); ?></span>
                    <i class="far fa-clock"></i>
                </div>
            </div>
            
            <p class="text-gray-500 <?php echo $s['company_size']; ?> <?php echo $size === 'main' ? 'mb-4 md:mb-6' : 'mb-2'; ?> truncate">
                <?php echo esc_html($job['company']); ?>
            </p>
            
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 <?php echo $s['info_size']; ?> max-md:flex-wrap">
                    <span class="flex items-center gap-0.5 whitespace-nowrap">
                        <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($job['location']); ?>
                    </span>
                    <span class="flex items-center gap-0.5 whitespace-nowrap">
                        <i class="fas fa-users"></i> <?php echo esc_html($job['users']); ?>
                    </span>
                    <?php if ($size === 'main') : ?>
                        <div class="flex gap-1 md:gap-2">
                    <?php endif; ?>
                    <?php foreach ($job['tags'] as $tag) : ?>
                        <span class="rounded-[123px] outline outline-1 outline-offset-[-1px] outline-zinc-200 bg-[#F9FAFB] <?php echo $s['tag_padding']; ?> <?php echo $s['tag_size']; ?> text-gray-500 border border-gray-100 whitespace-nowrap font-black">
                            <?php echo esc_html($tag); ?>
                        </span>
                    <?php endforeach; ?>
                    <?php if ($size === 'main') : ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="bg-[#D9A520] text-white <?php echo $s['btn_padding']; ?> <?php echo $s['btn_rounded']; ?> <?php echo $s['btn_size']; ?> hover:bg-[#B78B17] hover:scale-105 transition-all flex-shrink-0 shadow-md <?php echo $size === 'main' ? 'mr-auto' : ''; ?>">
                    <span>تقدم الآن</span>
                    <i class="fas fa-arrow-left <?php echo $s['icon_size']; ?>"></i>
                </button>
            </div>
        </div>
    </div>
    <?php
}

// Golden card function
function render_golden_card($jobs) {
    $all_golden = array_merge($jobs, $jobs); // Duplicate for seamless scroll
    ?>
    <div class="rounded-3xl p-6 shadow-lg relative overflow-hidden group h-[320px] md:h-[380px]">
        <div class="absolute w-full h-full bg-gradient-to-l from-yellow-50/25 via-yellow-500/50 to-amber-50/25"></div>
        <div class="absolute inset-0 z-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center">
            <h3 class="text-2xl md:text-3xl font-black text-black mb-2 drop-shadow-sm px-6 pb-2 bg-gradient-to-l from-yellow-500/80 to-amber-300/80 rounded-3xl">
                وظيفة ذهبية
            </h3>
            <p class="text-gray-700 text-sm md:text-base mb-6">اكتشف احدث الوضائف المميزة</p>
            
            <div class="scroll-mask w-full h-full absolute">
                <div class="scroll-container px-2">
                    <?php foreach ($all_golden as $job) : ?>
                        <div class="glass-job-card flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/40 p-1 flex-shrink-0 border border-white/20">
                                <img src="<?php echo esc_url($job['image']); ?>" class="w-full h-full object-cover rounded-full" alt="<?php echo esc_attr($job['company']); ?>">
                            </div>
                            <div class="flex-1 min-w-0 text-right">
                                <div class="flex justify-between items-center mb-0.5">
                                    <h4 class="font-black text-[10px] md:text-xs text-gray-900 truncate"><?php echo esc_html($job['title']); ?></h4>
                                    <span class="text-[7px] md:text-[8px] text-gray-800 opacity-60 font-bold" dir="ltr"><?php echo esc_html($job['date']); ?></span>
                                </div>
                                <p class="text-[8px] md:text-[9px] text-gray-700 font-bold mb-1 truncate"><?php echo esc_html($job['company']); ?></p>
                                <div class="flex items-center gap-2 text-[7px] md:text-[8px] text-gray-800 font-black">
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo esc_html($job['location']); ?></span>
                                    <span><i class="fas fa-users"></i> <?php echo esc_html($job['users']); ?></span>
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
    <?php
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
.scroll-container:hover { animation-play-state: paused; }
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
    padding: 0.75rem;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
</style>

<section <?php echo $wrapper_attributes; ?>>
    <?php if ($bg_image_url) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url($bg_image_url); ?>" alt="" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    
    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Header -->
        <div class="text-center mb-8 md:mb-12" data-aos="fade-down" data-aos-duration="1000">
            <div class="inline-block bg-[#229924] text-white px-6 py-2 pb-3 rounded-full text-base md:text-lg font-bold mb-4">
                <?php echo esc_html($attributes['badgeText']); ?>
            </div>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-lg leading-relaxed font-semibold px-4">
                <?php echo esc_html($attributes['description']); ?>
            </p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-start">
            
            <!-- Sidebar -->
            <div class="lg:col-span-4 flex flex-col gap-4 md:gap-6 order-2 lg:order-2" data-aos="fade-left" data-aos-delay="200">
                <?php 
                $delay = 400;
                foreach ($jobs_sections['sidebar'] as $job) {
                    render_job_card($job, 'sidebar', $delay);
                    $delay += 100;
                }
                render_golden_card($jobs_sections['golden']);
                ?>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-8 flex flex-col gap-4 md:gap-5 order-1 lg:order-1" data-aos="fade-right" data-aos-delay="200">
                <?php 
                $delay = 400;
                foreach ($jobs_sections['main'] as $job) {
                    render_job_card($job, 'main', $delay);
                    $delay += 150;
                }
                ?>
            </div>
        </div>

        <!-- Footer Button -->
        <div class="text-center mt-12 pb-12">
            <a href="#" class="inline-flex items-center gap-4 bg-white border border-gray-200 text-gray-800 px-8 md:px-10 py-3 md:py-4 rounded-2xl font-black hover:shadow-xl hover:text-green-600 transition-all hover:border-[#D9A520]/40 group text-sm md:text-base">
                <span><?php echo esc_html($attributes['buttonText']); ?></span>
                <i class="fas fa-arrow-left text-xs md:text-sm group-hover:translate-x-[-4px] transition-transform"></i>
            </a>
        </div>
    </div>
</section>