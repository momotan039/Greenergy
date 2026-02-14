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

$attributes = wp_parse_args($attributes ?? [], [
    'badgeText'          => 'أحدث الأخبار',
    'description'        => 'كن على اطلاع دائم على آخر التطورات في عالم الطاقة المتجددة، مع لمحة سريعة عن أكثر المواضيع التي يتحدث عنها الجميع.',
    'imageId'            => 0,
    'imageUrl'           => '',
    'selectionMode'      => 'dynamic',
    'selectedCategories' => [],
]);

$bg_image_url = $attributes['imageUrl'];
if (! empty($attributes['imageId'])) {
    $lib_url = wp_get_attachment_image_url($attributes['imageId'], 'full');
    if ($lib_url) {
        $bg_image_url = $lib_url;
    }
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'bg-green-100 py-8 lg:py-20 px-4 relative',
]);

// Categories for filter
$category_args = [
    'taxonomy'   => 'news_category',
    'hide_empty' => true,
    'number'     => 10,
];

if ($attributes['selectionMode'] === 'manual' && !empty($attributes['selectedCategories'])) {
    $category_args['include'] = $attributes['selectedCategories'];
    $category_args['orderby'] = 'include';
}

$categories = get_terms($category_args);

// Initial Query
$args = [
    'post_type'      => 'news',
    'posts_per_page' => 8,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
];

$query = new WP_Query($args);
?>
<style>
    .swiper-container-latest {
        width: 100%;
        padding-bottom: 50px !important;
    }
</style>

<section <?php echo $wrapper_attributes; ?>>
    <?php if ($bg_image_url) : ?>
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <img src="<?php echo esc_url($bg_image_url); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>
    <div class="max-w-[1400px] mx-auto relative z-10">
        <!-- Header -->
        <div class="text-center mb-10" data-aos="fade-down" data-aos-duration="1000">
            <div class="inline-block bg-[#229924] text-white font-bold px-6 py-2 pb-3 rounded-full mb-4 text-xl">
                <?php echo esc_html($attributes['badgeText']); ?>
            </div>
            <p class="text-[#656865] max-w-2xl mx-auto text-lg leading-relaxed">
                <?php echo esc_html($attributes['description']); ?>
            </p>
        </div>

        <!-- Filters -->
        <div style="scrollbar-width: none;" class="flex md:justify-center gap-3 mb-10 overflow-x-auto overflow-y-hidden js-latest-news-filters" data-aos="fade-up" data-aos-delay="200">
            <button class="bg-[#229924] min-w-max text-white px-6 py-2 rounded-lg hover:bg-[#1a7a1c] hover:scale-105 transition-all duration-300 shadow-md hover:shadow-green-500/20 active" data-category="all">كل الاخبار</button>
            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                <?php foreach ($categories as $cat) : ?>
                    <button class="bg-[#EFF2F5] min-w-max text-gray-600 px-6 py-2 rounded-lg hover:bg-green-600 hover:text-white hover:scale-105 transition-all duration-300"
                        data-category="<?php echo esc_attr($cat->slug); ?>"
                        data-term-id="<?php echo esc_attr($cat->term_id); ?>">
                        <?php echo esc_html($cat->name); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>


        <!-- Swiper Container -->
        <div class="swiper swiper-container-latest mb-12 js-latest-news-slider" data-aos="zoom-in" data-aos-delay="400" data-aos-duration="1000">
            <div class="swiper-wrapper js-latest-news-container">
                <?php
                if ($query->have_posts()) :
                    $index = 0;
                    while ($query->have_posts()) : $query->the_post();
                        $item = [
                            'title'     => get_the_title(),
                            'excerpt'   => get_the_excerpt() ?: wp_trim_words(get_the_content(), 15),
                            'date'      => get_the_date('d/m/Y'),
                            'views'     => Greenergy_Post_Views::get_views(get_the_ID()),
                            'image'     => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://placehold.co/800X800',
                            'permalink' => get_permalink(),
                        ];
                ?>
                        <div class="swiper-slide h-auto group" data-aos="fade-up" data-aos-delay="<?php echo esc_attr(300 + ($index * 100)); ?>">
                            <?php get_template_part('templates/components/news-card-grid', null, ['item' => $item]); ?>
                        </div>
                    <?php
                        $index++;
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="p-12 text-center w-full bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                        <p class="text-neutral-500"><?php _e('لا توجد أخبار متوفرة.', 'greenergy'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- View All Button -->
        <div class="text-center" data-aos="fade-up" data-aos-delay="500">
            <?php
            $news_page = get_page_by_path('الاخبار') ?: get_page_by_title('الاخبار');
            $view_all_url = $news_page ? get_permalink($news_page) : home_url('/news');
            ?>
            <a href="<?php echo esc_url($view_all_url); ?>" class="js-latest-news-view-all inline-flex items-center gap-3 bg-white border border-gray-200 text-gray-800 px-8 py-3 rounded-xl font-bold hover:bg-[#229924] hover:text-white hover:border-[#229924] hover:shadow-lg hover:scale-105 transition-all duration-300 group">
                <span>عرض الكل</span>
                <i class="fas fa-arrow-left text-sm transition-transform group-hover:-translate-x-1"></i>
            </a>
        </div>
    </div>
</section>

<script>
    (function() {
        const initSwiper = () => {
            if (typeof Swiper !== 'undefined') {
                document.querySelectorAll('.swiper-container-latest').forEach(el => {
                    if (el.swiper) return; // already initialized
                    new Swiper(el, {
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
                });
            }
        };

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initSwiper();
        } else {
            document.addEventListener('DOMContentLoaded', initSwiper);
        }

        // Try again after a short delay to catch dynamic updates in editor
        setTimeout(initSwiper, 500);
        setTimeout(initSwiper, 2000);
    })();
</script>