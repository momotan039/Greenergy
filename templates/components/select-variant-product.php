<?php

/**
 * Select variant product — عرض اختيار مواصفات المنتج (استطاعة / تقنية)
 * عند اختيار خيار يُعرض قسم تحميل الملف الخاص به.
 *
 * @param array $args {
 *   @type string $product_title  عنوان المنتج
 *   @type string $product_thumb   رابط صورة المنتج
 *   @type array  $variants       مصفوفة { feature, capacity, file_id? }
 *   @type bool   $show_close     إظهار زر الإغلاق في الهيدر (افتراضي true)
 * }
 */
if (! defined('ABSPATH')) {
    exit;
}
$args = wp_parse_args($args ?? [], [
    'product_title' => '',
    'product_thumb' => '',
    'variants'      => [],
    'show_close'    => true,
]);
$product_title = $args['product_title'];
$product_thumb = $args['product_thumb'] ?: '';
$variants      = is_array($args['variants']) ? $args['variants'] : [];
$show_close    = (bool) $args['show_close'];

// إثراء كل variant برابط الملف للعرض
foreach ($variants as $i => $v) {
    $fid = isset($v['file_id']) ? absint($v['file_id']) : 0;
    $variants[$i]['file_url'] = $fid ? wp_get_attachment_url($fid) : '';
}

$title_choose = sprintf(__('اختر مواصفات %s', 'greenergy'), $product_title ?: __('المنتج', 'greenergy'));
?>
<section class="greenergy-select-variant-product w-full max-w-[657px] min-w-0 px-4 sm:px-6 md:px-8 py-4 bg-white rounded-2xl outline outline-1 outline-offset-[-1px] outline-zinc-200 flex flex-col gap-4">

    <header class="w-full flex justify-between items-center">
        <h1 class="text-right text-neutral-950 text-base font-bold"><?php echo esc_html($title_choose); ?></h1>
        <?php if ($show_close) : ?>
            <button type="button" class="greenergy-product-modal-close p-1 rounded hover:bg-neutral-100 transition" aria-label="<?php esc_attr_e('إغلاق', 'greenergy'); ?>">
                <i class="fa-regular fa-circle-xmark text-xl"></i>
            </button>
        <?php endif; ?>
    </header>

    <div class="greenergy-variant-list-scroll overflow-y-auto max-h-[50vh] w-full">
        <div class="w-full flex flex-col gap-4 p-2">
            <?php if (empty($variants)) : ?>
                <p class="text-right text-neutral-500 text-sm"><?php esc_html_e('لا توجد خيارات لهذا المنتج.', 'greenergy'); ?></p>
            <?php else : ?>
                <?php foreach ($variants as $i => $v) : ?>
                    <?php
                    $feat  = isset($v['feature']) ? trim((string) $v['feature']) : '';
                    $cap   = isset($v['capacity']) ? trim((string) $v['capacity']) : '';
                    $line  = implode(' - ', array_filter([$cap, $feat]));
                    ?>
                    <article role="button" tabindex="0" class="js-select-variant greenergy-variant-option flex justify-between items-center w-full p-4 rounded-xl outline outline-1 outline-offset-[-1px] transition-colors duration-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 <?php echo $i === 0 ? 'bg-green-100 ring-2 ring-green-600 ring-offset-2' : 'bg-neutral-100 hover:bg-green-100'; ?>" data-variant-index="<?php echo esc_attr($i); ?>">
                        <div class="flex-1 flex flex-col gap-3">
                            <h2 class="text-right text-neutral-950 text-base font-medium"><?php echo esc_html($product_title); ?></h2>
                            <?php if ($line !== '') : ?>
                                <p class="text-right text-neutral-950 text-sm font-medium"><?php echo esc_html($line); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($product_thumb) : ?>
                            <img class="w-20 h-16 object-cover mix-blend-darken flex-shrink-0" src="<?php echo esc_url($product_thumb); ?>" alt="<?php echo esc_attr($product_title); ?>" />
                        <?php else : ?>
                            <div class="w-20 h-16 bg-neutral-200 rounded flex items-center justify-center text-neutral-500 text-xs flex-shrink-0"><?php esc_html_e('صورة', 'greenergy'); ?></div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! empty($variants)) : ?>
        <div class="greenergy-variant-downloads hidden absolute opacity-0 pointer-events-none w-0 h-0 overflow-hidden" aria-hidden="true">
            <?php foreach ($variants as $i => $v) : ?>
                <?php
                $feat = isset($v['feature']) ? trim((string) $v['feature']) : '';
                $cap  = isset($v['capacity']) ? trim((string) $v['capacity']) : '';
                $vlabel = implode(' — ', array_filter([$cap, $feat]));
                $file_url = isset($v['file_url']) ? $v['file_url'] : '';
                ?>
                <div class="greenergy-variant-download-block" data-variant-index="<?php echo esc_attr($i); ?>">
                    <?php get_template_part('templates/components/download-product-file', null, [
                        'product_title' => $product_title,
                        'file_url'      => $file_url,
                        'variant_label' => $vlabel,
                    ]); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>