<?php
/**
 * Sidebar Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'max-lg:hidden w-1/4 inline-flex flex-col justify-start items-end gap-4',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php echo $content; ?>
</div>
