<?php
/**
 * Default Header Template
 */
?>
<header>
    <!-- Mobile Header -->
    <div class="block lg:hidden bg-white pb-4">
        <!-- Row 1: Menu, Logo, Search -->
        <div class="flex justify-between items-center px-4 py-4">
            <!-- Menu Toggle -->
            <button id="mobile-menu-toggle" class="p-2">
                <svg class="w-8 h-8 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Logo -->
            <img class="h-16 w-16 mix-blend-darken" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" />
            
            <!-- Search Icon -->
            <button class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-300">
                 <svg class="w-6 h-6 text-neutral-950" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
            </button>
        </div>

        <!-- Row 2: Ads -->
        <div class="flex justify-between items-center px-4 gap-4 mb-4">
            <!-- right ad -->
            <div class="h-20 flex-1 rounded-xl border-2 border-green-700 overflow-hidden">
                <img class="h-full w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/google-ad.png" />
            </div>
             <!-- left ad -->
            <div class="h-20 flex-1 rounded-xl border-2 border-green-700 overflow-hidden">
                <img class="h-full w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/ad-spolar.jpg" />
            </div>
        </div>

        <!-- Row 3: Social & Lang -->
        <div class="flex justify-between items-center px-6">
                    <!-- social media links -->
                    <div class="h-6 flex justify-start items-center gap-4">
                        <div class="w-7 h-7 text-right justify-start text-neutral-950 text-base font-normal  leading-6">
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
                        <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 hover:bg-gray-100 transition-colors duration-300 cursor-pointer group">
                            <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                            <div class="w-4 h-5 justify-start text-neutral-950 text-base font-normal  leading-6">
                                Ar
                            </div>
                            <svg class="w-6 h-4 " aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                            </svg>
                        </div>
                    </div>
                </div>

        <!-- Mobile Menu Drawer (Hidden by default) -->
        <div id="mobile-menu" class="hidden absolute top-[72px] right-0 w-64 bg-white/95 backdrop-blur-xl shadow-lg z-50 h-screen p-4 border-l border-green-100">
             <div class="flex flex-col gap-4">
                <div class="py-2 px-4 rounded-lg bg-green-50 text-green-700 font-medium">الرئيسية</div>
                <div class="py-2 px-4 rounded-lg hover:bg-gray-50 text-neutral-950">الاخبار</div>
                <div class="py-2 px-4 rounded-lg hover:bg-gray-50 text-neutral-950">التدريبات</div>
                <div class="py-2 px-4 rounded-lg hover:bg-gray-50 text-neutral-950">الوضائف</div>
                <div class="py-2 px-4 rounded-lg hover:bg-gray-50 text-neutral-950">من نحن</div>
                <div class="py-2 px-4 rounded-lg hover:bg-gray-50 text-neutral-950">الدليل</div>
             </div>
        </div>

        <script>
            document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });
        </script>
    </div>

    <!-- Desktop Header -->
    <div class="hidden lg:block">
        <!-- ads and logo-->
        <div class="self-stretch h-50 px-2 pt-2 flex justify-center items-start gap-8">
            <!-- right ad -->
            <div class="self-stretch h-28 flex-1 rounded-2xl border-2 border-green-700">
                <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/google-ad.png" />
            </div>
            <img class="flex mix-blend-darken" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" />
            <!-- left ad -->
            <div class="self-stretch h-28 flex-1 rounded-2xl border-2 border-green-700">
                <img class="self-stretch h-full rounded-2xl w-full object-cover" src="<?php echo get_template_directory_uri(); ?>/assets/images/ad-spolar.jpg" />
            </div>
        </div>
        <!-- search - social media - lang -->
        <div class="container m-auto backdrop-blur-2xl justify-between items-center">
            <div class="flex justify-between items-center gap-10">
                <!-- serach input -->
                <div class="w-80 h-12 px-4 bg-white rounded-3xl outline outline-1 outline-offset-[-1px] outline-gray-200 flex justify-start items-center gap-2 transition-all duration-300 hover:shadow-md hover:outline-green-500 cursor-text group">
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

                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-green-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/ic16-linkedin.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-red-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/youtube.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-blue-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/google.svg"></use>
                        </svg>
                        <svg class="w-6 h-6 inline self-center cursor-pointer transition-all duration-300 hover:scale-125 hover:text-blue-700" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/facebook.svg"></use>
                        </svg>
                    </div>
                    <!-- language switcher -->
                    <div class="flex justify-start items-center gap-3">
                        <div class="h-9 px-4 py-2 rounded-[102px] flex justify-center gap-2 cursor-pointer hover:bg-gray-100 transition-all duration-300 group">
                            <img class="w-6 h-6" src="<?php echo get_template_directory_uri(); ?>/assets/images/flag-1.png" />
                            <div class="w-4 h-5 justify-start text-neutral-950 text-base font-normal  leading-6">
                                Ar
                            </div>
                            <svg class="w-6 h-4 " aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- menu nav -->
        <div class="bg-green-100 flex justify-center items-center">
            <div class="w-full container my-4 h-16 px-8 py-2 relative bg-white/60 rounded-[1000px] backdrop-blur-xl inline-flex justify-center items-center gap-1.5">
                <div class="flex justify-start items-center gap-10">
                    <div class="h-9 px-5 py-2 bg-green-700/10 rounded-3xl flex justify-center items-center gap-2.5 cursor-pointer hover:scale-105 transition-transform duration-300">
                        <div class="justify-start text-green-700 text-base font-medium  leading-6">
                            الرئيسية
                        </div>
                    </div>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer hover:bg-green-50 transition-all duration-300 hover:scale-105">
                        <div class="justify-start text-neutral-950 text-base font-normal  leading-6">
                            الاخبار
                        </div>
                    </div>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer hover:bg-green-50 transition-all duration-300 hover:scale-105">
                        <div class="justify-start text-neutral-950 text-base font-normal  leading-6">
                            التدريبات
                        </div>
                    </div>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer hover:bg-green-50 transition-all duration-300 hover:scale-105">
                        <div class="justify-start text-neutral-950 text-base font-normal  leading-6">
                            الوضائف
                        </div>
                    </div>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer hover:bg-green-50 transition-all duration-300 hover:scale-105">
                        <div class="justify-start text-neutral-950 text-base font-normal  leading-6">
                            من نحن
                        </div>
                    </div>
                    <div class="h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer hover:bg-green-50 transition-all duration-300 hover:scale-105">
                        <div class="justify-start text-neutral-950 text-base font-normal  leading-6">
                            الدليل
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
