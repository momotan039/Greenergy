<?php
/**
 * Header Template: Default
 *
 * Main header with full navigation, search, and CTA.
 * Figma → Tailwind conversion placeholder.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Get Redux options
$sticky_header  = greenergy_option( 'sticky_header', true );
$header_search  = greenergy_option( 'header_search', true );
$header_cta     = greenergy_option( 'header_cta_text', __( 'Subscribe', 'greenergy' ) );
$header_cta_url = greenergy_option( 'header_cta_url', '#' );
?>

<header id="masthead" class="site-header <?php echo $sticky_header ? 'sticky top-0 z-sticky' : ''; ?> 
                                          bg-white dark:bg-secondary-900 
                                          border-b border-secondary-100 dark:border-secondary-800
                                          transition-all duration-300">
    <div class="container">
        <div class="flex items-center justify-between h-16 lg:h-20">
            
            <!-- Logo -->
            <div class="site-branding shrink-0">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
                       class="text-xl font-bold text-secondary-900 dark:text-white hover:text-primary-500">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Desktop Navigation -->
            <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'greenergy' ); ?>">
                <?php
                wp_nav_menu( [
                    'theme_location' => 'primary',
                    'menu_class'     => 'flex items-center gap-1',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'walker'         => class_exists( 'Greenergy_Nav_Walker' ) ? new Greenergy_Nav_Walker() : null,
                ] );
                ?>
            </nav>

            <!-- Header Actions -->
            <div class="flex items-center gap-3">
                
                <?php if ( $header_search ) : ?>
                    <!-- Search Toggle -->
                    <button type="button" 
                            class="btn-icon text-secondary-600 dark:text-secondary-300"
                            aria-label="<?php esc_attr_e( 'Search', 'greenergy' ); ?>"
                            data-search-toggle>
                        <?php echo greenergy_icon( 'search', 20, 20 ); ?>
                    </button>
                <?php endif; ?>

                <!-- Dark Mode Toggle -->
                <button type="button" 
                        class="btn-icon text-secondary-600 dark:text-secondary-300"
                        aria-label="<?php esc_attr_e( 'Toggle Dark Mode', 'greenergy' ); ?>"
                        data-theme-toggle>
                    <span class="dark:hidden"><?php echo greenergy_icon( 'moon', 20, 20 ); ?></span>
                    <span class="hidden dark:inline"><?php echo greenergy_icon( 'sun', 20, 20 ); ?></span>
                </button>

                <!-- CTA Button -->
                <?php if ( $header_cta ) : ?>
                    <a href="<?php echo esc_url( $header_cta_url ); ?>" class="btn-primary hidden sm:inline-flex">
                        <?php echo esc_html( $header_cta ); ?>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button type="button" 
                        class="mobile-menu-toggle"
                        aria-label="<?php esc_attr_e( 'Open Menu', 'greenergy' ); ?>"
                        aria-expanded="false"
                        data-mobile-menu-toggle>
                    <?php echo greenergy_icon( 'menu', 24, 24 ); ?>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobile-menu" class="mobile-menu" aria-hidden="true">
    <div class="mobile-menu-header">
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="text-lg font-bold text-secondary-900 dark:text-white">
                    <?php bloginfo( 'name' ); ?>
                </span>
            <?php endif; ?>
        </div>
        <button type="button" 
                class="btn-icon"
                aria-label="<?php esc_attr_e( 'Close Menu', 'greenergy' ); ?>"
                data-mobile-menu-close>
            <?php echo greenergy_icon( 'x', 24, 24 ); ?>
        </button>
    </div>
    <nav class="mobile-menu-body">
        <?php
        wp_nav_menu( [
            'theme_location' => 'mobile',
            'menu_class'     => 'mobile-nav',
            'container'      => false,
            'fallback_cb'    => false,
            'depth'          => 2,
        ] );
        ?>
    </nav>
</div>
<div class="mobile-menu-overlay" data-mobile-menu-overlay></div>
