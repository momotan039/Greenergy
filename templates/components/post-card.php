<?php

/**
 * Post Card Template Part
 *
 * Preserves the original design exactly.
 * Reads real data from the current post in The Loop:
 *  - Thumbnail image (falls back to placeholder)
 *  - Title, permalink
 *  - post_description ACF field (falls back to excerpt)
 *  - Category (standard taxonomy)
 *  - Views via Greenergy_Post_Views (same system as news)
 *  - Date
 */

$post_id     = get_the_ID();
$image       = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/282x227';
$date        = get_the_date('d/m/Y', $post_id);
$categories  = get_the_terms($post_id, 'category');
$cat_name    = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';

// Description: prefer ACF field, fall back to excerpt
$description = get_field('post_description', $post_id);
if (empty($description)) {
    $description = wp_trim_words(get_the_excerpt(), 18, '...');
}

// Views — same system as news (manual override + real tracking)
$views = class_exists('Greenergy_Post_Views')
    ? Greenergy_Post_Views::get_views($post_id)
    : ((string) get_post_meta($post_id, '_news_view_count', true) ?: '0');
?>

<?php
$wrapper_class = $args['wrapper_class'] ?? 'w-full';
?>

<div class="relative group hover:bg-green-600 hover:text-white transition-colors <?php echo esc_attr($wrapper_class); ?> bg-white rounded-lg shadow-lg outline outline-1 outline-gray-200 flex flex-col overflow-hidden">
    <a href="<?php the_permalink(); ?>" class="absolute top-0 left-0 w-full h-full z-10"></a>
    <a href="<?php the_permalink(); ?>">
        <img class="w-full h-56 object-cover transition-transform duration-300" src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" />
    </a>

    <div class="p-3 flex flex-col gap-3 text-right">

        <a href="<?php the_permalink(); ?>" class=" group-hover:text-white transition-colors text-sm font-bold line-clamp-1 text-neutral-950 hover:text-green-700 transition-colors">
            <?php the_title(); ?>
        </a>

        <div class="text-xs line-clamp-2 text-neutral-600 group-hover:text-white transition-colors">
            <?php echo esc_html($description); ?>
        </div>

        <div class="flex justify-between items-center text-[12px]">
            <span class="px-3 py-1 bg-green-700/20 rounded-full group-hover:text-white group-hover:bg-green-700 transition-colors">
                <?php if ($cat_name) : ?># <?php echo esc_html($cat_name); ?><?php endif; ?>
            </span>
            <span>
                <i class="fa-regular fa-eye text-[12px]"></i>
                <?php echo esc_html($views); ?>
            </span>
            <span><?php echo esc_html($date); ?></span>
        </div>

        <a href="<?php the_permalink(); ?>" class=" group-hover:text-white transition-colors h-10 rounded-lg border text-sm flex items-center justify-center text-black hover:border-green-600 hover:text-green-700 transition-colors">
            عرض التفاصيل
        </a>

    </div>
</div>