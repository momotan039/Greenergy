<?php

/**
 * News Grid Block Template – Improved
 *
 * @param array $attributes
 */

$attributes = wp_parse_args($attributes ?? [], [
    'count'           => 6,   // عدد الأخبار الافتراضي
    'offset'          => 0,
    'title'           => 'اخبار اخرى',
    'selectionMode'   => 'dynamic',
    'selectedPosts'   => [],
    'queryCategories' => [],
]);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'self-stretch flex flex-col justify-start items-center gap-3',
]);

$args = [
    'post_type'      => 'news',
    'posts_per_page' => (int) $attributes['count'],
    'offset'         => (int) $attributes['offset'],
    'post_status'    => 'publish',
    'orderby'        => 'rand',
];

$tax_query = [];

// 1️⃣ استبعاد الخبر الحالي وتصنيفه في single-news
if (is_singular('news')) {
    $current_post_id = get_the_ID();
    $args['post__not_in'] = [$current_post_id];

    $terms = get_the_terms($current_post_id, 'news_category');
    if ($terms && !is_wp_error($terms)) {
        $term_slugs = wp_list_pluck($terms, 'slug');
        $tax_query[] = [
            'taxonomy' => 'news_category',
            'field'    => 'slug',
            'terms'    => $term_slugs,
            'operator' => 'NOT IN',
        ];
    }
}

// 2️⃣ Apply block attribute category filter (اختياري)
if (!empty($attributes['queryCategories'])) {
    $tax_query[] = [
        'taxonomy' => 'news_category',
        'field'    => 'term_id',
        'terms'    => $attributes['queryCategories'],
        'operator' => 'IN',
    ];
}

// 3️⃣ Apply URL parameter filter (استبعاد تصنيف محدد من GET)
if (isset($_GET['news_cat']) && !empty($_GET['news_cat'])) {
    $tax_query[] = [
        'taxonomy' => 'news_category',
        'field'    => 'slug',
        'terms'    => [rawurldecode(sanitize_text_field($_GET['news_cat']))],
        'operator' => 'NOT IN',
    ];

    // إعادة ضبط offset لأنه تم استبعاد بعض الأخبار
    $args['offset'] = 0;
}

// 4️⃣ ضبط relation لو أكثر من شرط
if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}

if (!empty($tax_query)) {
    $args['tax_query'] = $tax_query;
}

// 5️⃣ إذا وضع المستخدم اختيار يدوي
if ($attributes['selectionMode'] === 'manual' && !empty($attributes['selectedPosts'])) {
    $args['post__in'] = $attributes['selectedPosts'];
    $args['orderby'] = 'post__in';
}

$query = new WP_Query($args);


// Fix Pagination Calculation with Offset
if (isset($attributes['offset']) && $attributes['offset'] > 0) {
    $found_posts = $query->found_posts;
    $initial_offset = (int) $attributes['offset'];
    $effective_total = max(0, $found_posts - $initial_offset);
    $query->max_num_pages = ceil($effective_total / $attributes['count']);
}


?>
<div <?php echo $wrapper_attributes; ?>>
    <!-- Header -->
    <div class="self-stretch inline-flex justify-center items-center gap-6">
        <div class="justify-self-center max-w-max bg-[#229924] text-white font-bold px-6 py-2 pb-3 rounded-full mb-4 text-xl max-sm:text-lg">
            <?php echo esc_html($attributes['title']); ?>
        </div>
    </div>

    <!-- Carousel -->
    <div class="w-full relative px-4">
        <div class="swiper news-grid-swiper w-full rounded-lg overflow-hidden pb-12">
            <div class="swiper-wrapper">
                <?php
                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        // Use the template part inside specific slider wrapper
                ?>
                        <div class="swiper-slide">
                            <?php get_template_part('template-parts/content-news-card', null, ['wrapper_class' => 'w-full']); ?>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    // Fallback Content
                    $mock_grid = [
                        [
                            'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
                            'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة',
                            'date' => '08/08/2025',
                            'views' => '9,870',
                            'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                            'cat' => 'الطاقة_الشمسية',
                        ],
                        [
                            'title' => 'مشروع طاقة الرياح في خليج السويس يحقق أرقاماً قياسية',
                            'excerpt' => 'سجلت محطة طاقة الرياح الجديدة في خليج السويس معدلات إنتاج غير مسبوقة',
                            'date' => '05/08/2025',
                            'views' => '7,200',
                            'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                            'cat' => 'طاقة_رياح',
                        ],
                        [
                            'title' => 'المغرب يتصدر الدول العربية في مؤشر الطاقة المتجددة',
                            'excerpt' => 'احتل المغرب المرتبة الأولى عربياً في مؤشر جاذبية الدول للطاقة المتجددة',
                            'date' => '04/08/2025',
                            'views' => '6,500',
                            'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                            'cat' => 'استدامة',
                        ],
                        [
                            'title' => 'طاقة المستقبل: الهيدروجين الأخضر',
                            'excerpt' => 'استثمارات ضخمة في مجال الهيدروجين الأخضر في منطقة الشرق الأوسط',
                            'date' => '01/08/2025',
                            'views' => '5,100',
                            'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                            'cat' => 'تكنولوجيا',
                        ]
                    ];

                    foreach ($mock_grid as $item) :
                    ?>
                        <div class="swiper-slide">
                            <div class="group hover:bg-green-600 relative  w-full bg-neutral-50 rounded-lg inline-flex flex-col justify-start items-center overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-[1.03]">
                                <a href="#" class="self-stretch h-60 p-4 bg-cover bg-center block" style="background-image: url('<?php echo esc_url($item['image']); ?>');">
                                </a>
                                <a href="" class="absolute top-0 left-0 w-full h-full"></a>
                                <div class="self-stretch p-2 flex flex-col justify-start  gap-2">
                                    <div class="self-stretch flex flex-col justify-start gap-4">
                                        <div class="self-stretch inline-flex justify-end items-start gap-4">
                                            <a href="#" class="group-hover:text-white flex-1 text-right justify-start text-neutral-800 text-sm leading-5 hover:text-green-700 transition-colors line-clamp-2">
                                                <?php echo esc_html($item['title']); ?>
                                            </a>
                                            <svg class="w-6 h-4 inline" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
                                            </svg>
                                        </div>
                                        <div class="group-hover:text-white self-stretch text-right justify-start text-neutral-800 text-sm font-normal leading-5 line-clamp-2">
                                            <?php echo esc_html($item['excerpt']); ?>
                                        </div>
                                    </div>

                                    <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto pt-2">
                                        <div class="group-hover:text-white text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                                            <?php echo esc_html($item['date']); ?>
                                        </div>
                                        <div class="flex justify-start items-center gap-1.5">
                                            <div class="text-right justify-start text-neutral-950 text-sm font-normal flex items-center gap-1">
                                                <svg class="w-4 h-4 inline" aria-hidden="true">
                                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye.svg"></use>
                                                </svg>
                                                <span class="group-hover:text-white"><?php echo esc_html($item['views']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                endif;
                ?>
            </div>
        </div>
        <!-- Pagination for swiper -->
        <div class="swiper-pagination !relative mt-4"></div>


    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiperContainers = document.querySelectorAll('.news-grid-swiper');

        swiperContainers.forEach(container => {
            const paginationEl = container.parentElement.querySelector('.swiper-pagination');

            if (typeof Swiper !== 'undefined') {
                new Swiper(container, {
                    slidesPerView: 1.2,
                    spaceBetween: 16,
                    centeredSlides: false,
                    autoplay: false,
                    pagination: {
                        el: paginationEl,
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
                            spaceBetween: 32,
                        }
                    },
                    observer: true,
                    observeParents: true,
                });
            }
        });
    });
</script>