<?php
/**
 * Generic AJAX Handler Class
 *
 * Handles standard AJAX load more/pagination requests.
 *
 * @package Greenergy
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Greenergy_Ajax {

    /**
     * Instance of the class.
     *
     * @var Greenergy_Ajax
     */
    private static $instance = null;

    /**
     * Get instance of the class.
     *
     * @return Greenergy_Ajax
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_ajax_greenergy_load_posts', [ $this, 'load_posts' ] );
        add_action( 'wp_ajax_nopriv_greenergy_load_posts', [ $this, 'load_posts' ] );
    }

    /**
     * Handle load posts request.
     */
    public function load_posts() {
        // Verify Nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'greenergy_nonce' ) ) {
            wp_send_json_error( [ 'message' => 'Invalid nonce' ] );
        }

        // Get Args
        $args = isset( $_POST['query_args'] ) ? json_decode( stripslashes( $_POST['query_args'] ), true ) : [];
        $page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
        
        if ( ! is_array( $args ) ) {
            wp_send_json_error( [ 'message' => 'Invalid query args' ] );
        }

        // Update Args for Pagination
        $args['post_status'] = 'publish';
        
        // Handle Offset with Pagination
        // WP_Query with 'offset' breaks 'paged'. We must calculate offset manually.
        if ( isset( $args['offset'] ) && $args['offset'] > 0 ) {
            $ppp = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : get_option( 'posts_per_page' );
            $initial_offset = (int) $args['offset'];
            $args['offset'] = $initial_offset + ( ( $page - 1 ) * $ppp );
            
            // We need to calculate max_pages manually because WP logic breaks with offset
            // We'll do a separate count query or use found_posts after main query?
            // Actually, if we run the query with offset, found_posts returns total ignoring offset/limit (typically).
            // But max_num_pages will be 0 or inaccurate.
            
            // Let's run the query
            $query = new WP_Query( $args );
            
            // Recalculate max pages
            // Total available for this grid = Found - Initial Offset
            $found_posts = $query->found_posts;
            $effective_total = max( 0, $found_posts - $initial_offset );
            $query->max_num_pages = ceil( $effective_total / $ppp );
            
        } else {
            // Standard Pagination
            $args['paged'] = $page;
            $query = new WP_Query( $args );
        }

        $content = '';
        
        if ( $query->have_posts() ) {
            ob_start();
            $idx = 0;
            while ( $query->have_posts() ) {
                $query->the_post();
                // Pass delay for AOS if needed, although generic AJAX content usually shouldn't delay too much 
                // or we reset it.
                $delay = ($idx % 4) * 100;
                
                // Use the template part
                // We assume news card for now, but ideally we'd pass template name in args if we want to be fully generic.
                // For now, let's look for a 'template_part' arg or default to 'content-news-card'.
                $template_part = isset( $_POST['template_part'] ) ? sanitize_text_field( $_POST['template_part'] ) : 'template-parts/content-news-card';
                
                // Allow passing args to template
                get_template_part( $template_part, null, [ 'delay' => $delay ] );
                
                $idx++;
            }
            $content = ob_get_clean();
        } else {
             // Optional: Return no posts message or empty
        }

        // Generate Pagination HTML
        $pagination_html = '';
        $big = 999999999;
        $pages = paginate_links( [
            'base'      => '%_%',
            'format'    => '?paged=%#%',
            'current'   => $page,
            'total'     => $query->max_num_pages,
            'type'      => 'array',
            'prev_text' => greenergy_icon( 'arrow-left', 20, 20 ), // Re-use helper if available or simple text
            'next_text' => greenergy_icon( 'arrow-right', 20, 20 ),
        ] );

        if ( $pages ) {
            $pagination_html .= '<nav class="pagination mt-8 flex justify-center items-center gap-2" aria-label="Pagination">';
            foreach ( $pages as $page_link ) {
                // Add JS classes and data attributes
                // logic: replace page numbers with data-page attributes
                // We need to parse the link to get the page number
                
                // This is a bit tricky with simple string replacement. 
                // Better approach: build the links manually or use a simple loop if we trust the output.
                
                // Alternative: Just return the max_pages and current_page and let JS build simple "Previous 1 2 3 ... Next". 
                // But user wants "appealing pagination".
                
                // Let's try to adapt the HTML string.
                // Standard WP paginate_links with type=array returns <a> tags.
                
                // Add Generic Class
                $page_link = str_replace( 'page-numbers', 'pagination-link js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-full text-sm font-medium transition-colors hover:bg-green-50 hover:text-green-700', $page_link );
                
                // Handle Active State
                if ( strpos( $page_link, 'current' ) !== false ) {
                    $page_link = str_replace( 'current', 'bg-green-700 text-white hover:bg-green-800 hover:text-white', $page_link );
                    $page_link = str_replace( 'pagination-link', 'pagination-link active', $page_link );
                } else {
                    $page_link = str_replace( 'pagination-link', 'pagination-link text-gray-500', $page_link );
                }

                // Extract page number for data attribute
                if ( preg_match( '/paged=(\d+)/', $page_link, $matches ) ) {
                    $pg = $matches[1];
                } else {
                    // Check if it's the first page link (might not have paged param)
                    // or standard link.
                    // For AJAX, let's inject data-page.
                    // Actually, simpler to just inject `data-page="X"` via regex on the href?
                }
            }
        }
        
        // Simpler: Custom Loop for AJAX Pagination to be robust
        $pagination_html = '';
        if ( $query->max_num_pages > 1 ) {
             $pagination_html .= '<nav class="pagination mt-8 flex justify-center items-center gap-2">';
             
             // Prev
             if ( $page > 1 ) {
                 $pagination_html .= sprintf(
                     '<button class="js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition-all" data-page="%d">%s</button>',
                     $page - 1,
                     '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
                 );
             }
             
             // Numbers (Simplified: show all or small range)
             // For simplicity in this task, let's show 1, ... current, ... max or just standard range
             // Using paginate_links array is actually best, just need to sanitize it.
             
             $links = paginate_links( [
                'base'      => '%_%',
                'format'    => '?paged=%#%',
                'current'   => $page,
                'total'     => $query->max_num_pages,
                'type'      => 'array',
                'prev_next' => false, // Handled manually for better control or just style these
             ] );
             
             if ( $links ) {
                 foreach ( $links as $link ) {
                     // Extract page number
                     $link_page = 1; // Default
                     if ( preg_match( '/paged=(\d+)/', $link, $m ) ) {
                         $link_page = $m[1];
                     } elseif ( strpos( $link, 'current' ) !== false ) {
                         $link_page = $page;
                     } elseif ( strpos( $link, 'href' ) === false ) {
                         // Dots
                         $link_page = null;
                     }
                     
                     if ( $link_page ) {
                         $is_active = $link_page == $page;
                         $classes = $is_active 
                             ? 'bg-green-700 text-white shadow-lg shadow-green-700/30 border-transparent' 
                             : 'bg-white text-gray-500 border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200';
                             
                         $pagination_html .= sprintf(
                             '<button class="js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-full border text-sm font-bold transition-all %s" data-page="%d">%s</button>',
                             $classes,
                             $link_page,
                             $link_page
                         );
                     } else {
                         // Dots
                         $pagination_html .= '<span class="w-10 h-10 flex justify-center items-center text-gray-400">...</span>';
                     }
                 }
             }

             // Next
             if ( $page < $query->max_num_pages ) {
                 $pagination_html .= sprintf(
                     '<button class="js-ajax-pagination-link w-10 h-10 flex justify-center items-center rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition-all" data-page="%d">%s</button>',
                     $page + 1,
                     '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>'
                 );
             }

             $pagination_html .= '</nav>';
        }

        wp_reset_postdata();

        wp_send_json_success( [
            'content'    => $content,
            'pagination' => $pagination_html,
        ] );
    }
}

// Initialize
Greenergy_Ajax::get_instance();
