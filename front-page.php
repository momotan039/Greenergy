<?php
/**
 * Template Name: Home Page
 * The front page template file
 *
 * This is the template for displaying the home page with all Greenergy blocks.
 *
 * @package Greenergy
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    // Get the page content
    $content = '';
    if ( have_posts() ) {
        the_post();
        $content = get_the_content();
    }

    // Default sequence of blocks if editor is empty
    $default_blocks = '
        <!-- wp:greenergy/hero-block /-->
        <!-- wp:greenergy/stories /-->
        <!-- wp:greenergy/stats /-->
        <!-- wp:greenergy/courses /-->
        <!-- wp:greenergy/ad-block /-->
        <!-- wp:greenergy/latest-news /-->
        <!-- wp:greenergy/most-read-news /-->
        <!-- wp:greenergy/jobs /-->
    ';

    if ( empty( trim( strip_tags( $content ) ) ) ) {
        echo do_blocks( $default_blocks );
    } else {
        the_content();
    }
    ?>
</main>

<?php
get_footer();
