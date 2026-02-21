<?php

/**
 * Job Overview Block Render
 */

$post_id = get_the_ID();
$title = get_the_title($post_id);
$company = get_field('company_name', $post_id) ?: 'شركة غير محددة';
$location = get_field('location', $post_id) ?: 'غير محدد';
$job_type_val = get_field('job_type_acf', $post_id);
$post_date = get_the_date('d/m/Y', $post_id);
$thumbnail_url = get_the_post_thumbnail_url($post_id, 'medium') ?: get_template_directory_uri() . '/assets/images/new-1.jpg';

// Map job type value to label (ACF field)
$job_types = array(
    'full-time' => 'دوام كامل',
    'part-time' => 'دوام جزئي',
    'contract'  => 'عقد',
    'remote'    => 'عمل عن بعد',
);
$job_type_label = isset($job_types[$job_type_val]) ? $job_types[$job_type_val] : 'دوام كامل';

// Get categories and tags
$categories = get_the_category($post_id);
?>

<div class="relative w-full inline-flex items-start gap-2 max-sm:flex-col shadow-lg outline outline-1 outline-gray-200 p-6 rounded-lg">
    <a href="<?php echo get_post_type_archive_link('jobs'); ?>" class="bg-white hidden max-sm:flex text-stone-500 px-3 py-2  hover:bg-primary hover:text-white hover:scale-105 hover:shadow-xl transition-all duration-300 w-fit rounded-xl font-black inline-flex items-center gap-2 group">
        <i class="fas fa-arrow-right text-stone-500 group-hover:text-white transition-transform group-hover:-translate-x-1"></i>
        <span>العودة للوظائف</span>
    </a>
    <img class="w-56 h-56 max-sm:w-32 max-sm:h-32 max-lg:h-40 max-lg:w-40 max-lg:self-center rounded-lg object-cover" src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>" />
    <div class="flex flex-col justify-between self-start gap-[1.4rem] max-sm:gap-2">
        <a href="<?php echo get_post_type_archive_link('jobs'); ?>" class="bg-white max-sm:hidden text-stone-500 px-3 py-2 hover:bg-primary hover:text-white hover:scale-105 hover:shadow-xl transition-all duration-300 w-fit rounded-xl font-black inline-flex items-center gap-2 group">
            <i class="fas fa-arrow-right text-stone-500 group-hover:text-white transition-transform group-hover:-translate-x-1"></i>
            <span>العودة للوظائف</span>
        </a>
        <div class="self-stretch inline-flex justify-between items-center gap-4">
            <span class="text-right justify-start text-neutral-950 text-xl font-bold group-hover:text-white leading-5"><?php echo esc_html($title); ?></span>
        </div>
        <div class="flex gap-2">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/buliding.svg">
            <span class="self-stretch text-right justify-start text-neutral-800 text-sm font-medium  leading-5"><?php echo esc_html($company); ?></span>
        </div>
        <div class="self-stretch inline-flex justify-between items-center flex-wrap gap-4 max-sm:gap-2">
            <div class="flex justify-start items-center gap-1">
                <img class="w-4 h-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/location.svg">
                <span class=" text-stone-500 text-sm font-normal "><?php echo esc_html($location); ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <img class="w-4 h-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/briefcase.svg">
                <span class=" text-stone-500 text-sm font-normal "><?php echo esc_html($job_type_label); ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <img class="w-4 h-4" src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/clock.svg">
                <span class="text-neutral-800 text-xs font-normal  leading-5"><?php echo esc_html($post_date); ?></span>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <?php if ($categories) : foreach ($categories as $cat) : ?>
                    <div class="h-8 px-4 bg-green-700/20 rounded-[100px] w-fit flex items-center">
                        <a href="<?php echo get_category_link($cat->term_id); ?>">
                            <span class="text-right text-neutral-950 text-sm font-medium leading-5"><?php echo esc_html($cat->name); ?></span>
                        </a>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</div>