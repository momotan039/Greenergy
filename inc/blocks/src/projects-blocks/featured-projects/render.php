<?php
if (! defined('ABSPATH')) exit;

$block_attrs = (isset($block) && $block instanceof WP_Block)
    ? array_merge((array)($block->attributes ?? []), (array)($block->parsed_block['attrs'] ?? []))
    : [];
$attrs = wp_parse_args($attributes ?? [], wp_parse_args($block_attrs, [
    'title'            => 'المشاريع المميزة',
    'description'       => 'أبرز المشاريع الرائدة في مجال الطاقة المتجددة',
    'selectedProjects'   => [],
]));

$selected_ids = array_unique(array_filter(array_map(
    function ($i) {
        return absint(is_array($i) ? ($i['id'] ?? 0) : $i);
    },
    (array) $attrs['selectedProjects']
)));

$projects = [];
if (! empty($selected_ids) && post_type_exists('projects')) {
    $q = new WP_Query([
        'post_type'      => 'projects',
        'post__in'       => $selected_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);
    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $projects[] = get_the_ID();
        }
        wp_reset_postdata();
    }
}

$swiper_config = wp_json_encode([
    'slidesPerView'  => 1.3,
    'spaceBetween'   => 16,
    'loop'           => count($projects) > 3,
    'watchOverflow'  => true,
    'autoplay'       => count($projects) > 1 ? ['delay' => 5000, 'disableOnInteraction' => false] : false,
    'pagination'     => ['clickable' => true],
    'breakpoints'    => [
        '640'  => ['slidesPerView' => 2, 'spaceBetween' => 20],
        '1024' => ['slidesPerView' => 3, 'spaceBetween' => 24],
    ],
]);
?>
<section class="space-y-6 flex flex-col items-center w-full">
    <header class="text-center my-8">
        <div class="inline-flex items-center title-badge">
            <?php echo esc_html($attrs['title']); ?>
        </div>
        <p class="text-stone-500 text-base"><?php echo esc_html($attrs['description']); ?></p>
    </header>

    <?php if (! empty($projects)) : ?>
        <div class="swiper swiper-container js-swiper-init overflow-hidden w-full pb-16"
            data-swiper-config="<?php echo esc_attr($swiper_config); ?>">
            <div class="swiper-wrapper items-stretch">
                <?php foreach ($projects as $pid) : ?>
                    <div class="swiper-slide h-auto">
                        <?php get_template_part('templates/components/project-card', null, ['post_id' => $pid, 'is_featured' => true]); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination !relative mt-8"></div>
        </div>
    <?php else : ?>
        <p class="text-stone-500 text-sm text-center py-8">
            <?php esc_html_e('اختر مشاريع من إعدادات الكتلة لعرضها هنا.', 'greenergy'); ?>
        </p>
    <?php endif; ?>
</section>