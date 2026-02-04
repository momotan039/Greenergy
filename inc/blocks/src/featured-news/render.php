<?php
/**
 * Featured News Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'w-full flex flex-col justify-start items-center gap-4',
] );

// Query Logic
$args = [
    'post_type'      => 'news',
    'posts_per_page' => 5, // Fetch 5 posts for the slider
    'post_status'    => 'publish',
];

// 1. Check if specific ID is set
if ( ! empty( $attributes['postId'] ) ) {
    // If a specific ID is selected, we might still want related or just that one. 
    // Usually "Featured News" implies a selection. 
    // If specific ID is chosen, maybe we just show that one? 
    // Or maybe the user wants to select MULTIPLE IDs?
    // The attribute is 'postId' (singular). 
    // For now, if one ID is set, we just show one (slider of 1).
    $args['p'] = $attributes['postId'];
} else {
    // 2. Check URL Filters
    if ( isset( $_GET['news_cat'] ) && ! empty( $_GET['news_cat'] ) ) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'news_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['news_cat'] ),
            ],
        ];
    }
    
    // Check Sort
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
}

$query = new WP_Query( $args );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="w-full relative">
        <div class="swiper js-swiper-init w-full rounded-lg overflow-hidden" data-swiper-config='{"autoplay":{"delay":5000}}'>
            <div class="swiper-wrapper">
                <?php
                if ( $query->have_posts() ) :
                    while ( $query->have_posts() ) : $query->the_post();
                        $post_id = get_the_ID();
                        $title = get_the_title();
                        $excerpt = get_the_excerpt();
                        $author_name = get_the_author();
                        $views = get_post_meta( $post_id, 'views', true ) ?: '0';
                        $thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full' );
                        $permalink = get_permalink();
                        
                        // Fallback image if none
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = get_template_directory_uri() . '/assets/images/placeholder.jpg';
                        }
                ?>
                <div class="swiper-slide">
                    <div class="w-full h-64 sm:h-80 md:h-96 p-3 md:p-4 bg-center bg-cover flex flex-col justify-between items-center relative overflow-hidden group rounded-lg" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                        <!-- Overlay for better text readability -->
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>
                        
                        <div class="w-full inline-flex justify-start items-start gap-4 md:gap-20 relative z-10 transition-transform duration-500 group-hover:translate-x-2">
                            <div class="h-7 md:h-8 px-2 bg-black/25 rounded-lg flex justify-center items-center gap-2 backdrop-blur-sm hover:bg-black/40 transition-colors cursor-default">
                                <div class="text-center justify-start text-white text-xs md:text-sm font-medium leading-6">
                                    بواسطة: <?php echo esc_html( $author_name ); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full inline-flex justify-between items-start relative z-10 w-full">
                            <div class="flex-1 inline-flex flex-col justify-start items-end gap-2 md:gap-4 w-full">
                                <a href="<?php echo esc_url( $permalink ); ?>" class="w-full text-right justify-start text-white text-lg md:text-2xl font-medium capitalize leading-tight md:leading-6 hover:text-green-400 transition-colors">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                                <div class="w-full text-right justify-start text-white text-sm md:text-base font-normal capitalize leading-5 md:leading-6 line-clamp-2">
                                    <?php echo esc_html( $excerpt ); ?>
                                </div>
                                <div class="w-full inline-flex justify-between items-center flex-row-reverse w-full mt-2">
                                    <a href="<?php echo esc_url( $permalink ); ?>" class="text-right justify-start text-white text-sm md:text-base font-medium underline capitalize leading-6 hover:text-green-400 transition-colors">
                                        المزيد
                                    </a>
                                    <div class="flex justify-start items-center gap-1.5">
                                        <div class="text-right justify-start text-white text-xs md:text-sm font-normal flex items-center gap-1">
                                            <svg class="w-3 h-3 md:w-4 md:h-4 inline" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye_white.svg"></use>
                                            </svg>
                                            <?php echo esc_html( number_format_i18n( (int) $views ) ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else : 
                    // Fallback Mock Data - Create 3 slides to demonstrate carousel
                    $title = 'السعودية تُدشن أكبر مشروع للطاقة الشمسية في المنطقة بقدرة ٢٠٠٠ ميجاواط';
                    $excerpt = 'في خطوة رائدة نحو تحقيق رؤية ٢٠٣٠، أعلنت السعودية عن تدشين مشروع ضخم للطاقة الشمسية...';
                    $author_name = 'أحمد الزهراني';
                    $views = '9,870';
                    $thumbnail_url = get_template_directory_uri() . '/assets/images/new-1.jpg';
                    $permalink = '#';
                    
                    for ($i = 0; $i < 3; $i++) :
                ?>
                <div class="swiper-slide">
                    <div class="w-full h-64 sm:h-80 md:h-96 p-3 md:p-4 bg-center bg-cover flex flex-col justify-between items-center relative overflow-hidden group rounded-lg" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>
                        
                        <div class="w-full inline-flex justify-start items-start gap-4 md:gap-20 relative z-10 transition-transform duration-500 group-hover:translate-x-2">
                            <div class="h-7 md:h-8 px-2 bg-black/25 rounded-lg flex justify-center items-center gap-2 backdrop-blur-sm hover:bg-black/40 transition-colors cursor-default">
                                <div class="text-center justify-start text-white text-xs md:text-sm font-medium leading-6">
                                    بواسطة: <?php echo esc_html( $author_name ); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full inline-flex justify-between items-start relative z-10 w-full">
                            <div class="flex-1 inline-flex flex-col justify-start items-end gap-2 md:gap-4 w-full">
                                <a href="<?php echo esc_url( $permalink ); ?>" class="w-full text-right justify-start text-white text-md md:text-2xl font-medium capitalize leading-tight md:leading-6 hover:text-green-400 transition-colors">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                                <div class="w-full text-right justify-start text-white text-sm md:text-base font-normal capitalize leading-5 md:leading-6 line-clamp-2">
                                    <?php echo esc_html( $excerpt ); ?>
                                </div>
                                <div class="w-full inline-flex justify-between items-center flex-row-reverse w-full mt-2">
                                    <a href="<?php echo esc_url( $permalink ); ?>" class="text-right justify-start text-white text-sm md:text-base font-medium underline capitalize leading-6 hover:text-green-400 transition-colors">
                                        المزيد
                                    </a>
                                    <div class="flex justify-start items-center gap-1.5">
                                        <div class="text-right justify-start text-white text-xs md:text-sm font-normal flex items-center gap-1">
                                            <svg class="w-3 h-3 md:w-4 md:h-4 inline" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye_white.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $views ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; endif; ?>
            </div>
        </div>
        
        <!-- Dots / Pagination -->
        <div class="swiper-pagination !static !mt-4"></div> 
    </div>
</div>
