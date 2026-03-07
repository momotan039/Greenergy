<?php

/**
 * Render Social Share Block
 */
get_template_part('templates/components/share-buttons', null, [
    'post_id' => get_the_ID(),
    'label'   => $attributes['title'] ?? __('شارك على', 'greenergy'),
    'show'    => [
        'whatsapp'  => $attributes['showWhatsapp']  ?? true,
        'telegram'  => $attributes['showTelegram']  ?? true,
        'facebook'  => $attributes['showFacebook']  ?? true,
        'instagram' => $attributes['showInstagram'] ?? true,
        'copy'      => $attributes['showCopy']      ?? true,
    ]
]);
