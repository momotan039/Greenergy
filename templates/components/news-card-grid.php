<?php

/**
 * News Card Grid Component (Vertical/Square Style)
 *
 * @package Greenergy
 * @param array $args ['item' => $item]
 */

$item = $args['item'] ?? [];

if (empty($item)) {
    return;
}

// Map field names if necessary (compatibility between different blocks)
$title     = $item['title'] ?? '';
$excerpt   = $item['excerpt'] ?? '';
$date      = $item['date'] ?? '';
$views     = $item['views'] ?? '0';
$image     = $item['image'] ?? 'https://placehold.co/800X800';
$permalink = $item['permalink'] ?? '#';
$cat       = $item['cat'] ?? '';

?>
<div class="bg-white rounded-2xl overflow-hidden cursor-pointer group hover:shadow-2xl hover:shadow-green-600/10 hover:-translate-y-2 transition-all duration-500 h-full border border-gray-100 lg:border-none relative">
    <a href="<?php echo esc_url($permalink); ?>" class="absolute inset-0 z-10 w-full h-full"></a>
    <div class="relative aspect-square overflow-hidden">
        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
    </div>
    <div class="px-2 py-4 text-right group-hover:bg-green-600 relative">
        <div class="self-stretch inline-flex justify-end items-start gap-4 w-full">
            <div class="group-hover:text-white flex-1 text-right justify-start text-neutral-800 text-sm leading-5 line-clamp-2">
                <?php echo esc_html($title); ?>
            </div>
            <svg class="w-6 h-4 inline" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/more.svg"></use>
            </svg>
        </div>
        <p class="group-hover:text-white text-gray-600 text-xs md:text-sm mt-3 mb-3 line-clamp-2">
            <?php echo esc_html($excerpt); ?>
        </p>
        <div class="flex items-center justify-between text-[10px] md:text-xs font-bold text-gray-500 border-t border-gray-100 pt-3 group-hover:border-white/20">
            <div class="flex items-center gap-1">
                <i class="far fa-eye group-hover:text-white"></i>
                <span class="group-hover:text-white"><?php echo esc_html($views); ?></span>
            </div>
            <div dir="ltr" class="group-hover:text-white"><?php echo esc_html($date); ?></div>
        </div>
    </div>
</div>