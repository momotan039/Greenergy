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
    
    <!-- Design Assets -->
    <link href="https://db.onlinewebfonts.com/c/aba1a083bf50980a05f0265179103a09?family=DIN+Next+LT+Arabic+Medium" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DIN Next LT Arabic Medium', 'sans-serif'],
                        'din': ['DIN Next LT Arabic Medium', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            green: '#229924',
                            darkGreen: '#064015',
                            lightGreen: '#CDEECD',
                            gold: '#D9A520',
                            yellow: '#FFD700',
                            blue: '#0284C7',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'DIN Next LT Arabic Medium', sans-serif !important;
            background-color: #ffffff;
            overflow-x: hidden;
            width: 100%;
        }
        * {
            font-family: 'DIN Next LT Arabic Medium', sans-serif;
        }
    </style>

    <?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-white' ); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e( 'Skip to content', 'greenergy' ); ?>
    </a>

    <?php
    // Load header template variation
    get_template_part( 'templates/header/header', $header_style );
    ?>
