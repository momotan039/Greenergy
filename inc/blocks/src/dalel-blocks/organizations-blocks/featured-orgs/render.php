<?php
if (! defined('ABSPATH')) exit;

// --- Attrs ---
$block_attrs = (isset($block) && $block instanceof WP_Block)
    ? array_merge((array)($block->attributes ?? []), (array)($block->parsed_block['attrs'] ?? []))
    : [];

$attrs = wp_parse_args($attributes ?? [], wp_parse_args($block_attrs, [
    'title'                => 'أبرز المنظمات',
    'description'          => 'اكتشف أبرز المنظمات التي تقود التغيير في مجال الطاقة المستدامة',
    'manualEntries'        => [],
    'selectedOrganizations' => [],
]));

// --- Build items ---
$items = [];

foreach ((array)$attrs['manualEntries'] as $e) {
    if (! is_array($e)) continue;
    $items[] = [
        'type'    => 'manual',
        'title'   => (string)($e['title']    ?? ''),
        'thumb'   => esc_url_raw($e['imageUrl'] ?? '') ?: ($e['imageId'] ? wp_get_attachment_image_url((int)$e['imageId'], 'medium') : '') ?: 'https://placehold.co/155x154',
        'bg'      => esc_url_raw($e['imageUrl'] ?? '') ?: ($e['imageId'] ? wp_get_attachment_image_url((int)$e['imageId'], 'medium') : '') ?: 'https://placehold.co/400x300',
        'location' => (string)($e['location'] ?? ''),
        'tag'     => (string)($e['tag']      ?? ''),
        'excerpt' => (string)($e['excerpt']  ?? ''),
        'link'    => esc_url_raw($e['link']  ?? '') ?: '#',
    ];
}

$selected_ids = array_unique(array_filter(array_map(
    fn($i) => absint(is_array($i) ? ($i['id'] ?? 0) : $i),
    (array)$attrs['selectedOrganizations']
)));

if ($selected_ids) {
    $q = new WP_Query(['post_type' => 'organizations', 'post__in' => $selected_ids, 'orderby' => 'post__in', 'posts_per_page' => -1, 'post_status' => 'publish']);
    while ($q->have_posts()) {
        $q->the_post();
        $pid      = get_the_ID();
        $bg_img   = function_exists('get_field') ? get_field('organization_background_image', $pid) : null;
        $bg       = (is_array($bg_img) ? ($bg_img['url'] ?? wp_get_attachment_image_url($bg_img['ID'] ?? 0, 'medium_large')) : '') ?: get_the_post_thumbnail_url($pid, 'medium_large') ?: 'https://placehold.co/400x300';
        $loc_terms = get_the_terms($pid, 'organization_location');
        $location  = '';
        if ($loc_terms && ! is_wp_error($loc_terms)) {
            $parts = [];
            foreach ($loc_terms as $t) {
                $parent   = (int)$t->parent ? get_term($t->parent, 'organization_location') : null;
                $parts[]  = $parent && ! is_wp_error($parent) ? $parent->name . ' ،' . $t->name : $t->name;
            }
            $location = implode(' ،', array_slice($parts, 0, 2));
        }
        $tag_terms = get_the_terms($pid, 'organization_tag');
        $card_desc = get_post_meta($pid, 'org_card_description', true);
        $items[] = [
            'type'     => 'post',
            'title'    => get_the_title($pid),
            'thumb'    => get_the_post_thumbnail_url($pid, 'medium') ?: 'https://placehold.co/155x154',
            'bg'       => $bg,
            'location' => $location,
            'tag'      => ($tag_terms && ! is_wp_error($tag_terms)) ? $tag_terms[0]->name : '',
            'excerpt'  => $card_desc ?: (has_excerpt($pid) ? get_the_excerpt($pid) : wp_trim_words(get_the_content(null, false, $pid), 15)),
            'link'     => get_permalink($pid),
        ];
    }
    wp_reset_postdata();
}

// --- Swiper config ---
$swiper_config = wp_json_encode([
    'slidesPerView'  => 1.5,
    'spaceBetween'   => 16,
    'loop'           => count($items) > 3,
    'watchOverflow'  => true,
    'autoplay'       => count($items) > 1 ? ['delay' => 5000, 'disableOnInteraction' => false] : false,
    'pagination'     => ['clickable' => true],
    'breakpoints'    => [
        '640'  => ['slidesPerView' => 2,  'spaceBetween' => 20],
        '1024' => ['slidesPerView' => 3,  'spaceBetween' => 24],
    ],
]);
?>

<section class="flex flex-col items-center gap-8 w-full">

    <header class="text-center space-y-3">
        <div class="inline-flex items-center title-badge">
            <?php echo esc_html($attrs['title']); ?>
        </div>
        <p class="text-stone-500 text-base"><?php echo esc_html($attrs['description']); ?></p>
    </header>

    <?php if ($items) : ?>
        <div class="swiper swiper-container js-swiper-init overflow-hidden w-full pb-16"
            data-swiper-config="<?php echo esc_attr($swiper_config); ?>"
            data-aos="fade-up" data-aos-duration="1000">

            <div class="swiper-wrapper items-stretch">
                <?php foreach ($items as $item) : ?>
                    <div class="swiper-slide">
                        <article style="background-image: url('<?php echo esc_url($item['bg']); ?>');"
                            class="bg-green-600/85 bg-cover bg-center rounded-2xl p-4 flex flex-col items-center text-center shadow-sm border border-sky-500/30 h-[380px]">

                            <!-- Thumb — fixed size -->
                            <img src="<?php echo esc_url($item['thumb']); ?>"
                                class="w-32 h-32 object-cover rounded-xl flex-shrink-0"
                                alt="<?php echo esc_attr($item['title']); ?>">

                            <!-- Content — fills remaining height, no overflow -->
                            <div class="mt-4 p-4 bg-white/70 backdrop-blur rounded-xl w-full flex-1 flex flex-col overflow-hidden">

                                <h3 class="text-base font-medium line-clamp-2 flex-shrink-0">
                                    <?php echo esc_html($item['title']); ?>
                                </h3>

                                <div class="flex flex-wrap justify-center gap-3 text-xs text-neutral-800 mt-2 flex-shrink-0">
                                    <?php if ($item['location']) : ?>
                                        <span><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($item['location']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['tag']) : ?>
                                        <span class="px-3 py-1 bg-green-100 rounded-full">#<?php echo esc_html($item['tag']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-xs text-neutral-700 mt-2 flex-shrink-0">
                                    <?php echo esc_html($item['excerpt']); ?>
                                </p>

                                <a href="<?php echo esc_url($item['link']); ?>"
                                    class="mt-auto block w-full py-2 bg-[#229924] text-white text-sm rounded-lg hover:bg-[#22C55E] transition flex-shrink-0">
                                    <?php esc_html_e('عرض التفاصيل', 'greenergy'); ?>
                                </a>
                            </div>

                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination !relative mt-8"></div>
        </div>

    <?php else : ?>
        <p class="text-stone-500 text-sm text-center py-8">
            <?php esc_html_e('أضف عناصر يدوية أو اختر منظمات من إعدادات الكتلة.', 'greenergy'); ?>
        </p>
    <?php endif; ?>

</section>