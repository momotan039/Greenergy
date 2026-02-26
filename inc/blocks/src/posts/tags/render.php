<?php

/**
 * Render Tags Block
 */
get_template_part('templates/components/tags-list', null, [
    'post_id' => get_the_ID(),
    'taxonomy' => 'post_tag',
    'class' => 'mt-8 mb-8'
]);
