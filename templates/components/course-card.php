<?php

/**
 * Course Card Template Part
 */

$post_id  = get_the_ID();
$duration = get_field('course_duration_value', $post_id) ?: 'ساعة 20';
$students = get_field('course_registered_count', $post_id) ?: '0';
$image    = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/400x300';
?>

<div class="w-full sm:w-[48%] lg:w-[31%] px-0 group ">
    <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg border border-gray-100">
        <a href="<?php the_permalink(); ?>" class="absolute z-40 h-full w-full left-0 top-0"></a>

        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
            style="background-image:url('<?php echo esc_url($image); ?>');"></div>

        <!-- Gradient Overlay & Details -->
        <div class="absolute bottom-0 w-full text-right p-4 pt-20 bg-gradient-to-t from-black/90 via-black/40 to-transparent transition-all duration-300 z-10">
            <h3 class="text-white font-medium text-base mb-2 line-clamp-2">
                <?php the_title(); ?>
            </h3>
            <div class="flex gap-5 text-white/90 text-sm font-normal">
                <div class="flex items-center gap-2">
                    <i class="far fa-clock"></i>
                    <span><?php echo esc_html($duration); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    <span><?php echo esc_html($students); ?></span>
                </div>
            </div>
        </div>

        <!-- Cutted Edge Decor -->
        <div class="absolute bg-white rounded-lg z-20 -left-[.3rem] -bottom-2 w-20 h-20"></div>

        <!-- Floating Button -->
        <div class="absolute bottom-1 left-1 w-14 h-14 bg-[#bef264] rounded-full flex items-center justify-center text-[#013214] shadow-[0_4px_12px_rgba(163,230,53,0.4)] z-30 group-hover:scale-110 group-hover:rotate-[45deg] transition-all duration-300">
            <i class="fas fa-arrow-up text-lg rotate-[-45deg]"></i>
        </div>
    </div>
</div>