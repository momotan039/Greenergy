<?php
/**
 * Courses Widget Block Template.
 *
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'self-stretch p-2 bg-white rounded-xl shadow-[0px_4px_14px_0px_rgba(0,0,0,0.06)] flex flex-col justify-start items-center gap-2',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="inline-flex justify-end items-center gap-2">
        <div class="text-center justify-start text-neutral-950 text-base font-bold leading-6">
            <svg class="w-6 h-6 inline self-center" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/book.svg"></use>
            </svg>
            الدورات التدريبية
        </div>
    </div>
    <div class="self-stretch p-2 bg-stone-50 rounded-2xl flex flex-col justify-start items-end gap-5">
        <div class="self-stretch flex flex-col justify-start items-end gap-4">
            <div class="self-stretch h-72 px-3 py-6 rounded-lg inline-flex justify-start items-end gap-2.5 bg-cover bg-center" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/new-2.jpg');">
                <div class="text-center justify-start text-white text-2xl leading-6">
                    تعلم معنا
                </div>
            </div>
            <div class="self-stretch flex flex-col justify-center items-end gap-2">
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                    <div class="text-center justify-start text-green-700 text-base leading-6">12 دورة</div>
                    <div class="text-right justify-start text-stone-500 text-sm font-normal leading-6">أحدث الدورات</div>
                </div>
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                    <div class="text-center justify-start text-sky-500 text-base leading-6">8 دورات</div>
                    <div class="text-right justify-start text-stone-500 text-sm font-normal leading-6">الأكثر تسجيلاً</div>
                </div>
                <a href="#" class="self-stretch h-9 px-4 bg-gradient-to-br from-sky-500 to-blue-700 rounded-[55px] inline-flex justify-center items-center gap-2 hover:shadow-lg transition-shadow">
                    <div class="leading-5 h-6 text-white pt-1">اكتشف الدورات</div>
                    <span class="pb-1 text-white text-2xl leading-6">←</span>
                </a>
            </div>
        </div>
    </div>
</div>
