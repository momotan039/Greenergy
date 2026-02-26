<?php

/**
 * All Posts Block — render.php
 *
 * Order: Search+Sort bar → Category tabs → Featured posts → All posts grid
 *
 * Taxonomy  : category
 * GET params: post_cat | sort_post | s_post | paged
 *
 * Attributes:
 *  - postsPerPage         (int)   Posts per page, default 12
 *  - featuredCount        (int)   Featured posts to show, default 4
 *  - featuredSelectedPosts (array) Manually selected featured post IDs
 *  - selectedCategories   (array) Restrict category tab bar to these IDs
 */

?>

<div class="js-hub-content-view" data-type="articles">
    <?php
    get_template_part('templates/components/hub-navigation', null, [
        'coursesLabel'  => $attributes['coursesLabel']  ?? '',
        'coursesUrl'    => $attributes['coursesUrl']    ?? '',
        'articlesLabel' => $attributes['articlesLabel'] ?? '',
        'articlesUrl'   => $attributes['articlesUrl']   ?? '',
        'active_tab'    => 'articles'
    ]);
    ?>

    <?php
    $posts_per_page      = $attributes['postsPerPage']       ?? 12;
    $selected_categories = $attributes['selectedCategories'] ?? [];
    $show_category_bar   = $attributes['showCategoryBar']    ?? true;
    $grid_title          = $attributes['title']              ?? 'جميع المقالات';
    $grid_subtitle       = $attributes['subtitle']           ?? 'تصفح مجموعة كبيرة من المقالات التعليمية';

    // ── URL Params ────────────────────────────────────────────────────────────────
    $search      = isset($_GET['s_post'])   ? sanitize_text_field($_GET['s_post'])   : '';
    $sort        = isset($_GET['sort_post']) ? sanitize_text_field($_GET['sort_post']) : 'latest';
    $paged       = isset($_GET['paged'])    ? max(1, intval($_GET['paged']))          : 1;
    $current_cat = isset($_GET['post_cat']) ? sanitize_text_field($_GET['post_cat']) : '';

    // ── Shared tab CSS ────────────────────────────────────────────────────────────
    $tab_active   = 'h-10 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center justify-center text-white';
    $tab_inactive = 'h-10 px-4 rounded-lg flex items-center justify-center text-neutral-950 hover:bg-gray-100 transition-colors';

    $all_active = empty($current_cat);
    $all_class  = $all_active ? $tab_active  : $tab_inactive;
    $all_text   = $all_active ? 'text-white' : 'text-neutral-950';

    // ── Main (all posts) query ────────────────────────────────────────────────────
    $query_args = [
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ];

    if (!empty($search)) {
        $query_args['s'] = $search;
    }

    switch ($sort) {
        case 'oldest':
            $query_args['orderby'] = 'date';
            $query_args['order']   = 'ASC';
            break;
        case 'popular':
            $query_args['meta_query'] = [
                'relation' => 'OR',
                'views_clause' => [
                    'key'     => '_total_views_sort',
                    'type'    => 'NUMERIC',
                    'compare' => 'EXISTS',
                ],
                'not_exists_clause' => [
                    'key'     => '_total_views_sort',
                    'compare' => 'NOT EXISTS',
                ],
            ];
            $query_args['orderby'] = [
                'views_clause' => 'DESC',
                'date'         => 'DESC',
            ];
            break;
        default:
            $query_args['orderby'] = 'date';
            $query_args['order']   = 'DESC';
    }

    if ($current_cat) {
        $query_args['tax_query'] = [[
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => $current_cat,
        ]];
    }

    // ── Category terms ────────────────────────────────────────────────────────────
    $term_args = ['taxonomy' => 'category', 'hide_empty' => true];
    if (!empty($selected_categories)) {
        $term_ids             = array_map(fn($t) => is_array($t) ? $t['id'] : $t, $selected_categories);
        $term_args['include'] = $term_ids;
        $term_args['orderby'] = 'include';
        $term_args['hide_empty'] = false; // Show them even if empty if manually chosen
    }

    $terms       = get_terms($term_args);
    $posts_query = new \WP_Query($query_args);

    $sort_label = match ($sort) {
        'popular' => 'الأكثر قراءة',
        'oldest'  => 'الأقدم',
        default   => 'الأحدث',
    };
    ?>

    <div class="js-ajax-grid container mx-auto"
        data-query-args='<?php echo json_encode($query_args); ?>'
        data-template-part="templates/components/post-card"
        data-loader-text="جاري جلب المقالات">

        <!-- ── 1. Search + Sort Bar ───────────────────────────────── -->
        <div class="p-3 bg-white rounded-2xl mb-4">
            <form method="GET" action="<?php echo esc_url(get_permalink()); ?>" class="flex justify-between items-center gap-2">
                <?php foreach ($_GET as $key => $val) :
                    if (in_array($key, ['s_post', 'sort_post', 'paged', 'post_cat'])) continue; ?>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($val); ?>">
                <?php endforeach; ?>

                <!-- Search -->
                <div class="group sm:w-96 h-11 px-3 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-green-500 transition-all">
                    <svg class="w-6 h-6 text-stone-400 group-hover:text-green-600 transition-colors" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                    </svg>
                    <input
                        type="text"
                        name="s_post"
                        value="<?php echo esc_attr($search); ?>"
                        placeholder="ابحث في المقالات ..."
                        class="flex-1 h-6 text-stone-500 text-sm bg-transparent border-none outline-none focus:ring-0">
                    <?php if ($search) : ?>
                        <a href="<?php echo esc_url(remove_query_arg(['s_post', 'paged'])); ?>" class="text-stone-300 hover:text-red-500 transition-colors">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Sort -->
                <div class="group relative max-w-52 h-12 px-1 md:px-4 py-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 flex justify-between items-center cursor-pointer transition-all">
                    <select name="sort_post" onchange="this.form.submit()"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 appearance-none">
                        <option value="latest" <?php selected($sort, 'latest');  ?>>الأحدث</option>
                        <option value="oldest" <?php selected($sort, 'oldest');  ?>>الأقدم</option>
                        <option value="popular" <?php selected($sort, 'popular'); ?>>الأكثر قراءة</option>
                    </select>
                    <div class="md:w-28 h-7 text-neutral-950 text-sm leading-6">
                        <?php echo esc_html($sort_label); ?>
                    </div>
                    <svg class="w-6 h-6 text-stone-400 group-hover:text-green-600 transition-colors" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                </div>
            </form>
        </div>

        <!-- ── 2. Category Tab Bar ────────────────────────────────── -->
        <?php if ($show_category_bar) : ?>
            <div style="scrollbar-width:none; -webkit-overflow-scrolling:touch;"
                class="overflow-x-auto w-full mx-auto h-14 p-1.5 bg-green-100 rounded-xl flex items-center mb-10">
                <div class="px-4 inline-flex items-center gap-4 flex-nowrap">

                    <a href="<?php echo esc_url(remove_query_arg(['post_cat', 'paged'])); ?>"
                        class="<?php echo esc_attr($all_class); ?>">
                        <div class="min-w-max h-7 <?php echo esc_attr($all_text); ?> text-sm font-normal leading-6">
                            جميع المقالات
                        </div>
                    </a>

                    <?php if (!empty($terms) && !is_wp_error($terms)) :
                        foreach ($terms as $term) :
                            $is_active  = ($current_cat === $term->slug || $current_cat === urldecode($term->slug));
                            $item_class = $is_active ? $tab_active  : $tab_inactive;
                            $text_class = $is_active ? 'text-white' : 'text-neutral-950';
                            $url        = add_query_arg(['post_cat' => $term->slug, 'paged' => 1], remove_query_arg(['post_cat', 'paged']));
                    ?>
                            <a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($item_class); ?>">
                                <div class="min-w-max h-7 <?php echo esc_attr($text_class); ?> text-sm font-normal leading-6">
                                    <?php echo esc_html($term->name); ?>
                                </div>
                            </a>
                    <?php endforeach;
                    endif; ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- ── 3. Featured Posts ─────────────────────────────────── -->
        <?php require __DIR__ . '/../featured-posts/render.php'; ?>
        <!-- ── end featured posts ────────────────────────────────── -->

        <!-- ── 4. All Posts Grid ──────────────────────────────────── -->
        <?php if ($posts_query->have_posts()) : ?>

            <div class="flex flex-col items-center gap-3 self-stretch">
                <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
                        flex items-center justify-center
                        text-green-700 text-2xl font-medium leading-5">
                    <?php echo esc_html($grid_title); ?>
                </h2>
                <p class="text-stone-500 text-base font-medium leading-[1] mt-2 mb-8">
                    <?php echo esc_html($grid_subtitle); ?>
                </p>
            </div>

            <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 justify-start mb-12 js-ajax-grid-content">
                <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                    <?php get_template_part('templates/components/post-card'); ?>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center js-ajax-pagination-wrapper">
                <?php
                if (function_exists('greenergy_get_pagination_html')) {
                    echo greenergy_get_pagination_html($posts_query, $paged);
                } else {
                    the_posts_pagination();
                }
                ?>
            </div>

        <?php else : ?>
            <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-xl">لم يتم العثور على مقالات متوافقة مع بحثك.</p>
                <a href="<?php echo esc_url(get_permalink()); ?>"
                    class="text-green-700 font-medium underline mt-2 block">
                    عرض جميع المقالات
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>