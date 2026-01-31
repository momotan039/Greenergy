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
    <h1>Home Page</h1>
    <?php
    // Get the page content
    $content = '';
    if ( have_posts() ) {
        the_post();
        $content = get_the_content();
    }

    // Default sequence of blocks (using the registered pattern)
    // This follows DRY principles by referencing the single source of truth in inc/patterns/homepage.php
    $default_blocks = '<!-- wp:pattern {"slug":"greenergy/homepage"} /-->';

    // Logic:
    // 1. If "Your latest posts" is set: is_home() = true. We ignore the loop (which is posts) and show default blocks.
    // 2. If "Static page" is set: is_home() = false. We check that page's content.
    
    $show_default = false;

    if ( is_home() && is_front_page() ) {
        // "Latest posts" setting - Force default homepage layout
        $show_default = true;
    } elseif ( empty( trim( strip_tags( $content ) ) ) ) {
        // Static page with empty content
        $show_default = true;
    }

    if ( $show_default ) {
        echo do_blocks( $default_blocks );
    } else {
        // Static page with content
        the_content();
    }
    ?>
</main>

<?php
get_footer();
