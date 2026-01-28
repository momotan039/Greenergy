<?php
/**
 * The template for displaying all single posts, pages and attachments
 *
 * @package Greenergy
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'templates/content/content', get_post_type() );

        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;

    endwhile;
    ?>
</main>

<?php
get_footer();
