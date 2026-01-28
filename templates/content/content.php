<?php
/**
 * Template part for displaying posts
 *
 * @package Greenergy
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-12' ); ?>>
    <header class="entry-header mb-4">
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title text-3xl font-bold mb-2">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title text-2xl font-bold mb-2"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;

        if ( 'post' === get_post_type() ) :
            ?>
            <div class="entry-meta text-gray-500 text-sm mb-4">
                <?php
                echo get_the_date();
                echo ' | ';
                the_author();
                ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php
        the_content();
        ?>
    </div>
</article>
