<?php
/**
 * The footer for our theme
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Get footer style from Redux options
$footer_style = greenergy_option( 'footer_style', 'default' );
?>

    <?php
    // Load footer template variation
    get_template_part( 'templates/footer/footer', $footer_style );
    ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
