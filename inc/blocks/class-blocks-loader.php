<?php

/**
 * Block Loader Class
 *
 * @package Greenergy
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_Blocks_Loader
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Register custom block category (both filters for compatibility)
        add_filter('block_categories_all', [$this, 'register_block_category'], 10, 2);
        add_filter('block_categories', [$this, 'register_block_category'], 10, 2);

        // Register blocks on init
        add_action('init', [$this, 'register_blocks']);

        // Enqueue editor-specific assets
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Register Custom Block Category
     */
    public function register_block_category($categories, $post)
    {
        $greenergy_category = [
            'slug'  => 'greenergy-blocks',
            'title' => esc_html__('Greenergy Blocks', 'greenergy'),
            'icon'  => 'lightbulb',
        ];

        // Prepend to the categories array to show at the top
        array_unshift($categories, $greenergy_category);

        return $categories;
    }

    /**
     * Register all blocks found in /inc/blocks/src/
     */
    public function register_blocks()
    {
        $script_path = GREENERGY_ASSETS_DIR . '/js/dist/blocks.min.js';
        $version = file_exists($script_path) ? filemtime($script_path) : GREENERGY_VERSION;

        // Register Editor Script first so block.json can find it
        wp_register_script(
            'greenergy-blocks-editor',
            GREENERGY_ASSETS_URI . '/js/dist/blocks.min.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n'],
            $version,
            true
        );

        $blocks_dir = GREENERGY_INC_DIR . '/blocks/src/';
        if (! is_dir($blocks_dir)) return;

        $blocks_dir = GREENERGY_INC_DIR . '/blocks/src/';
        if (! is_dir($blocks_dir)) return;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($blocks_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getFilename() === 'block.json') {
                $block_path = $item->getPath();
                $result = register_block_type_from_metadata($block_path);
                if (! $result) {
                    error_log("Greenergy Blocks: Failed to register block at $block_path");
                }
            }
        }
    }

    /**
     * Enqueue generic editor script if needed
     * Note: block.json handles most of this, but we need to register the handles.
     */
    public function enqueue_editor_assets()
    {
        // Enqueue Editor Script (handle was registered in register_blocks)
        wp_enqueue_script('greenergy-blocks-editor');
    }
}
