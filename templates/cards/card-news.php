<?php
/**
 * Card Template: News
 *
 * Reusable news card component.
 * Figma → Tailwind conversion placeholder.
 *
 * @package Greenergy
 * @since 1.0.0
 * 
 * Variables available:
 * @var int    $post_id    Optional post ID
 * @var string $size       Card size (default|featured|compact)
 * @var bool   $show_excerpt
 */

// Set defaults
$post_id      = $post_id ?? get_the_ID();
$size         = $size ?? 'default';
$show_excerpt = $show_excerpt ?? true;

// Get post data
$categories   = get_the_terms( $post_id, 'news_category' );
$primary_cat  = $categories ? $categories[0] : null;
?>

<article <?php post_class( 'card group', $post_id ); ?>>
    
    <!-- Card Image -->
    <a href="<?php the_permalink( $post_id ); ?>" class="card-image block">
        <?php echo greenergy_get_thumbnail( $post_id, 'card-thumbnail' ); ?>
    </a>
    
    <!-- Card Body -->
    <div class="card-body">
        
        <?php if ( $primary_cat ) : ?>
            <a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>" class="card-category">
                <?php echo esc_html( $primary_cat->name ); ?>
            </a>
        <?php endif; ?>
        
        <h3 class="card-title">
            <a href="<?php the_permalink( $post_id ); ?>">
                <?php echo get_the_title( $post_id ); ?>
            </a>
        </h3>
        
        <?php if ( $show_excerpt && has_excerpt( $post_id ) ) : ?>
            <p class="card-excerpt">
                <?php echo greenergy_truncate( get_the_excerpt( $post_id ), 100 ); ?>
            </p>
        <?php endif; ?>
        
        <!-- Card Meta -->
        <div class="card-meta">
            <span class="card-meta-item">
                <?php echo greenergy_icon( 'calendar', 14, 14 ); ?>
                <time datetime="<?php echo get_the_date( 'c', $post_id ); ?>">
                    <?php echo greenergy_get_date( $post_id ); ?>
                </time>
            </span>
            <span class="card-meta-item">
                <?php echo greenergy_icon( 'clock', 14, 14 ); ?>
                <?php 
                /* translators: %d: number of minutes to read */
                printf( esc_html__( '%d min read', 'greenergy' ), greenergy_reading_time( $post_id ) ); 
                ?>
            </span>
        </div>
        
    </div>
</article>
