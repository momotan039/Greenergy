<?php
/**
 * Courses Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'badgeText'   => 'التدريبات',
    'title'       => 'الدورات التدريبية',
    'description' => 'كن على اطلاع دائم على آخر التطورات في عالم الطاقة المتجددة، مع لمحة سريعة عن أكثر المواضيع التي يتحدث عنها الجميع.',
    'buttonText'  => 'عرض كل الدورات',
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
    'class' => 'bg-[#013214] py-16 px-4 relative',
] );

// Dynamic Data Fetching: Get courses from CPT
$args = [
    'post_type'      => 'courses',
    'posts_per_page' => 6,
    'status'         => 'publish',
];
$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    $courses = [];
    while ( $query->have_posts() ) {
        $query->the_post();
        $courses[] = [
            'title'    => get_the_title(),
            'duration' => get_post_meta( get_the_ID(), 'duration', true ) ?: '8 أسابيع',
            'users'    => get_post_meta( get_the_ID(), 'users', true ) ?: '450',
            'image'    => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&auto=format&fit=crop',
        ];
    }
    wp_reset_postdata();
} else {
    // For now using static courses
    $courses = [
        [
            'title' => 'أساسيات الطاقة المتجددة: من النظرية إلى التطبيق',
            'duration' => '8 أسابيع',
            'users' => '450',
            'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'تقنيات الألواح الشمسية المتقدمة لعام 2025',
            'duration' => '6 أسابيع',
            'users' => '320',
            'image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'هندسة الرياح وتوليد الطاقة المستدامة',
            'duration' => '10 أسابيع',
            'users' => '180',
            'image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'مستقبل النقل الكهربائي وتحديات الشبكة',
            'duration' => '4 أسابيع',
            'users' => '540',
            'image' => 'https://images.unsplash.com/photo-1548337138-e87d889cc988?w=800&auto=format&fit=crop',
        ],
    ];
}
?>
<style>
    .course-card {
        background: #000000;
        border-radius: 24px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .course-card:hover {
        transform: translateY(-8px);
    }
    .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.3) !important;
        width: 12px;
        height: 12px;
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        background: #A3E635 !important;
        width: 32px;
        border-radius: 6px;
    }
</style>

<section <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_image_url ) : ?>
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <img src="<?php echo esc_url( $bg_image_url ); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Header -->
        <div class="text-center mb-16 px-4" data-aos="fade-down" data-aos-duration="1000">
            <div class="inline-block bg-white text-[#013214] px-6 py-1.5 rounded-full text-sm font-bold mb-6">
                <?php echo esc_html( $attributes['badgeText'] ); ?>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">
                <?php echo esc_html( $attributes['title'] ); ?>
            </h2>
            <p class="text-white/70 max-w-2xl mx-auto text-lg leading-relaxed font-semibold">
                <?php echo esc_html( $attributes['description'] ); ?>
            </p>
        </div>

        <!-- Carousel -->
        <div class="swiper swiper-container pb-16" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <div class="swiper-wrapper">
                <?php foreach ( $courses as $course ) : ?>
                    <div class="swiper-slide">
                        <div class="course-card relative group cursor-pointer bg-black/40 backdrop-blur-sm border border-white/10">
                            <!-- Floating Button -->
                            <div class="absolute bottom-0 left-0 w-12 h-12 bg-[#bef264] rounded-full flex items-center justify-center text-[#013214] shadow-[0_4px_12px_rgba(163,230,53,0.4)] z-10 hover:scale-105 transition-transform">
                                <i class="fas fa-arrow-up text-lg rotate-[-45deg]"></i>
                            </div>
                            <div class="relative h-64">
                                <img src="<?php echo esc_url( $course['image'] ); ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" alt="<?php echo esc_attr( $course['title'] ); ?>">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-14 h-14 rounded-full border border-white/60 flex items-center justify-center text-white backdrop-blur-[2px] hover:bg-white/20 transition-colors">
                                        <i class="fas fa-play text-lg ml-1"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8 pt-10 text-right">
                                <h3 class="text-white font-bold text-xl mb-4 leading-normal">
                                    <?php echo esc_html( $course['title'] ); ?>
                                </h3>
                                <div class="flex-row-reverse flex items-center justify-end gap-5 text-white/70 text-xs font-semibold">
                                    <div class="flex items-center gap-2">
                                        <span><?php echo esc_html( $course['duration'] ); ?></span>
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span><?php echo esc_html( $course['users'] ); ?></span>
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination mt-12"></div>
        </div>

        <!-- Footer Button -->
        <div class="text-center mt-12">
            <a href="#" class="bg-white text-[#013214] hover:bg-[#A3E635] hover:text-[#013214] transition-colors px-12 py-4 rounded-xl font-black text-lg inline-flex items-center gap-4">
                <span><?php echo esc_html( $attributes['buttonText'] ); ?></span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</section>

<?php if ( ! is_admin() ) : ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.swiper-container', {
                slidesPerView: 1.2,
                spaceBetween: 16,
                centeredSlides: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 32,
                    },
                    1280: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    }
                }
            });
        }
    });
</script>
<?php endif; ?>
