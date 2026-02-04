<?php
/**
 * Render for Hero Block
 */
$attributes = isset($attributes) ? $attributes : [];
$cta_url = isset($attributes['ctaUrl']) ? $attributes['ctaUrl'] : '#';
?>
<!-- Hero section -->
<div class="container w-full m-auto">
    <div class="flex flex-col h-auto items-center justify-between lg:flex-row lg:pb-20 lg:px-28 px-4 w-full">
        <!-- Text Content -->
        <div class="flex-1 flex flex-col justify-start items-center lg:items-start gap-4 w-full">
            <!-- Badge -->
            <div class="px-12 h-8 p-2.5 bg-[#2FF7337A] rounded-[44px] backdrop-blur-[2px] inline-flex justify-center items-center gap-2.5" data-aos="fade-down" data-aos-delay="100">
                <div class="text-center justify-start text-neutral-950 text-sm font-normal leading-5">
                    مستقبل الطاقة النظيفة
                </div>
            </div>
            
            <!-- Headlines -->
            <div class="self-stretch flex flex-col justify-start items-center lg:items-start gap-3">
                <div class="w-full lg:w-[610px] text-center lg:text-right justify-start" data-aos="fade-up" data-aos-delay="200">
                    <span class="text-green-700 text-3xl lg:text-4xl font-bold leading-[40px] lg:leading-[48.80px]">اكتشف</span>
                    <span class="text-neutral-950 text-3xl lg:text-4xl font-bold leading-[40px] lg:leading-[48.80px]">
                        عالم الطاقة المتجددة من دليل اللاعبين في السوق
                    </span>
                </div>
                <div class="w-full lg:w-96 text-center lg:text-right justify-start text-green-700 text-base lg:text-lg font-medium leading-6" data-aos="fade-up" data-aos-delay="300">
                    اكتشف دليل الطاقة المتجددة الشامل الذي غيّر مستقبل الملايين.
                </div>
                
                <!-- CTA -->
                <a href="<?php echo esc_url($cta_url); ?>" class="h-12 px-3 py-2 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-3xl inline-flex justify-start items-center gap-3 overflow-hidden hover:scale-105 transition-transform duration-300 shadow-lg hover:shadow-green-500/20" data-aos="zoom-in" data-aos-delay="400">
                    <div class="justify-start text-white text-sm font-medium leading-3">
                        المستقبل يبدأ من هنا
                    </div>
                    <svg class="w-8 h-8 inline self-center bg-white rounded-full" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/arrow-up.svg"></use>
                    </svg>
                </a>
                
             <!-- Stats Row -->
<div class="w-full pb-4" data-aos="fade-up" data-aos-delay="500">
    <div class="py-2 max-sm:py-4 max-sm:gap-[1rem] bg-lime-100 rounded-3xl flex flex-nowrap justify-center lg:justify-start items-center gap-2 overflow-x-auto">
        <a href="" class="flex-shrink-0">
            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/user-octagon.svg"></use>
                </svg>
                <div class="flex flex-col justify-start items-center gap-2.5 max-sm:gap-1">
                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                        +<span class="js-counter" data-target="1000">0</span>
                    </div>
                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                        خبير ومختص
                    </div>
                </div>
            </div>
        </a> 
        
        <a href="" class="flex-shrink-0">
            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/status-up.svg"></use>
                </svg>
                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                        +<span class="js-counter" data-target="500">0</span>
                    </div>
                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                        شركة
                    </div>
                </div>
            </div>
        </a>
        
        <a href="" class="flex-shrink-0">
            <div class="w-32 h-28 max-sm:w-16 max-sm:h-26 gap-2 bg-gradient-to-b from-sky-500 to-blue-700 rounded-[32px] max-sm:rounded-2xl outline outline-[11px] max-sm:outline-8 outline-stone-50 inline-flex flex-col justify-center items-center hover:scale-110 transition-transform duration-300 shadow-xl">
                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/eye.svg"></use>
                </svg>
                <div class="flex flex-col justify-start items-center gap-2 max-sm:gap-1">
                    <div class="text-center justify-start text-white text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                        +<span class="js-counter" data-target="500">0</span>
                    </div>
                    <div class="w-28 max-sm:w-auto text-center text-white text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                        زيارة و مهتمين
                    </div>
                </div>
            </div>
        </a>
        
        <a href="" class="flex-shrink-0">
            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/global.svg"></use>
                </svg>
                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                        +<span class="js-counter" data-target="50">0</span>
                    </div>
                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight">
                        منظمة
                    </div>
                </div>
            </div>
        </a>
        
        <a href="" class="flex-shrink-0">
            <div class="w-28 max-sm:w-14 rounded-xl inline-flex flex-col gap-2 justify-center items-center hover:scale-110 transition-transform duration-300">
                <svg class="w-9 h-10  max-sm:h-8 inline self-center" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/hero/buliding.svg"></use>
                </svg>
                <div class="flex flex-col justify-center items-center gap-2.5 max-sm:gap-1">
                    <div class="text-center justify-start text-green-700 text-2xl max-sm:text-base font-bold leading-6 max-sm:leading-tight whitespace-nowrap">
                        +<span class="js-counter" data-target="11">0</span>M
                    </div>
                    <div class="text-center justify-start text-stone-500 text-sm max-sm:text-xs font-normal leading-6 max-sm:leading-tight break-words">
                        محطة و مشروع
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
            </div>
        </div>
        <!-- Hero Image (Desktop Only) -->
        <div class="bg-center bg-cover bg-no-repeat flex-1 floating-animation gap-2.5 max-lg:hidden min-h-[450px] py-6 px-2 rounded-3xl" 
                style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/hero/image.png');"
                data-aos="zoom-in" data-aos-duration="1500">
        </div>
    </div>
</div>

<style>
@keyframes floating {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0px); }
}
.floating-animation {
    animation: floating 4s ease-in-out infinite;
}
</style>
