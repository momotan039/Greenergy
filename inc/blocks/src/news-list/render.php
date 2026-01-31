<?php
/**
 * News List Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 */

$attributes = wp_parse_args( $attributes ?? [], [
    'count'  => 5,
    'offset' => 1,
] );

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'self-stretch flex flex-col gap-2',
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
    // Reset offset if filtering because user expects to see top results
    $args['offset'] = 0; 
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
            
            $terms = get_the_terms( $post_id, 'news_category' );
            $cat_name = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
            
            $delay = ($idx % 5) * 100; // Reset delay every 5 items: 0, 100, 200, 300, 400
            $idx++;
        ?>
            <!-- News Card -->
            <div class="w-full rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-100 inline-flex justify-start items-center gap-4 overflow-hidden max-sm:flex-col hover:shadow-lg transition-all duration-300 hover:scale-[1.01]" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <a href="<?php echo esc_url( $permalink ); ?>" class="max-sm:h-60 max-sm:w-full w-36 self-stretch p-4 bg-cover bg-center rounded block" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                </a>
                
                <div class="flex-1 self-stretch pl-3 py-3 inline-flex flex-col justify-start items-end gap-1.5">
                    <div class="self-stretch flex flex-col justify-start items-end gap-4 max-sm:gap-2">
                        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                            <div class="text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                                <?php echo esc_html( $date ); ?>
                            </div>
                            <a href="<?php echo esc_url( $permalink ); ?>" class="text-right justify-start text-neutral-950 text-sm font-medium leading-5 hover:text-green-700 transition-colors">
                                <?php echo esc_html( $title ); ?>
                            </a>
                        </div>
                        
                        <div class="self-stretch text-right justify-start text-stone-500 text-xs font-normal capitalize leading-6 line-clamp-2">
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
                    
                    <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto">
                        <a href="<?php echo esc_url( $permalink ); ?>" class="text-center justify-start text-green-700 text-sm font-normal underline leading-5 hover:text-green-900">
                            المزيد
                        </a>
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
<?php else : ?>
    <!-- Fallback Content -->
    <div <?php echo $wrapper_attributes; ?>>
        <?php 
        $mock_news = [
            [
                'title' => 'السعودية تُدشن أكبر مشروع للطاقة الشمسية في المنطقة بقدرة ٢٠٠٠ ميجاواط',
                'excerpt' => 'في خطوة رائدة نحو تحقيق رؤية ٢٠٣٠، أعلنت السعودية عن تدشين مشروع ضخم للطاقة الشمسية...',
                'date' => '08/08/2025',
                'views' => '9,870',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'الطاقة_الشمسية',
            ],
            [
                'title' => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
                'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
                'date' => '07/08/2025',
                'views' => '5,430',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'بيئة',
            ],
             [
                'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
                'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة وتحقيق الاستدامة البيئية',
                'date' => '06/08/2025',
                'views' => '8,120',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'طاقة_متجددة',
            ],
            [
                'title' => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
                'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
                'date' => '07/08/2025',
                'views' => '5,430',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'بيئة',
            ],
            [
                'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
                'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة وتحقيق الاستدامة البيئية',
                'date' => '06/08/2025',
                'views' => '8,120',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'طاقة_متجددة',
            ],
            [
                'title' => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
                'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
                'date' => '07/08/2025',
                'views' => '5,430',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'بيئة',
            ],
            [
                'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
                'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة وتحقيق الاستدامة البيئية',
                'date' => '06/08/2025',
                'views' => '8,120',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'طاقة_متجددة',
            ],
            [
                'title' => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
                'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
                'date' => '07/08/2025',
                'views' => '5,430',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'بيئة',
            ],
            [
                'title' => 'الكويت تُطلق مشروعاً ضخماً للطاقة الشمسية بقدرة ١٥٠٠',
                'excerpt' => 'أعلنت دولة الكويت عن إطلاق مشروع جديد للطاقة الشمسية يهدف إلى تنويع مصادر الطاقة وتحقيق الاستدامة البيئية',
                'date' => '06/08/2025',
                'views' => '8,120',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'طاقة_متجددة',
            ],
            [
                'title' => 'الإمارات تعلن عن مبادرة جديدة لخفض الانبعاثات الكربونية',
                'excerpt' => 'أطلقت دولة الإمارات العربية المتحدة مبادرة وطنية تهدف إلى تقليل الانبعاثات الكربونية بنسبة 50% بحلول عام 2030...',
                'date' => '07/08/2025',
                'views' => '5,430',
                'image' => get_template_directory_uri() . '/assets/images/new-2.jpg',
                'cat' => 'بيئة',
            ],
        ];

        foreach ( $mock_news as $item ) :
        ?>
            <!-- News Card (Fallback) -->
            <div class="w-full rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-100 inline-flex justify-start items-center gap-4 overflow-hidden max-sm:flex-col hover:shadow-md transition-shadow duration-300">
                <a href="#" class="max-sm:h-60 max-sm:w-full w-36 self-stretch p-4 bg-cover bg-center rounded block" style="background-image: url('<?php echo esc_url( $item['image'] ); ?>');">
                </a>
                
                <div class="flex-1 self-stretch pl-3 py-3 inline-flex flex-col justify-start items-end gap-1.5">
                    <div class="self-stretch flex flex-col justify-start items-end gap-4 max-sm:gap-2">
                        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                            <div class="text-center justify-start text-neutral-800 text-xs font-normal leading-5">
                                <?php echo esc_html( $item['date'] ); ?>
                            </div>
                            <a href="#" class="text-right justify-start text-neutral-950 text-sm font-medium leading-5 hover:text-green-700 transition-colors">
                                <?php echo esc_html( $item['title'] ); ?>
                            </a>
                        </div>
                        
                        <div class="self-stretch text-right justify-start text-stone-500 text-xs font-normal capitalize leading-6 line-clamp-2">
                            <?php echo esc_html( $item['excerpt'] ); ?>
                        </div>
                        
                        <div class="h-6 px-2 bg-green-100 rounded-[100px] inline-flex justify-center items-center gap-2.5 self-start">
                            <div class="w-auto px-2 h-11 text-right justify-start text-neutral-950 text-xs font-normal capitalize leading-10">
                                #<?php echo esc_html( $item['cat'] ); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-auto">
                        <a href="#" class="text-center justify-start text-green-700 text-sm font-normal underline leading-5 hover:text-green-900">
                            المزيد
                        </a>
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
<?php endif; ?>
