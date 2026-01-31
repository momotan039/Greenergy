<?php
/**
 * Front Page Pattern
 *
 * @package Greenergy
 */

return [
    'title'      => __( 'الصفحة الرئيسية', 'greenergy' ),
    'categories' => [ 'greenergy' ],
    'content'    => '
        <!-- wp:greenergy/scroll-progress /-->
        <!-- wp:greenergy/hero-block /-->
        <!-- wp:greenergy/stories /-->
        <!-- wp:greenergy/most-read-news /-->
        
        <!-- wp:greenergy/ad-block {"adType":"image","imageUrl":"' . GREENERGY_ASSETS_URI . '/images/ad-spolar.jpg","adLink":"#"} /-->

        <!-- wp:greenergy/stats /-->
        
        <!-- wp:greenergy/ad-block {"adType":"image","imageUrl":"' . GREENERGY_ASSETS_URI . '/images/ad-spolar.jpg","adLink":"#"} /-->

        <!-- wp:greenergy/latest-news /-->
        
        <!-- wp:greenergy/ad-block {"adType":"image","imageUrl":"' . GREENERGY_ASSETS_URI . '/images/ad-spolar.jpg","adLink":"#"} /-->

        <!-- wp:greenergy/courses /-->
        <!-- wp:greenergy/jobs /-->
        
        <!-- wp:greenergy/ad-block {"adType":"image","imageUrl":"' . GREENERGY_ASSETS_URI . '/images/ad-spolar.jpg","adLink":"#"} /-->',
];
