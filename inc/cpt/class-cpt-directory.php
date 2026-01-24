<?php
/**
 * CPT: Directory
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Directory
 */
class Greenergy_CPT_Directory {

    const POST_TYPE = 'directory';

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    public function register() {
        $labels = [
            'name'               => _x( 'Directory', 'Post type general name', 'greenergy' ),
            'singular_name'      => _x( 'Listing', 'Post type singular name', 'greenergy' ),
            'menu_name'          => _x( 'Directory', 'Admin Menu text', 'greenergy' ),
            'add_new'            => __( 'Add New', 'greenergy' ),
            'add_new_item'       => __( 'Add New Listing', 'greenergy' ),
            'edit_item'          => __( 'Edit Listing', 'greenergy' ),
            'new_item'           => __( 'New Listing', 'greenergy' ),
            'view_item'          => __( 'View Listing', 'greenergy' ),
            'search_items'       => __( 'Search Directory', 'greenergy' ),
            'not_found'          => __( 'No listings found', 'greenergy' ),
            'not_found_in_trash' => __( 'No listings found in Trash', 'greenergy' ),
            'all_items'          => __( 'All Listings', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'directory', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 9,
            'menu_icon'          => 'dashicons-location-alt',
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    public function register_taxonomy() {
        register_taxonomy( 'directory_category', [ self::POST_TYPE ], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x( 'Directory Categories', 'taxonomy general name', 'greenergy' ),
                'singular_name' => _x( 'Directory Category', 'taxonomy singular name', 'greenergy' ),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'directory-category' ],
            'show_in_rest'      => true,
        ] );
    }
}
