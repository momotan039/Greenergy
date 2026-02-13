<?php
$attributes = wp_parse_args($attributes ?? [], [
    'badgeText'                 => 'الأكثر قراءة',
    'description'               => 'استكشف أبرز الأخبار التي حظيت باهتمام القراء في عالم الطاقة المتجددة، من المشاريع العملاقة إلى أحدث الابتكارات البيئية.',
    'buttonText'                => 'عرض جميع الاخبار',
    'selectionMode'             => 'auto',
    'leftCount'                 => 3,
    'rightCount'                => 3,
    'centerTopCount'            => 2,
    'centerBottomCount'         => 2,
    'selectedPostsLeft'         => [],
    'selectedPostsRight'        => [],
    'selectedPostsCenterTop'    => [],
    'selectedPostsCenterBottom' => [],
]);

/**
 * Helper to fetch news items by IDs or by Most Read
 */
if (!function_exists('greenergy_get_most_read_items')) {
    function greenergy_get_most_read_items($count, $selection_mode, $selected_posts)
    {
        $args = [
            'post_type'      => 'news',
            'posts_per_page' => (int) $count,
            'post_status'    => 'publish',
        ];

        if ($selection_mode === 'manual' && !empty($selected_posts)) {
            $post_ids = is_array($selected_posts[0] ?? null)
                ? wp_list_pluck($selected_posts, 'id')
                : $selected_posts;

            $args['post__in'] = $post_ids;
            $args['orderby']  = 'post__in';
            $args['posts_per_page'] = count($post_ids);
        } else {
            $args['meta_key'] = '_total_views_sort';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
        }

        $query = new WP_Query($args);
        $items = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id  = get_the_ID();
                $category = get_the_terms($post_id, 'news_category')[0] ?? null;
                $items[] = [
                    'category' => $category->name ?? 'أخبار',
                    'tag'      => $category->name ?? 'طاقة',
                    'title'    => get_the_title(),
                    'views'    => Greenergy_Post_Views::get_views($post_id),
                    'date'     => get_the_date('d/m/Y'),
                    'image'    => get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/800X800',
                    'link'     => get_permalink()
                ];
            }
            wp_reset_postdata();
        }

        // Fallback
        if (empty($items) && $count > 0) {
            // In manual mode, if we explicitly asked for 0 items OR we are in manual with empty selected posts, don't show items
            if ($selection_mode === 'manual' && empty($selected_posts)) {
                return [];
            }

            $items = array_fill(0, (int) $count, [
                'title' => 'افتتاح مزرعة رياح بحرية هي الأكبر في شمال إفريقيا',
                'tag'   => 'الطاقة الشمسية',
                'date'  => '16/08/2025',
                'views' => '9,870',
                'image' => 'https://placehold.co/800X800',
                'link'  => '#'
            ]);
        }

        return $items;
    }
}

// Fetch sections
if ($attributes['selectionMode'] === 'manual') {
    $news_sections = [
        'small_cards'       => greenergy_get_most_read_items(0, 'manual', $attributes['selectedPostsLeft']),
        'large_cards_1'     => greenergy_get_most_read_items(0, 'manual', $attributes['selectedPostsCenterTop']),
        'large_cards_2'     => greenergy_get_most_read_items(0, 'manual', $attributes['selectedPostsCenterBottom']),
        'other_small_cards' => greenergy_get_most_read_items(0, 'manual', $attributes['selectedPostsRight']),
    ];
} else {
    // Auto Mode: Fetch all needed items in one query to avoid duplicates
    $total_needed = $attributes['leftCount'] + $attributes['centerTopCount'] + $attributes['centerBottomCount'] + $attributes['rightCount'];
    $all_auto_news = greenergy_get_most_read_items($total_needed, 'auto', []);

    $news_sections = [
        'small_cards'       => array_slice($all_auto_news, 0, $attributes['leftCount']),
        'large_cards_1'     => array_slice($all_auto_news, $attributes['leftCount'], $attributes['centerTopCount']),
        'large_cards_2'     => array_slice($all_auto_news, $attributes['leftCount'] + $attributes['centerTopCount'], $attributes['centerBottomCount']),
        'other_small_cards' => array_slice($all_auto_news, $attributes['leftCount'] + $attributes['centerTopCount'] + $attributes['centerBottomCount'], $attributes['rightCount']),
    ];
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'py-16 px-4 flex flex-col items-center relative overflow-hidden bg-green-100',
]);

// Reusable card rendering function
if (!function_exists('render_news_card')) {
    function render_news_card($news, $size = 'small', $delay = 0)
    {
        $is_large = $size === 'large';
        $title_class = $is_large ? 'text-xl md:text-2xl' : 'text-base';
        $padding_class = $is_large ? 'p-5 md:p-6' : 'p-4';
        $tag_class = $is_large ? 'px-3 py-1' : 'px-2 py-1 text-[10px]';
?>
        <a href="<?php echo esc_url($news['link'] ?? '#'); ?>" class="block h-full group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr($delay); ?>">
            <div class="flex flex-col h-full hover:cursor-pointer bg-white hover:bg-green-600 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500">
                <div class="relative <?php echo $is_large ? 'h-[220px] md:h-[320px]' : 'h-[160px] md:h-[200px]'; ?> overflow-hidden shrink-0">
                    <img src="<?php echo esc_url($news['image']); ?>" alt="<?php echo esc_attr($news['title']); ?>"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="<?php echo $padding_class; ?> flex flex-col flex-1">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                        <span class="bg-[#E6F6EC] text-[#1F2937] <?php echo $tag_class; ?> rounded font-semibold">
                            <?php echo esc_html($news['tag'] ?? ''); ?>
                        </span>
                        <span class="group-hover:text-white"><?php echo esc_html($news['date']); ?></span>
                    </div>
                    <h3 class="text-[#1F2937] group-hover:text-white  font-bold <?php echo $title_class; ?> leading-snug mb-3 line-clamp-2">
                        <?php echo esc_html($news['title']); ?>
                    </h3>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex items-center text-gray-400 text-<?php echo $is_large ? 'sm' : 'xs'; ?> gap-1">
                            <i class="far fa-eye"></i>
                            <span class="group-hover:text-white"><?php echo esc_html($news['views']); ?></span>
                        </div>
                        <span class="group-hover:text-white text-[#22C55E] font-bold text-<?php echo $is_large ? 'base' : 'sm'; ?> hover:underline">المزيد</span>
                    </div>
                </div>
            </div>
        </a>
    <?php
    }
}

// Reusable swiper slider function
if (!function_exists('render_swiper_slider')) {
    function render_swiper_slider($cards, $slider_id, $autoplay_delay = 5000)
    {
    ?>
        <div class="relative h-full">
            <div class="swiper js-swiper-init w-full h-full rounded-2xl overflow-hidden"
                data-swiper-config='{"navigation": {"nextEl": ".swiper-button-next-<?php echo $slider_id; ?>", "prevEl": ".swiper-button-prev-<?php echo $slider_id; ?>"}, "autoplay": {"delay": <?php echo $autoplay_delay; ?>}}'>
                <div class="swiper-wrapper h-full">
                    <?php foreach ($cards as $news) : ?>
                        <div class="swiper-slide h-auto">
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
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-10 text-center relative z-10" data-aos="fade-down" data-aos-duration="1000">
        <span class="inline-block bg-[#229924] text-white px-6 py-2 pb-3 rounded-full mb-4 text-2xl">
            <?php echo esc_html($attributes['badgeText']); ?>
        </span>
        <h2 class="text-[#333333] text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            <?php echo esc_html($attributes['description']); ?>
        </h2>
    </div>

    <!-- Main Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 text-right relative z-10 lg:px-12">

        <!-- Right Column (Small Cards) -->
        <div class="flex md:col-span-3 flex-col gap-6 order-2 md:order-1" data-aos="fade-left" data-aos-delay="200">
            <?php
            $delay = 400;
            foreach ($news_sections['other_small_cards'] as $news) {
                render_news_card($news, 'small', $delay);
                $delay += 100;
            }
            ?>
        </div>

        <!-- Center Column (Sliders) -->
        <div class="col-span-1 md:col-span-6 flex flex-col gap-6 order-1 md:order-2" data-aos="fade-up" data-aos-delay="200">
            <?php if (!empty($news_sections['large_cards_1'])) render_swiper_slider($news_sections['large_cards_1'], 'top', 5000); ?>
            <?php if (!empty($news_sections['large_cards_2'])) render_swiper_slider($news_sections['large_cards_2'], 'bottom', 6000); ?>
        </div>

        <!-- Left Column (Small Cards) -->
        <div class="flex md:col-span-3 flex-col gap-6 order-3" data-aos="fade-right" data-aos-delay="200">
            <?php
            $delay = 400;
            foreach ($news_sections['small_cards'] as $news) {
                render_news_card($news, 'small', $delay);
                $delay += 100;
            }
            ?>
        </div>
    </div>

    <!-- Button -->
    <div class="mt-10 text-center relative z-10" data-aos="fade-up" data-aos-delay="500">
        <?php
        $news_page = get_page_by_title('الاخبار');
        $view_all_url = $news_page ? get_permalink($news_page) : home_url('/news');
        ?>
        <a href="<?php echo esc_url($view_all_url); ?>" class="inline-flex items-center bg-white border border-gray-200 text-[#1F2937] font-bold py-3 px-8 rounded-[20px] shadow-sm  hover:text-green-600 hover:border-[#229924] hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <span><?php echo esc_html($attributes['buttonText']); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18" />
            </svg>
        </a>
    </div>
</div>