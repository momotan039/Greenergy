<?php
/**
 * CPT: Stats
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Stats
 */
class Greenergy_CPT_Stats {

    /**
     * Post type slug
     */
    const POST_TYPE = 'stats';

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
            'name'                  => _x( 'Stats', 'Post type general name', 'greenergy' ),
            'singular_name'         => _x( 'Stat', 'Post type singular name', 'greenergy' ),
            'menu_name'             => _x( 'Stats', 'Admin Menu text', 'greenergy' ),
            'add_new'               => __( 'Add New', 'greenergy' ),
            'add_new_item'          => __( 'Add New Stat', 'greenergy' ),
            'edit_item'             => __( 'Edit Stat', 'greenergy' ),
            'new_item'              => __( 'New Stat', 'greenergy' ),
            'view_item'             => __( 'View Stat', 'greenergy' ),
            'search_items'          => __( 'Search Stats', 'greenergy' ),
            'not_found'             => __( 'No stats found', 'greenergy' ),
            'not_found_in_trash'    => __( 'No stats found in Trash', 'greenergy' ),
            'all_items'             => __( 'All Stats', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'stats', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-chart-area',
            'supports'           => [ 'title', 'editor', 'excerpt' ], // Title: Item name, Editor: Description, Excerpt: Value (+120 GW)
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }
}
