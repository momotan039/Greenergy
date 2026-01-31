<?php
/**
 * Footer Template: Default
 *
 * Pixel-perfect implementation based on Figma design.
 * Structure:
 * 1. Newsletter Section
 * 2. Main Footer (4 Widget Columns + Dynamic Logo)
 * 3. Bottom Bar (Copyright + Dynamic Socials)
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Get Redux options
$footer_logo        = greenergy_option( 'footer_logo', [] );
$footer_description = greenergy_option( 'footer_description', __( 'منصة Greenergy الرائدة في مجال الطاقة المتجددة والاستدامة، نقدم المحتوى التعليمي والفرص الوظيفية ودليل الخبراء في قطاع الطاقة النظيفة.', 'greenergy' ) );
$copyright_text     = greenergy_option( 'copyright_text', __( 'كل الحقوق محفوظة لدى © Greenergy {year}', 'greenergy' ) );
$show_newsletter    = greenergy_option( 'footer_newsletter', true );

// Replacing {year} placeholder
$copyright_text = str_replace( '{year}', date( 'Y' ), $copyright_text );
?>

<div class="max-w-7xl mx-auto px-4 relative">

    <?php if ( $show_newsletter ) : ?>
        <!-- Newsletter Section -->
        <div class="relative bg-[#CDEECD] rounded-[3rem] p-8 max-md:px-4 md:p-12 overflow-hidden shadow-xl mb-[-4rem] z-10 mx-4 lg:mx-0">
        <!-- circle decoration -->
        <div class="hidden sm:block w-80 h-80 right-[-4rem] top-[5rem] absolute bg-gradient-to-br from-green-700/25 via-lime-600/25 to-lime-300/25 rounded-full"></div> 
            <div class="relative z-10 flex flex-col items-center text-center gap-6 md:gap-8">
                <div class="w-full">
                    <h2 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight">
                        <?php esc_html_e( 'اشترك ليصلك كل جديد في الطاقة المتجددة', 'greenergy' ); ?>
                    </h2>
                </div>

                <div class="w-full">
                    <p class="text-black text-base max-w-2xl mx-auto opacity-80">
                        <?php esc_html_e( 'احصل على آخر الأخبار والتطورات والفرص في مجال الطاقة النظيفة مباشرة في بريدك الإلكتروني', 'greenergy' ); ?>
                    </p>
                </div>

                <div class="w-full max-w-2xl px-4">
                    <form class="bg-white p-2 rounded-xl shadow-lg flex items-center justify-between pl-2 pr-6">
                        <input type="email" placeholder="<?php esc_attr_e( 'ادخل بريدك الالكتروني', 'greenergy' ); ?>"
                            class="flex-1 bg-transparent border-none outline-none text-neutral-800  h-10 md:h-12">
                        <button type="submit"
                            class="bg-[#229924] rounded-lg  text-white  py-2.5 md:py-3 px-6 md:px-10 hover:bg-[#1a7a1b] transition-all shadow-sm whitespace-nowrap text-sm md:text-base">
                            <?php esc_html_e( 'اشتراك الان', 'greenergy' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Footer Section -->
<footer class="bg-[#054F1C] text-white pt-24 pb-8 mt-12 relative overflow-hidden">

    <div class="max-w-7xl mx-auto px-6 relative z-10">

        <!-- Main Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-12 gap-x-6 gap-y-10 lg:gap-8 pb-12 border-b border-white/10">

            <!-- Column 1 (Widget Area) -->
            <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-1 lg:order-1" data-aos="fade-up" data-aos-delay="100">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                <?php else : ?>
                    <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php esc_html_e( 'عن الشركة', 'greenergy' ); ?></h4>
                    <ul class="space-y-3 font-bold text-green-100/60 text-sm">
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'من نحن', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'فريق العمل', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الشركات', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'تواصل معنا', 'greenergy' ); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Column 2 (Widget Area) -->
            <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-2 lg:order-2" data-aos="fade-up" data-aos-delay="200">
                <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-2' ); ?>
                <?php else : ?>
                    <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php esc_html_e( 'خدماتنا', 'greenergy' ); ?></h4>
                    <ul class="space-y-3 font-bold text-green-100/60 text-sm">
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الأخبار والمقالات', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الدورات التدريبية', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'فرص العمل', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'دليل الشركات', 'greenergy' ); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Center: Logo Block -->
            <div class="col-span-2 lg:col-span-4 flex flex-col items-center text-center order-0 lg:order-3 lg:mx-auto" data-aos="zoom-in" data-aos-delay="300">
                <div class="flex flex-col items-center hover:scale-105 transition-transform duration-500 cursor-pointer">
                    <?php if ( has_custom_logo() ) : ?>
                        <div class="!w-32 !h-36">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else : ?>
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-yellow-400 rounded-full flex items-center justify-center mb-2 shadow-lg">
                            <i class="fas fa-leaf text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-black tracking-wider"><?php bloginfo( 'name' ); ?></h3>
                        <span class="text-[10px] tracking-[0.2em] opacity-60 uppercase"><?php bloginfo( 'description' ); ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-white text-base font-normal leading-loose max-w-xs px-2 opacity-80 hover:opacity-100 transition-opacity">
                    <?php echo esc_html( $footer_description ); ?>
                </p>
            </div>

            <!-- Column 3 (Widget Area) -->
            <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-3 lg:order-4" data-aos="fade-up" data-aos-delay="400">
                <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-3' ); ?>
                <?php else : ?>
                    <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php esc_html_e( 'أنواع الطاقة', 'greenergy' ); ?></h4>
                    <ul class="space-y-3 font-bold text-green-100/60 text-sm">
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الطاقة الشمسية', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'طاقة الرياح', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الطاقة المائية', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الطاقة الحيوية', 'greenergy' ); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Column 4 (Widget Area) -->
            <div class="col-span-1 lg:col-span-2 flex flex-col items-center text-center lg:text-right order-4 lg:order-5" data-aos="fade-up" data-aos-delay="500">
                <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-4' ); ?>
                <?php else : ?>
                    <h4 class="font-black text-lg mb-6 text-white border-b-2 border-brand-gold pb-2 inline-block"><?php esc_html_e( 'الموارد', 'greenergy' ); ?></h4>
                    <ul class="space-y-3 font-bold text-green-100/60 text-sm">
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'مكتبة الوسائط', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'المقالات', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الدراسات', 'greenergy' ); ?></a></li>
                        <li><a href="#" class="hover:text-brand-gold hover:translate-x-[-5px] transition-all duration-300 !text-white !text-base font-medium inline-block"><?php esc_html_e( 'الأسئلة الشائعة', 'greenergy' ); ?></a></li>
                    </ul>
                <?php endif; ?>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col md:flex-row items-center justify-between pt-8 gap-6 md:gap-0 mt-8 md:mt-0">

            <!-- Copyright -->
            <div class="text-white text-base order-last md:order-first">
                <?php echo esc_html( $copyright_text ); ?>
            </div>

            <!-- Social Icons -->
            <div class="flex items-center gap-4 order-first md:order-last">
                <span class=" text-base text-white"><?php esc_html_e( 'تابعنا', 'greenergy' ); ?></span>
                <div class="flex gap-2.5 md:gap-3">
                    <?php
                    $social_links = greenergy_option( 'social_media', [] );
                    if ( ! empty( $social_links ) && is_array( $social_links ) ) :
                        foreach ( $social_links as $social ) :
                            $icon_url = $social['icon'] ?? '';
                            $platform = strtolower( $social['platform'] ?? '' );
                            $url      = $social['url'] ?? '';
                            if ( empty( $url ) ) continue;

                            // Map common platforms to FontAwesome icons
                            $fa_icon = 'fas fa-link';
                            if ( strpos( $platform, 'twitter' ) !== false || strpos( $platform, 'x' ) !== false ) $fa_icon = 'fab fa-twitter';
                            elseif ( strpos( $platform, 'facebook' ) !== false ) $fa_icon = 'fab fa-facebook-f';
                            elseif ( strpos( $platform, 'instagram' ) !== false ) $fa_icon = 'fab fa-instagram';
                            elseif ( strpos( $platform, 'linkedin' ) !== false ) $fa_icon = 'fab fa-linkedin-in';
                            elseif ( strpos( $platform, 'youtube' ) !== false ) $fa_icon = 'fab fa-youtube';
                            elseif ( strpos( $platform, 'whatsapp' ) !== false ) $fa_icon = 'fab fa-whatsapp';
                            ?>
                            <a href="<?php echo esc_url( $url ); ?>"
                                class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D9A520] hover:text-white transition-all duration-300 !text-white !text-base font-medium text-white/70 hover:scale-110 shadow-lg hover:shadow-brand-gold/30"
                                title="<?php echo esc_attr( $social['platform'] ?? '' ); ?>">
                                <?php if ( ! empty( $icon_url ) ) : ?>
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $social['platform'] ?? '' ); ?>" class="w-4 h-4 object-contain filter invert">
                                <?php else : ?>
                                    <i class="<?php echo esc_attr( $fa_icon ); ?> text-sm md:text-base"></i>
                                <?php endif; ?>
                            </a>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

        </div>
    </div>
</footer>
