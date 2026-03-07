<?php
$post_id = get_the_ID();
$categories = get_the_category($post_id);

if ($categories) {
    $cat_ids = wp_list_pluck($categories, 'term_id');
    $args = [
        'post_type'      => 'jobs',
        'posts_per_page' => 2,
        'post__not_in'   => [$post_id],
        'category__in'   => $cat_ids,
        'post_status'    => 'publish',
    ];
} else {
    $args = [
        'post_type'      => 'jobs',
        'posts_per_page' => 2,
        'post__not_in'   => [$post_id],
        'post_status'    => 'publish',
    ];
}

$related_query = new WP_Query($args);

if ($related_query->have_posts()) :
?>
    <div class="mt-16">
        <div class="text-center mb-10">
            <div class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full text-base font-bold mb-4 shadow-sm">
                وظائف مماثلة
            </div>
            <h2 class="text-neutral-950 text-3xl font-bold leading-tight">اكتشف الوضائف الذهبية</h2>
        </div>
        <div class="flex flex-wrap gap-4">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                <?php get_template_part('templates/components/job-card', null, ['post' => get_post()]); ?>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    </div>
<?php else : ?>
    <div class="mt-16">
        <div class="text-center mb-10">
            <div class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full text-base font-bold mb-4 shadow-sm">
                وظائف مماثلة
            </div>
            <h2 class="text-neutral-950 text-3xl font-bold leading-tight">اكتشف الوضائف الذهبية</h2>
        </div>
        <div class="flex flex-wrap gap-4 justify-center">
            <p>لا توجد وظائف مماثلة</p>
        </div>
    </div>
<?php endif; ?>