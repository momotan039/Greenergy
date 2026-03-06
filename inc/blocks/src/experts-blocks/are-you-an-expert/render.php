<?php

/**
 * Are You an Expert Block — CTA section (editable title, description, image).
 * Button link is not editable here; bind it in theme/navigation.
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attrs = wp_parse_args($attributes ?? [], [
    'title'       => __('هل أنت خبير؟ انضم إلى مجتمعنا.', 'greenergy'),
    'description' => __('سجّل ملفك لزيادة فرص التعاون والظهور أمام شركات ومشاريع الطاقة المتجددة.', 'greenergy'),
    'buttonText'  => __('انشئ ملف خبير', 'greenergy'),
    'imageId'     => 0,
    'imageUrl'    => '',
]);

$title   = (string) $attrs['title'];
$desc    = (string) $attrs['description'];
$btn     = (string) $attrs['buttonText'];
$image_url = '';
if (! empty($attrs['imageUrl'])) {
    $image_url = esc_url_raw($attrs['imageUrl']);
} elseif (! empty($attrs['imageId'])) {
    $image_url = wp_get_attachment_image_url((int) $attrs['imageId'], 'large');
}
if ($image_url === '') {
    $image_url = 'https://placehold.co/1200x245';
}
?>

<section class="hover:scale-105 transition-all duration-300 are-you-an-expert-block w-full rounded-3xl shadow-lg overflow-hidden" style="background-image: url('<?php echo esc_url($image_url); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="h-60 bg-black/25 bg-blend-linear-burn flex flex-col items-center justify-center text-center gap-3 px-6">
        <?php if ($title !== '') : ?>
            <h2 class="text-white text-2xl font-bold max-md:text-lg"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ($desc !== '') : ?>
            <p class="text-white text-sm max-w-xl"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
        <?php if ($btn !== '') : ?>
            <a href="#" class="mt-2 w-60 h-10 flex items-center justify-center bg-green-700 text-white text-sm rounded-lg"><?php echo esc_html($btn); ?></a>
        <?php endif; ?>
    </div>
</section>