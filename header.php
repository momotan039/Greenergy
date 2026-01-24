<?php
/**
 * The header for our theme
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Get header style from Redux options
$header_style = greenergy_option( 'header_style', 'default' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e( 'Skip to content', 'greenergy' ); ?>
    </a>

    <?php
    // Load header template variation
    get_template_part( 'templates/header/header', $header_style );
    ?>
