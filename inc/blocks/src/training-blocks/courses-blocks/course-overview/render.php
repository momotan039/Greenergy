<?php

/**
 * Render Course Overview Block
 * 
 * @var array $attributes Block attributes
 */

$post_id = get_the_ID();
$is_editor = is_admin();

// Dynamic Data
$title = get_the_title($post_id);
$thumbnail_url = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/600x400';

// ACF Fields
$start_date      = get_field('course_start_date', $post_id);
$duration_text   = get_field('course_duration_value', $post_id);
$registered      = get_field('course_registered_count', $post_id);
$price_type      = get_field('course_price_type', $post_id);

// Taxonomies
$course_categories = get_the_terms($post_id, 'course_category');

// Process Terms
$training_type = 'أونلاين'; // Default fallback
$category_name = ($course_categories && !is_wp_error($course_categories)) ? $course_categories[0]->name : '';

// Fallbacks for Editor
if (!$post_id || $is_editor) {
    $start_date = $start_date ?: '10-10-2025';
    $duration_text = $duration_text ?: '5 أيام / 20 ساعة';
    $registered = $registered ?: '245';
    $training_type = $training_type ?: 'عن بُعد';
    $price_type = $price_type ?: 'free';
    $title = $title ?: 'دورة شاملة في تصميم أنظمة الطاقة الشمسية للمبتدئين';
    $category_name = $category_name ?: 'تدريب معتمد';
}

$price_label = ($price_type === 'paid') ? 'مدفوعة' : 'مجانية';
$price_class = 'bg-[#229924]'; // Fixed color for both paid and free

?>

<div class="flex flex-col gap-4 rounded-lg shadow-lg outline outline-1 outline-gray-200 p-4 bg-white mb-8">

    <!-- Image -->
    <div class="w-full h-72 rounded-lg bg-cover bg-center" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>');"></div>

    <!-- Info row -->
    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4">

        <!-- تاريخ البدء -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#DCEFDC] rounded-full flex items-center justify-center">
                <img class="w-6 h-6" src="<?php echo GREENERGY_ASSETS_URI; ?>/images/vuesax/outline/clock.svg" alt="">
            </div>
            <div>
                <div class="text-xs text-stone-500">تاريخ البدء</div>
                <div class="text-sm font-medium text-neutral-950"><?php echo esc_html($start_date ?: 'قريباً'); ?></div>
            </div>
        </div>

        <!-- المدة -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#DCEFDC] rounded-full flex items-center justify-center">
                <img class="w-6 h-6" src="<?php echo GREENERGY_ASSETS_URI; ?>/images/vuesax/outline/clock.svg" alt="">
            </div>
            <div>
                <div class="text-xs text-stone-500">المدة</div>
                <div class="text-sm font-medium text-neutral-950"><?php echo esc_html($duration_text ?: '--'); ?></div>
            </div>
        </div>

        <!-- المسجلين -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#DCEFDC] rounded-full flex items-center justify-center">
                <img class="w-6 h-6" src="<?php echo GREENERGY_ASSETS_URI; ?>/images/vuesax/outline/people.svg" alt="">
            </div>
            <div>
                <div class="text-xs text-stone-500">المسجلين</div>
                <div class="text-sm font-medium text-neutral-950"><?php echo esc_html($registered); ?> متدرب</div>
            </div>
        </div>

        <!-- نوع التدريب -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#DCEFDC] rounded-full flex items-center justify-center">
                <img class="w-6 h-6" src="<?php echo GREENERGY_ASSETS_URI; ?>/images/vuesax/outline/briefcase.svg" alt="">
            </div>
            <div>
                <div class="text-xs text-stone-500">نوع التدريب</div>
                <div class="text-sm font-medium text-neutral-950"><?php echo esc_html($training_type); ?></div>
            </div>
        </div>

    </div>

    <hr class="w-full border-gray-200">

    <!-- Badges -->
    <div class="flex gap-2">
        <span class="px-4 py-1 <?php echo $price_class; ?> text-white text-sm rounded-full"><?php echo esc_html($price_label); ?></span>
        <?php if (!empty($category_name)) : ?>
            <span class="px-4 py-1 bg-green-200 text-green-700 text-sm rounded-full"><?php echo esc_html($category_name); ?></span>
        <?php endif; ?>
    </div>

    <!-- Title -->
    <h2 class="text-2xl max-sm:text-lg font-bold">
        <?php echo esc_html($title); ?>
    </h2>

    <!-- Button -->
    <button class="px-10 py-3 w-fit bg-[#229924] text-white rounded-lg text-sm">
        سجل الآن
    </button>

</div>