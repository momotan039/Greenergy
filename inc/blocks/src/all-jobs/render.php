<?php
$show_gold_only = $attributes['showGoldOnly'] ?? false;
$posts_per_page = $attributes['postsPerPage'] ?? 6;
$gold_all_url = $attributes['goldAllUrl'] ?? '';

// Get current page and search/sort parameters
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$search = isset($_GET['s_job']) ? sanitize_text_field($_GET['s_job']) : '';
$sort = isset($_GET['sort_job']) ? sanitize_text_field($_GET['sort_job']) : 'latest';

// Main Jobs Query
$args = [
    'post_type'      => 'jobs',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'     => '_is_gold',
            'value'   => $show_gold_only ? 'yes' : 'no',
            'compare' => $show_gold_only ? '=' : '!=',
        ]
    ]
];

// If we are NOT in gold only mode, we should also allow posts that don't have the meta key at all (backwards compat)
if (!$show_gold_only) {
    $args['meta_query'][0]['compare'] = 'NOT LIKE'; // This is tricky in WP, better use mapping or relation
    $args['meta_query'] = [
        'relation' => 'OR',
        [
            'key'     => '_is_gold',
            'value'   => 'yes',
            'compare' => '!=',
        ],
        [
            'key'     => '_is_gold',
            'compare' => 'NOT EXISTS',
        ]
    ];
}


if ($search) {
    $args['s'] = $search;
}

if ($sort === 'popularity') {
    // Merge existing filter query with popularity query
    $existing_meta = $args['meta_query'];
    $args['meta_query'] = [
        'relation' => 'AND',
        $existing_meta,
        [
            'relation' => 'OR',
            'views_clause' => [
                'key'     => '_total_views_sort',
                'type'    => 'NUMERIC',
                'compare' => 'EXISTS',
            ],
            'exists_clause' => [
                'key'     => '_total_views_sort',
                'compare' => 'NOT EXISTS',
            ],
        ]
    ];
    $args['orderby'] = [
        'views_clause' => 'DESC',
        'date'         => 'DESC',
    ];
} elseif ($sort === 'oldest') {
    $args['orderby'] = 'date';
    $args['order']   = 'ASC';
} else {
    $args['orderby'] = 'date';
    $args['order']   = 'DESC';
}

$jobs_query = new WP_Query($args);

// Golden Jobs Query (latest gold jobs)
$gold_args = [
    'post_type'      => 'jobs',
    'posts_per_page' => 2,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'     => '_is_gold',
            'value'   => 'yes',
            'compare' => '=',
        ]
    ]
];
$gold_query = new WP_Query($gold_args);
?>

<div class="container mx-auto js-ajax-grid" data-query-args='<?php echo json_encode($args); ?>' data-template-part="templates/components/job-card">
    <!-- sort and search -->
    <div class="my-8 p-3 bg-white rounded-2xl">
        <form method="GET" action="<?php echo esc_url(get_permalink()); ?>" class="flex justify-between items-center gap-2">
            <!-- search -->
            <div class="group sm:w-96 h-11 px-3 py-3.5 rounded-lg outline outline-1 outline-offset-[-1px] outline-zinc-200 flex justify-center items-center gap-2 focus-within:outline-primary-500 transition-all">
                <svg class="w-6 h-6 inline self-center text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/search-normal.svg"></use>
                </svg>
                <input type="text" name="s_job" value="<?php echo esc_attr($search); ?>" placeholder="ابحث عن وظيفة ..." class="flex-1 h-6 text-right justify-start text-stone-500 text-sm font-normal leading-4 bg-transparent border-none outline-none focus:ring-0">
                <?php if ($search) : ?>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="text-stone-300 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- sort -->
            <div class="inline-flex flex-col justify-start gap-4">
                <div class="inline-flex items-center gap-4">
                    <div class="group relative max-w-52 h-12 px-1 md:px-4 py-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 flex justify-between items-center cursor-pointer hover:border-primary-500 transition-all">
                        <select name="sort_job" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 appearance-none">
                            <option value="latest" <?php selected($sort, 'latest'); ?>>الأحدث</option>
                            <option value="oldest" <?php selected($sort, 'oldest'); ?>>الأقدم</option>
                            <option value="popularity" <?php selected($sort, 'popularity'); ?>>الأكثر زيارة</option>
                        </select>
                        <div class="md:w-20 h-7 text-right justify-start text-neutral-950 text-sm font-normal capitalize leading-6">
                            <?php
                            if ($sort === 'popularity') echo 'الأكثر زيارة';
                            elseif ($sort === 'oldest') echo 'الأقدم';
                            else echo 'الأحدث';
                            ?>
                        </div>
                        <svg class="w-6 h-6 inline self-center text-stone-400 transition-colors duration-300 group-hover:text-green-600" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
                        </svg>
                    </div>
                </div>
            </div>

            <noscript>
                <button type="submit" class="bg-primary-500 text-white px-6 py-2 rounded-lg">بحث</button>
            </noscript>
        </form>
    </div>

    <?php if ($search) : ?>
        <div class="text-center mb-8">
            <div class="text-neutral-950 text-xl font-bold mb-2">
                تم العثور على <?php echo $jobs_query->found_posts; ?> وظيفة لـ "<?php echo esc_html($search); ?>"
            </div>
            <div class="text-stone-500 text-sm">إظهار كافة الفرص المطابقة في مجال الطاقة المتجددة</div>
        </div>
    <?php endif; ?>

    <?php if ($show_gold_only) : ?>
        <div class="w-full inline-flex flex-col justify-start items-center gap-3">
            <div
                class="h-14 px-8 py-2.5 bg-gradient-to-b from-orange-100 to-orange-200 rounded-3xl inline-flex justify-center items-center gap-2.5">
                <div
                    class="text-center justify-start text-yellow-500 text-2xl font-medium leading-5">
                    الوضائف الذهبية</div>
            </div>
            <div
                class="text-right justify-start text-stone-500 text-base font-medium leading-[48px]">
                إظهار الفرص في مجال الطاقة المتجددة</div>
        </div>
    <?php endif; ?>
    <!-- jobs list -->
    <div class="min-h-[400px]">
        <div class="flex flex-wrap gap-4 js-ajax-grid-content">
            <?php if ($jobs_query->have_posts()) : ?>
                <?php while ($jobs_query->have_posts()) : $jobs_query->the_post(); ?>
                    <?php get_template_part('templates/components/job-card', null, ['post' => get_post()]); ?>
                <?php endwhile;
                wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <!-- pagination -->
        <div class="mt-8 flex justify-center js-ajax-pagination-wrapper">
            <?php
            if (function_exists('greenergy_get_pagination_html')) {
                echo greenergy_get_pagination_html($jobs_query, $paged);
            } else {
                the_posts_pagination();
            }
            ?>
        </div>
    </div>

    <!-- golden jobs section -->
    <?php if (!$show_gold_only && $gold_query->have_posts()) : ?>
        <div class="mt-6">
            <div class="text-center my-10">
                <div class="inline-block bg-[#229924] text-white px-6 py-2 rounded-full text-base font-bold mb-4 shadow-sm">
                    الوظائف الذهبية
                </div>
                <h2 class="text-neutral-950 text-4xl font-bold leading-tight">اكتشف الوضائف الذهبية</h2>
            </div>

            <div class="rounded-3xl p-8 shadow-xl relative overflow-hidden group h-[400px] border border-yellow-200/50">
                <div class="absolute inset-0 bg-gradient-to-l from-yellow-50/25 via-yellow-500/50 to-amber-50/25"></div>
                <div class="absolute inset-0 z-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                <div class="relative z-10 flex flex-col items-center justify-center h-full text-center">
                    <h3 class="relative z-30 text-2xl md:text-3xl font-bold text-neutral-950 mb-2 px-8 py-3 bg-gradient-to-l from-yellow-500/80 to-amber-300/80 border border-yellow-400/30 rounded-full shadow-sm">
                        وظيفة ذهبية
                    </h3>
                    <p class="relative z-30 text-stone-600 text-sm md:text-base mb-8 italic">اكتشف احدث الوضائف المميزة</p>

                    <div class="scroll-mask w-full h-full absolute top-0 left-0 z-0">
                        <div class="scroll-container grid grid-cols-1 md:grid-cols-2 gap-4 px-6 pt-32">
                            <?php while ($gold_query->have_posts()) : $gold_query->the_post(); ?>
                                <div class="glass-job-card-wrapper pointer-events-none">
                                    <?php get_template_part('templates/components/job-card', null, ['is_gold' => true, 'post' => get_post()]); ?>
                                </div>
                            <?php endwhile; ?>

                            <!-- Repeat for seamless scroll effect -->
                            <?php
                            $gold_query->rewind_posts();
                            while ($gold_query->have_posts()) : $gold_query->the_post();
                            ?>
                                <div class="glass-job-card-wrapper pointer-events-none">
                                    <?php get_template_part('templates/components/job-card', null, ['is_gold' => true, 'post' => get_post()]); ?>
                                </div>
                            <?php endwhile;
                            wp_reset_postdata(); ?>
                        </div>
                    </div>

                    <div class="mt-8 relative z-30">
                        <?php
                        $all_url = !empty($gold_all_url) ? $gold_all_url : get_post_type_archive_link('jobs') . '?is_gold=1';
                        ?>
                        <a href="<?php echo esc_url($all_url); ?>" class="bg-gradient-to-br from-amber-400 to-amber-600 text-white py-4 px-12 rounded-2xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all flex items-center gap-3 font-bold group">
                            <span>اكتشف الكل</span>
                            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .scroll-mask {
        filter: blur(10px);
        mask-image: linear-gradient(to bottom, transparent, black 25%, black 75%, transparent);
        -webkit-mask-image: linear-gradient(to bottom, transparent, black 25%, black 75%, transparent);
        overflow: hidden;
    }

    .scroll-container {
        animation: scroll-vertical 25s linear infinite;
    }

    .scroll-container:hover {
        animation-play-state: paused;
    }

    @keyframes scroll-vertical {
        0% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(-50%);
        }
    }

    .glass-job-card-wrapper {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 1.25rem !important;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .glass-job-card-wrapper>div {
        background: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        width: 100%;
        padding: 0.75rem !important;
    }

    /* Custom select arrow */
    select[name="sort_job"] {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-position: left 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-left: 2.5rem;
    }
</style>