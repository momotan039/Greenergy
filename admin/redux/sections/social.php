<?php
/**
 * Redux Section: Social
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'Social Media', 'greenergy' ),
    'id'     => 'social',
    'icon'   => 'el el-twitter',
    'fields' => [
        [
            'id'       => 'social_facebook',
            'type'     => 'text',
            'title'    => __( 'Facebook URL', 'greenergy' ),
            'validate' => 'url',
        ],
        [
            'id'       => 'social_twitter',
            'type'     => 'text',
            'title'    => __( 'X (Twitter) URL', 'greenergy' ),
            'validate' => 'url',
        ],
        [
            'id'       => 'social_instagram',
            'type'     => 'text',
            'title'    => __( 'Instagram URL', 'greenergy' ),
            'validate' => 'url',
        ],
        [
            'id'       => 'social_linkedin',
            'type'     => 'text',
            'title'    => __( 'LinkedIn URL', 'greenergy' ),
            'validate' => 'url',
        ],
        [
            'id'       => 'social_youtube',
            'type'     => 'text',
            'title'    => __( 'YouTube URL', 'greenergy' ),
            'validate' => 'url',
        ],
        [
            'id'       => 'social_tiktok',
            'type'     => 'text',
            'title'    => __( 'TikTok URL', 'greenergy' ),
            'validate' => 'url',
        ],
    ],
] );
