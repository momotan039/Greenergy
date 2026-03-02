<?php

/**
 * Download product file — قسم تحميل ملف المنتج / الخيار
 * يُستخدم بعد اختيار variant في select-variant-product.
 *
 * @param array $args {
 *   @type string $product_title  عنوان المنتج (للعرض في الهيدر)
 *   @type string $file_url        رابط التحميل المباشر (إن وُجد)
 *   @type string $variant_label   وصف الخيار اختياري (استطاعة - تقنية)
 * }
 */
if (! defined('ABSPATH')) {
    exit;
}
$args = wp_parse_args($args ?? [], [
    'product_title' => '',
    'file_url'      => '',
    'variant_label' => '',
]);
$title   = $args['product_title'] ?: __('تحميل الملف', 'greenergy');
$file_url = $args['file_url'] ? trim($args['file_url']) : '';
$variant_label = $args['variant_label'] ? trim($args['variant_label']) : '';
$heading = $variant_label ? sprintf(__('تحميل ملف %s', 'greenergy'), $title) . ' — ' . $variant_label : sprintf(__('تحميل ملف %s', 'greenergy'), $title);
?>
<section class="greenergy-download-product-file w-full max-w-[657px] min-w-0 px-4 sm:px-6 md:px-8 py-4 bg-white rounded-2xl outline outline-1 outline-offset-[-1px] outline-zinc-200 flex flex-col gap-4">

    <header class="w-full flex justify-between items-center">
        <h2 class="text-right text-neutral-950 text-base font-bold flex-1 mr-2"><?php echo esc_html($heading); ?></h2>
        <button type="button" class="greenergy-download-modal-close p-1 rounded hover:bg-neutral-100 transition" aria-label="<?php esc_attr_e('رجوع لاختيار المواصفات', 'greenergy'); ?>">
            <i class="fa-regular fa-circle-xmark text-xl"></i>
        </button>
    </header>

    <?php if ($file_url) : ?>
        <div class="flex flex-col gap-2.5 w-full">
            <a href="<?php echo esc_url($file_url); ?>" class="greenergy-download-direct h-11 p-2.5 bg-[linear-gradient(90.94deg,#348934_24.86%,#64B24D_70.31%,#ABEF74_122.33%)]  hover:font-bold hover:text-white rounded-lg flex justify-center items-center gap-2.5 text-white text-sm font-medium no-underline  transition" download>
                <span class="relative w-4 h-4 inline-flex items-center justify-center" aria-hidden="true">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a1 1 0 01-.707-.293l-3-3a1 1 0 111.414-1.414L10 9.586V3a1 1 0 112 0v6.586l2.293-2.293a1 1 0 111.414 1.414l-3 3A1 1 0 0110 12z" />
                        <path d="M17 13v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a1 1 0 10-2 0v4a4 4 0 004 4h10a4 4 0 004-4v-4a1 1 0 10-2 0z" />
                    </svg>
                </span>
                <?php esc_html_e('تحميل مباشر', 'greenergy'); ?>
            </a>
            <?php
            $is_viewable = preg_match('/\.(pdf|pdf#|html?)$/i', $file_url);
            if ($is_viewable) :
            ?>
                <a href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener" class="h-11 p-2.5 bg-zinc-100 rounded-lg flex justify-center items-center gap-2.5 text-green-700 text-sm font-medium no-underline hover:bg-green-700 hover:text-white transition">
                    <span class="relative w-4 h-4 inline-flex" aria-hidden="true"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg></span>
                    <?php esc_html_e('معاينة على الموقع', 'greenergy'); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-2 w-full">
            <span class="flex-1 h-px bg-zinc-200"></span>
            <span class="text-stone-500 text-sm text-center"><?php esc_html_e('أو', 'greenergy'); ?></span>
            <span class="flex-1 h-px bg-zinc-200"></span>
        </div>

        <div class="flex flex-col gap-2 w-full ">
            <span class="text-neutral-950 text-sm font-medium text-right"><?php esc_html_e('إرسال عبر واتساب', 'greenergy'); ?></span>
            <div class="flex gap-2 w-full">
                <input type="text" placeholder="<?php esc_attr_e('ادخل الرقم', 'greenergy'); ?>" class="greenergy-whatsapp-number flex-1 h-11 px-3 py-2.5 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 text-stone-500 text-xs" />
                <a href="#" class="greenergy-share-whatsapp w-11 h-11 flex justify-center items-center rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 text-green-600 hover:bg-zinc-50 transition" aria-label="<?php esc_attr_e('مشاركة عبر واتساب', 'greenergy'); ?>" data-file-url="<?php echo esc_attr($file_url); ?>"><i class="fa-brands fa-whatsapp text-xl"></i></a>
            </div>
        </div>

        <div class="flex flex-col gap-2 w-full ">
            <span class="text-neutral-950 text-sm font-medium text-right"><?php esc_html_e('إرسال عبر البريد الإلكتروني', 'greenergy'); ?></span>
            <div class="flex gap-2 w-full">
                <input type="email" placeholder="<?php esc_attr_e('ادخل الايميل', 'greenergy'); ?>" class="greenergy-email-input flex-1 h-11 px-3 py-2.5 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 text-stone-500 text-xs" />
                <button type="button" class="greenergy-share-email w-11 h-11 flex justify-center items-center rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 hover:bg-zinc-50 transition" aria-label="<?php esc_attr_e('إرسال بالبريد', 'greenergy'); ?>" data-file-url="<?php echo esc_attr($file_url); ?>"><i class="fa-solid fa-envelope text-xl text-blue-500"></i></button>
            </div>
        </div>
    <?php else : ?>
        <p class="text-right text-neutral-500 text-sm"><?php esc_html_e('لا يوجد ملف مرفق لهذا الخيار.', 'greenergy'); ?></p>
    <?php endif; ?>

</section>