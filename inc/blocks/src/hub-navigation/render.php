<?php

/**
 * Hub Navigation Block — render.php
 *
 * @var array $attributes Block attributes
 */

get_template_part('templates/components/hub-navigation', null, [
    'coursesLabel'  => $attributes['coursesLabel']  ?? '',
    'coursesUrl'    => $attributes['coursesUrl']    ?? '',
    'articlesLabel' => $attributes['articlesLabel'] ?? '',
    'articlesUrl'   => $attributes['articlesUrl']   ?? '',
    'active_tab'    => $attributes['activeTab']     ?? 'courses'
]);
