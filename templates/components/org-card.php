<?php
if (! defined('ABSPATH')) exit;

$post_id = isset($args['post_id']) ? absint($args['post_id']) : get_the_ID();
if (! $post_id) return;

$is_featured = ! empty($args['is_featured']);
$post = get_post($post_id);
if (! $post || $post->post_type !== 'organizations') return;

$thumb     = get_the_post_thumbnail_url($post_id, 'medium') ?: 'https://placehold.co/163x163';
$title     = get_the_title($post_id);
$link      = get_permalink($post_id);
$card_desc = get_post_meta($post_id, 'org_card_description', true);
$excerpt   = ($card_desc !== '') ? $card_desc : (has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(get_the_content(null, false, $post_id), 15));
$views     = (class_exists('Greenergy_Post_Views') && method_exists('Greenergy_Post_Views', 'get_views'))
    ? Greenergy_Post_Views::get_views($post_id) : '—';

$location_display = '';
$locations = get_the_terms($post_id, 'organization_location');
if ($locations && ! is_wp_error($locations)) {
    $city = $country = null;
    foreach ($locations as $term) {
        if ((int) $term->parent !== 0) {
            $city = $term->name;
            $parent = get_term($term->parent, 'organization_location');
            if ($parent && ! is_wp_error($parent)) $country = $parent->name;
        } elseif (! $country) {
            $country = $term->name;
        }
    }
    $location_display = implode(' ،', array_filter([$city, $country]));
}

$tag_terms = get_the_terms($post_id, 'organization_tag');
$tag_name  = ($tag_terms && ! is_wp_error($tag_terms)) ? $tag_terms[0]->name : '';

$article_class = $is_featured
    ? 'bg-gradient-to-br from-slate-50 via-cyan-200 to-slate-50 shadow outline outline-[0.5px] outline-sky-500'
    : 'bg-white shadow-lg outline outline-1 outline-offset-[-1px] outline-neutral-200';

$btn_class = $is_featured
    ? 'bg-gradient-to-b from-sky-500 to-blue-700 text-white hover:opacity-90'
    : 'border border-neutral-200 text-neutral-800 hover:bg-neutral-50';
?>

<article class="flex <?php echo $article_class; ?> rounded-2xl overflow-hidden card-hover relative" dir="rtl">

    <a href="<?php echo esc_url($link); ?>" class="absolute inset-0 z-10 w-full h-full"></a>
    <div class="w-40 min-h-[10rem] h-full max-md:w-[128px] max-md:min-h-[8rem] bg-cover bg-center shrink-0"
        style="background-image: url('<?php echo esc_url($thumb); ?>');"
        role="img" aria-label="<?php echo esc_attr($title); ?>" class="card-image">
    </div>

    <div class="flex-1 p-4 max-md:p-2 pr-2 max-md:pr-1 flex flex-col justify-between text-right gap-2">

        <!-- Title + Views -->
        <div class="flex justify-between gap-2">
            <h3 class="text-neutral-950 text-base font-medium flex-1 line-clamp-2">
                <a href="<?php echo esc_url($link); ?>" class="!text-black hover:text-green-600 transition-colors">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
            <span class="text-stone-500 text-xs text-center mt-2 shrink-0">
                <i class="fas fa-eye"></i> <?php echo esc_html($views); ?>
            </span>
        </div>

        <!-- Location + Tag -->
        <div class="flex justify-between items-center text-xs gap-1">
            <?php if ($location_display) : ?>
                <span class="text-neutral-800">
                    <i class="fas fa-location-dot text-stone-500"></i>
                    <?php echo esc_html($location_display); ?>
                </span>
            <?php endif; ?>
            <?php if ($tag_name) : ?>
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-bold tag">
                    #<?php echo esc_html($tag_name); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Excerpt -->
        <p class="text-neutral-800 text-xs line-clamp-2"><?php echo esc_html($excerpt); ?></p>

        <!-- CTA -->
        <a href="<?php echo esc_url($link); ?>"
            class="h-9 <?php echo $btn_class; ?> rounded-lg text-sm flex items-center justify-center transition-colors">
            <?php esc_html_e('عرض التفاصيل', 'greenergy'); ?>
        </a>

    </div>
</article>