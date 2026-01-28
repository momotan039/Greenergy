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
    'posts_per_page' => 8,
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
    $large_cards = array_slice($all_news, 3, 2);
    $other_small_cards = array_slice($all_news, 5, 3);
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
    $large_cards = array_fill(0, 2, $fallback_item);
    $other_small_cards = array_fill(0, 3, $fallback_item);
}

$bg_image_url = '';
if (!empty($attributes['imageId'])) {
    $bg_image_url = wp_get_attachment_image_url($attributes['imageId'], 'full');
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'bg-white py-16 px-4 flex flex-col items-center relative overflow-hidden',
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
        <span class="inline-block bg-[#E6F6EC] text-[#22C55E] font-bold px-6 py-2 rounded-full mb-4 text-lg">
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
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span><?php echo esc_html( $news['date'] ); ?></span>
                            <span class="bg-[#E6F6EC] text-[#1F2937] px-2 py-1 rounded font-semibold text-[10px]"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                        </div>
                        <h3 class="text-[#1F2937] font-bold text-base leading-snug mb-3 line-clamp-2"><?php echo esc_html( $news['title'] ); ?></h3>
                        <div class="flex items-center justify-between mt-auto">
                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-sm hover:underline">المزيد</a>
                            <div class="flex items-center text-gray-400 text-xs gap-1">
                                <span><?php echo esc_html( $news['views'] ); ?></span>
                                <i class="far fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Center Column -->
        <div class="col-span-1 lg:col-span-6 flex flex-col gap-6 order-1 lg:order-2" data-aos="fade-up" data-aos-delay="200">
            <?php 
                $delay = 300;
                foreach ( $large_cards as $news ) : 
                    $delay += 150;
            ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group relative" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="relative h-[250px] md:h-[320px] overflow-hidden">
                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-5 md:p-6">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span><?php echo esc_html( $news['date'] ); ?></span>
                            <span class="bg-[#E6F6EC] text-[#1F2937] px-3 py-1 rounded font-semibold"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                        </div>
                        <h3 class="text-[#1F2937] font-bold text-xl md:text-2xl leading-snug mb-3"><?php echo esc_html( $news['title'] ); ?></h3>
                        <div class="flex items-center justify-between mt-4">
                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-base hover:underline">المزيد</a>
                            <div class="flex items-center text-gray-400 text-sm gap-1">
                                <span><?php echo esc_html( $news['views'] ); ?></span>
                                <i class="far fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Left Column -->
        <div class="flex lg:col-span-3 flex-col gap-6 order-3 lg:order-3" data-aos="fade-right" data-aos-delay="200">
            <?php 
                $delay = 400;
                foreach ( $other_small_cards as $news ) : 
                    $delay += 100;
            ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo esc_url( $news['image'] ); ?>" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                            <span><?php echo esc_html( $news['date'] ); ?></span>
                            <span class="bg-[#E6F6EC] text-[#1F2937] px-2 py-1 rounded font-semibold text-[10px]"><?php echo esc_html( $news['tag'] ?? '' ); ?></span>
                        </div>
                        <h3 class="text-[#1F2937] font-bold text-base leading-snug mb-3 line-clamp-2"><?php echo esc_html( $news['title'] ); ?></h3>
                        <div class="flex items-center justify-between mt-auto">
                            <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="text-[#22C55E] font-bold text-sm hover:underline">المزيد</a>
                            <div class="flex items-center text-gray-400 text-xs gap-1">
                                <span><?php echo esc_html( $news['views'] ); ?></span>
                                <i class="far fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-10 text-center relative z-10">
        <a href="#" class="inline-flex items-center bg-white border border-gray-200 text-[#1F2937] font-bold py-3 px-8 rounded-[20px] shadow-sm hover:shadow-md transition-shadow">
            <span><?php echo esc_html( $attributes['buttonText'] ); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18" />
            </svg>
        </a>
    </div>
</div>
