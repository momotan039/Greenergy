<?php
/**
 * The sidebar template
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Check if sidebar should be displayed
$show_sidebar = greenergy_option( 'show_sidebar', true );

if ( ! $show_sidebar || ! is_active_sidebar( 'sidebar-main' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar-main' ); ?>
</aside><!-- #secondary -->
