<?php
/**
 * Component: Pagination
 *
 * Styled pagination for archives.
 *
 * @package Greenergy
 * @since 1.0.0
 */

global $wp_query;

$big = 999999999;

$pages = paginate_links( [
    'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format'    => '?paged=%#%',
    'current'   => max( 1, get_query_var( 'paged' ) ),
    'total'     => $wp_query->max_num_pages,
    'type'      => 'array',
    'prev_text' => '<span class="sr-only">' . __( 'Previous', 'greenergy' ) . '</span>' . greenergy_icon( 'chevron-left', 18, 18 ),
    'next_text' => '<span class="sr-only">' . __( 'Next', 'greenergy' ) . '</span>' . greenergy_icon( 'chevron-right', 18, 18 ),
] );

if ( $pages ) : ?>
    <nav class="pagination mt-12" aria-label="<?php esc_attr_e( 'Pagination', 'greenergy' ); ?>">
        <?php foreach ( $pages as $page ) : 
            // Add Tailwind classes
            $page = str_replace( 'page-numbers', 'pagination-link', $page );
            $page = str_replace( 'current', 'active', $page );
            echo $page;
        endforeach; ?>
    </nav>
<?php endif; ?>
