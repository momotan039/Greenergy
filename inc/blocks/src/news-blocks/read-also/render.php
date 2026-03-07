<?php

/**
 * Block Render: Read Also
 *
 * @package Greenergy
 * @since 1.0.0
 */

$attributes = isset($attributes) ? $attributes : [];
$title = isset($attributes['title']) ? $attributes['title'] : 'اقرأ أيضا';
$selection_mode = isset($attributes['selectionMode']) ? $attributes['selectionMode'] : 'auto';
$posts_to_show = isset($attributes['postsToShow']) ? intval($attributes['postsToShow']) : 3;
$selected_posts_data = isset($attributes['selectedPosts']) ? $attributes['selectedPosts'] : [];

// Extract IDs from selected posts objects if in manual mode
$selected_ids = [];
if (! empty($selected_posts_data) && is_array($selected_posts_data)) {
    foreach ($selected_posts_data as $p) {
        if (isset($p['id'])) {
            $selected_ids[] = $p['id'];
        }
    }
}

$args = [
    'post_type'      => 'news',
    'posts_per_page' => $posts_to_show,
    'post_status'    => 'publish',
    'ignore_sticky_posts' => true,
];

if ($selection_mode === 'manual') {
    if (empty($selected_ids)) {
        return; // No posts selected
    }
    $args['post__in'] = $selected_ids;
    $args['orderby'] = 'post__in';
} else {
    // Auto mode
    $current_id = get_the_ID();
    $args['post__not_in'] = $current_id ? [$current_id] : [];

    // Check for manual filters from block attributes
    $query_cats = isset($attributes['queryCategories']) ? $attributes['queryCategories'] : [];
    $query_tags = isset($attributes['queryTags']) ? $attributes['queryTags'] : [];

    $has_manual_filter = !empty($query_cats) || !empty($query_tags);

    if ($has_manual_filter) {
        // User explicitly selected filters
        $tax_query = [];
        if (!empty($query_cats)) {
            $tax_query[] = [
                'taxonomy' => 'news_category',
                'field'    => 'term_id',
                'terms'    => $query_cats,
            ];
        }
        if (!empty($query_tags)) {
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $query_tags,
            ];
        }
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    } elseif ($current_id) {
        // No manual filter -> Auto-detect related by category OR tag from current post
        $cat_terms = get_the_terms($current_id, 'news_category');
        $tag_terms = get_the_terms($current_id, 'post_tag');

        $tax_query = [];

        // Add Category Query
        if ($cat_terms && ! is_wp_error($cat_terms)) {
            $cat_ids = wp_list_pluck($cat_terms, 'term_id');
            $tax_query[] = [
                'taxonomy' => 'news_category',
                'field'    => 'term_id',
                'terms'    => $cat_ids,
            ];
        }

        // Add Tag Query
        if ($tag_terms && ! is_wp_error($tag_terms)) {
            $tag_ids = wp_list_pluck($tag_terms, 'term_id');
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tag_ids,
            ];
        }

        // If we have any tax criteria, set the relation
        if (! empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'OR';
            }
            $args['tax_query'] = $tax_query;
        }
    }
}

$query = new WP_Query($args);

// Fallback logic: If no related posts found (or no terms to match), query latest news
if (! $query->have_posts() && $selection_mode === 'auto') {
    // Remove taxonomic constraints to get "all news" (latest)
    if (isset($args['tax_query'])) {
        unset($args['tax_query']);
    }
    $query = new WP_Query($args);
}

if (! $query->have_posts()) {
    // If in editor (REST request), show a placeholder to avoid "Block rendered as empty" error
    if (defined('REST_REQUEST') && REST_REQUEST) {
        echo '<div class="greenergy-read-also-placeholder p-4 border border-dashed border-gray-300 rounded text-center text-gray-400 bg-gray-50">';
        echo '<h3 class="text-sm font-bold text-gray-500 mb-2">' . esc_html($title) . '</h3>';
        echo '<p class="text-xs">' . __('لم يتم العثور على أخبار ذات صلة. أضف المزيد من الأخبار لملء هذه الكتلة.', 'greenergy') . '</p>';
        echo '</div>';
    }
    return;
}
?>

<div class="greenergy-read-also flex flex-col gap-2">
    <?php if (! empty($title)) : ?>
        <h2><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <div class="flex flex-col gap-2">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <div class="relative group hover:bg-green-600 shadow-lg outline outline-1 outline-gray-200 w-full rounded-lg inline-flex justify-start items-center gap-4 max-sm:gap-0 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <!-- Link -->
                <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10 w-full h-full" aria-label="<?php the_title_attribute(); ?>"></a>

                <!-- Image -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="w-18 h-18 shrink-0 bg-cover bg-center rounded"
                        style="background-image: url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>');"
                        role="img"
                        aria-label="<?php the_title_attribute(); ?>">
                    </div>
                <?php else : ?>
                    <div class="w-18 h-18 shrink-0 bg-cover bg-center rounded"
                        style="background-image: url('https://placehold.co/80X80');"
                        role="img"
                        aria-label="<?php the_title_attribute(); ?>">
                    </div>
                <?php endif; ?>


                <div class="flex-1 self-stretch pl-3 max-sm:pr-3 py-3 inline-flex flex-col justify-start items-end gap-1.5">
                    <div class="self-stretch flex flex-col justify-start items-end gap-4 max-sm:gap-2">
                        <div class="self-stretch inline-flex justify-between max-sm:flex-col">
                            <p class="group-hover:text-white lg:w-[80%] text-right justify-start text-green-700 text-lg line-clamp-1 md:pb-1 max-sm:text-base  leading-5 hover:text-green-700 transition-colors">
                                <?php the_title(); ?>
                            </p>
                            <time datetime="<?php echo get_the_date('Y-m-d'); ?>" class="group-hover:text-white text-center max-sm:text-left justify-start text-neutral-800 text-xs font-normal leading-5">
                                <?php echo get_the_date(); ?>
                            </time>
                        </div>
                    </div>
                </div>

            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
</div>