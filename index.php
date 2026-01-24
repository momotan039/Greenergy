<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package Greenergy
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main" role="main">
    <?php
    if ( have_posts() ) :
        
        // Load archive header if on archive
        if ( is_archive() || is_home() ) :
            ?>
            <header class="page-header">
                <?php
                the_archive_title( '<h1 class="page-title">', '</h1>' );
                the_archive_description( '<div class="archive-description">', '</div>' );
                ?>
            </header>
            <?php
        endif;

        // Start the Loop
        while ( have_posts() ) :
            the_post();

            // Get appropriate content template
            $post_type = get_post_type();
            get_template_part( 'templates/content/content', $post_type );

        endwhile;

        // Pagination
        get_template_part( 'templates/components/pagination' );

    else :

        // No posts found
        get_template_part( 'templates/content/content', 'none' );

    endif;
    ?>
</main>

<?php
get_sidebar();
get_footer();
