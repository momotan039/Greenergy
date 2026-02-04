<?php
/**
 * Pagination Template Part
 *
 * @package Greenergy
 */

// global $wp_query;

// if ( $wp_query->max_num_pages <= 1 ) return;

$paged = 1;
$max   = 10;

if ( $paged >= 1 ) $links[] = $paged;
if ( $paged >= 3 ) {
    $links[] = $paged - 1;
    $links[] = $paged - 2;
}
if ( ( $paged + 2 ) <= $max ) {
    $links[] = $paged + 2;
    $links[] = $paged + 1;
}

?>

<nav class="flex justify-center items-center gap-2 mt-8" aria-label="Pagination">
    <?php if ( $paged > 1 ) : ?>
        <a href="<?php echo get_pagenum_link( $paged - 1 ); ?>" 
           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    <?php endif; ?>

    <?php if ( ! in_array( 1, $links ) ) : ?>
        <a href="<?php echo get_pagenum_link( 1 ); ?>" 
           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all font-medium">
            1
        </a>
        <?php if ( ! in_array( 2, $links ) ) : ?>
            <span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    sort( $links );
    foreach ( (array) $links as $link ) :
        if ( $paged == $link ) :
    ?>
        <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-green-600 text-white font-semibold shadow-md">
            <?php echo $link; ?>
        </span>
    <?php else : ?>
        <a href="<?php echo get_pagenum_link( $link ); ?>" 
           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all font-medium">
            <?php echo $link; ?>
        </a>
    <?php
        endif;
    endforeach;
    ?>

    <?php if ( ! in_array( $max, $links ) ) : ?>
        <?php if ( ! in_array( $max - 1, $links ) ) : ?>
            <span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
        <?php endif; ?>
        <a href="<?php echo get_pagenum_link( $max ); ?>" 
           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all font-medium">
            <?php echo $max; ?>
        </a>
    <?php endif; ?>

    <?php if ( $paged < $max ) : ?>
        <a href="<?php echo get_pagenum_link( $paged + 1 ); ?>" 
           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    <?php endif; ?>
</nav>