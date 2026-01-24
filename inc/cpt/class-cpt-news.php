<?php
/**
 * CPT: News
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_News
 */
class Greenergy_CPT_News {

    /**
     * Post type slug
     */
    const POST_TYPE = 'news';

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    /**
     * Register custom post type
     */
    public function register() {
        $labels = [
            'name'                  => _x( 'News', 'Post type general name', 'greenergy' ),
            'singular_name'         => _x( 'News', 'Post type singular name', 'greenergy' ),
            'menu_name'             => _x( 'News', 'Admin Menu text', 'greenergy' ),
            'add_new'               => __( 'Add New', 'greenergy' ),
            'add_new_item'          => __( 'Add New News', 'greenergy' ),
            'edit_item'             => __( 'Edit News', 'greenergy' ),
            'new_item'              => __( 'New News', 'greenergy' ),
            'view_item'             => __( 'View News', 'greenergy' ),
            'search_items'          => __( 'Search News', 'greenergy' ),
            'not_found'             => __( 'No news found', 'greenergy' ),
            'not_found_in_trash'    => __( 'No news found in Trash', 'greenergy' ),
            'all_items'             => __( 'All News', 'greenergy' ),
            'archives'              => __( 'News Archives', 'greenergy' ),
            'featured_image'        => __( 'Featured Image', 'greenergy' ),
            'set_featured_image'    => __( 'Set featured image', 'greenergy' ),
            'remove_featured_image' => __( 'Remove featured image', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'news', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-megaphone',
            'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ],
            'show_in_rest'       => true, // Gutenberg support
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    /**
     * Register taxonomy
     */
    public function register_taxonomy() {
        $labels = [
            'name'              => _x( 'News Categories', 'taxonomy general name', 'greenergy' ),
            'singular_name'     => _x( 'News Category', 'taxonomy singular name', 'greenergy' ),
            'search_items'      => __( 'Search Categories', 'greenergy' ),
            'all_items'         => __( 'All Categories', 'greenergy' ),
            'parent_item'       => __( 'Parent Category', 'greenergy' ),
            'parent_item_colon' => __( 'Parent Category:', 'greenergy' ),
            'edit_item'         => __( 'Edit Category', 'greenergy' ),
            'update_item'       => __( 'Update Category', 'greenergy' ),
            'add_new_item'      => __( 'Add New Category', 'greenergy' ),
            'new_item_name'     => __( 'New Category Name', 'greenergy' ),
            'menu_name'         => __( 'Categories', 'greenergy' ),
        ];

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'news-category' ],
            'show_in_rest'      => true,
        ];

        register_taxonomy( 'news_category', [ self::POST_TYPE ], $args );
    }
}
