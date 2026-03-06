<?php

/**
 * Featured Experts Block — render
 *
 * Manual entries (not from DB) + selected experts from DB. Swiper: 2 slides on small, 4 on large; pagination.
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$attrs = wp_parse_args($attributes ?? [], [
    'title'           => __('أبرز الخبراء', 'greenergy'),
    'description'     => __('خبراء يقدمون عروضًا في مؤتمرات للطاقة المتجددة.', 'greenergy'),
    'manualEntries'   => [],
    'selectedExperts' => [],
]);

$title   = (string) $attrs['title'];
$desc    = (string) $attrs['description'];

// Build items: manual first, then from DB
$items = [];

foreach ((array) $attrs['manualEntries'] as $e) {
    if (! is_array($e)) {
        continue;
    }
    $thumb = '';
    if (! empty($e['imageUrl'])) {
        $thumb = esc_url_raw($e['imageUrl']);
    } elseif (! empty($e['imageId'])) {
        $thumb = wp_get_attachment_image_url((int) $e['imageId'], 'thumbnail');
    }
    if ($thumb === '') {
        $thumb = 'https://placehold.co/72x72';
    }
    $items[] = [
        'type'        => 'manual',
        'name'        => (string) ($e['name'] ?? ''),
        'avatar'      => $thumb,
        'role'        => (string) ($e['role'] ?? ''),
        'quote'       => (string) ($e['quote'] ?? ''),
        'work_for'    => (string) ($e['workFor'] ?? ''),
        'profile_url' => esc_url_raw($e['profileUrl'] ?? '') ?: '#',
        'phone'       => (string) ($e['phone'] ?? ''),
        'website'     => (string) ($e['website'] ?? ''),
        'twitter'     => (string) ($e['twitter'] ?? ''),
        'instagram'   => (string) ($e['instagram'] ?? ''),
        'facebook'    => (string) ($e['facebook'] ?? ''),
        'linkedin'    => (string) ($e['linkedin'] ?? ''),
    ];
}

$selected = (array) $attrs['selectedExperts'];
$expert_ids = [];
foreach ($selected as $item) {
    if (is_array($item)) {
        $id = absint($item['id'] ?? $item['ID'] ?? 0);
        if ($id) {
            $expert_ids[] = $id;
        }
    } elseif (is_object($item)) {
        $id = absint($item->id ?? $item->ID ?? 0);
        if ($id) {
            $expert_ids[] = $id;
        }
    } elseif (is_numeric($item)) {
        $expert_ids[] = absint($item);
    }
}
$expert_ids = array_unique(array_filter($expert_ids));

if (! empty($expert_ids)) {
    $query = new WP_Query([
        'post_type'      => 'experts',
        'post__in'       => $expert_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);
    while ($query->have_posts()) {
        $query->the_post();
        $items[] = ['type' => 'db', 'post_id' => get_the_ID()];
    }
    wp_reset_postdata();
}

$has_items = ! empty($items);
$swiper_id = 'featured-experts-swiper-' . (isset($block->context['postId']) ? (int) $block->context['postId'] : 0);
$swiper_config = [
    'slidesPerView'  => 2,
    'spaceBetween'   => 16,
    'loop'           => count($items) > 4,
    'watchOverflow'  => true,
    'grabCursor'     => true,
    'pagination'     => ['clickable' => true],
    'breakpoints'    => [
        '640'  => ['slidesPerView' => 2, 'spaceBetween' => 20],
        '1024' => ['slidesPerView' => 4, 'spaceBetween' => 24],
    ],
];
?>

<style>
.featured-experts-block .swiper-slide {
    padding-top: 2rem;
    overflow: hidden;
    box-sizing: border-box;
}
</style>

<section class="featured-experts-block flex flex-col items-center gap-6 mt-14 overflow-hidden" dir="rtl">
    <header class="flex flex-col items-center gap-2">
        <div class="px-8 py-2.5 bg-teal-50 rounded-3xl">
            <h2 class="text-green-700 text-2xl font-medium"><?php echo esc_html($title); ?></h2>
        </div>
        <?php if ($desc !== '') : ?>
            <p class="text-stone-500 text-base font-medium text-center"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
    </header>

    <?php if ($has_items) : ?>
    <div class="w-full swiper swiper-container js-swiper-init overflow-hidden pb-12"
        id="<?php echo esc_attr($swiper_id); ?>"
        data-swiper-config="<?php echo esc_attr(wp_json_encode($swiper_config)); ?>">
        <div class="swiper-wrapper items-stretch">
            <?php foreach ($items as $item) : ?>
                <div class="swiper-slide h-auto">
                    <?php if ($item['type'] === 'db') : ?>
                        <?php get_template_part('templates/components/expert-card', null, ['post_id' => $item['post_id']]); ?>
                    <?php else :
                        $m = $item;
                        $has_social = ($m['website'] !== '' || $m['twitter'] !== '' || $m['instagram'] !== '' || $m['facebook'] !== '' || $m['linkedin'] !== '');
                    ?>
                        <article class="rounded-2xl p-4 max-md:p-2 shadow-lg outline outline-1 outline-green-200 flex flex-col items-center gap-3 relative min-h-[320px]">
                            <img src="<?php echo esc_url($m['avatar']); ?>" alt="<?php echo esc_attr($m['name'] ?: 'Expert'); ?>" class="w-16 h-16 rounded-2xl outline outline-4 outline-sky-500 absolute top-[-20px] max-md:top-[-25px] mx-auto object-cover">
                            <div class="text-center mt-8 shrink-0">
                                <h3 class="text-xl font-medium text-neutral-950 max-md:text-lg line-clamp-1"><?php echo esc_html($m['name'] ?: '—'); ?></h3>
                                <?php if ($m['role'] !== '') : ?>
                                    <p class="text-stone-500 text-sm line-clamp-1"><?php echo esc_html($m['role']); ?></p>
                                <?php else : ?>
                                    <p class="text-stone-500 text-sm invisible">—</p>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 flex items-center justify-center w-full overflow-hidden">
                                <?php if ($m['quote'] !== '') : ?>
                                    <blockquote class="text-center text-xs text-stone-500 line-clamp-3 px-1">«<?php echo esc_html($m['quote']); ?>»</blockquote>
                                <?php endif; ?>
                            </div>
                            <div class="shrink-0 flex gap-2 text-sm h-5 items-center">
                                <?php if ($m['work_for'] !== '') : ?>
                                    <span class="text-stone-500"><?php esc_html_e('يعمل لدى :', 'greenergy'); ?></span>
                                    <span class="text-green-700 font-medium line-clamp-1"><?php echo esc_html($m['work_for']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex w-full gap-4 shrink-0 mt-auto">
                                <a href="<?php echo esc_url($m['profile_url']); ?>" class="flex-1 h-9 flex items-center justify-center border rounded-lg text-sm"><?php esc_html_e('عرض الملف', 'greenergy'); ?></a>
                                <?php if ($m['phone'] !== '') : ?>
                                    <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $m['phone'])); ?>" class="w-9 h-9 bg-green-700 rounded-lg flex items-center justify-center" aria-label="<?php esc_attr_e('اتصال', 'greenergy'); ?>"><i class="fa-solid fa-phone text-white text-lg"></i></a>
                                <?php else : ?>
                                    <span class="w-9 h-9 bg-gray-300 rounded-lg flex items-center justify-center cursor-not-allowed" aria-hidden="true"><i class="fa-solid fa-phone text-white text-lg"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="w-full shrink-0 h-7">
                                <?php if ($has_social) : ?>
                                    <div class="w-full px-4 py-1 pb-2 rounded-full flex justify-between items-center bg-gradient-to-b from-sky-500 to-blue-700 h-full">
                                        <?php if ($m['website'] !== '') : ?><a href="<?php echo esc_url($m['website']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('الموقع', 'greenergy'); ?>"><i class="fa-solid fa-link text-white text-xs my-auto"></i></a><?php else : ?><span aria-hidden="true"><i class="fa-solid fa-link text-white text-xs opacity-50"></i></span><?php endif; ?>
                                        <?php if ($m['twitter'] !== '') : ?><a href="<?php echo esc_url($m['twitter']); ?>" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="fa-brands fa-x-twitter text-white text-xs my-auto"></i></a><?php else : ?><span aria-hidden="true"><i class="fa-brands fa-x-twitter text-white text-xs opacity-50"></i></span><?php endif; ?>
                                        <?php if ($m['instagram'] !== '') : ?><a href="<?php echo esc_url($m['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('انستغرام', 'greenergy'); ?>"><i class="fa-brands fa-instagram text-white text-xs my-auto"></i></a><?php else : ?><span aria-hidden="true"><i class="fa-brands fa-instagram text-white text-xs opacity-50"></i></span><?php endif; ?>
                                        <?php if ($m['facebook'] !== '') : ?><a href="<?php echo esc_url($m['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('فيسبوك', 'greenergy'); ?>"><i class="fa-brands fa-facebook-f text-white text-xs my-auto"></i></a><?php else : ?><span aria-hidden="true"><i class="fa-brands fa-facebook-f text-white text-xs opacity-50"></i></span><?php endif; ?>
                                        <?php if ($m['linkedin'] !== '') : ?><a href="<?php echo esc_url($m['linkedin']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('لينكد إن', 'greenergy'); ?>"><i class="fa-brands fa-linkedin-in text-white text-xs my-auto"></i></a><?php else : ?><span aria-hidden="true"><i class="fa-brands fa-linkedin-in text-white text-xs opacity-50"></i></span><?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="w-full h-full"></div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination !relative mt-8"></div>
    </div>
    <?php else : ?>
    <p class="text-stone-500 text-sm text-center py-8"><?php esc_html_e('أضف خبراء يدويين أو اختر خبراء من القاعدة من إعدادات الكتلة.', 'greenergy'); ?></p>
    <?php endif; ?>
</section>
