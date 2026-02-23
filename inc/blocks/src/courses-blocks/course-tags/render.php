<?php

/**
 * Render Course Tags Block
 */

$post_id = get_the_ID();
$tags = get_the_terms($post_id, 'course_tag');

// Fallback for editor
if (!$post_id || is_admin()) {
    if (!$tags) {
        $tags = (object) [
            (object) ['name' => 'مشروع_الصداوي', 'slug' => 'الصداوي'],
            (object) ['name' => 'الطاقة_الشمسية', 'slug' => 'solar'],
            (object) ['name' => 'رؤية_2030', 'slug' => '2030'],
            (object) ['name' => 'السعودية', 'slug' => 'saudi'],
        ];
    }
}
?>

<div class="w-full pl-3 py-3 flex-wrap gap-2 inline-flex justify-between items-center shadow-lg outline outline-1 outline-gray-200 p-6 rounded-lg bg-white mb-6">
    <div class="flex flex-wrap items-center gap-2">
        <?php if ($tags && !is_wp_error($tags)) : ?>
            <?php foreach ($tags as $tag) : ?>
                <div class="h-8 px-3 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-sm font-normal text-neutral-950 capitalize">#<?php echo esc_html($tag->name); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- زر التقديم -->
    <a href="#register" class="group">
        <div class="h-10 px-6 py-2 bg-gradient-to-br from-sky-500 to-blue-700 rounded-[55px] flex justify-end items-center gap-2 transition-transform group-hover:scale-105">
            <div class="text-white text-base font-medium leading-5">تقديم الآن</div>
            <i class="fa-solid fa-arrow-left text-white transition-transform group-hover:-translate-x-1"></i>
        </div>
    </a>
</div>