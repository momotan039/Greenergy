<?php

/**
 * Default Header Template
 */
?>
<header>
    <!-- Mobile Header -->
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
                <img class="h-20 w-20 mix-blend-darken transition-transform duration-300 scale-105" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" />
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
                <img class="h-full w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/google-ad.png" />
            </div>
            <!-- left ad -->
            <div class="h-20 flex-1 rounded-xl border-2 border-green-700 overflow-hidden shadow-sm">
                <img class="h-full w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/ad-spolar.jpg" />
            </div>
        </div>

        <!-- Row 3: Social & Lang -->
        <div class="flex justify-between items-center px-6">
            <!-- social media links -->
            <div class="h-6 flex justify-start items-center gap-4">
                <div class="w-7 h-7 text-right justify-start text-neutral-950 text-base font-normal leading-6">
                    تابعنا
                </div>
                <svg class="w-6 h-6 inline scale-[0.8] self-center transition-transform duration-300 hover:scale-125 hover:text-green-600 cursor-pointer" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/ic16-linkedin.svg"></use>
                </svg>
                <svg class="w-6 h-6 inline scale-[0.8] self-center transition-transform duration-300 hover:scale-125 hover:text-red-600 cursor-pointer" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/youtube.svg"></use>
                </svg>
                <svg class="w-6 h-6 inline scale-[0.8] self-center transition-transform duration-300 hover:scale-125 hover:text-blue-600 cursor-pointer" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/google.svg"></use>
                </svg>
                <svg class="w-6 h-6 inline scale-[0.8] self-center transition-transform duration-300 hover:scale-125 hover:text-blue-700 cursor-pointer" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/facebook.svg"></use>
                </svg>
            </div>
            <!-- language switcher -->
            <div class="flex justify-start items-center gap-3">
                <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 hover:bg-gray-100 transition-colors duration-300 cursor-pointer group shadow-sm border border-gray-100">
                    <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                    <div class="w-4 h-5 justify-start text-neutral-950 text-base font-normal  leading-6">
                        Ar
                    </div>
                    <svg class="w-[2rem] h-[1.2rem] transform group-hover:rotate-180 transition-transform duration-300" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Backdrop -->
        <div id="mobile-menu-backdrop" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity duration-300"></div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu" class="fixed top-0 right-[-100%] w-[85%] max-w-[400px] h-[100dvh] bg-white/95 backdrop-blur-xl text-neutral-950 z-50 shadow-2xl transition-all duration-300 ease-in-out flex flex-col">

            <!-- 1. Header: Logo & Close -->
            <div class="px-6 py-5 flex justify-between items-center bg-white/50 border-b border-gray-100">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="block">
                    <img class="h-12 w-auto object-contain mix-blend-darken" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="Logo" />
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
                    <input type="search" class="w-full h-12 pl-12 pr-4 bg-gray-50 text-neutral-900 rounded-2xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-300 placeholder-gray-400 font-medium" placeholder="بحث عن..." name="s" />
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
                        <span class="text-base">الرئيسية</span>
                    </a>

                    <?php
                    $locations = get_nav_menu_locations();
                    if (isset($locations['primary'])) {
                        $menu_items = wp_get_nav_menu_items($locations['primary']);
                        if ($menu_items) {
                            foreach ($menu_items as $item) {
                                // Minimal active state check
                                $is_active = (get_queried_object_id() == $item->object_id);
                                $active_class = $is_active ? 'bg-green-50 text-green-700 font-bold' : 'text-neutral-700 font-medium hover:bg-gray-50 hover:text-green-700';
                    ?>
                                <a href="<?php echo esc_url($item->url); ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-xl <?php echo $active_class; ?> transition-all duration-200 group">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-green-500 transition-colors <?php echo $is_active ? 'bg-green-500' : ''; ?>"></span>
                                    <span class="text-base"><?php echo esc_html($item->title); ?></span>
                                </a>
                    <?php
                            }
                        }
                    }
                    ?>
                </nav>
            </div>

            <!-- 4. Footer: Social & Tools -->
            <div class="p-6 bg-gray-50/80 border-t border-gray-100 mt-auto backdrop-blur-sm">
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-500">تابعنا على</span>
                        <div class="flex gap-4">
                            <a href="#" class="text-gray-400 hover:text-[#0077b5] hover:scale-110 transition-all"><svg class="w-6 h-6">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/ic16-linkedin.svg"></use>
                                </svg></a>
                            <a href="#" class="text-gray-400 hover:text-[#FF0000] hover:scale-110 transition-all"><svg class="w-6 h-6">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/youtube.svg"></use>
                                </svg></a>
                            <a href="#" class="text-gray-400 hover:text-[#4285F4] hover:scale-110 transition-all"><svg class="w-6 h-6">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/google.svg"></use>
                                </svg></a>
                            <a href="#" class="text-gray-400 hover:text-[#1877F2] hover:scale-110 transition-all"><svg class="w-6 h-6">
                                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/facebook.svg"></use>
                                </svg></a>
                        </div>
                    </div>

                    <div class="w-full h-px bg-gray-200"></div>

                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-green-500 transition-colors group">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" class="w-6 h-6" alt="Lang">
                            <span class="text-sm font-bold text-neutral-800">العربية - AR</span>
                        </div>
                        <svg class="w-[2rem] h-[1.2rem] text-gray-400 group-hover:text-green-600 transition-colors">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <style>
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
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('mobile-menu-close');
            const menu = document.getElementById('mobile-menu');
            const backdrop = document.getElementById('mobile-menu-backdrop');

            function openMenu() {
                menu.style.right = '0';
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                menu.style.right = '-100%';
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }

            toggleBtn.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);
            backdrop.addEventListener('click', closeMenu);
        </script>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:block">
        <!-- ads and logo-->
        <div class="self-stretch h-50 px-2 pt-2 flex justify-center items-start gap-8">
            <!-- right ad -->
            <div class="self-stretch h-[136px] flex-1 rounded-2xl border-2 border-green-700 shadow-sm transition-transform hover:scale-[1.01] duration-500">
                <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/google-ad.png" />
            </div>

            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block transition-transform duration-300 hover:scale-105">
                <img class="flex mix-blend-darken w-[194px] h-[171px]" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" />
            </a>

            <!-- left ad -->
            <div class="self-stretch h-[136px] flex-1 rounded-2xl border-2 border-green-700 shadow-sm transition-transform hover:scale-[1.01] duration-500">
                <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/ad-spolar.jpg" />
            </div>
        </div>
        <!-- search - social media - lang -->
        <div class="container m-auto backdrop-blur-2xl justify-between items-center">
            <div class="flex justify-between items-center gap-10">
                <!-- serach input -->
                <div class="w-80 h-12 px-4 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-200 flex justify-start items-center gap-2 transition-all duration-300 hover:shadow-lg hover:outline-green-500 cursor-text group">
                    <svg class="w-6 h-6 inline self-center transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                    </svg>
                    <div class="justify-start text-zinc-500 text-base font-normal  leading-6">
                        بحث سريع
                    </div>
                </div>

                <div class="flex justify-center items-center gap-6">
                    <!-- social media links -->
                    <div class="h-6 flex justify-start items-center gap-4">
                        <div class="w-7 h-7 text-right justify-start text-neutral-950 text-base font-normal  leading-6">
                            تابعنا
                        </div>

                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-green-600 drop-shadow-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/ic16-linkedin.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-red-600 drop-shadow-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/youtube.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-blue-600 drop-shadow-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/google.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-blue-700 drop-shadow-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/facebook.svg"></use>
                        </svg>
                    </div>
                    <!-- language switcher -->
                    <div class="flex justify-start items-center gap-3">
                        <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 cursor-pointer hover:bg-gray-100 transition-all duration-300 group shadow-sm border border-transparent hover:border-gray-200">
                            <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                            <div class="w-4 h-5 justify-start text-neutral-950 text-base font-normal  leading-6">
                                Ar
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
        <div id="desktop-smart-nav" class="bg-green-100 flex justify-center items-center z-50 transition-all duration-300">
            <div class="w-full container my-4 h-16 px-8 py-2 relative bg-white/60 rounded-[1000px] backdrop-blur-xl inline-flex justify-center items-center gap-1.5 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex justify-start items-center gap-10">
                    <!-- Static Home Link: Ensures 'الرئيسية' always goes to home -->
                    <?php
                    $is_home_active = is_front_page() && is_home();
                    $home_class = $is_home_active ? 'bg-green-700/10 hover:bg-green-700/20 text-green-700 font-medium' : 'hover:bg-green-50 text-neutral-950 font-normal';
                    ?>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer transition-all duration-300 hover:scale-105 <?php echo esc_attr($home_class); ?>">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="justify-start text-base leading-6 w-full h-full flex items-center text-black">
                            الرئيسية
                        </a>
                    </div>

                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'fallback_cb'    => false,
                        'walker'         => new Greenergy_Nav_Walker(),
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>