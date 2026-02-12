<?php

/**
 * The template for displaying single news posts
 *
 * @package Greenergy
 * @since 1.0.0
 */

get_header();
?>
<div class="container mx-auto bg-white mb-8">
    <?php echo do_blocks('<!-- wp:greenergy/scroll-progress /-->'); ?>
    <div class="w-full inline-flex flex-col justify-start gap-4">
        <?php echo do_blocks('<!-- wp:greenergy/breadcrumb /-->'); ?>
        <?php echo do_blocks('<!-- wp:greenergy/main-banner /-->'); ?>
    </div>
</div>

<div class="mb-8">
    <?php echo do_blocks('<!-- wp:greenergy/stories /-->'); ?>
</div>

<div class="container mx-auto px-4">

    <div class="lg:flex lg:flex-row  justify-start items-start gap-6">

        <!-- Main Content Area (Right side) -->
        <article class="w-full flex-1 p-4 max-sm:p-0 bg-white rounded-2xl flex flex-col justify-start items-stretch gap-6 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up">
            <div class="flex justify-between">
                <!-- Categories -->
                <div class="flex flex-wrap gap-2">
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'news_category');
                    if ($categories && ! is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            echo '<a href="#" class="text-sm text-black px-4 bg-green-700/20 rounded-[100px] py-1"># ' . esc_html($cat->name) . '</a>';
                        }
                    }
                    ?>
                </div>

                <!-- Labels (Urgent/Important) -->
                <?php
                $labels = get_the_terms(get_the_ID(), 'news_label');
                if ($labels && ! is_wp_error($labels)) {
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

            <h1 class="text-black text-2xl font-medium max-md:text-lg"><?php the_title(); ?></h1>

            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php the_post_thumbnail_url('full'); ?>" class="h-[395px] max-md:h-[324px] w-full object-cover rounded-lg" alt="<?php the_title_attribute(); ?>">
            <?php endif; ?>

            <div class="flex gap-6 relative">
                <div class="flex items-center gap-1 relative">
                    <i class="far fa-eye text-neutral-950 text-sm"></i>
                    <span class="text-neutral-950 text-sm">
                        <?php
                        // Enhanced Views System
                        echo Greenergy_Post_Views::get_views(get_the_ID());
                        ?>
                    </span>
                    <div class="absolute top-0 left-[-0.8rem] w-[2px] h-full bg-stone-300 rounded-[100px]"></div>
                </div>
                <span class="text-neutral-800 text-sm"><?php echo get_the_date('d/m/Y'); ?></span>

                <?php
                // Source moved back to below content
                ?>
            </div>

            <!-- editor content -->
            <div class="new-content">
                <?php the_content(); ?>
            </div>

            <!-- source -->
            <?php
            $source_name = get_post_meta(get_the_ID(), '_news_source_name', true);
            $source_url = get_post_meta(get_the_ID(), '_news_source_url', true);
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


            <!-- tags -->
            <?php
            $tags = get_the_tags();
            if ($tags) {
                echo '<div class="flex flex-wrap gap-[12px]">';
                foreach ($tags as $tag) {
                    echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="text-sm text-black px-4 bg-green-700/20 rounded-[100px] py-1     hover:bg-green-700 hover:text-white transition-colors duration-300">#' . esc_html($tag->name) . '</a>';
                }
                echo '</div>';
            }
            ?>

            <!-- expert/author box -->
            <?php
            // Use CPT meta field toggle
            $show_author_box = get_post_meta(get_the_ID(), '_news_show_author_box', true);

            // Fallback to global setting if meta is not set (optional, or just stick to meta as per user request "via same cpt")
            // User said "let user hide and show it via same cpt", implying the control is ON the CPT.

            if ($show_author_box) :
                $author_name = get_post_meta(get_the_ID(), '_news_author_name', true);
                if (empty($author_name)) $author_name = get_the_author(); // Fallback to WP Author Name

                $author_title = get_post_meta(get_the_ID(), '_news_author_title', true);
                $author_img_id = get_post_meta(get_the_ID(), '_news_author_image_id', true);
                $author_url = get_post_meta(get_the_ID(), '_news_author_url', true);
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
                            echo get_avatar(get_the_author_meta('ID'), 40, '', '', ['class' => 'w-10 h-10 rounded-full']);
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
                                // Fallback to description if no title field, to match original "job" slot
                                $description = get_the_author_meta('description');
                                if ($description) {
                                    echo '<div class="text-right justify-start text-stone-500 text-xs font-normal ">' . wp_trim_words($description, 5) . '</div>';
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- share buttons -->
            <?php
            $news_settings = get_option('greenergy_news_settings', []);
            $share_providers = isset($news_settings['shareProviders']) ? $news_settings['shareProviders'] : [];
            if (! empty($share_providers)) :
                $link = get_permalink();
                $title = get_the_title();
            ?>
                <div class="text-center justify-start text-neutral-950 text-base font-medium capitalize  mt-6">شارك الخبر</div>
                <div class="flex flex-wrap justify-center items-center gap-4 bg-gray-50 rounded-2xl shadow-sm p-4 pt-0">

                    <?php if (in_array('whatsapp', $share_providers)) : ?>
                        <a href="https://wa.me/?text=<?php echo urlencode($title . ' ' . $link); ?>" target="_blank" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-[#25D366] hover:shadow-lg hover:-translate-y-1">
                            <i class="fab fa-whatsapp fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('telegram', $share_providers)) : ?>
                        <a href="https://t.me/share/url?url=<?php echo urlencode($link); ?>&text=<?php echo urlencode($title); ?>" target="_blank" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-[#0088cc] hover:shadow-lg hover:-translate-y-1">
                            <i class="fab fa-telegram fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('facebook', $share_providers)) : ?>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($link); ?>" target="_blank" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-[#1877F2] hover:shadow-lg hover:-translate-y-1">
                            <i class="fab fa-facebook-f fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('instagram', $share_providers)) : ?>
                        <!-- Instagram doesn't have a direct share link usually, just linking to profile or using a generic filler -->
                        <a href="#" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:shadow-lg hover:-translate-y-1">
                            <i class="fab fa-instagram fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('youtube', $share_providers)) : ?>
                        <a href="#" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-[#FF0000] hover:shadow-lg hover:-translate-y-1">
                            <i class="fab fa-youtube fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('rss', $share_providers)) : ?>
                        <a href="<?php bloginfo('rss2_url'); ?>" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-[#f26522] hover:shadow-lg hover:-translate-y-1">
                            <i class="fas fa-rss fa-xl"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('copy', $share_providers)) : ?>
                        <button onclick="navigator.clipboard.writeText('<?php echo $link; ?>'); alert('<?php echo __('Link Copied', 'greenergy'); ?>');" class="flex items-center justify-center w-12 h-12 max-sm:w-8 max-sm:h-8 rounded-full bg-white text-gray-400 shadow-sm border border-gray-100 transition-all duration-300 hover:text-white hover:bg-primary hover:shadow-lg hover:-translate-y-1">
                            <i class="fa-regular fa-copy fa-xl"></i>
                        </button>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
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

    <!-- ad block -->
    <?php echo do_blocks('<!-- wp:greenergy/ad-block {"height":"275px","hasContainer":true} /-->'); ?>

</div>

<?php
get_footer();
