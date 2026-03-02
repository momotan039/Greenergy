<?php

/**
 * Single company product card (for grid display)
 * Used by company-products block and AJAX pagination.
 *
 * @param array $args { post_id, block_id_attr }
 */
if (! defined('ABSPATH')) {
    exit;
}
$post_id       = isset($args['post_id']) ? absint($args['post_id']) : 0;
$block_id_attr = isset($args['block_id_attr']) ? $args['block_id_attr'] : '';
if (! $post_id) {
    return;
}
$thumb   = get_the_post_thumbnail_url($post_id, 'medium');
$excerpt = get_the_excerpt($post_id);
?>
<div class="bg-white rounded-2xl shadow-md overflow-hidden transition-all duration-300 group relative">
    <?php if ($thumb) : ?>
        <img src="<?php echo esc_url($thumb); ?>" class="w-full h-64 object-cover" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" />
    <?php else : ?>
        <div class="w-full h-64 bg-neutral-200 flex items-center justify-center text-neutral-500 text-sm"><?php esc_html_e('صورة المنتج', 'greenergy'); ?></div>
    <?php endif; ?>
    <div class="flex transition-all duration-300 scale-0 group-hover:scale-100 absolute inset-0 bg-gradient-to-t from-black/70 to-transparent p-4 flex-col justify-end text-white">
        <h4 class="text-lg font-medium"><?php echo esc_html(get_the_title($post_id)); ?></h4>
        <?php if ($excerpt) : ?>
            <p class="text-sm opacity-90 line-clamp-2"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
        <button type="button" class="greenergy-product-detail-btn mt-3 border border-white rounded-lg py-2 text-sm hover:bg-white hover:text-black transition inline-block text-center w-full cursor-pointer" data-modal-content-id="product-modal-content-<?php echo esc_attr($post_id); ?>" data-modal-id="<?php echo esc_attr($block_id_attr); ?>">
            <?php esc_html_e('عرض التفاصيل', 'greenergy'); ?>
        </button>
    </div>
</div>
