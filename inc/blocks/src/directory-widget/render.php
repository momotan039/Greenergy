<?php
/**
 * Directory Widget Block Template.
 *
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'self-stretch p-2 bg-white rounded-xl shadow-lg outline outline-1 outline-gray-200 flex flex-col justify-start items-center gap-2 max-md:max-w-[18rem] flex-none',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="text-center justify-start text-neutral-950 text-base font-bold leading-6">
        <svg class="w-6 h-6 inline self-center" aria-hidden="true">
            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/buildings-2.svg"></use>
        </svg>
        دليل الشركات
    </div>
    <div class="self-stretch p-2 bg-stone-50 rounded-2xl flex flex-col justify-start items-end gap-5">
        <div class="self-stretch flex flex-col justify-start items-end gap-4">
            <div class="max-md:place-content-end self-stretch max-md:h-[150px] md:h-72  px-3 py-6 rounded-lg inline-flex flex-col justify-start items-end md:justify-end md:items-start gap-2.5 bg-cover bg-center" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/new-2.jpg');">
                <div class="max-md:hidden text-center justify-start text-white text-lg md:text-2xl leading-6">
                    اكتشف الشركات
                </div>
                <p class="md:hidden w-full text-white font-medium text-sm">اكتشف أكثر من 200 شركة ومنظمة في مجال الطاقة النظيفة</p>
                   <div class="md:hidden self-stretch inline-flex justify-start items-center gap-2">
                    <div class="h-6 px-4 rounded-[55px] outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-start items-center gap-2">
                        <svg class="w-3 h-3 inline self-center" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/global_white.svg"></use>
                        </svg>
                        <div class="text-white text-xs font-normal">+15 دولة</div>
                    </div>
                    <div class="h-6 px-4 rounded-[55px] outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-start items-center gap-2">
                        <svg class="w-3 h-3 inline self-center" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/profile-2user_white.svg"></use>
                        </svg>
                        <div class="text-white text-xs font-normal">+150 خبير</div>
                    </div>
                </div>
            </div>
            <div class="self-stretch flex flex-col justify-center items-end gap-2">
                <div class="max-md:hidden self-stretch inline-flex justify-center items-center">
                    <div class="text-center justify-start">
                        <span class="text-neutral-950 text-sm font-medium leading-6">اكتشف أكثر من </span>
                        <span class="text-green-700 text-sm font-medium leading-6">200 شركة</span>
                        <span class="text-neutral-950 text-sm font-medium leading-6"> ومنظمة في مجال<br /> الطاقة النظيفة</span>
                    </div>
                </div>
                <div class="max-md:hidden self-stretch inline-flex justify-center items-center gap-2">
                    <div class="h-6 px-4 rounded-[55px] outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2">
                        <svg class="w-3 h-3 inline self-center" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/global.svg"></use>
                        </svg>
                        <div class="text-neutral-800 text-xs font-normal">+15 دولة</div>
                    </div>
                    <div class="h-6 px-4 rounded-[55px] outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2">
                        <svg class="w-3 h-3 inline self-center" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/profile-2user.svg"></use>
                        </svg>
                        <div class="text-neutral-800 text-xs font-normal">+150 خبير</div>
                    </div>
                </div>
                <a href="#" class="self-stretch h-9 px-4 max-md:border-2 max-md:border-sky-500 max-md:bg-white md:bg-gradient-to-br from-sky-500 to-blue-700 rounded-[55px] inline-flex justify-center  gap-2 hover:shadow-lg transition-shadow">
                    <div class="leading-5 h-6 text-white max-md:text-sky-500 pt-1">الانتقال الى الدليل</div>
                    <span class="pb-1 text-white max-md:text-sky-500 text-2xl leading-6">←</span>
                </a>
            </div>
        </div>
    </div>
</div>
