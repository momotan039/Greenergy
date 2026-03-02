<?php

/**
 * Single company card for all-companies block (featured + grid).
 *
 */
if (! defined('ABSPATH')) {
    exit;
}
$post_id = isset($args['post_id']) ? absint($args['post_id']) : get_the_ID();
if (! $post_id) {
    return;
}
$is_featured = ! empty($args['is_featured']);
$post = get_post($post_id);
if (! $post || $post->post_type !== 'companies') {
    return;
}

$thumb   = get_the_post_thumbnail_url($post_id, 'medium') ?: 'https://placehold.co/163x163';
$title   = get_the_title($post_id);
$link    = get_permalink($post_id);
$card_desc = get_post_meta($post_id, 'company_card_description', true);
$excerpt = ($card_desc !== '') ? $card_desc : (has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(get_the_content(null, false, $post_id), 15));
$views = (class_exists('Greenergy_Post_Views') && method_exists('Greenergy_Post_Views', 'get_views'))
    ? Greenergy_Post_Views::get_views($post_id)
    : '—';

$location_display = '';
$locations = get_the_terms($post_id, 'company_location');
if ($locations && ! is_wp_error($locations)) {
    $city = null;
    $country = null;
    foreach ($locations as $term) {
        if ((int) $term->parent !== 0) {
            // Child term = city
            $city = $term->name;
            $parent_term = get_term($term->parent, 'company_location');
            if ($parent_term && ! is_wp_error($parent_term)) {
                $country = $parent_term->name;
            }
        } else {
            // Parent term = country (fallback)
            if (! $country) {
                $country = $term->name;
            }
        }
    }
    if ($city && $country) {
        $location_display = $city . ' ، ' . $country;
    } elseif ($country) {
        $location_display = $country;
    } elseif ($city) {
        $location_display = $city;
    }
}
$tag_terms = get_the_terms($post_id, 'company_tag');
$tag_name = ($tag_terms && ! is_wp_error($tag_terms) && ! empty($tag_terms)) ? $tag_terms[0]->name : '';

$badge_url = function_exists('greenergy_company_verification_badge_url') ? greenergy_company_verification_badge_url($post_id) : '';
?>
<article class="flex <?php echo $is_featured ? 'bg-gradient-to-br from-slate-50 via-cyan-200 to-slate-50 shadow outline outline-[0.5px] outline-sky-500' : 'bg-white shadow-lg outline outline-1 outline-offset-[-1px] outline-neutral-200'; ?> rounded-2xl overflow-hidden">
    <div class="w-40 min-h-[10rem] h-full max-md:w-[128px] max-md:min-h-[8rem] bg-cover bg-center shrink-0"
        style="background-image: url('<?php echo esc_url($thumb); ?>');"
        role="img"
        aria-label="<?php echo esc_attr($title); ?>">
    </div>
    <div class="flex-1 p-4 max-md:p-2 pr-2 max-md:pr-1 flex flex-col justify-between text-right gap-2">
        <div class="flex justify-between gap-2 -mb-4">
            <span class="text-stone-500 text-xs">
                <i class="fas fa-eye text-stone-500"></i> <?php echo esc_html($views); ?> مشاهدات

            </span>
            <?php if ($badge_url) : ?>
                <img src="<?php echo esc_url($badge_url); ?>" alt="<?php esc_attr_e('موثوقة', 'greenergy'); ?>" class="w-6 h-6 inline self-center flex-shrink-0" />
            <?php endif; ?>
        </div>
        <h3 class="text-neutral-950 text-base font-medium">
            <a href="<?php echo esc_url($link); ?>" class="hover:text-green-600 !text-black transition-colors"><?php echo esc_html($title); ?></a>
        </h3>
        <div class="flex justify-between items-center text-xs gap-1">
            <?php if ($location_display) : ?>
                <span class="text-neutral-800">
                    <i class="fas fa-location-dot text-stone-500"></i>
                    <?php echo esc_html($location_display); ?>
                </span>
            <?php endif; ?>
            <?php if ($tag_name) : ?>
                <span class="px-2 py-2 bg-green-100 rounded-full">#<?php echo esc_html($tag_name); ?></span>
            <?php endif; ?>
        </div>

        <p class="text-neutral-800 text-xs line-clamp-2"><?php echo esc_html($excerpt); ?></p>
        <a href="<?php echo esc_url($link); ?>" class="h-9 <?php echo $is_featured ? 'bg-gradient-to-b from-sky-500 to-blue-700 text-white' : 'border border-neutral-200 text-neutral-800'; ?> rounded-lg text-sm flex items-center justify-center hover:opacity-90 transition-opacity">
            <?php esc_html_e('عرض التفاصيل', 'greenergy'); ?>
        </a>
    </div>
</article>