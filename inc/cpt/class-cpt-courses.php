<?php
/**
 * CPT: Courses
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_CPT_Courses
 */
class Greenergy_CPT_Courses {

    const POST_TYPE = 'courses';

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    public function register() {
        $labels = [
            'name'               => _x( 'Courses', 'Post type general name', 'greenergy' ),
            'singular_name'      => _x( 'Course', 'Post type singular name', 'greenergy' ),
            'menu_name'          => _x( 'Courses', 'Admin Menu text', 'greenergy' ),
            'add_new'            => __( 'Add New', 'greenergy' ),
            'add_new_item'       => __( 'Add New Course', 'greenergy' ),
            'edit_item'          => __( 'Edit Course', 'greenergy' ),
            'new_item'           => __( 'New Course', 'greenergy' ),
            'view_item'          => __( 'View Course', 'greenergy' ),
            'search_items'       => __( 'Search Courses', 'greenergy' ),
            'not_found'          => __( 'No courses found', 'greenergy' ),
            'not_found_in_trash' => __( 'No courses found in Trash', 'greenergy' ),
            'all_items'          => __( 'All Courses', 'greenergy' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'courses', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 8,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            'show_in_rest'       => true,
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    public function register_taxonomy() {
        register_taxonomy( 'course_category', [ self::POST_TYPE ], [
            'hierarchical'      => true,
            'labels'            => [
                'name'          => _x( 'Course Categories', 'taxonomy general name', 'greenergy' ),
                'singular_name' => _x( 'Course Category', 'taxonomy singular name', 'greenergy' ),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'course-category' ],
            'show_in_rest'      => true,
        ] );
    }
}
