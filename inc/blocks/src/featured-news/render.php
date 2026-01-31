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
    'class' => 'self-stretch flex flex-col justify-start items-center gap-4',
] );

// Query Logic
$args = [
    'post_type'      => 'news',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
];

// 1. Check if specific ID is set
if ( ! empty( $attributes['postId'] ) ) {
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
            $thumbnail_url = get_template_directory_uri() . '/assets/images/placeholder.jpg'; // or similar
        }
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="self-stretch h-96 p-4 bg-center bg-cover rounded-lg flex flex-col justify-between items-center relative overflow-hidden group" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>
        
        <div class="self-stretch inline-flex justify-start items-start gap-20 relative z-10 transition-transform duration-500 group-hover:translate-x-2">
            <div class="h-8 px-2 bg-black/25 rounded-lg flex justify-center items-center gap-2 backdrop-blur-sm hover:bg-black/40 transition-colors cursor-default">
                <div class="text-center justify-start text-white text-sm font-medium leading-6">
                    بواسطة: <?php echo esc_html( $author_name ); ?>
                </div>
            </div>
        </div>
        
        <div class="self-stretch inline-flex justify-between items-start relative z-10">
            <div class="flex-1 inline-flex flex-col justify-start items-end gap-4">
                <a href="<?php echo esc_url( $permalink ); ?>" class="self-stretch text-right justify-start text-white text-2xl font-medium capitalize leading-6 hover:text-green-400 transition-colors">
                    <?php echo esc_html( $title ); ?>
                </a>
                <div class="self-stretch text-right justify-start text-white text-base font-normal capitalize leading-6 line-clamp-2">
                    <?php echo esc_html( $excerpt ); ?>
                </div>
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="text-right justify-start text-white text-base font-medium underline capitalize leading-6 hover:text-green-400 transition-colors">
                        المزيد
                    </a>
                    <div class="flex justify-start items-center gap-1.5">
                        <div class="text-right justify-start text-white text-sm font-normal flex items-center gap-1">
                            <svg class="w-4 h-4 inline" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye_white.svg"></use>
                            </svg>
                            <?php echo esc_html( number_format_i18n( (int) $views ) ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dots (Decorative in static, maybe slider indicators if needed later) -->
    <div class="inline-flex justify-start items-center gap-2">
        <div class="w-2 h-2 bg-green-700 rounded-full"></div>
        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
    </div>
</div>

<?php
    endwhile;
    wp_reset_postdata();
else : 
    // Fallback Mock Data
    $title = 'السعودية تُدشن أكبر مشروع للطاقة الشمسية في المنطقة بقدرة ٢٠٠٠ ميجاواط';
    $excerpt = 'في خطوة رائدة نحو تحقيق رؤية ٢٠٣٠، أعلنت السعودية عن تدشين مشروع ضخم للطاقة الشمسية...';
    $author_name = 'أحمد الزهراني';
    $views = '9,870';
    $thumbnail_url = get_template_directory_uri() . '/assets/images/new-1.jpg';
    $permalink = '#';
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="self-stretch h-96 p-4 bg-center bg-cover rounded-lg flex flex-col justify-between items-center relative overflow-hidden group" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-colors"></div>
        
        <div class="self-stretch inline-flex justify-start items-start gap-20 relative z-10 transition-transform duration-500 group-hover:translate-x-2">
            <div class="h-8 px-2 bg-black/25 rounded-lg flex justify-center items-center gap-2 backdrop-blur-sm hover:bg-black/40 transition-colors cursor-default">
                <div class="text-center justify-start text-white text-sm font-medium leading-6">
                    بواسطة: <?php echo esc_html( $author_name ); ?>
                </div>
            </div>
        </div>
        
        <div class="self-stretch inline-flex justify-between items-start relative z-10">
            <div class="flex-1 inline-flex flex-col justify-start items-end gap-4">
                <a href="<?php echo esc_url( $permalink ); ?>" class="self-stretch text-right justify-start text-white text-2xl font-medium capitalize leading-6 hover:text-green-400 transition-colors">
                    <?php echo esc_html( $title ); ?>
                </a>
                <div class="self-stretch text-right justify-start text-white text-base font-normal capitalize leading-6 line-clamp-2">
                    <?php echo esc_html( $excerpt ); ?>
                </div>
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="text-right justify-start text-white text-base font-medium underline capitalize leading-6 hover:text-green-400 transition-colors">
                        المزيد
                    </a>
                    <div class="flex justify-start items-center gap-1.5">
                        <div class="text-right justify-start text-white text-sm font-normal flex items-center gap-1">
                            <svg class="w-4 h-4 inline" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/eye_white.svg"></use>
                            </svg>
                            <?php echo esc_html( $views ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="inline-flex justify-start items-center gap-2">
        <div class="w-2 h-2 bg-green-700 rounded-full"></div>
        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
    </div>
</div>
<?php endif; ?>
