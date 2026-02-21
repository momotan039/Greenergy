<?php

/**
 * Script to create 20 dummy jobs for Greenergy theme with Job Sections
 * 
 * Usage: Place this file in your theme root and access it via browser:
 * https://your-site.com/wp-content/themes/greenergy_theme/create-dummy-jobs.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('Access denied.');
}

// Function to generate the block content for job sections
function generate_job_content($title, $desc)
{
    ob_start();
?>
    <!-- wp:greenergy/job-section {"sectionType":"paragraph"} -->
    <div class="job-section-wrapper">
        <!-- wp:core/heading {"level":3} -->
        <h3>حول الوظيفة</h3>
        <!-- /wp:core/heading -->
        <!-- wp:core/paragraph -->
        <p><?php echo $desc; ?></p>
        <!-- /wp:core/paragraph -->
    </div>
    <!-- /wp:greenergy/job-section -->

    <!-- wp:greenergy/job-section {"sectionType":"list","listStyle":"bullets"} -->
    <div class="job-section-wrapper">
        <!-- wp:core/heading {"level":3} -->
        <h3>المسؤوليات الرئيسية</h3>
        <!-- /wp:core/heading -->
        <!-- wp:core/list -->
        <ul>
            <li>الإشراف المباشر على تنفيذ المشاريع الميدانية.</li>
            <li>ضمان الالتزام بمعايير السلامة والجودة العالمية.</li>
            <li>إعداد التقارير الدورية للإدارة العليا حول سير العمل.</li>
            <li>التنسيق مع الموردين والشركاء التقنيين.</li>
        </ul>
        <!-- /wp:core/list -->
    </div>
    <!-- /wp:greenergy/job-section -->

    <!-- wp:greenergy/job-section {"sectionType":"list","listStyle":"icons","iconType":"check"} -->
    <div class="job-section-wrapper">
        <!-- wp:core/heading {"level":3} -->
        <h3>المؤهلات المطلوبة</h3>
        <!-- /wp:core/heading -->
        <!-- wp:core/list -->
        <ul>
            <li>شهادة جامعية في التخصص ذو الصلة.</li>
            <li>خبرة لا تقل عن 3 سنوات في مجال الطاقة المتجددة.</li>
            <li>إجادة اللغة الإنجليزية (تحدثاً وكتابة).</li>
            <li>مهارات تواصل وقيادة فريق متميزة.</li>
        </ul>
        <!-- /wp:core/list -->
    </div>
    <!-- /wp:greenergy/job-section -->
<?php
    return ob_get_clean();
}

$jobs_data = [
    // طاقة شمسية (7 jobs)
    ['title' => 'مدير مشروع طاقة شمسية', 'company' => 'المستقبل الأخضر', 'location' => 'الرياض، السعودية', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'طاقة شمسية', 'tags' => ['طاقة_بديلة', 'السعودية', 'إدارة'], 'desc' => 'قيادة مشاريع الطاقة الشمسية الكبرى في منطقة نجد.'],
    ['title' => 'مهندس تركيبات ألواح شمسية', 'company' => 'المستقبل الأخضر', 'location' => 'جدة، السعودية', 'type' => 'contract', 'is_gold' => true, 'cat' => 'طاقة شمسية', 'tags' => ['تركيبات', 'شمس', 'صيانة'], 'desc' => 'المسؤول التقني عن تركيب وصيانة الأسطح الشمسية للمنازل.'],
    ['title' => 'مستشار فني طاقة شمسية', 'company' => 'المستقبل الأخضر', 'location' => 'الدمام، السعودية', 'type' => 'remote', 'is_gold' => false, 'cat' => 'طاقة شمسية', 'tags' => ['استشارات', 'عن_بعد', 'فني'], 'desc' => 'تقديم دراسات جدوى تقنية لمشاريع الطاقة الكهروضوئية.'],
    ['title' => 'فني صيانة مزارع شمسية', 'company' => 'شمسنا للطاقة', 'location' => 'سكاكا، السعودية', 'type' => 'full-time', 'is_gold' => false, 'cat' => 'طاقة شمسية', 'tags' => ['صيانة', 'ميداني', 'طاقة_نظيفة'], 'desc' => 'تنفيذ جداول الصيانة الدورية لمحطات الطاقة الشمسية المركزية.'],
    ['title' => 'مصمم أنظمة كهروضوئية', 'company' => 'شمسنا للطاقة', 'location' => 'الرياض، السعودية', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'طاقة شمسية', 'tags' => ['تصميم', 'هندسة', 'PV'], 'desc' => 'استخدام برمجيات متخصصة لتصميم أنظمة الطاقة الكهروضوئية للمصانع.'],
    ['title' => 'مندوب مبيعات حلول شمسية', 'company' => 'أفق الطاقة', 'location' => 'المدينة المنورة، السعودية', 'type' => 'full-time', 'is_gold' => false, 'cat' => 'طاقة شمسية', 'tags' => ['مبيعات', 'تسويق', 'عملاء'], 'desc' => 'توسيع قاعدة العملاء لمنتجات السخانات والألواح الشمسية.'],
    ['title' => 'مشرف موقع محطة شمسية', 'company' => 'أفق الطاقة', 'location' => 'تبوك، السعودية', 'type' => 'contract', 'is_gold' => true, 'cat' => 'طاقة شمسية', 'tags' => ['إشراف', 'موقع', 'بناء'], 'desc' => 'إدارة العمالة والمقاولين في موقع بناء المحطة الشمسية.'],

    // استدامة (7 jobs)
    ['title' => 'خبير استدامة معتمد', 'company' => 'نيوم للاستدامة', 'location' => 'نيوم، السعودية', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'استدامة', 'tags' => ['نيوم', 'استدامة', 'خبير'], 'desc' => 'تطوير استراتيجيات صفر انبعاثات لمشاريع المدينة المستقبلية.'],
    ['title' => 'أخصائي تدوير نفايات', 'company' => 'نيوم للاستدامة', 'location' => 'نيوم، السعودية', 'type' => 'full-time', 'is_gold' => false, 'cat' => 'استدامة', 'tags' => ['تدوير', 'بيئة', 'تنمية'], 'desc' => 'إدارة مشاريع تحويل النفايات إلى طاقة في المنشآت الحضرية.'],
    ['title' => 'مدقق معايير البيئة (ESG)', 'company' => 'نيوم للاستدامة', 'location' => 'تبوك، السعودية', 'type' => 'part-time', 'is_gold' => false, 'cat' => 'استدامة', 'tags' => ['تدقيق', 'قانون_بيئي', 'ESG'], 'desc' => 'مراجعة أداء الشركات لضمان التوافق مع المعايير البيئية العالمية.'],
    ['title' => 'باحث علوم البيئة', 'company' => 'بيئة بلس', 'location' => 'الشارقة، الإمارات', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'استدامة', 'tags' => ['بحث', 'علوم', 'تطوير'], 'desc' => 'إجراء دراسات حول تأثير التغير المناخي على التنوع البيولوجي المحلي.'],
    ['title' => 'منسق مبادرات خضراء', 'company' => 'بيئة بلس', 'location' => 'دبي، الإمارات', 'type' => 'contract', 'is_gold' => false, 'cat' => 'استدامة', 'tags' => ['تنسيق', 'مبادرات', 'مجتمع'], 'desc' => 'تنظيم فعاليات توعوية لرفع الحس البيئي لدى طلاب المدارس.'],
    ['title' => 'مدير مرافق مستدامة', 'company' => 'إدارة بلس', 'location' => 'أبوظبي، الإمارات', 'type' => 'full-time', 'is_gold' => false, 'cat' => 'استدامة', 'tags' => ['إدارة', 'مرافق', 'كفاءة'], 'desc' => 'تحسين استهلاك الطاقة والمياه في المباني الإدارية الكبرى.'],
    ['title' => 'محلل بصمة كربونية', 'company' => 'إدارة بلس', 'location' => 'المنامة، البحرين', 'type' => 'remote', 'is_gold' => true, 'cat' => 'استدامة', 'tags' => ['كربون', 'تحليل', 'تقارير'], 'desc' => 'حساب الانبعاثات الكربونية لعمليات الشحن والخدمات اللوجستية.'],

    // تكنولوجيا خضراء (6 jobs)
    ['title' => 'مطور تطبيقات تتبع كربون', 'company' => 'آي تي الخضراء', 'location' => 'دبي، الإمارات', 'type' => 'remote', 'is_gold' => true, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['برمجة', 'كربون', 'تطبيقات'], 'desc' => 'بناء تطبيقات موبايل ذكية لحساب البصمة الكربونية للأفراد.'],
    ['title' => 'مهندس أتمتة شبكات ذكية', 'company' => 'آي تي الخضراء', 'location' => 'عمان، الأردن', 'type' => 'full-time', 'is_gold' => false, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['أتمتة', 'كهرباء', 'تكنولوجيا'], 'desc' => 'تحسين كفاءة توزيع الطاقة باستخدام خوارزميات الذكاء الاصطناعي.'],
    ['title' => 'محلل برمجيات الطاقة النظيفة', 'company' => 'آي تي الخضراء', 'location' => 'القاهرة، مصر', 'type' => 'remote', 'is_gold' => false, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['تحليل', 'برمجيات', 'نظيف'], 'desc' => 'تقييم كفاءة البرمجيات المستخدمة في إدارة محطات الرياح.'],
    ['title' => 'مهندس تعلم آلي للبيئة', 'company' => 'سمارت إنرجي', 'location' => 'الدوحة، قطر', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['AI', 'بيانات', 'ذكاء_اصطناعي'], 'desc' => 'تطوير نماذج تنبؤية لإنتاج الطاقة المتجددة بناءً على أحوال الطقس.'],
    ['title' => 'تقني أنظمة ري ذكية', 'company' => 'سمارت إنرجي', 'location' => 'مسقط، عمان', 'type' => 'contract', 'is_gold' => false, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['زراعة', 'ري', 'تقنية'], 'desc' => 'تثبيت وبرمجة أنظمة الري التي تعمل بالطاقة الشمسية وتوفر المياه.'],
    ['title' => 'مدير تقني (CTO) لشركة ناشئة', 'company' => 'سمارت إنرجي', 'location' => 'الرياض، السعودية', 'type' => 'full-time', 'is_gold' => true, 'cat' => 'تكنولوجيا خضراء', 'tags' => ['قيادة', 'تكنولوجيا', 'تطوير'], 'desc' => 'قيادة الفريق التقني لتطوير حلول برمجية لتدوير النفايات الصلبة.'],
];

echo "<h2>جاري إنشاء 20 وظيفة تجريبية غنية بالبيانات...</h2>";

foreach ($jobs_data as $job) {
    $post_id = wp_insert_post([
        'post_title'   => $job['title'],
        'post_content' => generate_job_content($job['title'], $job['desc']),
        'post_status'  => 'publish',
        'post_type'    => 'jobs',
        'post_excerpt' => $job['desc'],
    ]);

    if ($post_id) {
        // Set Category (Taxonomy)
        if (!empty($job['cat'])) {
            wp_set_object_terms($post_id, $job['cat'], 'category');
        }

        // Set Tags (Taxonomy)
        if (!empty($job['tags'])) {
            wp_set_object_terms($post_id, $job['tags'], 'post_tag');
        }

        // Set ACF Fields (if ACF is active)
        if (function_exists('update_field')) {
            update_field('company_name', $job['company'], $post_id);
            update_field('location', $job['location'], $post_id);
            update_field('job_type_acf', $job['type'], $post_id);
            update_field('job_card_description', $job['desc'], $post_id);
            update_field('is_gold_acf', $job['is_gold'], $post_id);
            update_field('_news_view_count', rand(1500, 10000), $post_id);
        }

        // Set Redundant Meta Keys (used as fallbacks in theme)
        update_post_meta($post_id, '_job_company', $job['company']);
        update_post_meta($post_id, '_job_location', $job['location']);
        update_post_meta($post_id, '_job_type', $job['type']);
        update_post_meta($post_id, '_is_gold', $job['is_gold'] ? 'yes' : 'no');
        update_post_meta($post_id, 'job_card_description', $job['desc']);
        update_post_meta($post_id, '_total_views_sort', rand(1500, 10000));

        echo "تم إنشاء: <strong>{$job['title']}</strong> (" . ($job['is_gold'] ? "<span style='color:gold'>ذهبية</span>" : "عادية") . ")<br>";
    }
}

echo "<h3>اكتملت العملية بنجاح! تم إنشاء وتصنيف 20 وظيفة بكافة حقولها.</h3>";
echo "<p><a href='" . admin_url('edit.php?post_type=jobs') . "'>انتقل إلى لوحة التحكم لمشاهدة الوظائف</a></p>";
