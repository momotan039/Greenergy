<?php

/**
 * Expert Places Job Block — render
 * Shows all orgs/companies linked to the current expert (ACF expert_linked_entities).
 * Centered layout when only one place.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$post_id = isset($block->context['postId']) ? (int) $block->context['postId'] : get_the_ID();
if (! $post_id || get_post_type($post_id) !== 'experts') {
    return;
}

$places = function_exists('greenergy_expert_get_linked_entities') ? greenergy_expert_get_linked_entities($post_id) : [];

if (empty($places)) {
    return;
}

$location_tax_org = 'organization_location';
$location_tax_company = 'company_location';
$category_tax_org = 'organization_category';
$category_tax_company = 'company_category';

$count = count($places);
$cards_class = 'flex flex-wrap justify-start items-start gap-4';
if ($count === 1) {
    $cards_class = 'flex flex-wrap justify-center items-start gap-4';
}
?>

<section class="p-4 bg-white rounded-lg shadow-lg outline outline-1 outline-offset-[-1px] outline-gray-200">
    <div class="flex-1 pl-3 py-3 flex flex-col justify-start gap-4">
        <div class="flex items-center gap-4">
            <h2 class="text-right text-neutral-950 text-xl font-bold leading-5"><?php esc_html_e('الشركة أو المنظمة التي يعمل بها الخبير', 'greenergy'); ?></h2>
        </div>
        <div class="<?php echo esc_attr($cards_class); ?>">
            <?php foreach ($places as $place) :
                $pid = (int) $place['id'];
                $post_type = $place['type'];
                $location_tax = $post_type === 'organizations' ? $location_tax_org : $location_tax_company;
                $category_tax = $post_type === 'organizations' ? $category_tax_org : $category_tax_company;

                $title = get_the_title($pid);
                $url = get_permalink($pid);
                $logo_url = get_the_post_thumbnail_url($pid, 'medium') ?: 'https://placehold.co/67x67';

                $location_display = '—';
                $locs = get_the_terms($pid, $location_tax);
                if ($locs && ! is_wp_error($locs)) {
                    $names = wp_list_pluck($locs, 'name');
                    $location_display = implode(' ، ', array_slice($names, 0, 2));
                }

                $tag_name = '';
                $cats = get_the_terms($pid, $category_tax);
                if ($cats && ! is_wp_error($cats)) {
                    $first = reset($cats);
                    $tag_name = $first ? $first->name : '';
                }
            ?>
                <article class="relative flex-1 min-w-[200px]  gap-2 pl-4 pr-2 py-4 bg-white rounded-2xl shadow-[0px_4px_13.4px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-offset-[-1px] outline-zinc-200 hover:outline-blue-400 flex items-center">
                    <img class="w-16 h-16 rounded-lg object-cover mr-2 flex-shrink-0" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                    <a href="<?php echo esc_url($url); ?>" class="absolute inset-0 z-10" aria-label="<?php echo esc_attr($title); ?>"></a>
                    <div class="flex-1 min-w-0 flex flex-col justify-start gap-2">
                        <h3 class="text-right text-neutral-950 text-base font-medium"><?php echo esc_html($title); ?></h3>
                        <div class="flex justify-between items-center gap-2">
                            <div class="flex justify-start items-center gap-1.5 flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-stone-500 text-xs"></i>
                                <span class="text-right text-stone-500 text-xs font-normal"><?php echo esc_html($location_display); ?></span>
                            </div>
                            <?php if ($tag_name !== '') : ?>
                                <div class="h-6 px-2 bg-green-100 rounded-[100px] flex justify-center items-center">
                                    <span class="text-right text-neutral-950 text-[10px] font-normal">#<?php echo esc_html($tag_name); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>