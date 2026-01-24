<?php
/**
 * Gutenberg Blocks Loader
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Blocks_Loader
 */
class Greenergy_Blocks_Loader {

    public function __construct() {
        add_action( 'init', [ $this, 'register_blocks' ] );
        add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
    }

    /**
     * Register custom blocks
     */
    public function register_blocks() {
        // Block registration will go here
        // Each block in /inc/blocks/src/{block-name}/ will be registered
        
        $blocks_dir = GREENERGY_INC_DIR . '/blocks/src';
        
        if ( ! file_exists( $blocks_dir ) ) {
            return;
        }

        $block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

        foreach ( $block_folders as $block_folder ) {
            $block_json = $block_folder . '/block.json';
            
            if ( file_exists( $block_json ) ) {
                register_block_type( $block_folder );
            }
        }
    }

    /**
     * Register custom block category
     */
    public function register_block_category( $categories, $post ) {
        return array_merge(
            [
                [
                    'slug'  => 'greenergy',
                    'title' => __( 'Greenergy Blocks', 'greenergy' ),
                    'icon'  => 'admin-site-alt3',
                ],
            ],
            $categories
        );
    }
}
