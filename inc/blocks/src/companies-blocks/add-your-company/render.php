<?php
/**
 * Add Your Company Block — CTA with title, description, points (icons), button, and image.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$raw_attrs = $attributes ?? [];
if (isset($block) && $block instanceof WP_Block) {
    if (! empty($block->parsed_block['attrs'])) {
        $raw_attrs = array_merge($raw_attrs, (array) $block->parsed_block['attrs']);
    }
    if (! empty($block->attributes)) {
        $raw_attrs = array_merge($raw_attrs, (array) $block->attributes);
    }
}

$attrs = wp_parse_args($raw_attrs, [
    'title'            => 'أضف شركتك أو منظمتك',
    'description'      => 'هل لديك شركة أو منظمة في مجال الطاقة المتجددة؟ انضم إلى دليلنا الشامل واعرض خدماتك لآلاف العملاء المحتملين.',
    'points'           => [],
    'buttonText'       => 'أضف شركتك الآن',
    'buttonUrl'        => '',
    'backgroundImage'  => 'https://placehold.co/600x800',
    'backgroundImageId' => 0,
]);

$title   = (string) $attrs['title'];
$desc    = (string) $attrs['description'];
$points  = is_array($attrs['points']) ? $attrs['points'] : [];
if (empty($points)) {
    $points = [
        [ 'text' => 'إدراج مجاني مع إمكانية الترقية', 'icon' => 'award.svg', 'iconType' => 'platform' ],
        [ 'text' => 'عرض شامل لخدماتك ومشاريعك', 'icon' => 'buildings-2.svg', 'iconType' => 'platform' ],
        [ 'text' => 'وصول لآلاف العملاء المهتمين', 'icon' => 'people-2.svg', 'iconType' => 'platform' ],
    ];
}
$btn_text = (string) $attrs['buttonText'];
$btn_url  = (string) $attrs['buttonUrl'];
$bg_image = (string) $attrs['backgroundImage'];
$bg_id    = (int) ($attrs['backgroundImageId'] ?? 0);

if ($bg_id > 0) {
    $url = wp_get_attachment_image_url($bg_id, 'large');
    if ($url) {
        $bg_image = $url;
    }
}

$assets_uri = get_template_directory_uri();
?>

<div class="flex max-md:flex-col bg-white rounded-2xl group hover:bg-primary-300/20 shadow-lg outline outline-1 outline-gray-200 border border-zinc-100">
    <div class="flex-1 px-6 md:px-8 py-6 flex flex-col gap-5 ">
        <?php if ($title !== '') : ?>
            <h2 class="text-2xl font-bold text-neutral-950"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($desc !== '') : ?>
            <p class="text-sm text-stone-500 leading-relaxed max-w-xl"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>

        <?php if (! empty($points)) : ?>
            <ul class="flex flex-col gap-3 text-sm text-stone-600">
                <?php foreach ($points as $point) :
                    $text = isset($point['text']) ? (string) $point['text'] : '';
                    $icon_type = isset($point['iconType']) ? $point['iconType'] : 'platform';
                    $icon = isset($point['icon']) ? $point['icon'] : '';
                    $fa_icon = isset($point['faIcon']) ? $point['faIcon'] : '';
                    $fa_size = isset($point['faIconSize']) ? (int) $point['faIconSize'] : 12;
                    $icon_image_id = isset($point['iconImageId']) ? (int) $point['iconImageId'] : 0;
                    $icon_image_url = isset($point['iconImageUrl']) ? (string) $point['iconImageUrl'] : '';
                    if ($icon_image_id > 0 && $icon_image_url === '') {
                        $icon_image_url = wp_get_attachment_image_url($icon_image_id, 'thumbnail') ?: '';
                    }
                    if ($text === '') continue;
                ?>
                    <li class="flex items-center gap-2">
                        <?php if ($icon_type === 'image' && $icon_image_url !== '') : ?>
                            <img src="<?php echo esc_url($icon_image_url); ?>" alt="<?php echo esc_attr($text); ?>" class="w-6 h-6 object-contain">
                        <?php elseif ($icon_type === 'font-awesome' && $fa_icon !== '') : ?>
                            <i class="<?php echo esc_attr($fa_icon); ?> w-6 h-6 flex items-center justify-center" style="font-size: <?php echo esc_attr($fa_size); ?>px;"></i>
                        <?php elseif ($icon !== '') : ?>
                            <img src="<?php echo esc_url($assets_uri . '/assets/images/vuesax/outline/' . $icon); ?>" alt="<?php echo esc_attr($text); ?>" class="w-6 h-6">
                        <?php endif; ?>
                        <?php echo esc_html($text); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($btn_text !== '') : ?>
            <?php if ($btn_url !== '') : ?>
                <a href="<?php echo esc_url($btn_url); ?>" class="w-fit mt-2 bg-[#229924] group-hover:bg-green-800 group-hover:scale-105 transition text-white text-sm px-6 py-2.5 rounded-lg font-medium">
                    <?php echo esc_html($btn_text); ?>
                </a>
            <?php else : ?>
                <button type="button" class="w-fit mt-2 bg-[#229924] group-hover:bg-green-800 group-hover:scale-105 transition text-white text-sm px-6 py-2.5 rounded-lg font-medium">
                    <?php echo esc_html($btn_text); ?>
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="flex-1 max-md:order-first relative">
        <div class="group-hover:shadow-lg group-hover:shadow-green-600/50 group-hover:-translate-y-1 transition-all duration-500 w-full h-full max-md:h-[20rem] object-cover rounded-l-2xl max-md:rounded-t-2xl max-md:rounded-b-none bg-cover bg-center" style="background-image: url('<?php echo esc_url($bg_image); ?>');"></div>
        <?php if (! empty($points)) : ?>
            <div class="absolute inset-0 flex items-center justify-center z-10 gap-3">
                <?php foreach (array_slice($points, 0, 3) as $point) :
                    $icon_type = isset($point['iconType']) ? $point['iconType'] : 'platform';
                    $icon = isset($point['icon']) ? $point['icon'] : '';
                    $fa_icon = isset($point['faIcon']) ? $point['faIcon'] : '';
                    $fa_size = isset($point['faIconSize']) ? (int) $point['faIconSize'] : 12;
                    $icon_image_id = isset($point['iconImageId']) ? (int) $point['iconImageId'] : 0;
                    $icon_image_url = isset($point['iconImageUrl']) ? (string) $point['iconImageUrl'] : '';
                    if ($icon_image_id > 0 && $icon_image_url === '') {
                        $icon_image_url = wp_get_attachment_image_url($icon_image_id, 'thumbnail') ?: '';
                    }
                ?>
                    <?php if ($icon_type === 'image' && $icon_image_url !== '') : ?>
                        <span class="w-10 h-10 bg-green-300/30 p-1 rounded-md flex items-center justify-center"><img src="<?php echo esc_url($icon_image_url); ?>" alt="" class="w-full h-full object-contain"></span>
                    <?php elseif ($icon_type === 'font-awesome' && $fa_icon !== '') : ?>
                        <span class="w-10 h-10 bg-green-300/30 p-1 rounded-md flex items-center justify-center"><i class="<?php echo esc_attr($fa_icon); ?> text-green-700" style="font-size: <?php echo esc_attr($fa_size); ?>px;"></i></span>
                    <?php elseif ($icon !== '') : ?>
                        <img src="<?php echo esc_url($assets_uri . '/assets/images/vuesax/outline/' . $icon); ?>" alt="" class="w-10 h-10 bg-green-300/30 p-1 rounded-md">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
