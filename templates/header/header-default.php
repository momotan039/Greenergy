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
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img class="h-20 w-20 mix-blend-darken transition-transform duration-300 hover:scale-105" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" />
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

                        <svg class="w-6 h-6 inline self-center transition-transform duration-300 hover:scale-125 hover:text-green-600 cursor-pointer" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/ic16-linkedin.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center transition-transform duration-300 hover:scale-125 hover:text-red-600 cursor-pointer" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/youtube.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center transition-transform duration-300 hover:scale-125 hover:text-blue-600 cursor-pointer" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/google.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center transition-transform duration-300 hover:scale-125 hover:text-blue-700 cursor-pointer" aria-hidden="true">
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
                            <svg class="w-6 h-4 transform group-hover:rotate-180 transition-transform duration-300" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                            </svg>
                        </div>
                    </div>
                </div>

        <!-- Mobile Menu Backdrop -->
        <div id="mobile-menu-backdrop" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity duration-300"></div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-menu" class="fixed top-0 right-[-100%] w-[80%] max-w-sm bg-white/95 backdrop-blur-xl shadow-2xl z-50 h-screen transition-all duration-300 ease-in-out">
             <div class="flex flex-col h-full">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <span class="font-bold text-lg text-green-700">القائمة</span>
                    <button id="mobile-menu-close" class="p-2 hover:bg-red-50 hover:text-red-500 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto flex-1">
                     <div class="flex flex-col gap-2">
                        <!-- Static Home Link for Mobile -->
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block py-3 px-4 rounded-xl bg-green-50 text-green-700 font-medium hover:bg-green-100 transition-colors">
                            الرئيسية
                        </a>
                        <!-- Dynamic Menu -->
                        <?php
                            wp_nav_menu( [
                                'theme_location' => 'primary', // Use primary menu so user sees the same links
                                'container'      => false,
                                'items_wrap'     => '<ul class="flex flex-col gap-2">%3$s</ul>',
                                'fallback_cb'    => false,
                                // Using a simplified output for mobile
                                'walker'         => '', 
                            ] );
                        ?>
                     </div>
                </div>
             </div>
        </div>

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
            
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block transition-transform duration-300 hover:scale-105">
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
                            <svg class="w-6 h-4 transform group-hover:rotate-180 transition-transform duration-300" aria-hidden="true">
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
                         <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="justify-start text-base leading-6 w-full h-full flex items-center text-black">
                            الرئيسية
                        </a>
                    </div>

                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'fallback_cb'    => false,
                        'walker'         => new Greenergy_Nav_Walker(),
                    ] );
                    ?>
                </div>
            </div>
        </div>
    </div>
</header>
