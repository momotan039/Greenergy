<?php
/**
 * CPT: Jobs
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Jobs
 */
class Greenergy_CPT_Jobs {

    const POST_TYPE = 'jobs';

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
    }

    public function register() {
        $labels = [
            'name'               => _x( 'Jobs', 'Post type general name', 'greenergy' ),
            'singular_name'      => _x( 'Job', 'Post type singular name', 'greenergy' ),
            'menu_name'          => _x( 'Jobs', 'Admin Menu text', 'greenergy' ),
            'add_new'            => __( 'Add New', 'greenergy' ),
            'add_new_item'       => __( 'Add New Job', 'greenergy' ),
            'edit_item'          => __( 'Edit Job', 'greenergy' ),
            'new_item'           => __( 'New Job', 'greenergy' ),
            'view_item'          => __( 'View Job', 'greenergy' ),
            'search_items'       => __( 'Search Jobs', 'greenergy' ),
            'not_found'          => __( 'No jobs found', 'greenergy' ),
            'not_found_in_trash' => __( 'No jobs found in Trash', 'greenergy' ),
            'all_items'          => __( 'All Jobs', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'jobs', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-businessman',
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    public function register_taxonomies() {
        // Job Type (Full-time, Part-time, Contract, etc.)
        register_taxonomy( 'job_type', [ self::POST_TYPE ], [
            'hierarchical'      => false,
            'labels'            => [
                'name'          => _x( 'Job Types', 'taxonomy general name', 'greenergy' ),
                'singular_name' => _x( 'Job Type', 'taxonomy singular name', 'greenergy' ),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'job-type' ],
            'show_in_rest'      => true,
        ] );

        // Job Location
        register_taxonomy( 'job_location', [ self::POST_TYPE ], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x( 'Locations', 'taxonomy general name', 'greenergy' ),
                'singular_name' => _x( 'Location', 'taxonomy singular name', 'greenergy' ),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'job-location' ],
            'show_in_rest'      => true,
        ] );
    }
}
