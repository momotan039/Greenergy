<?php
/**
 * Template part for displaying a news card.
 *
 * @package Greenergy
 */

$post_id       = get_the_ID();
$title         = get_the_title();
$excerpt       = get_the_excerpt();
$date          = get_the_date( 'd/m/Y' );
$views         = get_post_meta( $post_id, 'views', true ) ?: '0';
$thumbnail_url = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: get_template_directory_uri() . '/assets/images/placeholder.jpg';
$permalink     = get_permalink();

// Get Category
$terms    = get_the_terms( $post_id, 'news_category' );
$cat_name = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';

// Optional delay for animation
$delay = isset($args['delay']) ? $args['delay'] : 0;
$wrapper_class = isset($args['wrapper_class']) ? $args['wrapper_class'] : 'w-60';
?>

<!-- Grid Item -->
<div class="max-md:w-[calc(50%-0.5rem)] <?php echo esc_attr( $wrapper_class ); ?> bg-neutral-50 rounded-lg inline-flex flex-col justify-start items-center overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-[1.03]" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $delay ); ?>">
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
