<?php

/**
 * Tags List Component
 *
 * @param array $args {
 *     @type int    $post_id   The post ID.
 *     @type string $taxonomy  Taxonomy to fetch (default: post_tag).
 *     @type string $class     Additional classes for the wrapper.
 * }
 */
$post_id  = $args['post_id'] ?? get_the_ID();
$taxonomy = $args['taxonomy'] ?? 'post_tag';
$class    = $args['class'] ?? '';

$terms = get_the_terms($post_id, $taxonomy);

if ($terms && !is_wp_error($terms)) : ?>
    <div class="tags-list-component <?php echo esc_attr($class); ?>">
        <div class="flex flex-wrap gap-3">
            <?php foreach ($terms as $term) : ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>"
                    class="text-sm text-black px-4 bg-green-700/20 rounded-[100px] py-1 hover:bg-green-700 hover:text-white transition-colors duration-300">
                    # <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>