<?php

/**
 * Single News Content Block Template.
 *
 * @package Greenergy
 */

if (!isset($post)) {
    global $post;
}

if (!$post || $post->post_type !== 'news') {
    return;
}

$post_id = $post->ID;
?>

<div class="container mx-auto px-4">
    <div class="lg:flex lg:flex-row justify-start items-start gap-6">

        <!-- Main Content Area (Right side) -->
        <article class="w-full sticky top-0 h-fit flex-1 p-4 max-sm:p-0 bg-white rounded-2xl flex flex-col justify-start items-stretch gap-6 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up">
            <div class="flex justify-between">
                <!-- Categories -->
                <div class="flex flex-wrap gap-2">
                    <?php
                    $categories = get_the_terms($post_id, 'news_category');
                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            echo '<a href="' . esc_url(get_term_link($cat)) . '" class="text-sm text-black px-4 bg-green-700/20 rounded-[100px] py-1"># ' . esc_html($cat->name) . '</a>';
                        }
                    }
                    ?>
                </div>

                <!-- Labels (Urgent/Important) -->
                <?php
                $labels = get_the_terms($post_id, 'news_label');
                if ($labels && !is_wp_error($labels)) {
                    foreach ($labels as $label) {
                        $is_urgent = ($label->slug === 'urgent' || $label->slug === 'important');
                        echo '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-xs font-bold tracking-wide shadow-sm transition-all hover:bg-red-100">';

                        if ($is_urgent) {
                            echo '<span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                                  </span>';
                        }

                        echo esc_html($label->name);
                        echo '</span>';
                    }
                }
                ?>
            </div>

            <h1 class="text-black text-2xl font-medium max-md:text-lg"><?php echo get_the_title($post_id); ?></h1>

            <?php if (has_post_thumbnail($post_id)) : ?>
                <img src="<?php echo get_the_post_thumbnail_url($post_id, 'full'); ?>" class="h-[395px] max-md:h-[324px] w-full object-cover rounded-lg" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
            <?php endif; ?>

            <div class="flex gap-6 relative">
                <div class="flex items-center gap-1 relative">
                    <i class="far fa-eye text-neutral-950 text-sm"></i>
                    <span class="text-neutral-950 text-sm">
                        <?php
                        if (class_exists('Greenergy_Post_Views')) {
                            echo Greenergy_Post_Views::get_views($post_id);
                        }
                        ?>
                    </span>
                    <div class="absolute top-0 left-[-0.8rem] w-[2px] h-full bg-stone-300 rounded-[100px]"></div>
                </div>
                <span class="text-neutral-800 text-sm"><?php echo get_the_date('d/m/Y', $post_id); ?></span>
            </div>

            <!-- editor content -->
            <div class="new-content">
                <?php
                $content = apply_filters('the_content', get_the_content(null, false, $post_id));
                echo $content;
                ?>
            </div>

            <!-- source -->
            <div>
                <?php
                $source_name = get_post_meta($post_id, '_news_source_name', true);
                $source_url = get_post_meta($post_id, '_news_source_url', true);
                if ($source_name) :
                ?>
                    <div class="flex">
                        <h3 class="text-black text-2xl font-medium max-md:text-lg">
                            المصدر :
                            <?php if ($source_url) : ?>
                                <a href="<?php echo esc_url($source_url); ?>" target="_blank" class="text-primary hover:underline"><?php echo esc_html($source_name); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($source_name); ?>
                            <?php endif; ?>
                        </h3>
                    </div>
                <?php endif; ?>
            </div>


            <!-- tags -->
            <?php get_template_part('templates/components/tags-list', null, [
                'post_id' => $post_id
            ]); ?>

            <!-- expert/author box -->
            <div class="max-sm:pr-3">
                <?php
                $show_author_box = get_post_meta($post_id, '_news_show_author_box', true);

                if ($show_author_box) :
                    $author_name = get_post_meta($post_id, '_news_author_name', true);
                    if (empty($author_name)) $author_name = get_the_author_meta('display_name', $post->post_author);

                    $author_title = get_post_meta($post_id, '_news_author_title', true);
                    $author_img_id = get_post_meta($post_id, '_news_author_image_id', true);
                    $author_url = get_post_meta($post_id, '_news_author_url', true);
                ?>
                    <div class="inline-flex gap-6">
                        <div class="bg-stone-50 rounded-lg flex justify-center items-center gap-2">
                            <div class="text-right justify-start text-neutral-950 text-sm font-bold ">بواسطة:</div>
                        </div>
                        <div class="flex justify-start items-center gap-2">
                            <?php
                            if ($author_img_id) {
                                echo wp_get_attachment_image($author_img_id, 'thumbnail', false, ['class' => 'w-10 h-10 rounded-full object-cover']);
                            } else {
                                echo get_avatar($post->post_author, 40, '', '', ['class' => 'w-10 h-10 rounded-full']);
                            }
                            ?>
                            <div class="flex flex-col">
                                <div class="text-right justify-center text-neutral-950 text-sm font-bold ">
                                    <?php if ($author_url) : ?>
                                        <a href="<?php echo esc_url($author_url); ?>" target="_blank" class="hover:underline text-black hover:text-primary transition-colors"><?php echo esc_html($author_name); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($author_name); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($author_title) : ?>
                                    <div class="text-right justify-start text-stone-500 text-xs font-normal "><?php echo esc_html($author_title); ?></div>
                                <?php else: ?>
                                    <?php
                                    $description = get_the_author_meta('description', $post->post_author);
                                    if ($description) {
                                        echo '<div class="text-right justify-start text-stone-500 text-xs font-normal ">' . wp_trim_words($description, 5) . '</div>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- share buttons -->
            <?php get_template_part('templates/components/share-buttons', null, [
                'post_id' => $post_id,
                'label'   => __('شارك الخبر', 'greenergy')
            ]); ?>

            <!-- Sidebar for mobile -->
            <div class="flex md:hidden flex-row flex-nowrap overflow-x-auto gap-4 px-4 -mx-4 pb-4 items-stretch scrollbar-hide">
                <?php echo do_blocks('
                    <!-- wp:greenergy/directory-widget /-->
                    <!-- wp:greenergy/courses-widget /-->
                    <!-- wp:greenergy/featured-jobs-widget /-->
                    <!-- wp:greenergy/ad-block {"height":"100%","width":"20rem","hasContainer":false} /-->
                    <!-- wp:greenergy/follow-us-widget /-->
                '); ?>
            </div>

            <!-- ad block -->
            <?php echo do_blocks('<!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false,"paddingY":false} /-->'); ?>

            <!-- related posts -->
            <div class="mt-6">
                <?php echo do_blocks('<!-- wp:greenergy/news-grid /-->'); ?>
            </div>
        </article>

        <!-- Sidebar Area (Left side) -->
        <aside class="max-lg:hidden lg:w-1/4 w-full flex flex-col justify-start items-stretch gap-4 lg:sticky lg:top-4" data-aos="fade-right">
            <?php echo do_blocks('
                    <!-- wp:greenergy/directory-widget /-->
                    <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                    <!-- wp:greenergy/courses-widget /-->
                    <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                    <!-- wp:greenergy/featured-jobs-widget /-->
                    <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                    <!-- wp:greenergy/follow-us-widget /-->
                '); ?>
        </aside>
    </div>
</div>