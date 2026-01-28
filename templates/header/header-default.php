<?php
/**
 * Header Template: Default
 *
 * Pixel-perfect implementation based on Figma design.
 * Structure:
 * 1. Mobile Header (Small Screen)
 * 2. Desktop Header (Large Screen)
 *
 * @package Greenergy
 * @since 1.0.0
 */

// $sticky_header = greenergy_option( 'sticky_header', true ); // Using static class for now as per design
?>

<!-- Header Section -->
<header class="w-full">

    <!-- MOBILE HEADER (Small Screen) -->
    <div class="md:hidden flex flex-col w-full bg-white border-b border-gray-100 px-4 py-2 gap-4">
        <!-- Row 1: Menu, Logo, Search -->
        <div class="flex items-center justify-between">
            <button class="text-gray-600 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    G</div>
                <span class="text-lg font-bold text-green-600">GREENERGY</span>
            </div>
            <button class="text-gray-600 p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </div>

        <!-- Row 2: Ads (Compact Banners) - Only on home page -->
        <?php if ( is_front_page() ) : ?>
            <div class="flex flex-col gap-2">
                <div class="bg-[#FFD700] rounded-xl p-3 flex items-center justify-between border border-yellow-300 relative overflow-hidden">
                    <span class="text-xs font-bold text-gray-800 relative z-10">شركة المستقبل للطاقة</span>
                    <span class="bg-black text-white text-[8px] px-1.5 py-0.5 rounded relative z-10 uppercase">New</span>
                    <div class="absolute inset-y-0 left-0 w-1/3 bg-blue-900/10 skew-x-12"></div>
                </div>
                <div class="bg-gradient-to-l from-[#0D9488] to-[#0F766E] rounded-xl p-3 flex items-center justify-between text-white border border-teal-600">
                    <span class="text-xs font-bold">تحول نحو الأخضر</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        <?php endif; ?>

        <!-- Row 3: Social Right, Lang Left -->
        <div class="flex items-center justify-between">
            <!-- Social Right (Start in RTL) -->
            <div class="flex items-center gap-3">
                <span class="font-bold text-[10px] text-gray-400 uppercase tracking-widest">تابِعنا</span>
                <div class="flex items-center gap-1.5">
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
                                class="w-7 h-7 rounded bg-gray-900 text-white flex items-center justify-center hover:bg-[#229924] transition text-[10px]"
                                title="<?php echo esc_attr( $social['platform'] ?? '' ); ?>">
                                <?php if ( ! empty( $icon_url ) ) : ?>
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $social['platform'] ?? '' ); ?>" class="w-3 h-3 object-contain filter invert">
                                <?php else : ?>
                                    <i class="<?php echo esc_attr( $fa_icon ); ?>"></i>
                                <?php endif; ?>
                            </a>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
            <!-- Lang Left (End in RTL) -->
            <div class="flex items-center gap-2 cursor-pointer hover:text-green-600 transition">
                <span class="font-bold text-sm">Ar</span>
                <img src="https://flagcdn.com/w40/sa.png" alt="Saudi Flag"
                    class="w-6 h-4 object-cover rounded-sm shadow-sm">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>


    <!-- DESKTOP HEADER (Large Screen) -->
    <div class="hidden md:block max-w-[1600px] mx-auto px-4 py-6">

        <!-- Top Banners & Logo Row -->
        <div class="flex items-center justify-between gap-6 mb-4">

            <!-- Right Side (In RTL): Adsense Banner (Blue/Green Gradient) -->
            <?php if ( is_front_page() ) : ?>
                <div class="flex-1 h-[140px] rounded-3xl overflow-hidden relative shadow-md group">
                    <div class="absolute inset-0 bg-gradient-to-l from-[#0D9488] to-[#0F766E]"></div>
                    <div class="relative z-10 w-full h-full flex items-center justify-between px-8 text-white">
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-3xl font-bold">إعلانات</span>
                            <span class="bg-[#F97316] text-white px-3 py-1 rounded text-sm font-bold">جوجل أدسنس</span>
                        </div>
                        <div class="w-24 h-24 bg-white/20 rounded-3xl backdrop-blur-sm transform rotate-12 shadow-2xl flex items-center justify-center border border-white/30">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-inner">
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                                    <path d="M21.352 14.93L12.924 1.156C12.28 0.1 10.908 -0.332 9.852 0.312L5.64 2.88C5.232 3.12 4.944 3.504 4.848 3.96C4.752 4.416 4.872 4.896 5.16 5.256L15.6 22.368C16.248 23.424 17.616 23.856 18.672 23.208L22.884 20.64C23.94 19.992 24.372 18.624 23.724 17.568L21.352 14.93Z" fill="#FACC15" />
                                    <path d="M8.28 4.248L2.016 14.544C1.368 15.6 1.8 16.968 2.856 17.616L6.552 19.872L10.392 13.56L8.28 4.248Z" fill="#4ADE80" />
                                    <path d="M8.28 4.248L10.368 13.584L17.76 22.104L18.672 23.208C19.728 22.56 20.16 21.192 19.512 20.136L9.072 3.024C8.784 2.664 8.256 2.592 7.8 2.856L8.28 4.248Z" fill="#3B82F6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="flex-1"></div>
            <?php endif; ?>

            <!-- Center: Logo -->
            <div class="flex-none px-4 flex flex-col items-center">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex flex-col items-center">
                    <div class="relative w-28 h-28 flex items-center justify-center mb-1">
                        <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                            <path d="M20 50 A 30 30 0 0 1 80 50" stroke="#F59E0B" stroke-width="4" stroke-linecap="round" fill="none" />
                            <path d="M25 45 A 25 25 0 0 1 75 45" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" fill="none" opacity="0.7" />
                            <path d="M50 50 Q 80 50 80 80 Q 50 80 50 50" fill="#3B82F6" opacity="0.8" />
                            <path d="M50 50 Q 20 50 20 80 Q 50 80 50 50" fill="#22C55E" />
                            <path d="M50 50 L 20 80" stroke="white" stroke-width="1" />
                        </svg>
                    </div>
                    <div class="text-center leading-[0.8] tracking-widest">
                        <h1 class="text-2xl font-bold text-[#4ADE80] m-0">GREENERGY</h1>
                        <span class="text-[0.6rem] text-gray-400 uppercase tracking-[0.2em]">Educates and Inspires</span>
                    </div>
                </a>
            </div>

            <!-- Left Side (In RTL): Solar Banner (Yellow) -->
            <?php if ( is_front_page() ) : ?>
                <div class="flex-1 h-[140px] rounded-3xl overflow-hidden relative bg-[#FFD700] shadow-md group border border-yellow-300">
                    <div class="absolute top-0 right-0 w-2/3 h-full bg-[#1E3A8A] transform -skew-x-12 translate-x-10 overflow-hidden border-l-4 border-white/20">
                        <img src="https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&fit=crop" class="w-full h-full object-cover opacity-60 mix-blend-overlay transform skew-x-12 scale-125">
                        <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, #fff 10px, #fff 11px);"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-between px-8 text-white drop-shadow-md">
                        <div class="w-1/3"></div>
                        <div class="relative z-10 text-right">
                            <h2 class="text-2xl font-black uppercase leading-tight italic">Solar Energy<br><span class="text-yellow-400 text-3xl">Company</span></h2>
                            <p class="text-xs font-light tracking-widest uppercase mt-1 opacity-90">Social Media Project</p>
                        </div>
                    </div>
                    <div class="absolute bottom-2 left-4 text-white/30 text-4xl font-black">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M0 10 L10 0 L20 10 L30 0 L40 10 L50 0" />
                            <path d="M0 20 L10 10 L20 20 L30 10 L40 20 L50 10" />
                        </svg>
                    </div>
                </div>
            <?php else : ?>
                <div class="flex-1"></div>
            <?php endif; ?>
        </div>

        <!-- Middle Row: Social & Search -->
        <div class="flex items-center justify-between px-2 mb-6">
            <!-- Left Side: Search -->
            <div class="flex-1 max-w-xl">
                <div class="relative group">
                    <input type="text" placeholder="بحث سريع"
                        class="w-full bg-white border border-gray-200 rounded-full px-6 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition">
                    <svg class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1"></div>
            <!-- Right Side: Social & Lang -->
            <div class="flex items-center gap-6 text-gray-700">
                <span class="font-bold text-sm">تابِعنا</span>
                <div class="flex items-center gap-3">
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
                                class="w-8 h-8 rounded bg-gray-900 text-white flex items-center justify-center hover:bg-green-600 transition text-sm"
                                title="<?php echo esc_attr( $social['platform'] ?? '' ); ?>">
                                <?php if ( ! empty( $icon_url ) ) : ?>
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $social['platform'] ?? '' ); ?>" class="w-4 h-4 object-contain filter invert">
                                <?php else : ?>
                                    <i class="<?php echo esc_attr( $fa_icon ); ?>"></i>
                                <?php endif; ?>
                            </a>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <div class="h-6 w-px bg-gray-300 mx-2"></div>
                <div class="flex items-center gap-2 cursor-pointer hover:text-green-600 transition">
                    <span class="font-bold text-sm">Ar</span>
                    <img src="https://flagcdn.com/w40/sa.png" alt="Saudi Flag"
                        class="w-6 h-4 object-cover rounded-sm shadow-sm">
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="bg-[#F0FDF4] rounded-full p-2 shadow-sm flex items-center justify-center">
            <nav class="flex items-center w-full px-2">
                <?php
                wp_nav_menu( [
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'flex items-center justify-between w-full text-gray-600 font-bold text-sm md:text-base px-4',
                    'fallback_cb'    => '__return_false',
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'walker'         => new Greenergy_Nav_Walker(), // Assuming this walker exists and handles the classes
                ] );
                ?>
            </nav>
        </div>
    </div>
</header>
