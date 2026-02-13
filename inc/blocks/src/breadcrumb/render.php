<?php

/**
 * Breadcrumb Block Template
 *
 * @package Greenergy
 */

?>
<nav aria-label="مسار التنقل">
    <ol class="inline-flex items-center gap-3" itemscope itemtype="https://schema.org/BreadcrumbList">

        <!-- الرئيسية -->
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>"
                class="text-stone-500 text-base hover:text-green-700">
                <span itemprop="name"><?php esc_html_e('الرئيسية', 'greenergy'); ?></span>
            </a>
            <meta itemprop="position" content="1" />
        </li>

        <?php
        $position = 2;

        // Single News
        if (is_singular('news')) : ?>

            <li class="text-stone-500">></li>

            <!-- صفحة الأخبار (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الاخبار')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الاخبار', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

            <li class="text-stone-500">></li>

            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="#"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name">الخبر</span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

            <li class="text-stone-500">></li>

            <!-- عنوان الخبر -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php

        // Archive News
        elseif (is_post_type_archive('news')) : ?>

            <li class="text-stone-500">></li>
            <li class="text-neutral-950 text-base font-medium">
                <?php esc_html_e('الأخبار', 'greenergy'); ?>
            </li>

        <?php

        // Default (pages / others)
        elseif (is_singular()) : ?>

            <li class="text-stone-500">></li>
            <li class="text-neutral-950 text-base font-medium">
                <?php echo esc_html(get_the_title()); ?>
            </li>

        <?php endif; ?>

    </ol>
</nav>