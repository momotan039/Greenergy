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
    .cutted-edge {
           left: 2.4rem;
           bottom: 2rem;
           background-color: #013214;
           border-radius: 11px 30px 10px 20px;
           width: 6rem;
           height: 6rem;
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
            <div class="inline-block bg-[#229924] text-white px-6 py-1.5 rounded-full text-sm font-bold mb-6">
                <?php echo esc_html( $attributes['badgeText'] ); ?>
            </div>
            <h2 class="text-4xl md:text-5xl font-medium text-white mb-6">
                <?php echo esc_html( $attributes['title'] ); ?>
            </h2>
            <p class="text-white max-w-2xl mx-auto text-lg leading-relaxed ">
                <?php echo esc_html( $attributes['description'] ); ?>
            </p>
        </div>


        <!-- Carousel -->
        <?php
        $swiper_settings = [
            'slidesPerView' => 1.2,
            'spaceBetween' => 16,
            'centeredSlides' => false,
            'pagination' => [
                'clickable' => true,
            ],
            'breakpoints' => [
                '640' => [
                    'slidesPerView' => 2,
                    'spaceBetween' => 24,
                ],
                '1024' => [
                    'slidesPerView' => 3,
                    'spaceBetween' => 32,
                ],
                '1280' => [
                    'slidesPerView' => 3,
                    'spaceBetween' => 40,
                ]
            ]
        ];
        ?>
        <div class="swiper swiper-container js-swiper-init pb-16" 
             data-swiper-config="<?php echo esc_attr( json_encode( $swiper_settings ) ); ?>"
             data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <div class="swiper-wrapper">
                <?php foreach ( $courses as $index => $course ) : ?>
                    <div class="swiper-slide" data-aos="fade-up" data-aos-delay="<?php echo esc_attr(300 + ($index * 100)); ?>">
                        <div class="h-64 rounded-2xl relative overflow-hidden bg-cover bg-center group transition-all duration-500 hover:shadow-2xl hover:shadow-lime-400/20"
                            style="background-image: url('<?php echo esc_url( $course['image'] ); ?>');"  
                        >
                       
                            <!-- Play Button -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    <div class="w-12 h-12 rounded-full border border-white/60 flex items-center justify-center text-white backdrop-blur-[4px] bg-white/10 hover:bg-white/30 transition-all scale-75 group-hover:scale-100 duration-500">
                                        <i class="fas fa-play text-xl ml-1"></i>
                                    </div>
                                </div>
                            <!-- Course Details -->
                                <div class="absolute bottom-0 w-full text-right p-4 pt-20 bg-gradient-to-t from-black/90 to-transparent transition-all duration-300">
                                <h3 class="text-white font-medium text-base w-3/4 mb-4 leading-normal">
                                    <?php echo esc_html( $course['title'] ); ?>
                                </h3>
                                <div class="flex flex items-center gap-5 text-white text-sm font-normal">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo esc_html( $course['duration'] ); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo esc_html( $course['users'] ); ?></span>
                                    </div>
                                </div>
                            </div>

                             <!-- Cutted Edge -->
                        <div class="cutted-edge absolute bottom-0 left-0 w-12 h-12 bg-white -translate-x-1/2 translate-y-1/2 rounded-lg"></div>
                        <!-- Floating Button -->
                            <div class="absolute bottom-[.5rem] left-[1rem] w-14 h-14 bg-[#bef264] rounded-full flex items-center justify-center text-[#013214] shadow-[0_4px_12px_rgba(163,230,53,0.4)] z-10 hover:scale-110 hover:rotate-[45deg] transition-all duration-300">
                                <i class="fas fa-arrow-up text-lg rotate-[-45deg]"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination !relative mt-12"></div>
        </div>

        <!-- Footer Button -->
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="500">
            <a href="#" class="bg-white text-[#013214] hover:bg-[#bef264] hover:text-[#013214] hover:scale-105 hover:shadow-xl transition-all duration-300 px-12 py-4 rounded-xl font-black text-lg inline-flex items-center gap-4 group">
                <span><?php echo esc_html( $attributes['buttonText'] ); ?></span>
                <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-2"></i>
            </a>
        </div>
    </div>
</section>

<?php if ( ! is_admin() ) : ?>

<?php endif; ?>
