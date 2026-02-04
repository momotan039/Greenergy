<?php
/**
 * Featured Jobs Widget Block Template.
 *
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'flex self-stretch p-2 bg-white rounded-xl shadow-lg outline outline-1 outline-gray-200 flex flex-col justify-start items-center gap-2 max-md:min-w-[18rem] flex-none',
] );

// Query Jobs
$args = [
    'post_type'      => 'jobs',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
];
$query = new WP_Query( $args );

// Fallback data if query is empty
$has_jobs = $query->have_posts();
$mock_jobs = [];

if ( ! $has_jobs ) {
    $mock_jobs = [
         [
            'title' => 'مدير الطاقة المتجددة',
            'company' => 'شركة الطاقة الخضراء',
            'location' => 'الرياض ، السعودية',
            'type' => 'دوام كامل',
            'deadline' => '08/08/2025',
        ],
        [
            'title' => 'مهندس طاقة شمسية',
            'company' => 'حلول الاستدامة',
            'location' => 'دبي ، الإمارات',
            'type' => 'دوام جزئي',
            'deadline' => '15/09/2025',
        ],
        [
            'title' => 'استشاري بيئي',
            'company' => 'البيئة أولاً',
            'location' => 'القاهرة ، مصر',
            'type' => 'عقد',
            'deadline' => '01/10/2025',
        ],
    ];
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="inline-flex justify-end items-center gap-2 self-start">
        <div class="text-center justify-start text-neutral-950 text-base leading-6">
            <svg class="w-6 h-6 inline self-center" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/briefcase.svg"></use>
            </svg>
            الوظائف المميزة
        </div>
    </div>

    <div class="self-stretch p-2 rounded-2xl flex flex-col justify-start items-end gap-5">
        <div class="self-stretch flex flex-col justify-start items-center gap-4">
            <div class="self-stretch flex  md:flex-col flex-row justify-start items-center gap-2">
                <?php 
                if ( $has_jobs ) { 
                    while ( $query->have_posts() ) : $query->the_post(); 
                        $title = get_the_title();
                        $company = get_post_meta( get_the_ID(), 'company', true ) ?: 'شركة الطاقة الخضراء';
                        $location = get_post_meta( get_the_ID(), 'location', true ) ?: 'الرياض ، السعودية';
                        $type = get_post_meta( get_the_ID(), 'type', true ) ?: 'دوام كامل';
                        $deadline = get_post_meta( get_the_ID(), 'deadline', true ) ?: '08/08/2025';
                        // Render Job Card
                        ?>
                        <div class="self-stretch p-3 bg-stone-50 rounded-xl outline outline-1 outline-offset-[-1px] outline-sky-500 inline-flex justify-start items-start gap-2 hover:bg-sky-50 transition-colors">
                            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2">
                                <div class="self-stretch flex flex-col gap-2">
                                    <a href="<?php echo get_permalink(); ?>" class="text-right justify-start text-neutral-950 text-lg leading-6 font-medium hover:text-sky-600 transition-colors">
                                        <svg class="w-6 h-6 inline" aria-hidden="true">
                                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/medal-star.svg"></use>
                                        </svg>
                                        <?php echo esc_html( $title ); ?>
                                    </a>
                                    <div class="text-right justify-start text-stone-500 text-sm font-normal leading-6">
                                        <?php echo esc_html( $company ); ?>
                                    </div>
                                </div>
                                <div class="self-stretch inline-flex justify-between items-center w-full">
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/location.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $location ); ?>
                                        </div>
                                    </div>
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/briefcase.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $type ); ?>
                                        </div>
                                    </div>
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/clock.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $deadline ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                } else {
                    // Render Mock Jobs
                     foreach ( $mock_jobs as $job ) : ?>
                        <div class="self-stretch p-3 bg-stone-50 rounded-xl outline outline-1 outline-offset-[-1px] outline-sky-500 inline-flex justify-start items-start gap-2 hover:bg-sky-50 transition-colors">
                            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2">
                                <div class="self-stretch flex flex-col gap-2">
                                    <a href="#" class="text-right justify-start text-neutral-950 text-lg leading-6 font-medium hover:text-sky-600 transition-colors">
                                        <svg class="w-6 h-6 inline" aria-hidden="true">
                                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/bold/medal-star.svg"></use>
                                        </svg>
                                        <?php echo esc_html( $job['title'] ); ?>
                                    </a>
                                    <div class="text-right justify-start text-stone-500 text-sm font-normal leading-6">
                                        <?php echo esc_html( $job['company'] ); ?>
                                    </div>
                                </div>
                                <div class="self-stretch inline-flex justify-between items-center w-full">
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/location.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $job['location'] ); ?>
                                        </div>
                                    </div>
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/briefcase.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $job['type'] ); ?>
                                        </div>
                                    </div>
                                    <div class="w-1/3">
                                        <div class="flex gap-1 text-right justify-start text-stone-500 text-xs font-normal">
                                            <svg class="w-6 h-6 inline pt-[3px]" aria-hidden="true">
                                                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/clock.svg"></use>
                                            </svg>
                                            <?php echo esc_html( $job['deadline'] ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                }
                ?>
            </div>

            <div class="h-6 px-4 rounded-[55px] outline outline-1 outline-offset-[-1px] outline-black/20 inline-flex justify-center items-center gap-2">
                <div class="w-auto h-6 justify-start text-neutral-800 text-xs font-normal leading-5">
                    +15 وظيفة أخرى
                </div>
            </div>
            
            <a href="#" class="self-stretch h-9 px-4 bg-gradient-to-br from-sky-500 to-blue-700 rounded-[55px] inline-flex justify-center items-center gap-2 hover:shadow-lg transition-shadow">
                <div class="leading-5 h-6 text-white pt-1">عرض كل الوضائف</div>
                <span class="pb-1 text-white text-2xl leading-6">←</span>
            </a>
        </div>
    </div>
</div>
