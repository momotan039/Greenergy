<?php
if (! defined('ABSPATH')) {
    exit;
}

$block_attrs = (isset($block) && $block instanceof WP_Block)
    ? array_merge((array) ($block->attributes ?? []), (array) ($block->parsed_block['attrs'] ?? []))
    : [];
$attrs = wp_parse_args($attributes ?? [], wp_parse_args($block_attrs, [
    'source'         => 'manual',
    'selectedCompany' => [],
    'badgeText'      => 'شركة الأسبوع',
    'heading'        => 'شركة مميزة هذا الأسبوع',
    'subheading'     => 'نسلط الضوء على إحدى أبرز الشركات في مجال الطاقة المتجددة',
    'title'          => '',
    'imageId'        => 0,
    'imageUrl'       => '',
    'location'       => '',
    'categoryLabel'  => '',
    'description'    => '',
    'statYears'      => '',
    'statRating'     => '',
    'statProjects'   => '',
    'linkKnowMore'   => '',
    'linkContact'    => '',
]));

$source = (isset($attrs['source']) && (string) $attrs['source'] === 'db') ? 'db' : 'manual';
$company_id = 0;
if ($source === 'db') {
    $sel = isset($attrs['selectedCompany']) ? $attrs['selectedCompany'] : [];
    if (! is_array($sel)) {
        $sel = [];
    }
    $first = ! empty($sel) ? $sel[0] : null;
    if ($first !== null) {
        if (is_array($first)) {
            $company_id = absint($first['id'] ?? $first['ID'] ?? 0);
            if ($company_id === 0 && is_numeric(key($first))) {
                $company_id = absint(reset($first));
            }
        } elseif (is_object($first)) {
            $company_id = isset($first->id) ? absint($first->id) : (isset($first->ID) ? absint($first->ID) : 0);
        } else {
            $company_id = is_numeric($first) ? absint($first) : 0;
        }
    }
}

$title        = '';
$image_url    = 'https://placehold.co/270x270';
$location     = '';
$category     = '';
$description  = '';
$link_know    = '#';
$link_contact = '';
$stat_years   = '';
$stat_rating  = '';
$stat_projects = '';

if ($source === 'db' && $company_id) {
    $post = get_post($company_id);
    if ($post && $post->post_type === 'companies' && $post->post_status === 'publish') {
        $title       = get_the_title($company_id);
        $image_url   = get_the_post_thumbnail_url($company_id, 'medium_large') ?: 'https://placehold.co/270x270';
        // الوصف: أولاً وصف شركة الأسبوع المخصص، ثم وصف البطاقة، ثم الملخص/المحتوى
        $weekly_desc = get_post_meta($company_id, 'company_weekly_description', true);
        $card_desc   = get_post_meta($company_id, 'company_card_description', true);
        $description = ($weekly_desc !== '') ? $weekly_desc : (($card_desc !== '') ? $card_desc : (has_excerpt($company_id) ? get_the_excerpt($company_id) : wp_trim_words(get_the_content(null, false, $company_id), 30)));
        $link_know   = get_permalink($company_id);
        // حقول عرض شركة الأسبوع (سنة خبرة، تقييم عملاء، مشاريع مكتملة، رابط تواصل)
        $stat_years    = (string) get_post_meta($company_id, 'company_years_experience', true);
        $stat_rating   = (string) get_post_meta($company_id, 'company_customer_rating', true);
        $stat_projects = (string) get_post_meta($company_id, 'company_projects_completed', true);
        $link_contact  = (string) get_post_meta($company_id, 'company_contact_url', true);

        $locs = get_the_terms($company_id, 'company_location');
        if ($locs && ! is_wp_error($locs)) {
            $city = $country = null;
            foreach ($locs as $t) {
                if ((int) $t->parent !== 0) {
                    $city = $t->name;
                    $parent = get_term($t->parent, 'company_location');
                    if ($parent && ! is_wp_error($parent)) {
                        $country = $parent->name;
                    }
                } else {
                    if (! $country) {
                        $country = $t->name;
                    }
                }
            }
            $location = $city && $country ? $city . ' ، ' . $country : ($country ?: $city ?: '');
        }

        $cat_terms = get_the_terms($company_id, 'company_category');
        if ($cat_terms && ! is_wp_error($cat_terms) && ! empty($cat_terms)) {
            $t = $cat_terms[0];
            if ((int) $t->parent !== 0) {
                $parent_term = get_term((int) $t->parent, 'company_category');
                $category = ($parent_term && ! is_wp_error($parent_term)) ? $parent_term->name : $t->name;
            } else {
                $category = $t->name;
            }
        }
    }
} else {
    $title        = (string) $attrs['title'];
    $location     = (string) $attrs['location'];
    $category     = (string) $attrs['categoryLabel'];
    $description  = (string) $attrs['description'];
    $stat_years   = (string) $attrs['statYears'];
    $stat_rating  = (string) $attrs['statRating'];
    $stat_projects = (string) $attrs['statProjects'];
    $link_know    = esc_url_raw($attrs['linkKnowMore']) ?: '#';
    $link_contact = esc_url_raw($attrs['linkContact']) ?: '';

    if (! empty($attrs['imageUrl'])) {
        $image_url = esc_url_raw($attrs['imageUrl']);
    } elseif (! empty($attrs['imageId'])) {
        $image_url = wp_get_attachment_image_url((int) $attrs['imageId'], 'medium_large') ?: 'https://placehold.co/270x270';
    }
}

$has_content = $title !== '' || $description !== '';
?>
<section class="py-12 bg-gradient-to-b from-white to-green-50/50">
    <div class="mx-auto px-4 flex flex-col gap-10">

        <header class="text-center flex flex-col gap-4">
            <span class="mx-auto px-8 py-2 bg-green-200/50 align-middle text-green-700 text-2xl font-medium rounded-full">
                <?php echo esc_html($attrs['badgeText']); ?>
            </span>
            <h2 class="text-2xl font-bold text-neutral-900">
                <?php echo esc_html($attrs['heading']); ?>
            </h2>
            <p class="text-neutral-600">
                <?php echo esc_html($attrs['subheading']); ?>
            </p>
        </header>

        <?php if ($has_content) : ?>
            <article class="relative overflow-hidden rounded-3xl border border-green-200/80 
    bg-gradient-to-br from-white via-green-50/40 to-emerald-50/60 
    p-6 max-md:p-1 flex flex-col lg:flex-row gap-8 items-center 
    shadow-[0_8px_30px_rgba(34,197,94,0.12)] 
    ring-1 ring-inset ring-green-100/50
    transition-all duration-300 
    hover:-translate-y-1 
    hover:shadow-[0_20px_50px_-12px_rgba(34,197,94,0.28)] 
    hover:border-green-300/90
    hover:bg-gradient-to-br hover:from-white hover:via-green-50/60 hover:to-emerald-50/80"> <!-- زخرفة: دوائر شفافة -->
                <span class="pointer-events-none absolute -top-12 -right-12 h-40 w-40 rounded-full border border-green-300/20 bg-green-200/10" aria-hidden="true"></span>
                <span class="pointer-events-none absolute -bottom-8 -left-8 h-32 w-32 rounded-full border border-green-300/15 bg-green-100/20" aria-hidden="true"></span>
                <span class="pointer-events-none absolute top-1/2 -left-16 h-24 w-24 rounded-full bg-green-200/15" aria-hidden="true"></span>
                <span class="pointer-events-none absolute bottom-8 right-12 h-16 w-16 rounded-full border border-green-200/25" aria-hidden="true"></span>

                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>"
                    class="relative z-10 w-64 h-64 max-md:w-[123px] max-md:h-[123px] object-cover rounded-2xl shadow-sm" />

                <div class="relative z-10 flex-1 flex flex-col gap-5 text-right">
                    <h3 class="text-2xl max-md:text-base font-bold text-neutral-900">
                        <?php echo esc_html($title); ?>
                    </h3>

                    <div class="flex flex-wrap items-center gap-6 max-md:gap-2 text-sm">
                        <?php if ($location) : ?>
                            <span class="text-stone-500">
                                <i class="fas fa-location-dot text-stone-500"></i>
                                <?php echo esc_html($location); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($category) : ?>
                            <span class="px-4 py-1 bg-green-100 text-neutral-800 rounded-full">
                                <?php echo esc_html($category); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($description) : ?>
                        <p class="text-stone-600 leading-6 max-md:text-xs">
                            <?php echo esc_html($description); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($stat_years !== '' || $stat_rating !== '' || $stat_projects !== '') : ?>
                        <div class="flex flex-wrap gap-4">
                            <?php if ($stat_years !== '') : ?>
                                <div class="w-28 h-20 bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-2xl flex flex-col items-center justify-center">
                                    <span class="text-green-700 text-2xl font-bold"><?php echo esc_html($stat_years); ?></span>
                                    <span class="text-stone-500 text-sm"><?php esc_html_e('سنة خبرة', 'greenergy'); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($stat_rating !== '') : ?>
                                <div class="w-28 h-20 bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-2xl flex flex-col items-center justify-center">
                                    <span class="text-green-700 text-2xl font-bold"><?php echo esc_html($stat_rating); ?></span>
                                    <span class="text-stone-500 text-sm"><?php esc_html_e('تقييم العملاء', 'greenergy'); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($stat_projects !== '') : ?>
                                <div class="w-28 h-20 bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-2xl flex flex-col items-center justify-center">
                                    <span class="text-green-700 text-2xl font-bold"><?php echo esc_html($stat_projects); ?></span>
                                    <span class="text-stone-500 text-sm"><?php esc_html_e('مشروع مكتمل', 'greenergy'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex sm:flex-row gap-4 mt-2 max-md:p-4">
                        <a href="<?php echo esc_url($link_know); ?>" class="flex-1 h-10 bg-green-700 text-white rounded-lg text-sm inline-flex items-center justify-center gap-2 hover:opacity-90">
                            <?php esc_html_e('اعرف المزيد', 'greenergy'); ?>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <?php if ($link_contact) : ?>
                            <a href="<?php echo esc_url($link_contact); ?>" class="sm:w-56 h-10 border border-neutral-300 rounded-lg text-neutral-800 text-sm inline-flex items-center justify-center hover:bg-neutral-50">
                                <?php esc_html_e('تواصل معنا', 'greenergy'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php else : ?>
            <p class="text-center text-neutral-500 py-8">
                <?php esc_html_e('اختر شركة من القاعدة أو أضف محتوى يدوياً من إعدادات الكتلة.', 'greenergy'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>