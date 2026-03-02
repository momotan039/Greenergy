<?php

/**
 * Company Products Block — render
 * Dynamic block: products selected in editor from Company Product CPT.
 * Pagination via AJAX without full page reload.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attributes = wp_parse_args($attributes ?? [], [
    'title'            => 'المنتجات',
    'selectedProducts' => [],
]);

$title    = $attributes['title'];
$selected = is_array($attributes['selectedProducts']) ? $attributes['selectedProducts'] : [];

$ids = array_values(array_filter(array_map(function ($item) {
    if (is_array($item) && isset($item['id'])) {
        return (int) $item['id'];
    }
    return is_numeric($item) ? (int) $item : null;
}, $selected)));

$per_page    = 4;
$total       = count($ids);
$total_pages = $total <= 0 ? 0 : (int) ceil($total / $per_page);
$page_ids    = $total_pages > 0 ? array_slice($ids, 0, $per_page) : [];
$current_page = 1;

$block_id_attr = isset($block->block_type->name) ? 'greenergy-company-products-' . (isset($block->context['blockId']) ? $block->context['blockId'] : wp_unique_id('block')) : 'greenergy-company-products-' . wp_unique_id('block');
?>

<div class="w-full bg-white p-4 rounded-lg shadow-lg outline outline-1 outline-gray-200 border border-zinc-100 flex flex-col gap-6 js-company-products-block"
    data-product-ids="<?php echo esc_attr(implode(',', $ids)); ?>"
    data-block-id="<?php echo esc_attr($block_id_attr); ?>"
    data-per-page="<?php echo esc_attr($per_page); ?>"
    data-total-pages="<?php echo esc_attr($total_pages); ?>">
    <h2 class="text-xl font-bold text-right"><?php echo esc_html($title); ?></h2>

    <div class="relative min-h-[200px]">
        <div class="js-company-products-loader absolute inset-0 bg-white/80 rounded-lg flex items-center justify-center z-10 transition-opacity duration-200 pointer-events-none opacity-0" aria-hidden="true">
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-2 border-green-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-neutral-600"><?php esc_html_e('جاري التحميل...', 'greenergy'); ?></span>
            </div>
        </div>
        <div class="grid grid-cols-2  md:grid-cols-3 xl:grid-cols-4 gap-4 js-company-products-grid">
            <?php if (empty($ids)) : ?>
                <p class="col-span-full text-neutral-500 text-right text-sm"><?php esc_html_e('لم تتم إضافة منتجات بعد. اختر المنتجات من الإعدادات.', 'greenergy'); ?></p>
            <?php elseif (empty($page_ids)) : ?>
                <p class="col-span-full text-neutral-500 text-right text-sm"><?php esc_html_e('لا توجد منتجات.', 'greenergy'); ?></p>
            <?php else : ?>
                <?php
                $query = new WP_Query([
                    'post_type'      => 'company_product',
                    'post__in'       => $page_ids,
                    'orderby'        => 'post__in',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                ]);
                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        get_template_part('templates/components/company-product-card', null, [
                            'post_id'       => get_the_ID(),
                            'block_id_attr' => $block_id_attr,
                        ]);
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($total_pages > 1) : ?>
        <nav class="greenergy-pagination greenergy-company-products-pagination mt-2 flex justify-center items-center gap-2 flex-wrap" aria-label="<?php esc_attr_e('تنقل المنتجات', 'greenergy'); ?>">
            <?php if ($current_page > 1) : ?>
                <button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page - 1); ?>" aria-label="<?php esc_attr_e('الصفحة السابقة', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg></button>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <?php $active = $i === $current_page; ?>
                <button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg transition-all text-sm <?php echo $active ? 'bg-green-600 text-white font-semibold border border-transparent' : 'border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500'; ?>" data-page="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></button>
            <?php endfor; ?>
            <?php if ($current_page < $total_pages) : ?>
                <button type="button" class="js-company-products-page w-10 h-10 flex justify-center items-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:text-green-600 hover:border-green-500 transition-all" data-page="<?php echo (int) ($current_page + 1); ?>" aria-label="<?php esc_attr_e('الصفحة التالية', 'greenergy'); ?>"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg></button>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <?php
    if (! empty($ids)) {
        $query_all = new WP_Query([
            'post_type'      => 'company_product',
            'post__in'       => $ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);
        if ($query_all->have_posts()) {
            while ($query_all->have_posts()) {
                $query_all->the_post();
                $post_id  = get_the_ID();
                $thumb    = get_the_post_thumbnail_url($post_id, 'medium');
                $variants = class_exists('Greenergy_CPT_Company_Product') ? Greenergy_CPT_Company_Product::get_variants($post_id) : [];
    ?>
                <div id="product-modal-content-<?php echo esc_attr($post_id); ?>" class="hidden" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;">
                    <?php get_template_part('templates/components/select-variant-product', null, [
                        'product_title' => get_the_title(),
                        'product_thumb' => $thumb ?: '',
                        'variants'      => $variants,
                        'show_close'    => true,
                    ]); ?>
                </div>
        <?php
            }
            wp_reset_postdata();
        }
        ?>
        <div id="<?php echo esc_attr($block_id_attr); ?>-modal" class="greenergy-product-modal flex fixed inset-0 z-[9999] items-center justify-center p-3 sm:p-4" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('تفاصيل المنتج', 'greenergy'); ?>" style="display: none; background: rgba(0,0,0,0.6);">
            <div class="greenergy-product-modal-backdrop absolute inset-0 cursor-pointer" aria-hidden="true"></div>
            <div class="greenergy-product-modal-content relative z-10 max-h-[90vh] overflow-auto w-full max-w-[657px]"></div>
        </div>
        <div id="<?php echo esc_attr($block_id_attr); ?>-download-modal" class="greenergy-product-download-modal flex fixed inset-0 z-[10000] items-center justify-center p-3 sm:p-4" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('تحميل الملف', 'greenergy'); ?>" style="display: none; background: rgba(0,0,0,0.6);">
            <div class="greenergy-download-modal-backdrop absolute inset-0 cursor-pointer" aria-hidden="true"></div>
            <div class="greenergy-download-modal-content relative z-10 w-full max-w-[657px] max-h-[90vh] overflow-auto"></div>
        </div>
    <?php } ?>
</div>