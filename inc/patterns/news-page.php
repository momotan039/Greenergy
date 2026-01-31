<?php
/**
 * News Page Pattern
 *
 * @package Greenergy
 */

return [
    'title'      => __( 'الاخبار', 'greenergy' ),
    'categories' => [ 'greenergy' ],
    'content'    => '
        <div class="container mx-auto bg-white p-4">
        <!-- wp:greenergy/scroll-progress /-->
        <div class="w-full inline-flex flex-col justify-start gap-4">
        <!-- wp:greenergy/breadcrumb /-->
        <!-- wp:greenergy/main-banner /-->
        </div>
        </div>

        <div class="container mx-auto mt-8 p-4">
                <!-- wp:greenergy/news-filter /-->
        </div>

        <div class="container mx-auto mt-8 p-4">
        <div class="w-full self-stretch flex lg:flex-row flex-col-reverse justify-start items-start gap-6">
            <!-- Main Content Area -->
            <div class="flex-1 p-4 max-sm:p-0 bg-white rounded-2xl flex flex-col justify-start items-stretch gap-6 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up" data-aos-duration="800">
                <!-- wp:greenergy/featured-news /-->
                
                <div class="self-stretch flex flex-col gap-2">
                <!-- wp:greenergy/news-list /-->
                </div>
                <!-- wp:greenergy/ad-block  /-->
                <!-- wp:greenergy/news-grid /-->
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/4 w-full flex flex-col justify-start items-stretch gap-4 lg:sticky lg:top-4" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200" max-md:hidden>
                <!-- wp:greenergy/directory-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px"} /-->
                <!-- wp:greenergy/courses-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px"} /-->
                <!-- wp:greenergy/featured-jobs-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px"} /-->
                <!-- wp:greenergy/follow-us-widget /-->
            </div>
            
        </div>

                <!-- wp:greenergy/ad-block /-->
        </div>
    ',
];
