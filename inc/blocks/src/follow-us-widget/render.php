<?php
/**
 * Follow Us Widget Block Template.
 *
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'self-stretch p-2 bg-white rounded-xl shadow-[0px_4px_14px_0px_rgba(0,0,0,0.06)] flex flex-col justify-start items-center gap-2',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="inline-flex justify-end items-center gap-2">
        <div class="text-center justify-start text-neutral-950 text-base leading-6">
            <svg class="w-6 h-6 inline self-center" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/add-circle.svg"></use>
            </svg>
            تابعنا
        </div>
    </div>
    <div class="self-stretch p-2 bg-stone-50 rounded-2xl flex flex-col justify-start items-end gap-5">
        <div class="self-stretch flex flex-col justify-start items-end gap-4">
            <div class="self-stretch flex flex-col justify-center items-end gap-2">
                <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                    <div class="flex-1 text-center justify-start text-neutral-950 text-sm font-medium leading-6">
                        تابعنا لتبقى على اطلاع على آخر الفرص والدورات في مجال الطاقة المتجددة
                    </div>
                </div>
                <div class="self-stretch inline-flex justify-center items-center gap-2">
                    <a href="#" class="p-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2 hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-gray-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/linkedin_icon.svg"></use>
                        </svg>
                    </a>
                    <a href="#" class="p-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2 hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-gray-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/facebook_icon.svg"></use>
                        </svg>
                    </a>
                    <a href="#" class="p-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2 hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-gray-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/isntagram_icon.svg"></use>
                        </svg>
                    </a>
                    <a href="#" class="p-4 rounded-lg outline outline-1 outline-offset-[-1px] outline-black/20 flex justify-center items-center gap-2 hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-gray-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/twitter_icon.svg"></use>
                        </svg>
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</div>
