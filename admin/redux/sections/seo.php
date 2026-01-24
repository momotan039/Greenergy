<?php
/**
 * Redux Section: SEO
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

Redux::set_section( $opt_name, [
    'title'  => __( 'SEO', 'greenergy' ),
    'id'     => 'seo',
    'icon'   => 'el el-search',
    'fields' => [
        [
            'id'       => 'seo_info',
            'type'     => 'info',
            'style'    => 'info',
            'title'    => __( 'Rank Math Recommended', 'greenergy' ),
            'desc'     => __( 'This theme is Rank Math ready. These settings provide fallbacks when no SEO plugin is active.', 'greenergy' ),
        ],
        [
            'id'       => 'default_meta_description',
            'type'     => 'textarea',
            'title'    => __( 'Default Meta Description', 'greenergy' ),
            'subtitle' => __( 'Used when no custom description is set', 'greenergy' ),
            'default'  => '',
        ],
        [
            'id'       => 'og_default_image',
            'type'     => 'media',
            'title'    => __( 'Default OG Image', 'greenergy' ),
            'subtitle' => __( 'Fallback image for social sharing (1200x630)', 'greenergy' ),
        ],
        [
            'id'       => 'schema_organization_name',
            'type'     => 'text',
            'title'    => __( 'Organization Name', 'greenergy' ),
            'subtitle' => __( 'For Schema.org markup', 'greenergy' ),
        ],
        [
            'id'       => 'schema_organization_logo',
            'type'     => 'media',
            'title'    => __( 'Organization Logo', 'greenergy' ),
            'subtitle' => __( 'Square logo for Schema.org (min 112x112)', 'greenergy' ),
        ],
    ],
] );
