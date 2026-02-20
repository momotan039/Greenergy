<?php
$job_post = $args['post'] ?? null;
if (!$job_post) {
    global $post;
    $job_post = $post;
}

if (!$job_post) return;

$post_id = $job_post->ID;
$is_gold = $args['is_gold'] ?? (get_field('is_gold_acf', $post_id) ?: (get_post_meta($post_id, '_is_gold', true) === 'yes'));
$classes = $is_gold ? 'outline outline-2 outline-offset-[-2px] outline-yellow-500 bg-amber-50 rounded-3xl shadow-[0px_2px_14px_0px_rgba(255,209,82,0.56)]' : 'shadow-lg outline outline-1 outline-gray-200  bg-white rounded-2xl';

$company = get_post_meta($post_id, '_job_company', true) ?: get_bloginfo('name');
$location = get_post_meta($post_id, '_job_location', true) ?: 'الرياض ، السعودية';
$type_val = get_post_meta($post_id, '_job_type', true);
$type_map = [
    'full-time' => 'دوام كامل',
    'part-time' => 'دوام جزئي',
    'contract'  => 'عقد',
    'remote'    => 'عمل عن بعد',
];
$type = isset($type_map[$type_val]) ? $type_map[$type_val] : ($type_val ?: 'دوام كامل');
$date = get_the_date('d/m/Y', $post_id);
$cats = get_the_category($post_id);
$thumbnail = get_the_post_thumbnail_url($post_id, 'medium') ?: get_template_directory_uri() . '/assets/images/new-1.jpg';
?>
<div class="relative basis-[calc(50%-0.5rem)] max-md:basis-full max-lg:flex-col pl-4 pr-2 py-4 inline-flex items-start gap-2 overflow-hidden group hover:bg-green-600 transition-all duration-300  <?php echo $classes; ?>">
    <a href="<?php echo get_permalink($post_id); ?>" class="absolute inset-0 z-10 w-full h-full"></a>
    <?php if ($is_gold) { ?>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/gold-job-corner.svg" class="w-14 h-14 right-[2px] top-[2px] absolute">
    <?php } ?>
    <img class="w-56 h-56 max-sm:w-32 max-sm:h-32 max-lg:h-40 max-lg:w-40 max-lg:self-center rounded-lg object-cover" src="<?php echo $thumbnail; ?>" alt="<?php echo esc_attr($job_post->post_title); ?>" />
    <div class="flex-1 pl-3 inline-flex flex-col justify-start gap-3">
        <div class="self-stretch inline-flex justify-between items-center gap-4">
            <span class="text-right justify-start text-neutral-950 group-hover:text-white text-xl font-bold leading-5"><?php echo esc_html($job_post->post_title); ?></span>
            <?php if ($is_gold) { ?>
                <div class="h-8 px-4 bg-gradient-to-b from-yellow-500 to-yellow-600 rounded-[100px] inline-flex justify-center items-center gap-2.5">
                    <svg class="w-4 h-4 inline" aria-hidden="true">
                        <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/medal-star-white.svg"></use>
                    </svg>
                    <div class="h-6 text-right justify-start text-white group-hover:text-white text-sm font-medium leading-5">ذهبية</div>
                </div>
            <?php } ?>
        </div>
        <span class="self-stretch text-right justify-start text-neutral-800 group-hover:text-white text-sm font-medium leading-5"><?php echo esc_html($company); ?></span>
        <div class="self-stretch inline-flex justify-between items-center flex-wrap">
            <div class="flex justify-start items-center gap-1">
                <svg class="w-3 h-3.5 inline pt-[2px]" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/location.svg"></use>
                </svg>
                <span class="group-hover:text-white text-stone-500 text-sm font-normal"><?php echo esc_html($location); ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <svg class="w-4 h-3.5 inline pt-[2px]" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/briefcase.svg"></use>
                </svg>
                <span class="group-hover:text-white text-stone-500 text-sm font-normal"><?php echo esc_html($type); ?></span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <i class="fas fa-user  text-stone-500 text-sm font-normal"></i>
                <span class="group-hover:text-white text-stone-500 text-sm font-normal">
                    <?php echo class_exists('Greenergy_Post_Views') ? Greenergy_Post_Views::get_views($post_id) : '0'; ?>
                </span>
            </div>
            <div class="flex justify-start items-center gap-1">
                <svg class="w-3 h-3.5 inline pt-[2px]" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/clock.svg"></use>
                </svg>
                <span class="group-hover:text-white text-neutral-800 text-xs font-normal leading-5"><?php echo $date; ?></span>
            </div>
        </div>
        <?php if (!empty($cats)) : ?>
            <div class="h-8 px-4 bg-green-700/20 rounded-[100px] w-fit flex items-center">
                <span class="text-right group-hover:text-white text-neutral-950 text-sm font-medium leading-5"># <?php echo esc_html($cats[0]->name); ?></span>
            </div>
        <?php endif; ?>
        <?php
        $card_desc = get_post_meta($post_id, 'job_card_description', true);
        if (empty($card_desc)) {
            $card_desc = wp_trim_words(get_the_excerpt($post_id), 20);
        }
        ?>
        <p class="self-stretch text-right justify-start group-hover:text-white text-stone-500 text-xs font-normal line-clamp-2 leading-5">
            <?php echo esc_html($card_desc); ?>
        </p>
        <div class="self-stretch inline-flex justify-center items-center gap-4 mt-auto">
            <button class="flex-1 h-9 p-2.5 group hover:bg-primary-500 rounded-lg outline outline-1 outline-offset-[-1px] outline-stone-300 flex justify-center items-center gap-2.5">
                <span class="text-right justify-start group-hover:font-bold group-hover:text-white text-neutral-800 text-sm font-normal leading-5">عرض التفاصيل</span>
            </button>
        </div>
    </div>
</div>