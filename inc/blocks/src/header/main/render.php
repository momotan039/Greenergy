<?php

/**
 * Unified Header Block Template.
 *
 * @package Greenergy
 */

// --- Attributes & Defaults ---
$logo_url = $attributes['logoUrl'] ?? '';
if (empty($logo_url)) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : get_template_directory_uri() . '/assets/images/logo.png';
}

$ad_right = $attributes['adRight'] ?? [];
$ad_left  = $attributes['adLeft'] ?? [];

$home_label = !empty($attributes['homeLabel']) ? $attributes['homeLabel'] : __('الرئيسية', 'greenergy');
$search_label = !empty($attributes['searchLabel']) ? $attributes['searchLabel'] : __('ابحث هنا...', 'greenergy');
$search_placeholder = !empty($attributes['searchPlaceholder']) ? $attributes['searchPlaceholder'] : __('بحث عن...', 'greenergy');
$follow_us_text = !empty($attributes['followUsText']) ? $attributes['followUsText'] : __('تابعنا', 'greenergy');
$lang_label = !empty($attributes['langLabel']) ? $attributes['langLabel'] : __('العربية - AR', 'greenergy');

// Social links for mobile drawer
$social_links = !empty($attributes['socialLinks']) ? $attributes['socialLinks'] : [];

// Menu Selection
$menu_id = !empty($attributes['menuId']) ? $attributes['menuId'] : 0;
$menu_items = [];

if ($menu_id > 0) {
    $menu_items = wp_get_nav_menu_items($menu_id);
} else {
    $locations = get_nav_menu_locations();
    // Prioritize 'primary' for desktop, but fall back if needed.
    // Since we want ONE menu for both, we prioritize the selected one, or primary.
    if (isset($locations['primary'])) {
        $menu_items = wp_get_nav_menu_items($locations['primary']);
    } elseif (isset($locations['mobile'])) {
        $menu_items = wp_get_nav_menu_items($locations['mobile']);
    }
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'w-full relative z-50',
]);

/**
 * Helper: Recursive Mobile Menu
 */
if (!function_exists('greenergy_render_mobile_menu_recursive')) {
    function greenergy_render_mobile_menu_recursive($items, $parent_id = 0)
    {
        if (empty($items)) return;
        foreach ($items as $item) {
            if ($item->menu_item_parent != $parent_id) continue;

            $has_children = false;
            foreach ($items as $sub) {
                if ($sub->menu_item_parent == $item->ID) {
                    $has_children = true;
                    break;
                }
            }

            $is_active = (get_queried_object_id() == $item->object_id);
            $active_class = $is_active ? 'bg-green-50 text-green-700 font-bold' : 'text-neutral-700 font-medium hover:bg-gray-50 hover:text-green-700';

            if ($has_children) {
?>
                <div class="mobile-menu-item-has-children">
                    <div class="flex items-center justify-between px-4 py-3.5 rounded-xl <?php echo $active_class; ?> cursor-pointer group transition-all duration-200" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180');">
                        <div class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-green-500 transition-colors <?php echo $is_active ? 'bg-green-500' : ''; ?>"></span>
                            <span class="text-base"><?php echo esc_html($item->title); ?></span>
                        </div>
                        <svg class="w-5 h-5 transition-transform duration-300 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="hidden pr-6 mt-1 flex flex-col gap-1 border-r-2 border-green-100 mr-2">
                        <?php greenergy_render_mobile_menu_recursive($items, $item->ID); ?>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <a href="<?php echo esc_url($item->url); ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-xl <?php echo $active_class; ?> transition-all duration-300 group">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-green-500 transition-colors <?php echo $is_active ? 'bg-green-500' : ''; ?>"></span>
                    <span class="text-base"><?php echo esc_html($item->title); ?></span>
                </a>
<?php
            }
        }
    }
}
?>

<div <?php echo $wrapper_attributes; ?>>

    <!-- =========================================
         DESKTOP VIEW (hidden on mobile)
         ========================================= -->
    <div class="hidden lg:block">
        <!-- ads and logo-->
        <div class="self-stretch h-50 px-2 pt-2 flex justify-center items-start gap-8">
            <!-- right ad -->
            <div class="self-stretch h-[136px] flex-1 rounded-2xl border-2 border-green-700 shadow-sm transition-transform hover:scale-[1.01] duration-500 overflow-hidden">
                <?php if (!empty($ad_right['adType']) && $ad_right['adType'] === 'code'): ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-50 text-xs text-gray-400">
                        <?php echo $ad_right['adCode'] ?? ''; ?>
                    </div>
                <?php else:
                    $r_img = !empty($ad_right['imageUrl']) ? $ad_right['imageUrl'] : get_template_directory_uri() . '/assets/images/google-ad.png';
                    $r_link = $ad_right['adLink'] ?? '#';
                ?>
                    <a href="<?php echo esc_url($r_link); ?>" class="block w-full h-full">
                        <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo esc_url($r_img); ?>" alt="Ad">
                    </a>
                <?php endif; ?>
            </div>

            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block transition-transform duration-300 hover:scale-105">
                <img class="flex mix-blend-darken w-[194px] h-[171px]" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
            </a>

            <!-- left ad -->
            <div class="self-stretch h-[136px] flex-1 rounded-2xl border-2 border-green-700 shadow-sm transition-transform hover:scale-[1.01] duration-500 overflow-hidden">
                <?php if (!empty($ad_left['adType']) && $ad_left['adType'] === 'code'): ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-50 text-xs text-gray-400">
                        <?php echo $ad_left['adCode'] ?? ''; ?>
                    </div>
                <?php else:
                    $l_img = !empty($ad_left['imageUrl']) ? $ad_left['imageUrl'] : get_template_directory_uri() . '/assets/images/ad-spolar.jpg';
                    $l_link = $ad_left['adLink'] ?? '#';
                ?>
                    <a href="<?php echo esc_url($l_link); ?>" class="block w-full h-full">
                        <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo esc_url($l_img); ?>" alt="Ad">
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <!-- search - social media - lang -->
        <div class="container m-auto backdrop-blur-2xl justify-between items-center">
            <div class="flex justify-between items-center gap-10">
                <!-- serach input -->
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="w-80 h-12 px-4 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-200 flex justify-start items-center gap-2 transition-all duration-300 hover:shadow-lg hover:outline-green-500 cursor-text group">
                    <button type="submit" class="p-0 border-none bg-transparent flex items-center">
                        <svg class="w-6 h-6 inline self-center transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                        </svg>
                    </button>
                    <input type="search" name="s" class="w-full bg-transparent border-none focus:ring-0 p-0 justify-start text-zinc-500 text-base font-normal leading-6 placeholder-zinc-500" placeholder="<?php echo esc_attr($search_placeholder); ?>">
                </form>

                <div class="flex justify-center items-center gap-6">
                    <!-- social media links -->
                    <div class="h-6 flex justify-start items-center gap-4">
                        <div class="text-right justify-start text-neutral-950 text-base font-normal leading-6 whitespace-nowrap">
                            <?php echo esc_html($follow_us_text); ?>
                        </div>
                        <?php
                        $filtered_socials = array_filter($social_links, function ($link) {
                            $s_icon = $link['icon'] ?? '';
                            return !($s_icon && (strpos(strtolower($s_icon), 'twitter') !== false || strpos(strtolower($s_icon), 'x-icon') !== false));
                        });
                        foreach (array_slice($filtered_socials, 0, 4) as $social) :
                            $s_icon = $social['icon'] ?? '';
                            $s_url  = $social['url'] ?? '#';
                            $icon_path = '/assets/images/vuesax/bold/' . $s_icon;
                            // Map icons to hover colors
                            $hover_class = 'hover:text-green-600';
                            if (strpos($s_icon, 'youtube') !== false) $hover_class = 'hover:text-red-600';
                            if (strpos($s_icon, 'google') !== false) $hover_class = 'hover:text-blue-600';
                            if (strpos($s_icon, 'facebook') !== false) $hover_class = 'hover:text-blue-700';
                        ?>
                            <a href="<?php echo esc_url($s_url); ?>" target="_blank" class="flex items-center transition-all duration-300 hover:scale-125">
                                <svg class="w-6 h-6 inline self-center <?php echo $hover_class; ?> cursor-pointer shadow-sm" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri() . $icon_path; ?>"></use>
                                </svg>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <!-- language switcher -->
                    <div class="flex justify-start items-center gap-3">
                        <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 cursor-pointer hover:bg-gray-100 transition-all duration-300 group shadow-sm border border-transparent hover:border-gray-200">
                            <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                            <div class="justify-start text-neutral-950 text-base font-normal leading-6">
                                <?php echo esc_html($lang_label); ?>
                            </div>
                            <svg class="w-[2rem] h-[1.2rem] transform group-hover:rotate-180 transition-transform duration-300" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- menu nav -->
        <div id="unified-desktop-nav" class="bg-green-100 flex justify-center items-center z-50 transition-all duration-300">
            <div class="w-full container my-4 h-16 px-8 py-2 relative bg-white/60 rounded-[1000px] backdrop-blur-xl inline-flex justify-center items-center gap-1.5 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex justify-start items-center gap-10">
                    <!-- Static Home Link: Ensures 'الرئيسية' always goes to home -->
                    <?php
                    $is_home_active = is_front_page() && is_home();
                    $home_class = $is_home_active ? 'bg-green-700/10 hover:bg-green-700/20 text-green-700 font-medium' : 'hover:bg-green-50 text-neutral-950 font-normal';
                    ?>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer transition-all duration-300 hover:scale-105 <?php echo esc_attr($home_class); ?>">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="justify-start text-base leading-6 w-full h-full flex items-center text-black">
                            <?php echo esc_html($home_label); ?>
                        </a>
                    </div>

                    <?php
                    if ($menu_id > 0) {
                        wp_nav_menu([
                            'menu'           => $menu_id,
                            'container'      => false,
                            'items_wrap'     => '%3$s',
                            'fallback_cb'    => false,
                            'walker'         => class_exists('Greenergy_Nav_Walker') ? new Greenergy_Nav_Walker() : '',
                        ]);
                    } else {
                        wp_nav_menu([
                            'theme_location' => 'primary',
                            'container'      => false,
                            'items_wrap'     => '%3$s',
                            'fallback_cb'    => false,
                            'walker'         => class_exists('Greenergy_Nav_Walker') ? new Greenergy_Nav_Walker() : '',
                        ]);
                    }
                    ?>

                    <!-- Custom Extra Links -->
                    <?php
                    $nav_links = !empty($attributes['navLinks']) ? $attributes['navLinks'] : [];
                    foreach ($nav_links as $link):
                        if (empty($link['label'])) continue;
                    ?>
                        <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer transition-all duration-300 hover:scale-105 hover:bg-green-50 text-neutral-950 font-normal">
                            <a href="<?php echo esc_url($link['url']); ?>" class="justify-start text-base leading-6 w-full h-full flex items-center text-black">
                                <?php echo esc_html($link['label']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div><!-- End Desktop View -->


    <!-- =========================================
         MOBILE VIEW (hidden on desktop)
         ========================================= -->
    <!-- =========================================
         MOBILE VIEW (hidden on desktop)
         ========================================= -->
    <div id="mobile-header-container" class="block lg:hidden bg-white pb-4 relative z-50">
        <!-- Row 1: Menu, Logo, Search (Smart Sticky) -->
        <div id="mobile-smart-header" class="flex justify-between items-center px-4 py-4 relative w-full bg-white z-[60] shadow-sm transition-all duration-300">
            <!-- Menu Toggle -->
            <button id="mobile-menu-toggle" class="p-2 transition-colors duration-300 hover:bg-gray-100 rounded-lg">
                <svg class="w-8 h-8 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img class="h-20 w-20 mix-blend-darken transition-transform duration-300 scale-105 object-contain" src="<?php echo esc_url($logo_url); ?>" alt="Logo" />
            </a>

            <!-- Search Icon -->
            <button class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-300 group">
                <svg class="w-6 h-6 text-neutral-950 group-hover:text-green-700 transition-colors" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
            </button>
        </div>

        <!-- Row 2: Ads -->
        <div class="flex justify-between items-center px-4 gap-4 mb-4">
            <!-- right ad -->
            <div class="h-20 flex-1 rounded-xl border-2 border-green-700 overflow-hidden shadow-sm">
                <?php
                $r_img = !empty($ad_right['imageUrl']) ? $ad_right['imageUrl'] : get_template_directory_uri() . '/assets/images/google-ad.png';
                $r_link = $ad_right['adLink'] ?? '#';
                ?>
                <a href="<?php echo esc_url($r_link); ?>" class="block h-full w-full">
                    <img class="h-full w-full object-cover" src="<?php echo esc_url($r_img); ?>" alt="Ad">
                </a>
            </div>
            <!-- left ad -->
            <div class="h-20 flex-1 rounded-xl border-2 border-green-700 overflow-hidden shadow-sm">
                <?php
                $l_img = !empty($ad_left['imageUrl']) ? $ad_left['imageUrl'] : get_template_directory_uri() . '/assets/images/ad-spolar.jpg';
                $l_link = $ad_left['adLink'] ?? '#';
                ?>
                <a href="<?php echo esc_url($l_link); ?>" class="block h-full w-full">
                    <img class="h-full w-full object-cover" src="<?php echo esc_url($l_img); ?>" alt="Ad">
                </a>
            </div>
        </div>

        <!-- Row 3: Social & Lang -->
        <div class="flex justify-between items-center px-6">
            <!-- social media links -->
            <div class="h-6 flex justify-start items-center gap-4">
                <div class="text-right justify-start text-neutral-950 text-base font-normal leading-6 whitespace-nowrap">
                    <?php echo esc_html($follow_us_text); ?>
                </div>
                <?php
                // Using the same filtered list for mobile
                foreach (array_slice($filtered_socials, 0, 4) as $link) :
                    $s_icon = $link['icon'] ?? '';
                    $s_url  = $link['url'] ?? '#';
                    $icon_path = '/assets/images/vuesax/bold/' . $s_icon;
                    // Map icons to hover colors
                    $hover_class = 'hover:text-green-600';
                    if (strpos($s_icon, 'youtube') !== false) $hover_class = 'hover:text-red-600';
                    if (strpos($s_icon, 'google') !== false) $hover_class = 'hover:text-blue-600';
                    if (strpos($s_icon, 'facebook') !== false) $hover_class = 'hover:text-blue-700';
                ?>
                    <a href="<?php echo esc_url($s_url); ?>" target="_blank" class="flex items-center scale-[0.8] transition-all duration-300 hover:scale-110">
                        <svg class="w-6 h-6 inline self-center <?php echo $hover_class; ?> cursor-pointer shadow-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri() . $icon_path; ?>"></use>
                        </svg>
                    </a>
                <?php endforeach; ?>
            </div>
            <!-- language switcher -->
            <div class="flex justify-start items-center gap-3">
                <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 hover:bg-gray-100 transition-colors duration-300 cursor-pointer group shadow-sm border border-gray-100">
                    <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                    <div class="justify-start text-neutral-950 text-base font-normal leading-6">
                        <?php echo esc_html($lang_label); ?>
                    </div>
                    <svg class="w-[2rem] h-[1.2rem] transform group-hover:rotate-180 transition-transform duration-300" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Backdrop -->
        <div id="mobile-menu-backdrop" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[1000] transition-opacity duration-300"></div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu" class="fixed top-0 right-[-100%] w-[85%] max-w-[400px] h-[100dvh] bg-white/95 backdrop-blur-xl text-neutral-950 z-[1001] shadow-2xl transition-all duration-300 ease-in-out flex flex-col">

            <!-- 1. Header: Logo & Close -->
            <div class="px-6 py-5 flex justify-between items-center bg-white/50 border-b border-gray-100">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="block">
                    <img class="h-12 w-auto object-contain mix-blend-darken" src="<?php echo esc_url($logo_url); ?>" alt="Logo" />
                </a>
                <button id="mobile-menu-close" class="p-2 -mr-2 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-full transition-all duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- 2. Search Bar -->
            <div class="px-6 py-6">
                <form role="search" method="get" class="relative group" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="w-full h-12 pl-12 pr-4 bg-gray-50 text-neutral-900 rounded-2xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-300 placeholder-gray-400 font-medium" placeholder="<?php echo esc_attr($search_placeholder); ?>" name="s" />
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 p-1 text-gray-400 group-focus-within:text-green-600 transition-colors">
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- 3. Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-2 custom-scrollbar">
                <nav class="flex flex-col gap-1.5">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-xl <?php echo is_front_page() ? 'bg-green-50 text-green-700 font-bold' : 'text-neutral-700 font-medium hover:bg-gray-50 hover:text-green-700'; ?> transition-all duration-200">
                        <span class="text-base"><?php echo esc_html($home_label); ?></span>
                    </a>

                    <?php if ($menu_items) : greenergy_render_mobile_menu_recursive($menu_items);
                    endif; ?>
                    <?php
                    // Render Custom Links in Mobile Drawer
                    $nav_links = !empty($attributes['navLinks']) ? $attributes['navLinks'] : [];
                    foreach ($nav_links as $link):
                        if (empty($link['label'])) continue;
                    ?>
                        <a href="<?php echo esc_url($link['url']); ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-neutral-700 font-medium hover:bg-gray-50 hover:text-green-700 transition-all duration-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-green-500 transition-colors"></span>
                            <span class="text-base"><?php echo esc_html($link['label']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- 4. Footer: Social & Tools -->
            <div class="p-6 bg-gray-50/80 border-t border-gray-100 mt-auto backdrop-blur-sm">
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-500">تابعنا على</span>
                        <div class="flex gap-4">
                            <?php foreach (array_slice($filtered_socials, 0, 4) as $link) :
                                $s_icon = $link['icon'] ?? '';
                                $s_url  = $link['url'] ?? '#';
                                $icon_path = '/assets/images/vuesax/bold/' . $s_icon;
                                // Map icons to hover colors
                                $hover_class = 'hover:text-green-600';
                                if (strpos($s_icon, 'youtube') !== false) $hover_class = 'hover:text-red-600';
                                if (strpos($s_icon, 'google') !== false) $hover_class = 'hover:text-blue-600';
                                if (strpos($s_icon, 'facebook') !== false) $hover_class = 'hover:text-blue-700';
                            ?>
                                <a href="<?php echo esc_url($s_url); ?>" target="_blank" class="text-gray-400 <?php echo $hover_class; ?> hover:scale-110 transition-all">
                                    <svg class="w-6 h-6">
                                        <use href="<?php echo get_template_directory_uri() . $icon_path; ?>"></use>
                                    </svg>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="w-full h-px bg-gray-200"></div>

                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-green-500 transition-colors group">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" class="w-6 h-6" alt="Lang">
                            <span class="text-sm font-bold text-neutral-800"><?php echo esc_html($lang_label); ?></span>
                        </div>
                        <svg class="w-[2rem] h-[1.2rem] text-gray-400 group-hover:text-green-600 transition-colors">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- End Mobile View -->


    <!-- =========================================
         SCRIPTS & STYLES
         ========================================= -->
    <style>
        .desktop-nav-sticky {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            transform: translateY(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .desktop-nav-hidden {
            transform: translateY(-100%);
        }

        /* Mobile Sticky from header.php */
        .mobile-smart-header-fixed {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        #mobile-menu.is-open {
            right: 0;
        }

        /* Ensure drawer is very high z-index */
        #mobile-menu-backdrop {
            z-index: 2000;
        }

        #mobile-menu {
            z-index: 2001;
        }

        /* Custom scrollbar for mobile menu */
        #mobile-menu .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        #mobile-menu .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        #mobile-menu .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }

        #mobile-menu .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
        }
    </style>

    <script>
        (function() {
            const initHeader = () => {
                // Desktop Sticky Logic
                const deskNav = document.getElementById('unified-desktop-nav');
                if (deskNav) {
                    let lastScroll = 0;
                    const threshold = 200;
                    window.addEventListener('scroll', () => {
                        const currentScroll = window.pageYOffset;
                        if (currentScroll <= threshold) {
                            deskNav.classList.remove('desktop-nav-sticky', 'desktop-nav-hidden');
                            return;
                        }
                        deskNav.classList.add('desktop-nav-sticky');
                        if (currentScroll > lastScroll && currentScroll > threshold + 100) {
                            deskNav.classList.add('desktop-nav-hidden');
                        } else if (currentScroll < lastScroll) {
                            deskNav.classList.remove('desktop-nav-hidden');
                        }
                        lastScroll = currentScroll;
                    }, {
                        passive: true
                    });
                }

                // Mobile Always Sticky Logic (from header.php)
                const mobileNav = document.getElementById('mobile-smart-header');
                const mobileParent = document.getElementById('mobile-header-container');

                if (mobileNav) {
                    window.addEventListener('scroll', () => {
                        const st = window.pageYOffset;
                        if (st > 0) {
                            if (!mobileNav.classList.contains('mobile-smart-header-fixed')) {
                                mobileNav.classList.add('mobile-smart-header-fixed');
                                if (mobileParent) mobileParent.style.paddingTop = mobileNav.offsetHeight + 'px';
                            }
                        } else {
                            if (mobileNav.classList.contains('mobile-smart-header-fixed')) {
                                mobileNav.classList.remove('mobile-smart-header-fixed');
                                if (mobileParent) mobileParent.style.paddingTop = '0px';
                            }
                        }
                    }, {
                        passive: true
                    });
                }

                // Mobile Drawer Logic
                const toggleBtn = document.getElementById('mobile-menu-toggle');
                const closeBtn = document.getElementById('mobile-menu-close');
                const menu = document.getElementById('mobile-menu');
                const backdrop = document.getElementById('mobile-menu-backdrop');

                if (toggleBtn && menu && backdrop) {
                    const openMenu = (e) => {
                        if (e) e.preventDefault();
                        menu.style.right = '0';
                        backdrop.classList.remove('hidden');
                        requestAnimationFrame(() => {
                            backdrop.style.opacity = '1';
                        });
                        document.body.style.overflow = 'hidden';
                    };

                    const closeMenu = (e) => {
                        if (e) e.preventDefault();
                        menu.style.right = '-100%';
                        backdrop.style.opacity = '0';
                        setTimeout(() => backdrop.classList.add('hidden'), 300);
                        document.body.style.overflow = '';
                    };

                    toggleBtn.addEventListener('click', openMenu);
                    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
                    backdrop.addEventListener('click', closeMenu);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeader);
            } else {
                initHeader();
            }
        })();
    </script>
</div>