<?php

/**
 * Render Trainer Block
 */

$post_id = get_the_ID();
$is_editor = is_admin();

// ACF Fields
$show_trainer    = get_field('show_trainer', $post_id);
$trainer_name    = get_field('trainer_name', $post_id);
$trainer_title   = get_field('trainer_title', $post_id);
$trainer_bio     = get_field('trainer_bio', $post_id);
$trainer_image   = get_field('trainer_image', $post_id) ?: 'https://placehold.co/72x72';
$trainer_link    = get_field('trainer_link', $post_id);
$stat1_val      = get_field('trainer_stat1_val', $post_id);
$stat1_lab      = get_field('trainer_stat1_lab', $post_id);
$stat2_val      = get_field('trainer_stat2_val', $post_id);
$stat2_lab      = get_field('trainer_stat2_lab', $post_id);

// Fallbacks for Editor
if (!$post_id || $is_editor) {
    $show_trainer = true;
    $trainer_name  = $trainer_name ?: 'د. سارة العنزي';
    $trainer_title = $trainer_title ?: 'خبيرة في أنظمة الطاقة المتجددة';
    $trainer_bio   = $trainer_bio ?: 'دكتوراه في هندسة الطاقة المتجددة مع 12 عامًا من الخبرة في تصميم وتنفيذ مشاريع الطاقة الشمسية.';
    $trainer_image = $trainer_image ?: 'https://placehold.co/72x72';
    $stat1_val    = $stat1_val ?: '+50';
    $stat1_lab    = $stat1_lab ?: 'مشروع منفذ';
    $stat2_val    = $stat2_val ?: '+500';
    $stat2_lab    = $stat2_lab ?: 'متدرب';
}
?>

<?php if ($show_trainer) : ?>
    <div class="p-4 bg-white rounded-2xl flex flex-col gap-4 text-right
            shadow-[0_2px_14px_rgba(11,143,11,0.06)]
            outline outline-1 outline-zinc-100 mb-8">

        <!-- عنوان -->
        <div class="text-lg sm:text-xl font-bold text-neutral-950">
            المدرب
        </div>

        <!-- بيانات المدرب -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <!-- صورة + اسم + نبذة -->
            <div class="flex flex-col gap-2">

                <!-- صف الصورة والاسم -->
                <div class="flex items-center gap-3">
                    <?php if ($trainer_link) : ?>
                        <a href="<?php echo esc_url($trainer_link); ?>" class="block flex-shrink-0">
                        <?php endif; ?>
                        <img class="w-16 h-16 rounded-full object-cover" src="<?php echo esc_url($trainer_image); ?>" alt="<?php echo esc_attr($trainer_name); ?>" />
                        <?php if ($trainer_link) : ?>
                        </a>
                    <?php endif; ?>
                    <div>
                        <div class="text-xl font-medium text-neutral-950">
                            <?php if ($trainer_link) : ?>
                                <a href="<?php echo esc_url($trainer_link); ?>" class="hover:text-green-700 transition-colors">
                                <?php endif; ?>
                                <?php echo esc_html($trainer_name); ?>
                                <?php if ($trainer_link) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($trainer_title) : ?>
                            <div class="text-base text-stone-500"><?php echo esc_html($trainer_title); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- النبذة (صف لوحده) -->
                <?php if ($trainer_bio) : ?>
                    <div class="text-md text-stone-500 leading-relaxed">
                        <?php echo esc_html($trainer_bio); ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- إحصائيات -->
            <div class="grid grid-cols-2 gap-3 w-full sm:w-auto">

                <?php if ($stat1_val) : ?>
                    <div class="bg-neutral-100 rounded-lg
                        flex flex-col items-center justify-center
                        h-14 sm:h-16 px-3 sm:w-36">
                        <div class="text-lg sm:text-xl font-bold text-primary"><?php echo esc_html($stat1_val); ?></div>
                        <div class="text-xs sm:text-sm text-stone-500"><?php echo esc_html($stat1_lab); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($stat2_val) : ?>
                    <div class="bg-neutral-100 rounded-lg
                        flex flex-col items-center justify-center
                        h-14 sm:h-16 px-3 sm:w-36">
                        <div class="text-lg sm:text-xl font-bold text-primary"><?php echo esc_html($stat2_val); ?></div>
                        <div class="text-xs sm:text-sm text-stone-500"><?php echo esc_html($stat2_lab); ?></div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
<?php endif; ?>