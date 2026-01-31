<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Greenergy
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages( array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'greenergy' ),
            'after'  => '</div>',
        ) );
        ?>
    </div>
</article>
