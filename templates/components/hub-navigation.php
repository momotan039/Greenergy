<?php

/**
 * Shared Hub Navigation Component
 * 
 * @param array $args {
 *     @type string $active_tab    Current active tab ('courses' or 'articles')
 *     @type string $coursesLabel  Custom label for courses
 *     @type string $coursesUrl    Custom URL for courses
 *     @type string $articlesLabel Custom label for articles
 *     @type string $articlesUrl   Custom URL for articles
 * }
 */

$active_tab     = $args['active_tab']     ?? 'courses';
$courses_label  = !empty($args['coursesLabel'])  ? $args['coursesLabel']  : 'التدريبات';
$articles_label = !empty($args['articlesLabel']) ? $args['articlesLabel'] : 'المقالات التعليمية';

$courses_url    = !empty($args['coursesUrl'])    ? $args['coursesUrl']    : (function_exists('get_post_type_archive_link') ? get_post_type_archive_link('courses') : '#');
$articles_url   = !empty($args['articlesUrl'])   ? $args['articlesUrl']   : (function_exists('get_post_type_archive_link') ? get_post_type_archive_link('post') : '#');
?>

<nav class="mb-3 flex w-full md:w-fit mx-auto items-center gap-3 sm:gap-6 p-1.5 bg-green-100 rounded-2xl js-hub-nav">
    <a href="<?php echo esc_url($courses_url); ?>"
        data-type="courses"
        class="js-hub-nav-link flex-1 sm:flex-none sm:w-52 h-12 px-4 rounded-lg flex items-center justify-center text-base font-medium leading-6 <?php echo $active_tab === 'courses' ? 'bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 text-white' : 'text-neutral-950'; ?>">
        <?php echo esc_html($courses_label); ?>
    </a>
    <a href="<?php echo esc_url($articles_url); ?>"
        data-type="articles"
        class="js-hub-nav-link flex-1 sm:flex-none sm:w-52 h-12 px-4 rounded-lg flex items-center justify-center text-base font-medium leading-6 <?php echo $active_tab === 'articles' ? 'bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 text-white' : 'text-neutral-950'; ?>">
        <?php echo esc_html($articles_label); ?>
    </a>
</nav>