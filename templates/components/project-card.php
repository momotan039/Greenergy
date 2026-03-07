<?php

/**
 * Project Card Component — dynamic from post_id, lazy-load images.
 *
 * @package Greenergy
 * @param array $args ['post_id' => int, 'is_featured' => bool]
 */
$post_id     = isset($args['post_id']) ? absint($args['post_id']) : 0;
$is_featured = !empty($args['is_featured']);

if (!$post_id || get_post_type($post_id) !== 'projects') {
    return;
}

$title        = get_the_title($post_id);
$link         = get_permalink($post_id);
$thumb_url    = get_the_post_thumbnail_url($post_id, 'medium_large') ?: 'https://placehold.co/384x269';
$excerpt      = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(get_the_content(null, false, $post_id), 18);
$established  = get_post_meta($post_id, 'project_established_date', true);
$capacity     = get_post_meta($post_id, 'project_capacity', true);
$country_code = get_post_meta($post_id, 'project_country_code', true);

if ($country_code === '' && class_exists('Greenergy_CPT_Projects')) {
    $loc_terms = get_the_terms($post_id, 'project_location');
    if ($loc_terms && !is_wp_error($loc_terms)) {
        foreach ($loc_terms as $t) {
            $parent = (int) $t->parent ? get_term($t->parent, 'project_location') : null;
            if ($parent && !is_wp_error($parent)) {
                $country_code = get_term_meta($parent->term_id, 'project_location_country_code', true);
                break;
            }
        }
    }
}

$flag_url = '';
if ($country_code && class_exists('Greenergy_CPT_Projects')) {
    $flag_url = Greenergy_CPT_Projects::get_country_flag_url($country_code);
}

$location_parts = [];
$loc_terms = get_the_terms($post_id, 'project_location');
if ($loc_terms && !is_wp_error($loc_terms)) {
    foreach ($loc_terms as $t) {
        $parent = (int) $t->parent ? get_term($t->parent, 'project_location') : null;
        $location_parts[] = $parent && !is_wp_error($parent) ? $parent->name . ' – ' . $t->name : $t->name;
    }
}
$location = implode(' ، ', array_slice($location_parts, 0, 2));

$cat_terms = get_the_terms($post_id, 'project_type');
$cat_name  = ($cat_terms && !is_wp_error($cat_terms) && !empty($cat_terms)) ? $cat_terms[0]->name : '';

$established_year = '';
if ($established && preg_match('/^\d{4}/', $established)) {
    $established_year = date_i18n('Y', strtotime($established . ' 12:00:00'));
}
?>
<article class="overflow-hidden group relative card-hover bg-white border border-sky-500 rounded-2xl shadow-[0_4px_13px_rgba(0,0,0,0.05)] flex flex-col h-full">

    <a href="<?php echo esc_url($link); ?>" class="absolute inset-0 z-10 w-full h-full" aria-label="<?php echo esc_attr($title); ?>"></a>

    <div class="shrink-0">
        <img
            src="<?php echo esc_url($thumb_url); ?>"
            alt="<?php echo esc_attr($title); ?>"
            class="object-cover w-full h-64"
            loading="lazy"
            decoding="async"
            width="384"
            height="192">
    </div>

    <div class="flex flex-col flex-1 p-4 max-md:[.swiper-slide_&]:px-2 gap-3 text-right min-h-0">

        <h3 class="text-base font-medium text-neutral-950 leading-snug overflow-hidden h-[2.6rem]">
            <?php echo esc_html($title); ?>
            <?php if ($is_featured && ($location || $flag_url)) : ?>
                <span>
                    <?php if ($location) : ?> – <?php echo esc_html($location); ?><?php endif; ?>
                        <?php if ($flag_url) : ?>
                            <img src="<?php echo esc_url($flag_url); ?>" alt="" class="w-5 h-5 inline-block object-contain align-middle" loading="lazy" width="20" height="20">
                        <?php endif; ?>
                </span>
            <?php endif; ?>
        </h3>

        <!-- Meta chips -->
        <div class="flex items-center gap-2 text-xs flex-wrap shrink-0 ">
            <?php if ($location) : ?>
                <span class="text-neutral-800 truncate max-w-[10rem]">
                    <i class="fa-solid fa-location-dot"></i>
                    <?php echo esc_html($location); ?>
                </span>
            <?php endif; ?>
            <?php if ($cat_name) : ?>
                <span class="px-2 py-1 bg-green-100 rounded-full tag group-hover:text-black">#<?php echo esc_html($cat_name); ?></span>
            <?php endif; ?>
        </div>

        <!-- Excerpt: max 2 lines (featured only) -->
        <?php if ($is_featured && $excerpt) : ?>
            <p class="text-xs text-neutral-800 line-clamp-2 leading-relaxed min-h-[2.5rem]">
                <?php echo esc_html($excerpt); ?>
            </p>
        <?php endif; ?>

        <!-- Stats (non-featured only) — pushed to bottom -->
        <?php if (!$is_featured) : ?>
            <dl class="flex items-center justify-between text-right mt-auto shrink-0">
                <?php if ($capacity) : ?>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs text-stone-500">القدرة المركبة</dt>
                        <dd class="text-xs font-medium text-green-700"><?php echo esc_html($capacity); ?></dd>
                    </div>
                <?php endif; ?>
                <?php if ($established_year) : ?>
                    <div class="flex flex-col gap-1">
                        <dt class="text-xs text-stone-500">سنة التشغيل</dt>
                        <dd class="text-xs font-medium text-neutral-950"><?php echo esc_html($established_year); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        <?php endif; ?>

        <!-- CTA — always at the bottom -->
        <a
            href="<?php echo esc_url($link); ?>"
            class="flex items-center justify-center text-black btn h-9 text-sm border rounded-lg border-neutral-300  transition-colors z-10 relative mt-auto shrink-0">
            <?php esc_html_e('عرض التفاصيل', 'greenergy'); ?>
        </a>

    </div>

</article>