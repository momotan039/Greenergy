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
        <div class="container mx-auto bg-white mb-8">
        <!-- wp:greenergy/scroll-progress /-->
        <div class="w-full inline-flex flex-col justify-start gap-4">
        <!-- wp:greenergy/breadcrumb /-->
        <!-- wp:greenergy/main-banner /-->
        </div>
        </div>
        <!-- wp:greenergy/stories/-->
        <div class="container mx-auto mt-8">
        <!-- wp:greenergy/news-filter /-->
        </div>

        <div class="container mx-auto mt-8">
        <div class="w-full self-stretch lg:flex lg:flex-row  justify-start items-start gap-6">
            <!-- Main Content Area -->
            <div class="w-full flex-1 p-4 max-sm:p-0 bg-white rounded-2xl flex flex-col justify-start items-stretch gap-6 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300" data-aos="fade-up" data-aos-duration="800">
                <!-- wp:greenergy/featured-news /-->
                
                <div class="self-stretch flex flex-col gap-2">
                <!-- wp:greenergy/news-list /-->
                </div>
                <hr class="w-full border-gray-200 py-3">
                <div class="flex md:hidden flex-row flex-nowrap overflow-x-auto gap-4 px-4 -mx-4 pb-4 items-stretch scrollbar-hide">
                    <!-- wp:greenergy/directory-widget /-->
                    <!-- wp:greenergy/courses-widget /-->
                    <!-- wp:greenergy/featured-jobs-widget /-->
                    <!-- wp:greenergy/ad-block {"height":"100%","width":"20rem","hasContainer":false} /-->
                <!-- wp:greenergy/follow-us-widget /-->
                </div>

                <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                <!-- wp:greenergy/news-grid /-->
            </div>

            <!-- Sidebar -->
            <div class="max-lg:hidden lg:w-1/4 w-full flex flex-col justify-start items-stretch gap-4 lg:sticky lg:top-4" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200" max-md:hidden>
                <!-- wp:greenergy/directory-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                <!-- wp:greenergy/courses-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                <!-- wp:greenergy/featured-jobs-widget /-->
                <!-- wp:greenergy/ad-block {"height":"136px","hasContainer":false} /-->
                <!-- wp:greenergy/follow-us-widget /-->
            </div>
            
        </div>

            <!-- wp:greenergy/ad-block {"height":"275px","hasContainer":false} /-->
        </div>
    ',
];
