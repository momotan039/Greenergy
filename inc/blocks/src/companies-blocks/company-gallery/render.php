<?php

/**
 * Company Gallery Block — render
 * Dynamic gallery with unlimited images, Swiper (3 per slide), pagination when > 4, lightbox on click.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attributes = wp_parse_args($attributes ?? [], [
    'title'  => 'معرض الصور',
    'images' => [],
]);

$title  = $attributes['title'];
$images = is_array($attributes['images']) ? $attributes['images'] : [];
$block_id = 'company-gallery-' . (isset($block->context['postId']) ? $block->context['postId'] : wp_unique_id('gallery-'));

// Normalize images: ensure id and url (accept url, source_url, or resolve from id)
$images = array_values(array_filter(array_map(function ($img) {
    if (! is_array($img)) {
        return null;
    }
    $id  = isset($img['id']) ? (int) $img['id'] : 0;
    $url = isset($img['url']) ? $img['url'] : (isset($img['source_url']) ? $img['source_url'] : '');
    if (empty($url) && $id > 0) {
        $url = wp_get_attachment_image_url($id, 'medium_large');
        if (! $url) {
            $url = wp_get_attachment_image_url($id, 'full');
        }
    }
    if (empty($url)) {
        return null;
    }
    return ['id' => $id, 'url' => $url];
}, $images)));

$show_pagination = count($images) > 4;
$swiper_config = [
    'slidesPerView'  => 'auto',
    'spaceBetween'   => 12,
    'loop'           => count($images) > 3,
    'pagination'     => $show_pagination ? ['clickable' => true, 'el' => '.swiper-pagination'] : false,
    'observer'       => true,
    'observeParents' => true,
    'breakpoints'    => [
        '320'  => ['spaceBetween' => 8, 'slidesPerView' => 1.2],
        '640'  => ['spaceBetween' => 12, 'slidesPerView' => 3.2],
    ],
];
?>

<div class="greenergy-company-gallery bg-white p-4 rounded-lg shadow-lg outline outline-1 outline-gray-200 border border-zinc-100 flex flex-col gap-4" id="<?php echo esc_attr($block_id); ?>">
    <h2 class="text-xl font-bold text-right"><?php echo esc_html($title); ?></h2>

    <?php if (!empty($images)) : ?>
        <div class="greenergy-company-gallery-swiper swiper js-swiper-init js-company-gallery-swiper overflow-hidden pb-2 w-full"
            data-swiper-config="<?php echo esc_attr(wp_json_encode($swiper_config)); ?>">
            <div class="swiper-wrapper">
                <?php foreach ($images as $index => $img) : ?>
                    <div class="swiper-slide greenergy-gallery-slide" style="width: 11rem;">
                        <img src="<?php echo esc_url($img['url']); ?>"
                            class="js-gallery-lightbox-trigger w-full aspect-square rounded-lg object-cover cursor-pointer block"
                            style="height: 11rem;"
                            data-gallery-id="<?php echo esc_attr($block_id); ?>"
                            data-index="<?php echo (int) $index; ?>"
                            alt=""
                            loading="lazy" />
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($show_pagination) : ?>
                <div class="swiper-pagination !relative mt-5 flex justify-center gap-2"></div>
            <?php endif; ?>
        </div>

        <!-- Lightbox (Swiper): images centered, border on placeholder, nav buttons outside image -->
        <div class="greenergy-gallery-lightbox fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/90 p-4" data-gallery-id="<?php echo esc_attr($block_id); ?>" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('معرض الصور', 'greenergy'); ?>">
            <button type="button" class="greenergy-lightbox-close absolute top-4 left-4 z-10 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 text-2xl leading-none" aria-label="<?php esc_attr_e('إغلاق', 'greenergy'); ?>">&times;</button>
            <div class="greenergy-lightbox-inner flex items-center justify-center gap-3 w-full max-w-5xl h-[85vh] px-2">
                <button type="button" class="greenergy-lightbox-prev flex-shrink-0 w-12 h-12 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 z-10" aria-label="<?php esc_attr_e('السابق', 'greenergy'); ?>"><i class="fa-solid fa-chevron-right text-lg" aria-hidden="true"></i></button>
                <div class="swiper js-gallery-lightbox-swiper flex-1 min-w-0 h-full flex items-center justify-center">
                    <div class="swiper-wrapper">
                        <?php foreach ($images as $img) : ?>
                            <div class="swiper-slide flex items-center justify-center">
                                <div class="w-full h-full flex items-center justify-center p-1">
                                    <div class="rounded border border-white/30 bg-neutral-800/40 max-w-full max-h-full flex items-center justify-center overflow-hidden">
                                        <img src="<?php echo esc_url($img['url']); ?>" class="max-w-full max-h-full object-contain block" alt="" />
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" class="greenergy-lightbox-next flex-shrink-0 w-12 h-12 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 z-10" aria-label="<?php esc_attr_e('التالي', 'greenergy'); ?>"><i class="fa-solid fa-chevron-left text-lg" aria-hidden="true"></i></button>
            </div>
        </div>
    <?php else : ?>
        <p class="text-neutral-500 text-right"><?php esc_html_e('لم تتم إضافة صور بعد.', 'greenergy'); ?></p>
    <?php endif; ?>
</div>