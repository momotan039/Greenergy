<?php

/**
 * Expert Practical Experience Block — render
 * Admin-addable items: title, year, points. Collapsible when more than 2 items.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attrs   = $attributes ?? [];
$items = isset($attrs['experiences']) && is_array($attrs['experiences']) ? $attrs['experiences'] : [];
$items = array_values(array_filter($items, function ($i) {
    return is_array($i);
}));

if (empty($items)) {
    return;
}

$use_collapsible = count($items) > 2;
?>

<style>
.expert-practical-experience-block details.expert-exp-item {
    outline-color: rgb(229 231 235);
    transition: outline-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
}
.expert-practical-experience-block details.expert-exp-item[open] {
    background-color: rgb(240 249 255);
    outline-color: rgb(14 165 233);
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.12);
}
.expert-practical-experience-block details.expert-exp-item:hover {
    outline-color: rgb(14 165 233);
}
.expert-practical-experience-block .expert-exp-summary {
    transition: color 0.2s ease;
}
.expert-practical-experience-block details.expert-exp-item[open] .expert-exp-summary {
    color: rgb(7 89 133);
}
.expert-practical-experience-block .expert-exp-chevron {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}
.expert-practical-experience-block details.expert-exp-item[open] .expert-exp-chevron {
    transform: rotate(180deg);
}
.expert-practical-experience-block .expert-exp-content {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.expert-practical-experience-block details.expert-exp-item[open] .expert-exp-content {
    grid-template-rows: 1fr;
}
.expert-practical-experience-block .expert-exp-content-inner {
    overflow: hidden;
}
.expert-practical-experience-block .expert-exp-content-inner > div {
    animation: expert-exp-fade-in 0.3s ease-out;
}
@keyframes expert-exp-fade-in {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="expert-practical-experience-block p-4 bg-white rounded-lg shadow-lg outline outline-1 outline-offset-[-1px] outline-gray-200">
    <div class="flex flex-col gap-4">
        <h2 class="text-right text-neutral-950 text-xl font-bold leading-5"><?php esc_html_e('الخبرات العملية', 'greenergy'); ?></h2>
        <div class="flex flex-col gap-2.5">
            <?php foreach ($items as $index => $item) :
                $title  = isset($item['title']) ? trim((string) $item['title']) : '';
                $year   = isset($item['year']) ? trim((string) $item['year']) : '';
                $points = isset($item['points']) && is_array($item['points']) ? array_filter(array_map('trim', $item['points'])) : [];
                $is_first = ($index === 0);
                $is_open = $use_collapsible ? $is_first : true;
            ?>
                <?php if ($use_collapsible) : ?>
                    <details class="expert-exp-item p-2 bg-neutral-100 rounded-lg outline outline-1 outline-offset-[-1px] outline-gray-200 hover:outline-sky-500" <?php echo $is_open ? 'open' : ''; ?>>
                        <summary class="expert-exp-summary cursor-pointer list-none flex flex-wrap items-center justify-between gap-2 py-2 pr-2 ps-10 relative">
                            <span class="text-right text-sky-500 text-base font-bold leading-6 flex-1 min-w-0"><?php echo $title !== '' ? esc_html($title) : esc_html__('خبرة', 'greenergy') . ' #' . ($index + 1); ?></span>
                            <?php if ($year !== '') : ?>
                                <span class="h-6 px-4 bg-sky-500 rounded-[55px] flex items-center justify-center text-white text-xs font-bold leading-5 flex-shrink-0"><?php echo esc_html($year); ?></span>
                            <?php endif; ?>
                            <span class="expert-exp-chevron absolute start-2 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-sky-500" aria-hidden="true">
                                <i class="fa-solid fa-chevron-down text-sm"></i>
                            </span>
                        </summary>
                        <div class="expert-exp-content">
                            <div class="expert-exp-content-inner">
                                <div class="pt-3 pb-1 pr-2 pl-2 flex flex-col gap-2">
                                    <?php foreach ($points as $point) : ?>
                                        <div class="text-right text-neutral-800 text-base font-normal leading-6"><?php echo esc_html($point); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php else : ?>
                    <div class="p-2 bg-neutral-100 rounded-lg outline outline-1 outline-offset-[-1px] outline-gray-200 hover:outline-sky-500 flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-right text-sky-500 text-base font-bold leading-6"><?php echo $title !== '' ? esc_html($title) : esc_html__('خبرة', 'greenergy'); ?></span>
                            <?php if ($year !== '') : ?>
                                <span class="h-6 px-4 bg-sky-500 rounded-[55px] flex items-center justify-center text-white text-xs font-bold leading-5"><?php echo esc_html($year); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php foreach ($points as $point) : ?>
                            <div class="text-right text-neutral-800 text-base font-normal leading-6"><?php echo esc_html($point); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>