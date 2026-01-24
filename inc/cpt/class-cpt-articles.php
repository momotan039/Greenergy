<?php
/**
 * CPT: Articles
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Articles
 */
class Greenergy_CPT_Articles {

    const POST_TYPE = 'articles';

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
    }

    public function register() {
        $labels = [
            'name'               => _x( 'Articles', 'Post type general name', 'greenergy' ),
            'singular_name'      => _x( 'Article', 'Post type singular name', 'greenergy' ),
            'menu_name'          => _x( 'Articles', 'Admin Menu text', 'greenergy' ),
            'add_new'            => __( 'Add New', 'greenergy' ),
            'add_new_item'       => __( 'Add New Article', 'greenergy' ),
            'edit_item'          => __( 'Edit Article', 'greenergy' ),
            'new_item'           => __( 'New Article', 'greenergy' ),
            'view_item'          => __( 'View Article', 'greenergy' ),
            'search_items'       => __( 'Search Articles', 'greenergy' ),
            'not_found'          => __( 'No articles found', 'greenergy' ),
            'not_found_in_trash' => __( 'No articles found in Trash', 'greenergy' ),
            'all_items'          => __( 'All Articles', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'articles', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-welcome-write-blog',
            'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ],
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }
}
