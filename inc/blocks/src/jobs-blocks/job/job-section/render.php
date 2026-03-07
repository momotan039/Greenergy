<?php

/**
 * Job Section Block Render
 */

$section_type = isset($attributes['sectionType']) ? $attributes['sectionType'] : 'paragraph';
$list_style = isset($attributes['listStyle']) ? $attributes['listStyle'] : 'bullets';
$icon_strategy = isset($attributes['iconStrategy']) ? $attributes['iconStrategy'] : 'uniform';
$icon_type = isset($attributes['iconType']) ? $attributes['iconType'] : 'certificate';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';

$container_classes = array(
    'job-section-container shadow-lg outline outline-1 outline-gray-200 p-6 rounded-lg',
    'mb-8',
    'job-section-type-' . $section_type,
    'list-style-' . $list_style,
    'icon-strategy-' . $icon_strategy,
    ($icon_strategy === 'uniform') ? 'icon-type-' . $icon_type : ''
);

?>
<div class="<?php echo esc_attr(implode(' ', $container_classes)); ?>" <?php echo $anchor; ?>>
    <div class="job-section-content <?php echo esc_attr($section_type); ?>">
        <?php echo $content; ?>
    </div>
</div>