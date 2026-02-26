<?php

/**
 * Render Post Header Block
 */

$post_id = get_the_ID();
$is_editor = is_admin();

// Data
$title = get_the_title($post_id);
$thumbnail = get_the_post_thumbnail_url($post_id, 'full') ?: 'https://placehold.co/800x400';
$date = get_the_date('j F Y', $post_id);

// Category
$categories = get_the_terms($post_id, 'category');
$cat_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';

// Views
$views = '0';
if (class_exists('Greenergy_Post_Views')) {
    $views = Greenergy_Post_Views::get_views($post_id);
}

// Writer
$writer_name = get_field('writer_name', $post_id);
if (!$writer_name) {
    $author_id = get_post_field('post_author', $post_id);
    $writer_name = get_the_author_meta('display_name', $author_id);
}

// Reading Time Calculation
$content = get_post_field('post_content', $post_id);
$word_count = count(preg_split('/\s+/u', strip_tags((string)$content), -1, PREG_SPLIT_NO_EMPTY));
$reading_time = max(1, ceil($word_count / 200));

// Fallbacks for Editor
if (!$post_id || $is_editor) {
    if (!$cat_name) $cat_name = 'الطاقة الشمسية';
    if (!$title) $title = 'مستقبل الطاقة الشمسية في الشرق الأوسط: بين التحديات والفرص';
    $writer_name = $writer_name ?: 'م. أحمد الزهراني';
    $date = $date ?: '25 سبتمبر 2025';
    $views = ($views === '0') ? '12,450' : $views;
    $reading_time = $reading_time ?: 8;
}
?>

<div class="flex flex-col gap-4">

    <!-- Tag -->
    <?php if ($cat_name) : ?>
        <span class="px-4 py-2 bg-green-700/20 rounded-full text-sm font-medium w-fit">
            # <?php echo esc_html($cat_name); ?>
        </span>
    <?php endif; ?>

    <!-- Title -->
    <h2 class="text-2xl font-bold text-right leading-tight">
        <?php echo esc_html($title); ?>
    </h2>

    <!-- Image / Cover -->
    <div class="relative w-full h-72 rounded-xl overflow-hidden">

        <!-- Background image -->
        <img src="<?php echo esc_url($thumbnail); ?>" class="absolute inset-0 w-full h-full object-cover" alt="<?php echo esc_attr($title); ?>" />

        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/0 to-black/70"></div>

        <!-- Meta info -->
        <div class="absolute bottom-4 right-4 flex flex-wrap gap-4 text-white text-sm">
            <?php if ($writer_name) : ?>
                <span><i class="fa-solid fa-user"></i> <?php echo esc_html($writer_name); ?></span>
            <?php endif; ?>
            <span><i class="fa-solid fa-calendar-days"></i> <?php echo esc_html($date); ?></span>
            <span><i class="fa-solid fa-eye"></i> <?php echo esc_html($views); ?> مشاهدة</span>
            <span><i class="fa-solid fa-clock"></i> <?php echo esc_html($reading_time); ?> دقائق للقراءة</span>
        </div>
    </div>

</div>