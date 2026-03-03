<?php

/**
 * Company About Block — render
 * Dynamic block with editable title and description.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attributes = wp_parse_args($attributes ?? [], [
    'title'       => 'نبذة عن الشركة',
    'description' => '',
]);

$post_type     = get_post_type(get_the_ID());
$title_company = 'نبذة عن الشركة';
$title_org     = 'نبذة عن المنظمة';
$title         = $attributes['title'];
if ($post_type === 'organizations' && ($title === '' || $title === $title_company)) {
    $title = $title_org;
} elseif ($title === '') {
    $title = $title_company;
}
$description = $attributes['description'];

// Fallback to ACF if description empty (backward compatibility)
if ($description === '' && function_exists('get_field')) {
    $description = get_field('company_overview');
}
$description = $description ?: '';

$company_id = get_the_ID();
$tags = [];
if ($company_id) {
    $tag_tax = ($post_type === 'organizations') ? 'organization_tag' : 'company_tag';
    $terms = get_the_terms($company_id, $tag_tax);
    if (is_array($terms)) {
        $tags = $terms;
    }
}
?>

<div class="w-full p-4 bg-white rounded-lg border border-zinc-100 shadow-lg outline outline-1 outline-gray-200">
    <div class="flex flex-col gap-4 text-right">
        <h3 class="text-xl font-bold text-neutral-950"><?php echo esc_html($title); ?></h3>
        <?php if ($description !== '') : ?>
            <div class="text-neutral-800 leading-6 prose prose-neutral max-w-none"><?php echo wp_kses_post(wpautop($description)); ?></div>
        <?php endif; ?>
        <?php if (!empty($tags)) : ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="px-3 py-1 bg-green-100 rounded-full text-sm text-neutral-950"><?php echo esc_html($tag->name); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
