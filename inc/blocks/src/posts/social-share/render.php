<?php

/**
 * Render Social Share Block
 */
get_template_part('templates/components/share-buttons', null, [
    'post_id' => get_the_ID(),
    'label'   => $attributes['title'] ?? __('شارك المقال', 'greenergy'),
    'show'    => [
        'whatsapp'  => $attributes['showWhatsapp']  ?? true,
        'telegram'  => $attributes['showTelegram']  ?? true,
        'facebook'  => $attributes['showFacebook']  ?? true,
        'instagram' => $attributes['showInstagram'] ?? false,
        'youtube'   => $attributes['showYoutube']   ?? false,
        'rss'       => $attributes['showRss']       ?? false,
        'copy'      => $attributes['showCopy']      ?? true,
    ]
]);
