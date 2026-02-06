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
    $news_args['meta_key'] = 'post_views_count';
    $news_args['orderby']  = 'meta_value_num';
    $news_args['order']    = 'DESC';
}

$query = new WP_Query( $news_args );

// Process news items
$all_news = [];
if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
        $query->the_post();
        $category = get_the_terms( get_the_ID(), 'news_category' )[0] ?? null;
        $all_news[] = [
            'category' => $category->name ?? 'أخبار',
            'tag'      => '# ' . ($category->name ?? 'طاقة'),
            'title'    => get_the_title(),
            'views'    => get_post_meta( get_the_ID(), 'post_views_count', true ) ?: '0',
            'date'     => get_the_date('d/m/Y'),
            'image'    => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: 'https://images.unsplash.com/photo-1549439602-43ebca2327af?w=400&auto=format&fit=crop',
            'link'     => get_permalink()
        ];
    }
    wp_reset_postdata();
}

// Fallback if no posts
if ( empty($all_news) ) {
    $all_news = array_fill(0, 10, [
        'title' => 'افتتاح مزرعة رياح بحرية هي الأكبر في شمال إفريقيا',
        'tag' => '# الطاقة الشمسية',
        'date' => '16/08/2025',
        'views' => '9,870',
        'image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop',
        'link' => '#'
    ]);
}

// Distribute news
$news_sections = [
    'small_cards'       => array_slice($all_news, 0, 3),
    'large_cards_1'     => array_slice($all_news, 3, 2),
    'large_cards_2'     => array_slice($all_news, 5, 2),
    'other_small_cards' => array_slice($all_news, 7, 3),
];

$bg_image_url = !empty($attributes['imageId']) ? wp_get_attachment_image_url($attributes['imageId'], 'full') : '';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'py-16 px-4 flex flex-col items-center relative overflow-hidden bg-green-100',
]);

// Reusable card rendering function
function render_news_card($news, $size = 'small', $delay = 0) {
    $is_large = $size === 'large';
    $title_class = $is_large ? 'text-xl md:text-2xl' : 'text-base';
    $padding_class = $is_large ? 'p-5 md:p-6' : 'p-4';
    $tag_class = $is_large ? 'px-3 py-1' : 'px-2 py-1 text-[10px]';
    ?>
    <div class="h-full hover:cursor-pointer group bg-white hover:bg-green-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500" 
         data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
        <div class="relative aspect-<?php echo $is_large ? '[3/2]' : '[4/3]'; ?> overflow-hidden">
            <img src="<?php echo esc_url($news['image']); ?>" alt="<?php echo esc_attr($news['title']); ?>" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        <div class="<?php echo $padding_class; ?>">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span class="bg-[#E6F6EC] text-[#1F2937] <?php echo $tag_class; ?> rounded font-semibold">
                    <?php echo esc_html($news['tag'] ?? ''); ?>
                </span>
                <span class="group-hover:font-bold"><?php echo esc_html($news['date']); ?></span>
            </div>
            <h3 class="text-[#1F2937]  font-bold <?php echo $title_class; ?> leading-snug mb-3 line-clamp-2">
                <?php echo esc_html($news['title']); ?>
            </h3>
            <div class="flex items-center justify-between mt-auto">
                <div class="flex items-center text-gray-400 text-<?php echo $is_large ? 'sm' : 'xs'; ?> gap-1">
                    <i class="far fa-eye"></i>
                    <span class="group-hover:font-bold"><?php echo esc_html($news['views']); ?></span>
                </div>
                <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" 
                   class="group-hover:text-green-600 text-[#22C55E] font-bold text-<?php echo $is_large ? 'base' : 'sm'; ?> hover:underline">المزيد</a>
            </div>
        </div>
    </div>
    <?php
}

// Reusable swiper slider function
function render_swiper_slider($cards, $slider_id, $autoplay_delay = 5000) {
    ?>
    <div class="relative">
        <div class="swiper js-swiper-init w-full rounded-2xl overflow-hidden" 
             data-swiper-config='{"navigation": {"nextEl": ".swiper-button-next-<?php echo $slider_id; ?>", "prevEl": ".swiper-button-prev-<?php echo $slider_id; ?>"}, "autoplay": {"delay": <?php echo $autoplay_delay; ?>}}'>
            <div class="swiper-wrapper">
                <?php foreach ($cards as $news) : ?>
                    <div class="swiper-slide">
                        <?php render_news_card($news, 'large'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="swiper-button-next-<?php echo $slider_id; ?> absolute top-1/2 -left-3 -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg">
            <i class="fas fa-arrow-left"></i>
        </div>
        <div class="swiper-button-prev-<?php echo $slider_id; ?> absolute top-1/2 -right-3 -translate-y-1/2 z-20 w-10 h-10 bg-[#22C55E] rounded-full border-2 border-white flex items-center justify-center text-white cursor-pointer shadow-lg">
            <i class="fas fa-arrow-right"></i>
        </div>
    </div>
    <?php
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ($bg_image_url) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url($bg_image_url); ?>" alt="" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-10 text-center relative z-10" data-aos="fade-down" data-aos-duration="1000">
        <span class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full mb-4 text-2xl">
            <?php echo esc_html($attributes['badgeText']); ?>
        </span>
        <h2 class="text-[#333333] text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            <?php echo esc_html($attributes['description']); ?>
        </h2>
    </div>

    <!-- Main Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 text-right relative z-10">
        
        <!-- Left Column -->
        <div class="flex lg:col-span-3 flex-col gap-6 order-2 lg:order-1" data-aos="fade-left" data-aos-delay="200">
            <?php 
            $delay = 400;
            foreach ($news_sections['small_cards'] as $news) {
                render_news_card($news, 'small', $delay);
                $delay += 100;
            }
            ?>
        </div>

        <!-- Center Column -->
        <div class="col-span-1 lg:col-span-6 flex flex-col gap-6 order-1 lg:order-2" data-aos="fade-up" data-aos-delay="200">
            <?php render_swiper_slider($news_sections['large_cards_1'], 'top', 5000); ?>
            <?php render_swiper_slider($news_sections['large_cards_2'], 'bottom', 6000); ?>
        </div>

        <!-- Right Column -->
        <div class="flex lg:col-span-3 flex-col gap-6 order-3" data-aos="fade-right" data-aos-delay="200">
            <?php 
            $delay = 400;
            foreach ($news_sections['other_small_cards'] as $news) {
                render_news_card($news, 'small', $delay);
                $delay += 100;
            }
            ?>
        </div>
    </div>

    <!-- Button -->
    <div class="mt-10 text-center relative z-10" data-aos="fade-up" data-aos-delay="500">
        <a href="#" class="inline-flex items-center bg-white border border-gray-200 text-[#1F2937] font-bold py-3 px-8 rounded-[20px] shadow-sm  hover:text-green-600 hover:border-[#229924] hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <span><?php echo esc_html($attributes['buttonText']); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18" />
            </svg>
        </a>
    </div>
</div>