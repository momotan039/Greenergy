<?php

/**
 * Company Projects Block — render
 * Dynamic block: list of projects with title, year, capacity, and optional link to Project CPT.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

// Post ID and company_type for color theme (gradient badge per type).
$post_id = isset($block->context['postId']) ? (int) $block->context['postId'] : get_the_ID();
if (! $post_id && is_singular('companies')) {
    $post_id = get_queried_object_id();
}
if (! $post_id && is_singular('organizations')) {
    $post_id = get_queried_object_id();
}
$post_type = $post_id ? get_post_type($post_id) : '';
$type_terms = ($post_id && $post_type !== 'organizations') ? get_the_terms($post_id, 'company_type') : null;
$type_slug = ($type_terms && ! is_wp_error($type_terms) && ! empty($type_terms)) ? $type_terms[0]->slug : 'normal';
$type_slug = in_array($type_slug, ['normal', 'gold', 'silver', 'diamond', 'trusted'], true) ? $type_slug : 'normal';

// Gradient style for year badge (appealing gradients per company_type).
$type_badge_style = [
    'normal'  => 'background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);',
    'gold'    => 'background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #b45309 100%);',
    'silver'  => 'background: linear-gradient(135deg, #9ca3af 0%, #4b5563 50%, #374151 100%);',
    'diamond' => 'background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);',
    'trusted' => 'background: linear-gradient(135deg, #34d399 0%, #059669 50%, #047857 100%);',
][$type_slug];

// Title text color per company_type (match badge theme).
$type_title_class = [
    'normal'  => 'text-neutral-900',
    'gold'    => 'text-amber-800',
    'silver'  => 'text-neutral-700',
    'diamond' => 'text-sky-500',
    'trusted' => 'text-emerald-800',
][$type_slug];

// Attributes: from render args, then $block->attributes, then parse from post content (frontend sometimes misses attrs).
$raw_attrs = $attributes ?? [];
if (isset($block) && $block instanceof WP_Block && ! empty($block->attributes)) {
    $raw_attrs = array_merge($raw_attrs, $block->attributes);
}
// Fallback: read block attrs from post content when projects are empty (fixes projects not showing on frontend).
if (empty($raw_attrs['projects']) && $post_id) {
    $post_content = get_post_field('post_content', $post_id);
    if ($post_content && function_exists('parse_blocks')) {
        $find_projects_attrs = function ($blocks) use (&$find_projects_attrs) {
            foreach ($blocks as $b) {
                if (isset($b['blockName']) && $b['blockName'] === 'greenergy/company-projects' && ! empty($b['attrs'])) {
                    return $b['attrs'];
                }
                if (! empty($b['innerBlocks'])) {
                    $found = $find_projects_attrs($b['innerBlocks']);
                    if ($found) {
                        return $found;
                    }
                }
            }
            return null;
        };
        $from_content = $find_projects_attrs(parse_blocks($post_content));
        if ($from_content) {
            $raw_attrs = array_merge($raw_attrs, $from_content);
        }
    }
}
$attributes = wp_parse_args($raw_attrs, [
    'title'    => 'المشاريع التي شاركت بها الشركة',
    'projects' => [],
]);

$title_company = 'المشاريع التي شاركت بها الشركة';
$title_org     = 'المشاريع التي شاركت بها المنظمة';
$post_type     = $post_id ? get_post_type($post_id) : '';
$title = (string) ($attributes['title'] ?? $title_company);
if ($post_type === 'organizations' && ($title === '' || $title === $title_company)) {
    $title = $title_org;
} elseif ($title === '') {
    $title = $title_company;
}
$projects = isset($attributes['projects']) && is_array($attributes['projects']) ? $attributes['projects'] : [];
?>

<div class="w-full bg-white p-4 rounded-lg shadow-lg outline outline-1 outline-gray-200 border border-zinc-100 flex flex-col gap-4">
    <h2 class="text-xl font-bold text-neutral-950"><?php echo esc_html($title); ?></h2>
    <div class="flex flex-col gap-2">
        <?php if (empty($projects)) : ?>
            <p class="text-neutral-500 text-right text-sm"><?php esc_html_e('لم تتم إضافة مشاريع بعد.', 'greenergy'); ?></p>
        <?php else : ?>
            <?php foreach ($projects as $item) : ?>
                <?php
                $project_title = isset($item['projectTitle']) ? $item['projectTitle'] : '';
                $year          = isset($item['year']) ? $item['year'] : '';
                $establishment = isset($item['establishment']) ? $item['establishment'] : '';
                $capacity      = isset($item['capacity']) ? $item['capacity'] : '';
                $project_id    = isset($item['projectId']) ? (int) $item['projectId'] : 0;
                $project_link  = isset($item['projectLink']) ? $item['projectLink'] : '';
                $url           = '';
                if ($project_id > 0) {
                    $url = get_permalink($project_id);
                }
                if ($url === '' && $project_link !== '') {
                    $url = $project_link;
                }
                if ($project_title === '' && $project_id > 0) {
                    $project_title = get_the_title($project_id);
                }
                if ($project_title === '') {
                    continue;
                }
                ?>
                <div class="bg-neutral-100 rounded-lg p-2 flex items-center gap-3">
                    <div class="flex flex-col text-right flex-1">
                        <div class="text-base font-bold <?php echo esc_attr($type_title_class); ?>">
                            <?php if ($url) : ?>
                                <a href="<?php echo esc_url($url); ?>" class="hover:underline"><?php echo esc_html($project_title); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($project_title); ?>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-neutral-700">
                            <?php if ($capacity !== '') : ?><span>القدرة: <?php echo esc_html($capacity); ?></span><?php endif; ?>
                            -
                            <?php if ($establishment !== '') : ?><span>السنة: <?php echo esc_html($establishment); ?></span><?php endif; ?>
                        </div>
                    </div>
                    <?php if ($year !== '') : ?>
                        <div class="px-4 py-1 text-white text-xs font-bold rounded-full shrink-0" style="<?php echo esc_attr($type_badge_style); ?>"><?php echo esc_html($year); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>