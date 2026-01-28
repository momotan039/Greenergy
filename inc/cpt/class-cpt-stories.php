<?php
/**
 * CPT: Stories
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Stories
 */
class Greenergy_CPT_Stories {

    /**
     * Post type slug
     */
    const POST_TYPE = 'stories';

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
    }

    /**
     * Register custom post type
     */
    public function register() {
        $labels = [
            'name'                  => _x( 'Stories', 'Post type general name', 'greenergy' ),
            'singular_name'         => _x( 'Story', 'Post type singular name', 'greenergy' ),
            'menu_name'             => _x( 'Stories', 'Admin Menu text', 'greenergy' ),
            'add_new'               => __( 'Add New', 'greenergy' ),
            'add_new_item'          => __( 'Add New Story', 'greenergy' ),
            'edit_item'             => __( 'Edit Story', 'greenergy' ),
            'new_item'              => __( 'New Story', 'greenergy' ),
            'view_item'             => __( 'View Story', 'greenergy' ),
            'search_items'          => __( 'Search Stories', 'greenergy' ),
            'not_found'             => __( 'No stories found', 'greenergy' ),
            'not_found_in_trash'    => __( 'No stories found in Trash', 'greenergy' ),
            'all_items'             => __( 'All Stories', 'greenergy' ),
            'archives'              => __( 'Story Archives', 'greenergy' ),
            'featured_image'        => __( 'Story Image', 'greenergy' ),
            'set_featured_image'    => __( 'Set story image', 'greenergy' ),
            'remove_featured_image' => __( 'Remove story image', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => false, // Usually stories are just thumbnails pointing to links
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'stories', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => [ 'title', 'thumbnail', 'excerpt' ], // Title for label, Thumbnail for circle, Excerpt for link
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }
}
