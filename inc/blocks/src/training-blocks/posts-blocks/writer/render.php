<?php

/**
 * Render Writer Block
 */

$post_id = get_the_ID();
$is_editor = is_admin();

// ACF Fields for Posts
$show_writer       = get_field('show_writer', $post_id);
$writer_name       = get_field('writer_name', $post_id);
$writer_title      = get_field('writer_title', $post_id);
$writer_bio        = get_field('writer_bio', $post_id);
$writer_image      = get_field('writer_image', $post_id) ?: 'https://placehold.co/72x72';
$writer_link       = get_field('writer_link', $post_id);
$writer_post_count = get_field('writer_post_count', $post_id);

// Fallbacks for Editor
if (!$post_id || $is_editor) {
    $show_writer = true;
    $writer_name  = $writer_name ?: 'م. أحمد الزهراني';
    $writer_title = $writer_title ?: 'خبير في الطاقة المتجددة';
    $writer_bio   = $writer_bio ?: 'كاتب ومحلل متخصص في تقنيات الطاقة الشمسية واستراتيجيات الاستدامة في الشرق الأوسط.';
    $writer_image = $writer_image ?: 'https://placehold.co/72x72';
    $writer_post_count = $writer_post_count ?: '42';
}
?>

<?php if ($show_writer) : ?>
    <div class="p-4 bg-white rounded-2xl flex flex-col gap-4 text-right
            shadow-lg
            outline outline-1 outline-gray-200 mb-8">

        <!-- عنوان القسم -->
        <div class="text-lg sm:text-xl font-bold text-neutral-950">
            عن الكاتب
        </div>

        <!-- بيانات الكاتب -->
        <div class="flex flex-col sm:flex-row sm:items-center  gap-4 md:gap-8">

            <!-- صورة + اسم + نبذة -->
            <div class="flex flex-col gap-2">

                <!-- صف الصورة والاسم -->
                <div class="flex items-center gap-3">
                    <?php if ($writer_link) : ?>
                        <a href="<?php echo esc_url($writer_link); ?>" class="block flex-shrink-0">
                        <?php endif; ?>
                        <img class="w-16 h-16 rounded-full object-cover" src="<?php echo esc_url($writer_image); ?>" alt="<?php echo esc_attr($writer_name); ?>" />
                        <?php if ($writer_link) : ?>
                        </a>
                    <?php endif; ?>
                    <div>
                        <div class="text-xl font-medium text-neutral-950">
                            <?php if ($writer_link) : ?>
                                <a href="<?php echo esc_url($writer_link); ?>" class="hover:text-green-700 transition-colors">
                                <?php endif; ?>
                                <?php echo esc_html($writer_name); ?>
                                <?php if ($writer_link) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($writer_title) : ?>
                            <div class="text-base text-stone-500"><?php echo esc_html($writer_title); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- النبذة -->
                <?php if ($writer_bio) : ?>
                    <div class="text-md text-stone-500 leading-relaxed">
                        <?php echo esc_html($writer_bio); ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- إحصائيات -->
            <div class="grid grid-cols-1 gap-3 w-full sm:w-auto">

                <?php if ($writer_post_count !== false && $writer_post_count !== '') : ?>
                    <div class="bg-neutral-100 rounded-lg
                        flex flex-col items-center justify-center
                        h-14 sm:h-16 px-3 sm:w-36">
                        <div class="text-lg sm:text-xl font-bold text-primary"><?php echo esc_html($writer_post_count); ?></div>
                        <div class="text-xs sm:text-sm text-stone-500">مقال منشور</div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
<?php endif; ?>