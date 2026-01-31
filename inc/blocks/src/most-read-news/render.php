<?php
$attributes = wp_parse_args( $attributes ?? [], [
    'badgeText'     => 'الأكثر قراءة',
    'description'   => 'استكشف أبرز الأخبار التي حظيت باهتمام القراء في عالم الطاقة المتجددة، من المشاريع العملاقة إلى أحدث الابتكارات البيئية.',
    'buttonText'    => 'عرض جميع الاخبار',
    'selectionMode' => 'auto',
    'selectedPosts' => [],
    'imageId'       => 0,
] );

// News Query
$news_args = [
    'post_type'      => 'news',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
];

if ( $attributes['selectionMode'] === 'manual' && !empty($attributes['selectedPosts']) ) {
    $news_args['post__in'] = $attributes['selectedPosts'];
    $news_args['orderby'] = 'post__in';
} else {
    // Top read by views (meta_key: post_views_count)
    $news_args['meta_key'] = 'post_views_count';
    $news_args['orderby']  = 'meta_value_num';
    $news_args['order']    = 'DESC';
}

$query = new WP_Query( $news_args );

if ( $query->have_posts() ) {
    $all_news = [];
    while ( $query->have_posts() ) {
        $query->the_post();
        $all_news[] = [
            'category' => get_the_terms( get_the_ID(), 'news_category' ) ? get_the_terms( get_the_ID(), 'news_category' )[0]->name : 'أخبار',
            'tag'      => '# ' . (get_the_terms( get_the_ID(), 'news_category' ) ? get_the_terms( get_the_ID(), 'news_category' )[0]->name : 'طاقة'),
            'title'    => get_the_title(),
            'views'    => get_post_meta( get_the_ID(), 'post_views_count', true ) ?: '0',
            'date'     => get_the_date('d/m/Y'),
            'image'    => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: 'https://images.unsplash.com/photo-1549439602-43ebca2327af?w=400&auto=format&fit=crop',
            'link'     => get_permalink()
        ];
    }
    wp_reset_postdata();

    // Distribute
    $small_cards = array_slice($all_news, 0, 3);
    $large_cards_1 = array_slice($all_news, 3, 2);
    $large_cards_2 = array_slice($all_news, 5, 2);
    $other_small_cards = array_slice($all_news, 7, 3);
} else {
    // Fallback news items
    $fallback_item = [
        'title' => 'افتتاح مزرعة رياح بحرية هي الأكبر في شمال إفريقيا',
        'tag' => '# الطاقة الشمسية',
        'date' => '16/08/2025',
        'views' => '9,870',
        'image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop',
        'link' => '#'
    ];
    $small_cards = array_fill(0, 3, $fallback_item);
    $large_cards_1 = array_fill(0, 2, $fallback_item);
    $large_cards_2 = array_fill(0, 2, $fallback_item);
    $other_small_cards = array_fill(0, 3, $fallback_item);
}

$bg_image_url = '';
if (!empty($attributes['imageId'])) {
    $bg_image_url = wp_get_attachment_image_url($attributes['imageId'], 'full');
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'py-16 px-4 flex flex-col items-center relative overflow-hidden bg-green-100',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ($bg_image_url) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url($bg_image_url); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="max-w-7xl mx-auto mb-10 text-center relative z-10" data-aos="fade-down" data-aos-duration="1000">
        <span class="inline-block bg-[#E6F6EC] text-[#22C55E] px-6 py-2 rounded-full mb-4 text-2xl">
            <?php echo esc_html( $attributes['badgeText'] ); ?>
        </span>
        <h2 class="text-[#333333] text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            <?php echo esc_html( $attributes['description'] ); ?>
        </h2>
    </div>

    <!-- Main Grid Layout -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 text-right relative z-10">
        <!-- Right Column -->
        <div class="flex lg:col-span-3 flex-col gap-6 order-2 lg:order-1" data-aos="fade-left" data-aos-delay="200">
            <?php 
                $delay = 400;
                foreach ( $small_cards as $news ) : 
                    $delay += 100;
            ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500 group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span class="bg-[#E6F6EC] text-[#1F2937] px-2 py-1 rounded font-semibold text-[10px]"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                            <span><?php echo esc_html( $news['date'] ); ?></span>
                        </div>
                        <h3 class="text-[#1F2937] font-bold text-base leading-snug mb-3 line-clamp-2"><?php echo esc_html( $news['title'] ); ?></h3>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-gray-400 text-xs gap-1">
                                <i class="far fa-eye"></i>
                                <span><?php echo esc_html( $news['views'] ); ?></span>
                            </div>
                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-sm hover:underline">المزيد</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Center Column -->
        <div class="col-span-1 lg:col-span-6 flex flex-col gap-6 order-1 lg:order-2 h-full" data-aos="fade-up" data-aos-delay="200">
             
             <!-- Top Slider -->
             <div class="relative flex-1">
                 <div class="swiper most-read-swiper-top w-full h-full rounded-2xl overflow-hidden">
                    <div class="swiper-wrapper">
                        <?php foreach ( $large_cards_1 as $news ) : ?>
                            <div class="swiper-slide h-auto">
                                <div class="bg-white h-full rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-1 transition-all duration-500 group relative flex flex-col">
                                    <div class="relative flex-1 min-h-[250px] overflow-hidden">
                                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <div class="p-5 md:p-6 mt-auto">
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                            <span class="bg-[#E6F6EC] text-[#1F2937] px-3 py-1 rounded font-semibold"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                                            <span><?php echo esc_html( $news['date'] ); ?></span>
                                        </div>
                                        <h3 class="text-[#1F2937] font-bold text-xl md:text-2xl leading-snug mb-3"><?php echo esc_html( $news['title'] ); ?></h3>
                                        <div class="flex items-center justify-between mt-4">
                                            <div class="flex items-center text-gray-400 text-sm gap-1">
                                                <i class="far fa-eye"></i>
                                                <span><?php echo esc_html( $news['views'] ); ?></span>
                                            </div>
                                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-base hover:underline">المزيد</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Custom Navigation Buttons Top -->
                <div class="swiper-button-next-top absolute top-1/2 -left-3 transform -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg hover:bg-[#1a9945] transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="swiper-button-prev-top absolute top-1/2 -right-3 transform -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg hover:bg-[#1a9945] transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </div>
             </div>

             <!-- Bottom Slider -->
             <div class="relative flex-1">
                 <div class="swiper most-read-swiper-bottom w-full h-full rounded-2xl overflow-hidden">
                    <div class="swiper-wrapper">
                        <?php foreach ( $large_cards_2 as $news ) : ?>
                            <div class="swiper-slide h-auto">
                                <div class="bg-white h-full rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-1 transition-all duration-500 group relative flex flex-col">
                                    <div class="relative flex-1 min-h-[250px] overflow-hidden">
                                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <div class="p-5 md:p-6 mt-auto">
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                            <span class="bg-[#E6F6EC] text-[#1F2937] px-3 py-1 rounded font-semibold"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                                            <span><?php echo esc_html( $news['date'] ); ?></span>
                                        </div>
                                        <h3 class="text-[#1F2937] font-bold text-xl md:text-2xl leading-snug mb-3"><?php echo esc_html( $news['title'] ); ?></h3>
                                        <div class="flex items-center justify-between mt-4">
                                            <div class="flex items-center text-gray-400 text-sm gap-1">
                                                <i class="far fa-eye"></i>
                                                <span><?php echo esc_html( $news['views'] ); ?></span>
                                            </div>
                                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-base hover:underline">المزيد</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Custom Navigation Buttons Bottom -->
                <div class="swiper-button-next-bottom absolute top-1/2 -left-3 transform -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg hover:bg-[#1a9945] transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="swiper-button-prev-bottom absolute top-1/2 -right-3 transform -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg hover:bg-[#1a9945] transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </div>
             </div>

        </div>

        <!-- Left Column -->
        <div class="flex lg:col-span-3 flex-col gap-6 order-3 lg:order-3" data-aos="fade-right" data-aos-delay="200">
            <?php 
                $delay = 400;
                foreach ( $other_small_cards as $news ) : 
                    $delay += 100;
            ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500 group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span class="bg-[#E6F6EC] text-[#1F2937] px-2 py-1 rounded font-semibold text-[10px]"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                            <span><?php echo esc_html( $news['date'] ); ?></span>
                        </div>
                        <h3 class="text-[#1F2937] font-bold text-base leading-snug mb-3 line-clamp-2"><?php echo esc_html( $news['title'] ); ?></h3>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-gray-400 text-xs gap-1">
                                <i class="far fa-eye"></i>
                                <span><?php echo esc_html( $news['views'] ); ?></span>
                            </div>
                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-sm hover:underline">المزيد</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-10 text-center relative z-10" data-aos="fade-up" data-aos-delay="500">
        <a href="#" class="inline-flex items-center bg-white border border-gray-200 text-[#1F2937] font-bold py-3 px-8 rounded-[20px] shadow-sm hover:bg-[#229924] hover:text-white hover:border-[#229924] hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <span><?php echo esc_html( $attributes['buttonText'] ); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18" />
            </svg>
        </a>
    </div>
</div>
<?php if ( ! is_admin() ) : ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            // Top Slider
            new Swiper('.most-read-swiper-top', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next-top',
                    prevEl: '.swiper-button-prev-top',
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
            });
            
            // Bottom Slider
            new Swiper('.most-read-swiper-bottom', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next-bottom',
                    prevEl: '.swiper-button-prev-bottom',
                },
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
            });
        }
    });
</script>
<?php endif; ?>
