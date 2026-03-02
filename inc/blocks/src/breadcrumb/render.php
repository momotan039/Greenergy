<?php

/**
 * Breadcrumb Block Template
 *
 * @package Greenergy
 */
$current_template = get_page_template_slug(get_queried_object_id());
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

            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة الأخبار (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الاخبار')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الاخبار', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- عنوان الخبر -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php
        // Single News
        elseif (is_singular('jobs')) : ?>

            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة الوظائف (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الوظائف')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الوظائف', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- عنوان الخبر -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php
        elseif (is_singular('courses')) : ?>

            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة الدورات (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/التدريبات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('التدريبات', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- عنوان الدورة -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php
        elseif (is_singular('post')) : ?>

            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة المقالات (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/المقالات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('المقالات', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- عنوان المقالة -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php
        elseif (is_singular('companies')) : ?>
            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- صفحة الدليل -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الشركات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الدليل', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>
            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة الشركات (الأرشيف) -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الشركات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الشركات', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- عنوان الشركة -->
            <li class="line-clamp-1" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name" class="text-neutral-950 text-base font-medium">
                    <?php echo esc_html(get_the_title()); ?>
                </span>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

        <?php
        elseif ($current_template === 'archive-companies') : ?>
            <i class="fa fa-angle-left text-stone-500"></i>

            <!-- صفحة الدليل -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الشركات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الدليل', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>
            <i class="fa fa-angle-left text-stone-500"></i>
            <!-- صفحة الشركات -->
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a itemprop="item" href="<?php echo esc_url(home_url('/الشركات')); ?>"
                    class="text-stone-500 text-base hover:text-green-700">
                    <span itemprop="name"><?php esc_html_e('الشركات', 'greenergy'); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>


        <?php

        // Default (pages / others)
        elseif (is_singular()) : ?>

            <i class="fa fa-angle-left text-stone-500"></i>
            <li class="text-neutral-950 text-base font-medium">
                <?php echo esc_html(get_the_title()); ?>
            </li>

        <?php endif; ?>

    </ol>
</nav>