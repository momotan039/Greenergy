<?php
/**
 * Content Template: None
 *
 * Displayed when no content is found.
 *
 * @package Greenergy
 * @since 1.0.0
 */
?>

<section class="no-results py-16 text-center">
    <div class="max-w-lg mx-auto">
        
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-secondary-100 dark:bg-secondary-800 
                    flex items-center justify-center text-secondary-400">
            <?php echo greenergy_icon( 'search', 40, 40 ); ?>
        </div>
        
        <?php if ( is_search() ) : ?>
            
            <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-3">
                <?php esc_html_e( 'No results found', 'greenergy' ); ?>
            </h2>
            <p class="text-secondary-600 dark:text-secondary-400 mb-6">
                <?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'greenergy' ); ?>
            </p>
            
            <!-- Search Form -->
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="max-w-sm mx-auto">
                <div class="input-with-icon">
                    <span class="input-icon"><?php echo greenergy_icon( 'search', 20, 20 ); ?></span>
                    <input type="search" 
                           name="s" 
                           class="form-input" 
                           placeholder="<?php esc_attr_e( 'Search...', 'greenergy' ); ?>"
                           value="<?php echo get_search_query(); ?>">
                </div>
            </form>
            
        <?php else : ?>
            
            <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-3">
                <?php esc_html_e( 'Nothing here yet', 'greenergy' ); ?>
            </h2>
            <p class="text-secondary-600 dark:text-secondary-400 mb-6">
                <?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'greenergy' ); ?>
            </p>
            
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary">
                <?php esc_html_e( 'Back to Home', 'greenergy' ); ?>
            </a>
            
        <?php endif; ?>
        
    </div>
</section>
