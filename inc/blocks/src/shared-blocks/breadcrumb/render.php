<?php

/**
 * Breadcrumb Block Template
 * @package Greenergy
 */

$current_template = get_page_template_slug(get_queried_object_id());

// Map: condition => [ [label, url], ... ] — last item without url = current page (no link)
$crumbs = [];

$singular_map = [
    'news'          => [['الاخبار',    '/الاخبار']],
    'courses'       => [['التدريبات',  '/التدريبات']],
    'post'          => [['المقالات',   '/المقالات']],
    'jobs'          => [['الدليل',    '/الوظائف'], ['الوظائف',    '/الوظائف']],
    'companies'     => [['الدليل',     '/الشركات'],   ['الشركات',   '/الشركات']],
    'organizations' => [['الدليل',     '/المنظمات'],  ['المنظمات',  '/المنظمات']],
    'experts'       => [['الدليل',     '/الخبراء'],  ['الخبراء',  '/الخبراء']],
    'projects'      => [['الدليل',     '/المشاريع'],  ['المشاريع',  '/المشاريع']],
];

$archive_map = [
    'archive-companies'     => [['الدليل', '/الشركات'],   ['الشركات',  '/الشركات']],
    'archive-organizations' => [['الدليل', '/المنظمات'],  ['المنظمات', '/المنظمات']],
    'archive-jobs'          => [['الدليل', '/الوظائف'],   ['الوظائف',  '/الوظائف']],
    'archive-experts'       => [['الدليل', '/الخبراء'],   ['الخبراء',  '/الخبراء']],
    'archive-projects'      => [['الدليل', '/المشاريع'],   ['المشاريع',  '/المشاريع']],
];

foreach ($singular_map as $post_type => $parents) {
    if (is_singular($post_type)) {
        $crumbs = $parents;
        $crumbs[] = [get_the_title(), null]; // current — no link
        break;
    }
}

if (empty($crumbs)) {
    if (isset($archive_map[$current_template])) {
        $crumbs = $archive_map[$current_template];
    } elseif (is_singular()) {
        $crumbs = [[get_the_title(), null]];
    }
}
?>

<nav aria-label="مسار التنقل">
    <ol class="inline-flex items-center gap-3" itemscope itemtype="https://schema.org/BreadcrumbList">

        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>" class="text-stone-500 text-base hover:text-green-700">
                <span itemprop="name"><?php esc_html_e('الرئيسية', 'greenergy'); ?></span>
            </a>
            <meta itemprop="position" content="1" />
        </li>

        <?php $position = 2;
        foreach ($crumbs as [$label, $url]) : ?>
            <i class="fa fa-angle-left text-stone-500"></i>
            <li class="<?php echo $url ? '' : 'line-clamp-1'; ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($url) : ?>
                    <a itemprop="item" href="<?php echo esc_url(home_url($url)); ?>" class="text-stone-500 text-base hover:text-green-700">
                        <span itemprop="name"><?php echo esc_html__($label, 'greenergy'); ?></span>
                    </a>
                <?php else : ?>
                    <span itemprop="name" class="text-neutral-950 text-base font-medium"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>
        <?php endforeach; ?>

    </ol>
</nav>