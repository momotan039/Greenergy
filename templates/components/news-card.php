<?php

/**
 * News Card Component
 *
 * @package Greenergy
 * @param array $args ['item' => $item]
 */

$item = $args['item'] ?? [];

if (empty($item)) {
    return;
}
?>
<div class="relative md:h-44 group hover:bg-green-600 w-full rounded-2xl inline-flex justify-start items-center gap-4 overflow-hidden max-sm:flex-col bg-white border border-gray-100 hover:shadow-xl transition-all duration-300">
    <a href="<?php echo esc_url($item['permalink']); ?>" class="absolute inset-0 z-10 w-full h-full" aria-label="<?php echo esc_attr($item['title']); ?>"></a>

    <div class="h-full w-44 max-sm:w-full max-sm:h-60 shrink-0 bg-cover bg-center"
        style="background-image: url('<?php echo esc_url($item['image']); ?>');"
        role="img"
        aria-label="<?php echo esc_attr($item['title']); ?>">
    </div>

    <div class="flex-1 self-stretch pl-6 max-sm:px-4 py-4 inline-flex flex-col justify-between ">
        <div class="self-stretch flex flex-col justify-start  gap-2">
            <div class="self-stretch inline-flex justify-between items-center flex-row-reverse">
                <time datetime="<?php echo esc_attr($item['date']); ?>" class="group-hover:text-white text-neutral-500 text-xs font-normal">
                    <?php echo esc_html($item['date']); ?>
                </time>
                <?php if (!empty($item['cat'])) : ?>
                    <span class=" text-primary group-hover:text-white group-hover:bg-primary/50  text-xs font-bold px-3 py-1 bg-primary/10 rounded-full">
                        #<?php echo esc_html($item['cat']); ?>
                    </span>
                <?php endif; ?>
            </div>

            <h3 class="text-right text-neutral-900 text-lg font-bold leading-tight line-clamp-2 transition-colors group-hover:text-white">
                <?php echo esc_html($item['title']); ?>
            </h3>

            <p class="group-hover:text-white text-right text-neutral-500 text-sm font-normal line-clamp-1">
                <?php echo esc_html($item['excerpt']); ?>
            </p>
        </div>

        <div class="self-stretch inline-flex justify-between items-center flex-row-reverse mt-4 pt-4 border-t border-gray-50">
            <span class="group-hover:text-white text-primary text-sm font-bold flex items-center gap-2">
                إقرأ المزيد
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </span>
            <div class="flex justify-start items-center gap-1.5 group-hover:text-white text-neutral-400 text-sm ">
                <i class="far fa-eye text-xs"></i>
                <span><?php echo esc_html($item['views']); ?></span>
            </div>
        </div>
    </div>
</div>