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
        
        /* Sticky Nav Styles */
        .greenergy-fixed-nav {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-in-out;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            setTimeout(function() {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        once: true,
                        offset: 50,
                        duration: 800,
                    });
                }
            }, 500);

            // Always Sticky Logic
            const desktopNav = document.getElementById('desktop-smart-nav');
            const mobileNav = document.getElementById('mobile-smart-header');
            const desktopNavOffset = desktopNav ? desktopNav.offsetTop : 300;

            window.addEventListener('scroll', function() {
                let st = window.scrollY; // Current scroll position
                
                // Desktop Always Sticky
                if(desktopNav && window.innerWidth >= 1024) {
                    if (st > desktopNavOffset) {
                         // We are past the initial nav position -> Stick it
                         desktopNav.classList.add('greenergy-fixed-nav');
                         desktopNav.classList.remove('my-4', 'rounded-[1000px]');
                         desktopNav.classList.add('py-2');
                         desktopNav.style.transform = 'translateY(0)';
                    } else {
                        // At top -> Reset
                        desktopNav.classList.remove('greenergy-fixed-nav');
                        desktopNav.classList.add('my-4', 'rounded-[1000px]');
                        desktopNav.style.transform = 'translateY(0)';
                    }
                }
                
                // Mobile Always Sticky with Spacer
                if(mobileNav && window.innerWidth < 1024) {
                     const mobileParent = document.getElementById('mobile-header-container');
                     
                     if (st > 0) {
                         // Scroll > 0 -> Fixed + Add Spacer
                         if (!mobileNav.classList.contains('fixed')) {
                             mobileNav.classList.remove('relative');
                             mobileNav.classList.add('fixed', 'top-0', 'left-0', 'right-0', 'shadow-md');
                             if(mobileParent) mobileParent.style.paddingTop = mobileNav.offsetHeight + 'px';
                         }
                     } else {
                         // Scroll at top -> Relative + Remove Spacer
                         if (mobileNav.classList.contains('fixed')) {
                             mobileNav.classList.remove('fixed', 'top-0', 'left-0', 'right-0', 'shadow-md');
                             mobileNav.classList.add('relative');
                             if(mobileParent) mobileParent.style.paddingTop = '0px';
                         }
                     }
                }
            });
        });
    </script>

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
