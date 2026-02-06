<?php
/**
 * Latest News Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'badgeText'   => 'أحدث الأخبار',
    'description' => 'كن على اطلاع دائم على آخر التطورات في عالم الطاقة المتجددة، مع لمحة سريعة عن أكثر المواضيع التي يتحدث عنها الجميع.',
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
    'class' => 'bg-green-100 py-8 lg:py-20 px-4 relative',
] );

// Dynamic Data Fetching: Get news from CPT
$args = [
    'post_type'      => 'news',
    'posts_per_page' => 8,
    'status'         => 'publish',
];
$query = new WP_Query( $args );

if ( $query->have_posts() ) {
    $news_items = [];
    while ( $query->have_posts() ) {
        $query->the_post();
        $news_items[] = [
            'title'   => get_the_title(),
            'excerpt' => get_the_excerpt() ?: wp_trim_words( get_the_content(), 15 ),
            'views'   => get_post_meta( get_the_ID(), 'views', true ) ?: '0',
            'date'    => get_the_date('d/m/Y'),
            'image'   => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800&auto=format&fit=crop',
        ];
    }
    wp_reset_postdata();
} else {
    // Placeholder news items
    $news_items = [
        [
            'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
            'excerpt' => 'الإمارات تُطلق مشروعاً ضخماً للطاقة الشمس ...',
            'views' => '9,870',
            'date' => '16/08/2025',
            'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
            'excerpt' => 'الإمارات تُطلق مشروعاً ضخماً للطاقة الشمس',
            'views' => '9,870',
            'date' => '16/08/2025',
            'image' => 'https://images.unsplash.com/photo-1532601224476-15c79f2f7a51?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
            'excerpt' => 'الإمارات تُطلق مشروعاً ضخماً للطاقة الشمس',
            'views' => '9,870',
            'date' => '16/08/2025',
            'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&auto=format&fit=crop',
        ],
        [
            'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
            'excerpt' => 'الإمارات تُطلق مشروعاً ضخماً للطاقة الشمس',
            'views' => '9,870',
            'date' => '16/08/2025',
            'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&auto=format&fit=crop',
        ],
    ];
}
?>
<style>
    .swiper-container-latest {
        width: 100%;
        padding-bottom: 50px !important;
    }
</style>

<section <?php echo $wrapper_attributes; ?>>
    <?php if ( $bg_image_url ) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url( $bg_image_url ); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    <div class="max-w-[1400px] mx-auto relative z-10">
        <!-- Header -->
        <div class="text-center mb-10" data-aos="fade-down" data-aos-duration="1000">
            <div class="inline-block bg-[#229924] text-white font-bold px-6 py-2 rounded-full mb-4 text-xl">
                <?php echo esc_html( $attributes['badgeText'] ); ?>
            </div>
            <p class="text-[#656865] max-w-2xl mx-auto text-lg leading-relaxed">
                <?php echo esc_html( $attributes['description'] ); ?>
            </p>
        </div>

        <!-- Filters -->
        <div class="flex md:justify-center gap-3 mb-10  overflow-x-auto" data-aos="fade-up" data-aos-delay="200">
            <button class="bg-[#229924] min-w-max text-white px-6 py-2 rounded-lg hover:bg-[#1a7a1c] hover:scale-105 transition-all duration-300 shadow-md hover:shadow-green-500/20">كل الاخبار</button>
            <button class="bg-[#EFF2F5] min-w-max text-gray-600 px-6 py-2 rounded-lg hover:bg-green-600 hover:text-white hover:scale-105 transition-all duration-300">طاقة شمسية</button>
            <button class="bg-[#EFF2F5] min-w-max text-gray-600 px-6 py-2 rounded-lg hover:bg-green-600 hover:text-white hover:scale-105 transition-all duration-300">رياح</button>
            <button class="bg-[#EFF2F5] min-w-max text-gray-600 px-6 py-2 rounded-lg hover:bg-green-600 hover:text-white hover:scale-105 transition-all duration-300">بيئة</button>
        </div>

        <!-- Swiper Container -->
        <div class="swiper swiper-container-latest mb-12" data-aos="zoom-in" data-aos-delay="400" data-aos-duration="1000">
            <div class="swiper-wrapper">
                <?php foreach ( $news_items as $index => $news ) : ?>
                    <div class="swiper-slide h-auto group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr(300 + ($index * 100)); ?>">
                        <div class="bg-white rounded-2xl overflow-hidden cursor-pointer group hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500 h-full border border-gray-100 lg:border-none">
                            <div class="relative aspect-square overflow-hidden">
                                <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="px-2 py-4 text-right group-hover:bg-green-600">
                                <div class="self-stretch inline-flex justify-end items-start gap-4">
                                        <div
                                            class="group-hover:text-white flex-1 text-right justify-start text-neutral-800 text-sm leading-5">
                                            الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠
                                          
                                        </div>
                                          <svg class="w-6 h-4 inline" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
                                            </svg>
                                    </div>
                                <p class="group-hover:text-white text-gray-600 text-xs md:text-sm mb-3"><?php echo esc_html( $news['excerpt'] ); ?></p>
                                <div class="flex items-center justify-between text-[10px] md:text-xs font-bold text-gray-500 border-t border-gray-100 pt-3">
                                    <div class="flex items-center gap-1">
                                        <i class="far fa-eye group-hover:text-white"></i>
                                        <span class="group-hover:text-white"><?php echo esc_html( $news['views'] ); ?></span>
                                    </div>
                                    <div dir="ltr" class="group-hover:text-white"><?php echo esc_html( $news['date'] ); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- View All Button -->
        <div class="text-center" data-aos="fade-up" data-aos-delay="500">
            <a href="#" class="inline-flex items-center gap-3 bg-white border border-gray-200 text-gray-800 px-8 py-3 rounded-xl font-bold hover:bg-[#229924] hover:text-white hover:border-[#229924] hover:shadow-lg hover:scale-105 transition-all duration-300 group">
                <span>عرض الكل</span>
                <i class="fas fa-arrow-left text-sm transition-transform group-hover:-translate-x-1"></i>
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.swiper-container-latest', {
                slidesPerView: 2,
                spaceBetween: 16,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 24
                    }
                }
            });
        }
    });
</script>
