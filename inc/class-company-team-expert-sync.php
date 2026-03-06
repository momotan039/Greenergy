<?php

/**
 * Sync expert ↔ company/organization links from company-team block.
 * When an expert is added in company-team on a company/org page, that entity
 * is stored on the expert (expert_linked_entity_ids). No need for admin to add
 * companies/organizations manually on the expert page.
 *
 * @package Greenergy
 */

if (! defined('ABSPATH')) {
    exit;
}

class Greenergy_Company_Team_Expert_Sync
{
    const META_EXPERT_LINKED_IDS = 'expert_linked_entity_ids';
    const META_TEAM_EXPERT_IDS   = 'company_team_expert_ids';

    public function __construct()
    {
        add_action('save_post_companies', [$this, 'sync_on_save'], 10, 2);
        add_action('save_post_organizations', [$this, 'sync_on_save'], 10, 2);
        add_action('rest_after_insert_companies', [$this, 'sync_on_rest'], 10, 2);
        add_action('rest_after_insert_organizations', [$this, 'sync_on_rest'], 10, 2);
    }

    /**
     * Sync when post is updated via REST (block editor).
     *
     * @param WP_Post $post Inserted or updated post.
     */
    public function sync_on_rest($post)
    {
        if (! $post instanceof WP_Post || ! in_array($post->post_type, ['companies', 'organizations'], true)) {
            return;
        }
        $fresh = get_post($post->ID);
        if ($fresh instanceof WP_Post) {
            $this->sync_on_save($post->ID, $fresh);
        }
    }

    /**
     * Parse post content and return expert IDs from all company-team blocks.
     *
     * @param string $content Post content.
     * @return int[] Expert post IDs.
     */
    public static function get_expert_ids_from_content($content)
    {
        if (! is_string($content) || trim($content) === '') {
            return [];
        }
        $blocks = parse_blocks($content);
        $expert_ids = [];
        self::collect_expert_ids_from_blocks($blocks, $expert_ids);
        return array_values(array_unique(array_filter(array_map('absint', $expert_ids))));
    }

    /**
     * @param array[] $blocks
     * @param int[]   $expert_ids
     */
    private static function collect_expert_ids_from_blocks(array $blocks, array &$expert_ids)
    {
        foreach ($blocks as $block) {
            if (isset($block['blockName']) && $block['blockName'] === 'greenergy/company-team') {
                $attrs = isset($block['attrs']) && is_array($block['attrs']) ? $block['attrs'] : [];
                $selected = isset($attrs['selectedExperts']) && is_array($attrs['selectedExperts']) ? $attrs['selectedExperts'] : [];
                foreach ($selected as $item) {
                    $id = null;
                    if (is_array($item) && isset($item['id'])) {
                        $id = (int) $item['id'];
                    } elseif (is_numeric($item)) {
                        $id = (int) $item;
                    }
                    if ($id > 0) {
                        $expert_ids[] = $id;
                    }
                }
            }
            if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
                self::collect_expert_ids_from_blocks($block['innerBlocks'], $expert_ids);
            }
        }
    }

    /**
     * On save of company or organization: update each expert's linked-entity list.
     *
     * @param int      $post_id Post ID.
     * @param WP_Post  $post    Post object.
     */
    public function sync_on_save($post_id, $post)
    {
        if (! $post instanceof WP_Post || $post->post_status !== 'publish') {
            return;
        }
        if (! in_array($post->post_type, ['companies', 'organizations'], true)) {
            return;
        }
        $post_id = (int) $post_id;
        $content = $post->post_content;
        $new_expert_ids = self::get_expert_ids_from_content($content);
        $old_expert_ids = get_post_meta($post_id, self::META_TEAM_EXPERT_IDS, true);
        if (! is_array($old_expert_ids)) {
            $old_expert_ids = [];
        }
        $old_expert_ids = array_map('absint', $old_expert_ids);
        $to_add = array_diff($new_expert_ids, $old_expert_ids);
        $to_remove = array_diff($old_expert_ids, $new_expert_ids);

        foreach ($to_add as $expert_id) {
            $this->add_entity_to_expert($expert_id, $post_id);
        }
        foreach ($to_remove as $expert_id) {
            $this->remove_entity_from_expert($expert_id, $post_id);
        }

        update_post_meta($post_id, self::META_TEAM_EXPERT_IDS, $new_expert_ids);
    }

    /**
     * @param int $expert_id  Expert post ID.
     * @param int $entity_id Company or organization post ID.
     */
    private function add_entity_to_expert($expert_id, $entity_id)
    {
        if (get_post_type($expert_id) !== 'experts' || get_post_type($entity_id) === '') {
            return;
        }
        $ids = get_post_meta($expert_id, self::META_EXPERT_LINKED_IDS, true);
        if (! is_array($ids)) {
            $ids = [];
        }
        $ids = array_map('intval', $ids);
        if (in_array($entity_id, $ids, true)) {
            return;
        }
        $ids[] = $entity_id;
        update_post_meta($expert_id, self::META_EXPERT_LINKED_IDS, $ids);
    }

    /**
     * @param int $expert_id  Expert post ID.
     * @param int $entity_id Company or organization post ID.
     */
    private function remove_entity_from_expert($expert_id, $entity_id)
    {
        $ids = get_post_meta($expert_id, self::META_EXPERT_LINKED_IDS, true);
        if (! is_array($ids)) {
            return;
        }
        $ids = array_values(array_filter(array_map('intval', $ids), function ($id) use ($entity_id) {
            return $id !== $entity_id;
        }));
        update_post_meta($expert_id, self::META_EXPERT_LINKED_IDS, $ids);
    }
}
