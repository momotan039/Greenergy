<!--start nav tabs -->
<?php
$active_tab = 'courses';
?>
<nav class="mb-3 flex w-full md:w-fit mx-auto items-center gap-3 sm:gap-6 p-1.5 bg-green-100 rounded-2xl ">
    <button class="flex-1 sm:flex-none sm:w-52 h-12 px-4 rounded-lg
                    flex items-center justify-center
                    <?php echo $active_tab === 'courses' ? 'bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 text-white' : 'text-neutral-950'; ?>
                    text-base font-medium leading-6">
        التدريبات
    </button>
    <button class="flex-1 sm:flex-none sm:w-52 h-12 px-4 rounded-lg
                    flex items-center justify-center
                    <?php echo $active_tab === 'articles' ? 'bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 text-white' : 'text-neutral-950'; ?>
                    text-base font-medium leading-6">
        المقالات التعليمية
    </button>
</nav>
<!--end nav tabs -->

<!-- sort and search (courses) -->
<?php
// Attributes
$posts_per_page         = $attributes['postsPerPage'] ?? 12;
$featured_count         = $attributes['featuredCount'] ?? 4;
$featured_selected_posts = $attributes['featuredSelectedPosts'] ?? [];
$selected_categories     = $attributes['selectedCategories'] ?? [];

$search = isset($_GET['s_course']) ? sanitize_text_field($_GET['s_course']) : '';
$sort = isset($_GET['sort_course']) ? sanitize_text_field($_GET['sort_course']) : 'latest';
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
$current_cat = isset($_GET['course_cat']) ? sanitize_text_field($_GET['course_cat']) : '';

$all_courses_active = empty($current_cat);
$all_courses_class = $all_courses_active
    ? 'h-10 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center justify-center text-white'
    : 'h-10 px-4 rounded-lg flex items-center justify-center text-neutral-950 hover:bg-gray-100 transition-colors';
$all_courses_text_class = $all_courses_active ? 'text-white' : 'text-neutral-950';

// 1. Prepare Query Arguments for All Courses (Always Dynamic)
$query_args = [
    'post_type'      => 'courses',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish',
    's'              => $search,
];

// Sorting Logic
if ($sort === 'oldest') {
    $query_args['order'] = 'ASC';
    $query_args['orderby'] = 'date';
} elseif ($sort === 'popular') {
    $query_args['orderby'] = 'meta_value_num';
    $query_args['meta_key'] = 'course_registered_count';
    $query_args['order'] = 'DESC';
} else {
    $query_args['order'] = 'DESC';
    $query_args['orderby'] = 'date';
}

// Category Filter
if ($current_cat) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'course_category',
            'field'    => 'slug',
            'terms'    => $current_cat,
        ],
    ];
}
?>

<div class="js-ajax-grid container mx-auto"
    data-query-args='<?php echo json_encode($query_args); ?>'
    data-template-part="templates/components/course-card"
    data-loader-text="جاري جلب الدورات">

    <div class="p-3 bg-white rounded-2xl">
        <form method="GET" action="<?php echo esc_url(get_permalink()); ?>" class="flex justify-between items-center gap-2">
            <!-- search -->
            <div class="group sm:w-96 h-11 px-3 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex items-center gap-2 focus-within:outline-primary-500 transition-all">
                <svg class="w-6 h-6 text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
                <input
                    type="text"
                    name="s_course"
                    value="<?php echo esc_attr($search); ?>"
                    placeholder="ابحث عن دورة ..."
                    class="flex-1 h-6  text-stone-500 text-sm bg-transparent border-none outline-none focus:ring-0">
                <?php if ($search) : ?>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="text-stone-300 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- sort -->
            <div class="inline-flex items-center">
                <div class="group relative max-w-52 h-12 px-1 md:px-4 py-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 flex justify-between items-center cursor-pointer hover:border-primary-500 transition-all">
                    <select name="sort_course" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 appearance-none">
                        <option value="latest" <?php selected($sort, 'latest'); ?>>الأحدث</option>
                        <option value="oldest" <?php selected($sort, 'oldest'); ?>>الأقدم</option>
                        <option value="popular" <?php selected($sort, 'popular'); ?>>الأكثر شيوعًا</option>
                    </select>
                    <div class="md:w-24 h-7  text-neutral-950 text-sm leading-6">
                        <?php
                        if ($sort === 'popular') echo 'الأكثر شيوعًا';
                        elseif ($sort === 'oldest') echo 'الأقدم';
                        else echo 'الأحدث';
                        ?>
                    </div>
                    <svg class="w-6 h-6 text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                    </svg>
                </div>
            </div>
        </form>
    </div>
    <!-- end sort and search (courses) -->

    <!-- start categories (courses) -->
    <?php
    $courses_query = new WP_Query($query_args);

    // 2. Featured Query (Always Manual)
    if (!empty($featured_selected_posts)) {
        $f_post_ids = array_map(function ($post) {
            return is_array($post) ? $post['id'] : $post;
        }, $featured_selected_posts);
        $featured_args = [
            'post_type'      => 'courses',
            'post__in'       => $f_post_ids,
            'posts_per_page' => $featured_count,
            'orderby'        => 'post__in',
            'post_status'    => 'publish',
        ];
    } else {
        // Fallback if none selected, or user hasn't configured it yet
        $featured_args = [
            'post_type'      => 'courses',
            'posts_per_page' => $featured_count,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];
    }

    $featured_query = new WP_Query($featured_args);

    // Fetch Real Terms for navigation
    $term_args = [
        'taxonomy' => 'course_category',
        'hide_empty' => false,
    ];

    if (!empty($selected_categories)) {
        $term_ids = array_map(function ($term) {
            return is_array($term) ? $term['id'] : $term;
        }, $selected_categories);
        $term_args['include'] = $term_ids;
        $term_args['orderby'] = 'include';
    }

    $terms = get_terms($term_args);
    ?>

    <div style="scrollbar-width:none; -webkit-overflow-scrolling:touch;" class="overflow-x-auto w-full mx-auto h-14 p-1.5 bg-green-100 rounded-xl flex items-center mb-10">
        <div class="px-4 inline-flex items-center gap-4 flex-nowrap">
            <!-- All Courses -->
            <a href="<?php echo esc_url(remove_query_arg(['course_cat', 'paged'])); ?>" class="<?php echo esc_attr($all_courses_class); ?>">
                <div class="min-w-max h-7 <?php echo esc_attr($all_courses_text_class); ?> text-sm font-normal leading-6">
                    جميع الدورات
                </div>
            </a>

            <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                <?php foreach ($terms as $term) :
                    $is_active = ($current_cat == $term->slug || $current_cat == urldecode($term->slug));
                    $item_class = $is_active
                        ? 'h-10 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex items-center justify-center text-white'
                        : 'h-10 px-4 rounded-lg flex items-center justify-center text-neutral-950 hover:bg-gray-100 transition-colors';
                    $text_class = $is_active ? 'text-white' : 'text-neutral-950';
                    $url = add_query_arg(['course_cat' => $term->slug, 'paged' => 1], remove_query_arg(['course_cat', 'paged']));
                ?>
                    <a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($item_class); ?>">
                        <div class="min-w-max h-7 <?php echo esc_attr($text_class); ?> text-sm font-normal leading-6">
                            <?php echo esc_html($term->name); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- end categories (courses) -->



    <!-- featured courses -->
    <?php if ($featured_query->have_posts()) : ?>
        <div class="flex flex-col items-center gap-3 self-stretch">
            <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
                flex items-center justify-center
                text-green-700 text-2xl font-medium leading-5">
                الدورات المميزة
            </h2>
            <p class="text-stone-500 text-base font-medium leading-[48px] ">
                أفضل الدورات التدريبية المتوفرة حالياً
            </p>
        </div>

        <div class="flex flex-wrap justify-start items-start gap-x-4 gap-y-6 mb-16">
            <?php while ($featured_query->have_posts()) : $featured_query->the_post();
                $post_id          = get_the_ID();
                $duration         = get_field('course_duration_value', $post_id) ?: 'ساعة 20';
                $students         = get_field('course_registered_count', $post_id) ?: '0';
                $trainer          = get_field('trainer_name', $post_id) ?: 'مدرب معتمد';
                $categories       = get_the_terms($post_id, 'course_category');
                $category_name    = ($categories && !is_wp_error($categories)) ? $categories[0]->name : 'عام';
                $image            = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/588x448';
            ?>
                <div class="w-full md:basis-[calc(50%-0.5rem)] bg-white rounded-2xl shadow-lg outline outline-2 outline-offset-[-2px] outline-gray-200 flex flex-col overflow-hidden group">
                    <a href="<?php the_permalink(); ?>" class="block overflow-hidden h-72 sm:h-[32rem]">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" />
                    </a>
                    <div class="p-4 flex flex-col gap-4">
                        <a href="<?php the_permalink(); ?>" class="line-clamp-1 text-neutral-950 text-xl font-medium hover:text-green-700 transition-colors">
                            <?php the_title(); ?>
                        </a>
                        <div class="text-stone-500 text-sm leading-5">
                            <?php echo esc_html($trainer); ?>
                        </div>

                        <div class="flex gap-3 text-stone-500 text-sm">
                            <div class="flex items-center gap-1.5">
                                <img class="w-4 h-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/clock.svg" alt="">
                                <?php echo esc_html($duration); ?>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <img class="w-4 h-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/people.svg" alt="">
                                <?php echo esc_html($students); ?> طلاب
                            </div>
                        </div>

                        <div class="flex flex-row items-center justify-between w-full gap-3 mt-2">
                            <div class="h-8 px-4 bg-green-100 rounded-[100px] flex items-center justify-center">
                                <div class="text-neutral-950 text-sm font-medium">
                                    #<?php echo esc_html($category_name); ?>
                                </div>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="w-1/2 h-10 px-4 bg-green-700 text-white rounded-lg flex items-center justify-center text-sm leading-5 hover:bg-green-800 transition-colors">
                                سجل الان
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php endif; ?>
    <!-- end featured courses -->
    <!-- end featured courses -->

    <!-- all courses -->
    <?php if ($courses_query->have_posts()) : ?>
        <div class="flex flex-col items-center gap-3 self-stretch">
            <h2 class="h-14 px-8 py-2.5 bg-teal-50 rounded-3xl
                 flex items-center justify-center
                 text-green-700 text-2xl font-medium leading-5">
                جميع الدورات
            </h2>
            <p class="text-stone-500 text-base font-medium leading-[1] mt-2 mb-8">
                اختر من مجموعة واسعة من الدورات التدريبية
            </p>
        </div>

        <div class="max-w-7xl mx-auto flex flex-wrap gap-6 justify-start mb-12 js-ajax-grid-content">
            <?php while ($courses_query->have_posts()) : $courses_query->the_post(); ?>
                <?php get_template_part('templates/components/course-card'); ?>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center js-ajax-pagination-wrapper">
            <?php
            if (function_exists('greenergy_get_pagination_html')) {
                echo greenergy_get_pagination_html($courses_query, $paged);
            } else {
                the_posts_pagination();
            }
            ?>
        </div>

    <?php else : ?>
        <div class="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 text-xl">لم يتم العثور على دورات متوافقة مع بحثك.</p>
            <a href="<?php echo esc_url(get_permalink()); ?>" class="text-green-700 font-medium underline mt-2 block">عرض جميع الدورات</a>
        </div>
    <?php endif; ?>

</div>
<!-- end all courses -->