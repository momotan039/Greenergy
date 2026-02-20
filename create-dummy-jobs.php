<?php

/**
 * Script to create 10 dummy jobs for Greenergy theme
 * 
 * Usage: Place this file in your theme root and access it via browser:
 * https://your-site.com/wp-content/themes/greenergy_theme/create-dummy-jobs.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('Access denied.');
}

$jobs_data = [
    [
        'title' => 'مهندس طاقة شمسية أول',
        'company' => 'شركة قرين إنرجي الدولية',
        'location' => 'الرياض، السعودية',
        'type' => 'full-time',
        'is_gold' => true,
        'cat' => 'هندسة',
        'desc' => 'نبحث عن مهندس خبير في تصميم وإدارة محطات الطاقة الشمسية الكبيرة.'
    ],
    [
        'title' => 'أخصائي استدامة بيئية',
        'company' => 'منظمة آفاق خضراء',
        'location' => 'دبي، الإمارات',
        'type' => 'remote',
        'is_gold' => false,
        'cat' => 'استشارة بيئية',
        'desc' => 'تقديم حلول واستشارات لتقليل الانبعاثات الكربونية في المنشآت الصناعية.'
    ],
    [
        'title' => 'فني توربينات رياح',
        'company' => 'رياح المستقبل للطاقة',
        'location' => 'خليج السويس، مصر',
        'type' => 'contract',
        'is_gold' => true,
        'cat' => 'فني',
        'desc' => 'صيانة وتشغيل توربينات الرياح في أكبر مزرعة رياح بالمنطقة.'
    ],
    [
        'title' => 'مدير تسويق رقمي (طاقة متجددة)',
        'company' => 'وكالة الطاقة المتجددة',
        'location' => 'عمان، الأردن',
        'type' => 'full-time',
        'is_gold' => false,
        'cat' => 'تسويق',
        'desc' => 'إدارة الحملات التسويقية لرفع الوعي بحلول الطاقة البديلة.'
    ],
    [
        'title' => 'محلل بيانات طاقة',
        'company' => 'مركز أبحاث الطاقة المتجددة',
        'location' => 'الكويت العاصمة، الكويت',
        'type' => 'part-time',
        'is_gold' => false,
        'cat' => 'تكنولوجيا المعلومات',
        'desc' => 'تحليل بيانات الاستهلاك والإنتاج لتحسين كفاءة الشبكات الذكية.'
    ],
    [
        'title' => 'خبير هيدروجين أخضر',
        'company' => 'نيوم للطاقة الهيدروجينية',
        'location' => 'نيوم، السعودية',
        'type' => 'full-time',
        'is_gold' => true,
        'cat' => 'تطوير الأعمال',
        'desc' => 'قيادة مشاريع إنتاج الهيدروجين الأخضر وتصديره للأسواق العالمية.'
    ],
    [
        'title' => 'مصمم جرافيك (هوية بصرية بيئية)',
        'company' => 'ستوديو الأرض',
        'location' => 'الدار البيضاء، المغرب',
        'type' => 'remote',
        'is_gold' => false,
        'cat' => 'تصميم',
        'desc' => 'تصميم مواد بصرية تدعم قضايا المناخ والبيئة.'
    ],
    [
        'title' => 'مدير مشروع طاقة كهروضوئية',
        'company' => 'شمس للمقاولات',
        'location' => 'المنامة، البحرين',
        'type' => 'contract',
        'is_gold' => true,
        'cat' => 'إدارة مشاريع',
        'desc' => 'الإشراف على تنفيذ أنظمة الطاقة الشمسية فوق أسطح المباني التجارية.'
    ],
    [
        'title' => 'محامي تخصص قوانين الطاقة',
        'company' => 'مكتب العدالة الخضراء',
        'location' => 'مسقط، عمان',
        'type' => 'full-time',
        'is_gold' => false,
        'cat' => 'قانون',
        'desc' => 'التعامل مع التشريعات والقوانين المتعلقة باستثمارات الطاقة المتجددة.'
    ],
    [
        'title' => 'أخصائي موارد بشرية',
        'company' => 'العالم الأخضر للتوظيف',
        'location' => 'الدوحة، قطر',
        'type' => 'full-time',
        'is_gold' => false,
        'cat' => 'موارد بشرية',
        'desc' => 'استقطاب الكفاءات المتخصصة في مجالات الاستدامة والطاقة.'
    ]
];

echo "<h2>جاري إنشاء 10 وظائف تجريبية...</h2>";

foreach ($jobs_data as $job) {
    $post_id = wp_insert_post([
        'post_title'   => $job['title'],
        'post_content' => 'هذا محتوى تجريبي للوظيفة. ' . $job['desc'],
        'post_status'  => 'publish',
        'post_type'    => 'jobs',
        'post_excerpt' => $job['desc'],
    ]);

    if ($post_id) {
        // Set Category
        if (!empty($job['cat'])) {
            wp_set_object_terms($post_id, $job['cat'], 'category');
        }

        // Set ACF Fields (if ACF is active)
        if (function_exists('update_field')) {
            update_field('company_name', $job['company'], $post_id);
            update_field('location', $job['location'], $post_id);
            update_field('job_type_acf', $job['type'], $post_id);
            update_field('job_card_description', $job['desc'], $post_id);
            update_field('is_gold_acf', $job['is_gold'], $post_id);
            update_field('_news_view_count', rand(100, 5000), $post_id);
        }

        // Set Redundant Meta Keys (used as fallbacks in theme)
        update_post_meta($post_id, '_job_company', $job['company']);
        update_post_meta($post_id, '_job_location', $job['location']);
        update_post_meta($post_id, '_job_type', $job['type']);
        update_post_meta($post_id, '_is_gold', $job['is_gold'] ? 'yes' : 'no');
        update_post_meta($post_id, 'job_card_description', $job['desc']);
        update_post_meta($post_id, '_total_views_sort', rand(100, 5000));

        echo "تم إنشاء: <strong>{$job['title']}</strong> (ID: $post_id)<br>";
    }
}

echo "<h3>تم الانتهاء من إنشاء جميع الوظائف!</h3>";
echo "<p><a href='" . admin_url('edit.php?post_type=jobs') . "'>انتقل إلى لوحة التحكم لمشاهدة الوظائف</a></p>";
