<?php
/**
 * Homepage Block Pattern
 *
 * @package Greenergy
 */

return array(
    'title'       => __( 'Greenergy Homepage', 'greenergy' ),
    'categories'  => array( 'pages' ),
    'description' => _x( 'Full homepage layout matching Figma design.', 'Block pattern description', 'greenergy' ),
    'content'     => '
        <!-- wp:greenergy/hero {"badgeText":"مستقبل الطاقة الكهروضوئية","headlineHighlight":"اكتشف","headlineMain":"عالم الطاقة المتجددة من دليل اللاعبين في السوق","description":"توفر لك منصتنا أحدث المعلومات والتحليلات في سوق الطاقة المتجددة، لتكون دائماً في الطليعة.","ctaText":"سجّل الآن مجاناً","imageUrl":"https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=2072&auto=format&fit=crop"} /-->

        <!-- wp:greenergy/stories-carousel {"stories":[
            {"title":"طاقة الرياح","image":"https://images.unsplash.com/photo-1532601224476-15c79f2f7a51?w=400&auto=format&fit=crop","ringColor":"green-500"},
            {"title":"الطاقة الشمسية","image":"https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=400&auto=format&fit=crop","ringColor":"zinc-300"},
            {"title":"الهيدروجين","image":"https://images.unsplash.com/photo-1629814681602-0ec5a1fb5f56?w=400&auto=format&fit=crop","ringColor":"green-500"},
            {"title":"السيارات الكهربائية","image":"https://images.unsplash.com/photo-1593941707882-a5bba14938c7?w=400&auto=format&fit=crop","ringColor":"zinc-300"},
            {"title":"الاستدامة","image":"https://images.unsplash.com/photo-1542601906990-b4d3fb77c356?w=400&auto=format&fit=crop","ringColor":"green-500"},
            {"title":"المناخ","image":"https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=400&auto=format&fit=crop","ringColor":"zinc-300"}
        ]} /-->

        <!-- wp:greenergy/news-grid {"sectionTitle":"الأكثر قراءة","sectionDesc":"الموضوعات التي نالت اهتمام مجتمع الطاقة المتجددة هذا الأسبوع.","orderBy":"date","order":"DESC"} /-->

        <!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignfull">
            <!-- wp:image {"align":"center","sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image aligncenter size-full"><img src="https://placehold.co/1200x150/1F2937/FFFFFF?text=Advertisement+Space" alt="Advertisement"/></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:group -->

        <!-- wp:greenergy/quick-stats {"stats":[
            {"number":"+20","label":"سنوات خبرة","icon":"clock","highlight":false},
            {"number":"+100","label":"مشروع ناجح","icon":"check-circle","highlight":false},
            {"number":"+5000","label":"طواحين هواء","icon":"wind","highlight":true},
            {"number":"+200","label":"محطة طاقة","icon":"zap","highlight":false},
            {"number":"+15k","label":"عميل سعيد","icon":"users","highlight":false}
        ]} /-->

        <!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignfull">
            <!-- wp:image {"align":"center","sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image aligncenter size-full"><img src="https://placehold.co/1200x150/1F2937/FFFFFF?text=Advertisement+Space" alt="Advertisement"/></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:group -->

        <!-- wp:greenergy/news-grid {"sectionTitle":"آخر الأخبار","sectionDesc":"ابقَ على اطلاع دائم بآخر التطورات والتقارير الحصرية.","postsToShow":3} /-->

        <!-- wp:greenergy/news-grid {"sectionTitle":"الدورات التدريبية","sectionDesc":"طور مهاراتك مع نخبة من الخبراء في مجال الطاقة.","postType":"courses"} /-->

        <!-- wp:greenergy/jobs-list /-->

        <!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignfull">
             <!-- wp:image {"align":"center","sizeSlug":"full","linkDestination":"none"} -->
            <figure class="wp-block-image aligncenter size-full"><img src="https://placehold.co/1200x150/1F2937/FFFFFF?text=Advertisement+Space" alt="Advertisement"/></figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:group -->

        <!-- wp:greenergy/newsletter-cta /-->
    ',
);
