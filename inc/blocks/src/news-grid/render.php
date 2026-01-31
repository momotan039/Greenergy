<?php
/**
 * News Grid Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'count'  => 3,
    'offset' => 6,
    'title'  => 'اخبار اخرى'
] );

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'self-stretch flex flex-col justify-start items-center gap-3',
] );

// Query logic
$args = [
    'post_type'      => 'news',
    'posts_per_page' => $attributes['count'],
    'offset'         => $attributes['offset'],
    'post_status'    => 'publish',
];

// Apply Filters
if ( isset( $_GET['news_cat'] ) && ! empty( $_GET['news_cat'] ) ) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'news_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $_GET['news_cat'] ),
        ],
    ];
    $args['offset'] = 0; // Reset offset on filter
}

// Apply Sort
if ( isset( $_GET['sort'] ) ) {
    switch ( $_GET['sort'] ) {
        case 'oldest':
            $args['order'] = 'ASC';
            $args['orderby'] = 'date';
            break;
        case 'popular':
            $args['meta_key'] = 'views';
            $args['orderby'] = 'meta_value_num';
            break;
        default: // latest
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
            break;
    }
}

$query = new WP_Query( $args );

if ( $query->have_posts() ) :
?>
    <div <?php echo $wrapper_attributes; ?>>
        <!-- Header -->
        <div class="self-stretch inline-flex justify-center items-center gap-6">
            <div class="px-8 py-2.5 bg-teal-50 rounded-3xl flex justify-center items-center gap-2.5">
                <div class="w-auto px-4 h-7 text-center justify-start text-green-700 text-2xl font-medium leading-5">
                    <?php echo esc_html( $attributes['title'] ); ?>
                </div>
            </div>
        </div>

        <!-- Grid -->
        <div class="self-stretch inline-flex justify-start items-center gap-6 flex-wrap max-md:gap-2">
            <?php 
            $idx = 0;
            while ( $query->have_posts() ) : $query->the_post();
                $post_id = get_the_ID();
                $title = get_the_title();
                $excerpt = get_the_excerpt();
                $date = get_the_date( 'd/m/Y' );
                $views = get_post_meta( $post_id, 'views', true ) ?: '0';
                $thumbnail_url = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
                $permalink = get_permalink();
                
                 // Get Category
                $terms = get_the_terms( $post_id, 'news_category' );
                $cat_name = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
                
                $delay = ($idx % 4) * 100;
                $idx++;
            ?>
                <!-- Grid Item -->
                <div class="max-md:w-[calc(50%-0.5rem)] w-60 bg-neutral-50 rounded-lg inline-flex flex-col justify-start items-center overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-[1.03]" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="self-stretch h-60 p-4 bg-cover bg-center block" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                    </a>
                    
                    <div class="self-stretch p-2 flex flex-col justify-start items-end gap-2">
                        <div class="self-stretch flex flex-col justify-start items-end gap-4">
                            <div class="self-stretch inline-flex justify-end items-start gap-4">
                                <a href="<?php echo esc_url( $permalink ); ?>" class="flex-1 text-right justify-start text-neutral-800 text-sm leading-5 hover:text-green-700 transition-colors line-clamp-2">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                                <svg class="w-6 h-4 inline" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
                                </svg>
                            </div>
                            
                            <div class="self-stretch text-right justify-start text-neutral-800 text-sm font-normal leading-5 line-clamp-2">
                                <?php echo esc_html( $excerpt ); ?>
                            </div>
                            
                            <?php if ( $cat_name ) : ?>
                                <div class="h-6 px-2 bg-green-100 rounded-[100px] inline-flex justify-center items-center gap-2.5 self-start">
                                    <div class="w-auto px-2 h-11 text-right justify-start text-neutral-950 text-xs font-normal capitalize leading-10">
                                        #<?php echo esc_html( $cat_name ); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto pt-2">
                            <div class="text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                                <?php echo esc_html( $date ); ?>
                            </div>
                            <div class="flex justify-start items-center gap-1.5">
                                <div class="text-right justify-start text-neutral-950 text-sm font-normal flex items-center gap-1">
                                    <svg class="w-4 h-4 inline" aria-hidden="true">
                                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye.svg"></use>
                                    </svg>
                                    <?php echo esc_html( number_format_i18n( (int) $views ) ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php else : ?>
    <!-- Fallback Content -->
    <div <?php echo $wrapper_attributes; ?>>
        <!-- Header -->
        <div class="self-stretch inline-flex justify-center items-center gap-6">
            <div class="px-8 py-2.5 bg-teal-50 rounded-3xl flex justify-center items-center gap-2.5">
                <div class="w-auto px-4 h-7 text-center justify-start text-green-700 text-2xl font-medium leading-5">
                    <?php echo esc_html( $attributes['title'] ); ?>
                </div>
            </div>
        </div>

        <!-- Grid -->
        <div class="self-stretch inline-flex justify-start items-center gap-6 flex-wrap max-md:gap-2">
            <?php 
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
            ];

            foreach ( $mock_grid as $item ) :
            ?>
                <!-- Grid Item (Fallback) -->
                <div class="max-md:w-[calc(50%-0.5rem)] md:flex-1 l-50 rounded-lg inline-flex flex-col justify-start items-center overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <a href="#" class="self-stretch h-60 p-4 bg-cover bg-center block" style="background-image: url('<?php echo esc_url( $item['image'] ); ?>');">
                    </a>
                    
                    <div class="self-stretch p-2 flex flex-col justify-start items-end gap-2">
                        <div class="self-stretch flex flex-col justify-start items-end gap-4">
                            <div class="self-stretch inline-flex justify-end items-start gap-4">
                                <a href="#" class="flex-1 text-right justify-start text-neutral-800 text-sm leading-5 hover:text-green-700 transition-colors line-clamp-2">
                                    <?php echo esc_html( $item['title'] ); ?>
                                </a>
                                <svg class="w-6 h-4 inline" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
                                </svg>
                            </div>
                            
                            <div class="self-stretch text-right justify-start text-neutral-800 text-sm font-normal leading-5 line-clamp-2">
                                <?php echo esc_html( $item['excerpt'] ); ?>
                            </div>
                            
                            <div class="h-6 px-2 bg-green-100 rounded-[100px] inline-flex justify-center items-center gap-2.5 self-start">
                                <div class="w-auto px-2 h-11 text-right justify-start text-neutral-950 text-xs font-normal capitalize leading-10">
                                    #<?php echo esc_html( $item['cat'] ); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto pt-2">
                            <div class="text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                                <?php echo esc_html( $item['date'] ); ?>
                            </div>
                            <div class="flex justify-start items-center gap-1.5">
                                <div class="text-right justify-start text-neutral-950 text-sm font-normal flex items-center gap-1">
                                    <svg class="w-4 h-4 inline" aria-hidden="true">
                                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye.svg"></use>
                                    </svg>
                                    <?php echo esc_html( $item['views'] ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
