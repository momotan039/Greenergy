<?php

/**
 * Company Overview Block — render
 * Dynamic block: overview fields editable from block editor (no ACF).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

// Post ID: from block context (editor/template), current post, or queried object. Title, thumbnail, location are always from this post.
$post_id = isset($block->context['postId']) ? (int) $block->context['postId'] : get_the_ID();
if (! $post_id && is_singular('companies')) {
    $post_id = get_queried_object_id();
}
if (! $post_id) {
    return;
}

$attrs = wp_parse_args($attributes ?? [], [
    'establishedDate' => '',
    'phone'           => '',
    'website'         => '',
    'email'           => '',
    'contactLabel'    => 'معلومات التواصل',
    'xLink'           => '',
    'instagramLink'  => '',
    'facebookLink'   => '',
    'linkedinLink'   => '',
]);
$views = (class_exists('Greenergy_Post_Views') && method_exists('Greenergy_Post_Views', 'get_views'))
    ? Greenergy_Post_Views::get_views($post_id)
    : '—';
// Sub-category: from company_category taxonomy (prefer child terms as sub-category)
$category_terms = get_the_terms($post_id, 'company_category');
$sub_category_display = '—';
if ($category_terms && ! is_wp_error($category_terms)) {
    // Prefer child terms (sub-categories); else use first term
    $children = array_filter($category_terms, fn($t) => (int) $t->parent !== 0);
    $term_to_show = ! empty($children) ? reset($children) : reset($category_terms);
    $sub_category_display = $term_to_show ? $term_to_show->name : '—';
}

// Display date: stored as Y-m-d from date picker; format to d/m/Y for display
$established_date = get_the_date('d/m/Y', $post_id);
if ($attrs['establishedDate'] !== '') {
    $raw = $attrs['establishedDate'];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        $established_date = date_i18n('d/m/Y', strtotime($raw . ' 12:00:00'));
    } else {
        $established_date = $raw;
    }
}

// Location from taxonomy
$locations = get_the_terms($post_id, 'company_location');
$location_display = 'غير محدد';
if ($locations && ! is_wp_error($locations)) {
    $city = null;
    $country = null;
    foreach ($locations as $term) {
        if ($term->parent != 0) {
            $city = $term->name;
            $parent_term = get_term($term->parent, 'company_location');
            if ($parent_term && ! is_wp_error($parent_term)) {
                $country = $parent_term->name;
            }
        } else {
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

$logo_url = get_the_post_thumbnail_url($post_id, 'medium') ?: 'https://placehold.co/122x122';
$title = get_the_title($post_id);

$classes = [
    'diamond' => 'bg-blue-500/10',
    'silver' => 'bg-gray-300/50',
    'gold' => 'bg-[linear-gradient(135deg,rgba(255,244,200,0.95)_0%,rgba(255,210,122,0.9)_45%,rgba(255,184,77,1)_100%)]',
    'trusted' => 'bg-[linear-gradient(135deg,rgba(200,255,200,0.95)_0%,rgba(122,255,122,0.9)_45%,rgba(77,184,77,1)_100%)]',
    'normal' => 'bg-white',
];
$badges = [
    'diamond' => [
        'image' => GREENERGY_ASSETS_URI . '/images/vuesax/bold/crown-blue.svg',
        'label' => 'شريك ماسي',
        'text'  => 'text-sky-500',
        'outline' => 'outline-sky-500',
        'from' => '#0EA5E9',
        'to'   => '#1D4ED8',
    ],
    'silver' => [
        'image' => GREENERGY_ASSETS_URI . '/images/vuesax/bold/crown-gray.svg',
        'label' => 'شريك فضي',
        'text'  => 'text-stone-400',
        'outline' => 'outline-neutral-300',
        'from' => '#D6D3D1',
        'to'   => '#71717A',
    ],
    'gold' => [
        'image' => GREENERGY_ASSETS_URI . '/images/vuesax/bold/crown-gold.svg',
        'label' => 'شريك ذهبي',
        'text'  => 'text-yellow-500',
        'outline' => 'outline-yellow-500',
        'from' => '#EAB308',
        'to'   => '#CA8A04',
    ],
];

$type_terms = get_the_terms($post_id, 'company_type');
$type_slug = ($type_terms && ! is_wp_error($type_terms) && ! empty($type_terms)) ? $type_terms[0]->slug : 'normal';
$class = isset($classes[$type_slug]) ? $classes[$type_slug] : $classes['normal'];

?>

<div class="company-overview-card w-full p-4 <?php echo $class; ?> rounded-2xl flex flex-col md:flex-row justify-between items-start gap-4 shadow-lg outline outline-1 outline-gray-200">

    <div class="flex-1 flex flex-col gap-4">

        <div class="flex flex-col md:flex-row gap-4 w-full">
            <img class="w-32 h-32 rounded-lg object-cover self-center md:self-start" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($title); ?>">
            <div class="flex flex-col gap-2">
                <div class="flex gap-2 items-center">
                    <h2 class="text-sm md:text-2xl font-bold text-neutral-950 text-right"><?php echo esc_html($title); ?></h2>
                    <?php if ((bool) get_post_meta($post_id, 'company_verified', true)) : ?>
                        <span class="inline-flex w-fit justify-center items-center gap-1.5 px-3 py-1 mt-2 bg-sky-100 rounded-3xl outline outline-1 outline-offset-[-1px] outline-sky-500 text-sky-500 text-base font-medium">
                            <img src="<?php echo GREENERGY_ASSETS_URI; ?>/images/vuesax/bold/verify-gold.svg" alt="موثوقة" class="w-4 h-4 mt-1">
                            موثوقة
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($type_slug !== 'normal' && $type_slug !== 'trusted') : ?>
                    <span
                        class="w-fit inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/60 outline outline-1 <?php echo $badges[$type_slug]['outline']; ?> <?php echo $badges[$type_slug]['text']; ?> text-base">
                        <img src="<?php echo $badges[$type_slug]['image']; ?>" alt="<?php echo $badges[$type_slug]['label']; ?>" class="w-6 h-6 mt-1">
                        <?php echo $badges[$type_slug]['label']; ?>
                    </span>
                <?php endif; ?>
                <div class="flex flex-wrap items-center gap-4 text-stone-500 text-sm max-md:gap-2 ">
                    <span><i class="fa-solid fa-location-dot ml-1"></i><?php echo esc_html($location_display); ?></span>
                    <span><i class="fa-solid fa-briefcase ml-1"></i><?php echo esc_html($sub_category_display); ?></span>
                    <span><i class="fa-solid fa-calendar-days ml-1"></i><?php echo esc_html($established_date); ?></span>
                    <span><i class="fa-solid fa-eye ml-1"></i><?php echo esc_html($views); ?> مشاهدات</span>
                </div>
            </div>
        </div>

        <button class="js-contact-toggle flex w-fit items-center gap-2.5 px-4 py-2 bg-sky-500/10 rounded-lg text-neutral-950 text-sm font-medium hover:bg-sky-500/20 transition-colors">
            <?php echo esc_html($attrs['contactLabel']); ?>
            <i class="fa-solid fa-chevron-down transition-transform duration-300"></i>
        </button>

        <div class="js-contact-info grid grid-rows-[0fr] transition-all duration-300 overflow-hidden">
            <div class="min-h-0">
                <div class="p-4 bg-stone-50 rounded-2xl mt-2 mb-2">

                    <div class="grid grid-cols-2 gap-4">

                        <?php if ($attrs['phone']) : ?>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-phone text-green-700 w-4 h-4"></i>
                                <a href="tel:<?= esc_attr($attrs['phone']); ?>"
                                    class="text-black text-sm font-medium">
                                    <?= esc_html($attrs['phone']); ?>
                                </a>
                            </div>
                        <?php endif; ?>


                        <?php if ($attrs['website']) : ?>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-globe text-green-700 w-4 h-4"></i>
                                <a href="<?= esc_url($attrs['website']); ?>"
                                    target="_blank" rel="noopener"
                                    class="text-black text-sm font-medium">
                                    <?= esc_html(parse_url($attrs['website'], PHP_URL_HOST) ?: $attrs['website']); ?>
                                </a>
                            </div>
                        <?php endif; ?>


                        <?php if ($attrs['email']) : ?>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-green-700 w-4 h-4"></i>
                                <a href="mailto:<?= esc_attr($attrs['email']); ?>"
                                    class="text-black text-sm font-medium">
                                    <?= esc_html($attrs['email']); ?>
                                </a>
                            </div>
                        <?php endif; ?>


                        <?php
                        $hasSocial =
                            $attrs['xLink'] ||
                            $attrs['instagramLink'] ||
                            $attrs['facebookLink'] ||
                            $attrs['linkedinLink'];
                        ?>

                        <?php if ($hasSocial) : ?>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-link text-green-700 w-4 h-4"></i>

                                <div class="flex gap-2">
                                    <?php if ($attrs['xLink']) : ?>
                                        <a href="<?= esc_url($attrs['xLink']); ?>"
                                            target="_blank" rel="noopener"
                                            class="w-8 h-8 flex items-center justify-center bg-black text-white rounded hover:bg-neutral-800">
                                            <i class="fa-brands fa-x-twitter"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($attrs['instagramLink']) : ?>
                                        <a href="<?= esc_url($attrs['instagramLink']); ?>"
                                            target="_blank" rel="noopener"
                                            class="w-8 h-8 flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($attrs['facebookLink']) : ?>
                                        <a href="<?= esc_url($attrs['facebookLink']); ?>"
                                            target="_blank" rel="noopener"
                                            class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white rounded">
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($attrs['linkedinLink']) : ?>
                                        <a href="<?= esc_url($attrs['linkedinLink']); ?>"
                                            target="_blank" rel="noopener"
                                            class="w-8 h-8 max-md:w-6 max-md:h-6 flex items-center justify-center bg-sky-600 text-white rounded">
                                            <i class="fa-brands fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>