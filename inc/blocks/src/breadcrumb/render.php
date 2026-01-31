<?php
/**
 * Breadcrumb Block Template
 *
 * @package Greenergy
 */

?>
<nav aria-label="breadcrumb">
    <ol class="inline-flex items-center gap-3">
        <li>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-12 h-7 justify-start text-stone-500 text-base font-normal leading-6 hover:text-green-700">
                <?php esc_html_e( 'الرئيسية', 'greenergy' ); ?>
            </a>
        </li>
        <li class="text-stone-500">></li>
        <?php 
        // Ancestors
        if ( ! is_home() && ! is_front_page() ) {
            // If it's a sub-page, show parents
            $post_id = get_queried_object_id();
            $ancestors = get_post_ancestors( $post_id );
            if ( ! empty( $ancestors ) ) {
                $ancestors = array_reverse( $ancestors );
                foreach ( $ancestors as $ancestor ) {
                    ?>
                    <li>
                        <a href="<?php echo esc_url( get_permalink( $ancestor ) ); ?>" class="w-auto h-7 justify-start text-stone-500 text-base font-normal leading-6 hover:text-green-700">
                            <?php echo esc_html( get_the_title( $ancestor ) ); ?>
                        </a>
                    </li>
                    <li class="text-stone-500">></li>
                    <?php
                }
            }
        }
        ?>
        <li>
            <div class="w-auto h-7 justify-start text-neutral-950 text-base font-medium leading-6">
                <?php
                if ( is_search() ) {
                    printf( esc_html__( 'Search Results for: %s', 'greenergy' ), '<span>' . get_search_query() . '</span>' );
                } elseif ( is_archive() ) {
                    the_archive_title();
                } elseif ( is_home() ) {
                    esc_html_e( 'الاخبار', 'greenergy' );
                } else {
                    the_title();
                }
                ?>
            </div>
        </li>
    </ol>
</nav>
