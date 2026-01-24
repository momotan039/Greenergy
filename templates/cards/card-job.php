<?php
/**
 * Card Template: Job
 *
 * Job listing card with Schema.org ready structure.
 * Figma → Tailwind conversion placeholder.
 *
 * @package Greenergy
 * @since 1.0.0
 */

$post_id     = $post_id ?? get_the_ID();
$job_type    = get_the_terms( $post_id, 'job_type' );
$job_type    = $job_type ? $job_type[0] : null;
$location    = get_the_terms( $post_id, 'job_location' );
$location    = $location ? $location[0] : null;

// Meta fields (placeholder - implement based on your meta setup)
$salary      = get_post_meta( $post_id, '_job_salary', true );
$company     = get_post_meta( $post_id, '_job_company', true );
?>

<article <?php post_class( 'card-job group', $post_id ); ?>>
    <div class="card-body">
        
        <!-- Job Header -->
        <div class="flex items-start justify-between gap-4 mb-3">
            <div>
                <?php if ( $job_type ) : ?>
                    <span class="job-type"><?php echo esc_html( $job_type->name ); ?></span>
                <?php endif; ?>
                
                <h3 class="card-title mt-2">
                    <a href="<?php the_permalink( $post_id ); ?>">
                        <?php echo get_the_title( $post_id ); ?>
                    </a>
                </h3>
            </div>
            
            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                <div class="w-14 h-14 rounded-lg overflow-hidden shrink-0 bg-secondary-100 dark:bg-secondary-700">
                    <?php echo greenergy_get_thumbnail( $post_id, 'square-sm' ); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Job Details -->
        <div class="flex flex-wrap items-center gap-4 text-sm text-secondary-500 dark:text-secondary-400 mb-4">
            <?php if ( $company ) : ?>
                <span class="inline-flex items-center gap-1.5">
                    <?php echo greenergy_icon( 'building', 16, 16 ); ?>
                    <?php echo esc_html( $company ); ?>
                </span>
            <?php endif; ?>
            
            <?php if ( $location ) : ?>
                <span class="inline-flex items-center gap-1.5">
                    <?php echo greenergy_icon( 'map-pin', 16, 16 ); ?>
                    <?php echo esc_html( $location->name ); ?>
                </span>
            <?php endif; ?>
            
            <span class="inline-flex items-center gap-1.5">
                <?php echo greenergy_icon( 'calendar', 16, 16 ); ?>
                <?php echo greenergy_get_date( $post_id ); ?>
            </span>
        </div>
        
        <!-- Excerpt -->
        <?php if ( has_excerpt( $post_id ) ) : ?>
            <p class="card-excerpt mb-4">
                <?php echo greenergy_truncate( get_the_excerpt( $post_id ), 120 ); ?>
            </p>
        <?php endif; ?>
        
        <!-- Job Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-secondary-100 dark:border-secondary-700">
            <?php if ( $salary ) : ?>
                <span class="job-salary"><?php echo esc_html( $salary ); ?></span>
            <?php else : ?>
                <span></span>
            <?php endif; ?>
            
            <a href="<?php the_permalink( $post_id ); ?>" class="btn-primary btn-sm">
                <?php esc_html_e( 'View Job', 'greenergy' ); ?>
            </a>
        </div>
        
    </div>
</article>
