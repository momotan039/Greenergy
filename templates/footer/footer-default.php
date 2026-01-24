<?php
/**
 * Footer Template: Default
 *
 * 4-column footer with widgets, newsletter, and social links.
 * Figma → Tailwind conversion placeholder.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Get Redux options
$footer_logo        = greenergy_option( 'footer_logo', [] );
$footer_description = greenergy_option( 'footer_description', '' );
$copyright_text     = greenergy_option( 'copyright_text', '© {year} Greenergy. All rights reserved.' );
$show_newsletter    = greenergy_option( 'footer_newsletter', true );

// Replace {year} placeholder
$copyright_text = str_replace( '{year}', date( 'Y' ), $copyright_text );
?>

<footer id="colophon" class="site-footer bg-secondary-900 text-secondary-300">
    
    <!-- Main Footer -->
    <div class="py-12 lg:py-16">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                
                <!-- Column 1: About -->
                <div class="lg:col-span-1">
                    <?php if ( ! empty( $footer_logo['url'] ) ) : ?>
                        <img src="<?php echo esc_url( $footer_logo['url'] ); ?>" 
                             alt="<?php bloginfo( 'name' ); ?>" 
                             class="h-10 w-auto mb-4">
                    <?php else : ?>
                        <span class="text-xl font-bold text-white mb-4 block">
                            <?php bloginfo( 'name' ); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ( $footer_description ) : ?>
                        <p class="text-sm leading-relaxed mb-6">
                            <?php echo esc_html( $footer_description ); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Social Links -->
                    <div class="flex items-center gap-3">
                        <?php
                        $social_links = [
                            'facebook'  => greenergy_option( 'social_facebook' ),
                            'twitter'   => greenergy_option( 'social_twitter' ),
                            'instagram' => greenergy_option( 'social_instagram' ),
                            'linkedin'  => greenergy_option( 'social_linkedin' ),
                            'youtube'   => greenergy_option( 'social_youtube' ),
                        ];
                        
                        foreach ( $social_links as $network => $url ) :
                            if ( $url ) :
                                ?>
                                <a href="<?php echo esc_url( $url ); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="w-9 h-9 flex items-center justify-center rounded-full 
                                          bg-secondary-800 text-secondary-400 
                                          hover:bg-primary-500 hover:text-white 
                                          transition-colors duration-200"
                                   aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
                                    <?php echo greenergy_icon( $network, 18, 18 ); ?>
                                </a>
                                <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>

                <!-- Widget Columns -->
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                    <?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar( 'footer-' . $i ); ?>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
                
            </div>
        </div>
    </div>

    <?php if ( $show_newsletter ) : ?>
        <!-- Newsletter Section -->
        <div class="py-8 bg-secondary-800/50 border-t border-secondary-800">
            <div class="container">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="text-center lg:text-start">
                        <h4 class="text-lg font-bold text-white mb-1">
                            <?php esc_html_e( 'Subscribe to our newsletter', 'greenergy' ); ?>
                        </h4>
                        <p class="text-sm text-secondary-400">
                            <?php esc_html_e( 'Get the latest news and updates delivered to your inbox.', 'greenergy' ); ?>
                        </p>
                    </div>
                    <form class="flex w-full max-w-md gap-2">
                        <input type="email" 
                               placeholder="<?php esc_attr_e( 'Your email address', 'greenergy' ); ?>"
                               class="form-input flex-1 bg-secondary-700 border-secondary-600 
                                      text-white placeholder:text-secondary-400">
                        <button type="submit" class="btn-primary shrink-0">
                            <?php esc_html_e( 'Subscribe', 'greenergy' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Copyright Bar -->
    <div class="py-4 border-t border-secondary-800">
        <div class="container">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
                <p class="text-secondary-400">
                    <?php echo esc_html( $copyright_text ); ?>
                </p>
                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <nav aria-label="<?php esc_attr_e( 'Footer Menu', 'greenergy' ); ?>">
                        <?php
                        wp_nav_menu( [
                            'theme_location' => 'footer',
                            'menu_class'     => 'flex items-center gap-6',
                            'container'      => false,
                            'depth'          => 1,
                        ] );
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>
