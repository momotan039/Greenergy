<?php
/**
 * SEO Helper Class
 *
 * Handles Schema.org, Open Graph, meta tags.
 * Provides fallbacks when Rank Math is not active.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_SEO
 */
class Greenergy_SEO {

    /**
     * Constructor
     */
    public function __construct() {
        // Only add fallbacks if no SEO plugin is active
        if ( ! $this->is_seo_plugin_active() ) {
            add_action( 'wp_head', [ $this, 'output_meta_tags' ], 5 );
            add_action( 'wp_head', [ $this, 'output_open_graph' ], 5 );
            add_action( 'wp_head', [ $this, 'output_twitter_cards' ], 5 );
        }
        
        // Schema.org always (supplements SEO plugins)
        add_action( 'wp_footer', [ $this, 'output_schema_markup' ], 100 );
    }

    /**
     * Check if an SEO plugin is active
     *
     * @return bool
     */
    private function is_seo_plugin_active() {
        // Rank Math
        if ( class_exists( 'RankMath' ) ) {
            return true;
        }
        
        // Yoast SEO
        if ( defined( 'WPSEO_VERSION' ) ) {
            return true;
        }
        
        // All in One SEO
        if ( class_exists( 'AIOSEO\Plugin\AIOSEO' ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * Output basic meta tags
     */
    public function output_meta_tags() {
        if ( is_singular() ) {
            global $post;
            
            $description = $this->get_meta_description( $post );
            if ( $description ) {
                echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
            }
        }
    }

    /**
     * Output Open Graph tags
     */
    public function output_open_graph() {
        global $post;
        
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
        
        if ( is_singular() ) {
            echo '<meta property="og:type" content="article">' . "\n";
            echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '">' . "\n";
            echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">' . "\n";
            
            $description = $this->get_meta_description( $post );
            if ( $description ) {
                echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
            }
            
            if ( has_post_thumbnail() ) {
                $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
                if ( $image ) {
                    echo '<meta property="og:image" content="' . esc_url( $image[0] ) . '">' . "\n";
                    echo '<meta property="og:image:width" content="' . esc_attr( $image[1] ) . '">' . "\n";
                    echo '<meta property="og:image:height" content="' . esc_attr( $image[2] ) . '">' . "\n";
                }
            }
            
            echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
            echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
            
        } elseif ( is_front_page() ) {
            echo '<meta property="og:type" content="website">' . "\n";
            echo '<meta property="og:title" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
            echo '<meta property="og:url" content="' . esc_url( home_url( '/' ) ) . '">' . "\n";
        }
    }

    /**
     * Output Twitter Card tags
     */
    public function output_twitter_cards() {
        global $post;
        
        $card_type = 'summary_large_image';
        
        echo '<meta name="twitter:card" content="' . esc_attr( $card_type ) . '">' . "\n";
        
        // Get Twitter handle from Redux options
        $twitter_handle = greenergy_option( 'social_twitter', '' );
        if ( $twitter_handle ) {
            echo '<meta name="twitter:site" content="@' . esc_attr( ltrim( $twitter_handle, '@' ) ) . '">' . "\n";
        }
        
        if ( is_singular() ) {
            echo '<meta name="twitter:title" content="' . esc_attr( get_the_title() ) . '">' . "\n";
            
            $description = $this->get_meta_description( $post );
            if ( $description ) {
                echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
            }
            
            if ( has_post_thumbnail() ) {
                $image = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
                if ( $image ) {
                    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
                }
            }
        }
    }

    /**
     * Output Schema.org JSON-LD
     */
    public function output_schema_markup() {
        global $post;
        
        $schema = [];
        
        // Organization schema for all pages
        $schema[] = $this->get_organization_schema();
        
        // Website schema
        $schema[] = $this->get_website_schema();
        
        // Page-specific schema
        if ( is_singular() ) {
            $post_type = get_post_type();
            
            switch ( $post_type ) {
                case 'news':
                    $schema[] = $this->get_news_article_schema( $post );
                    break;
                case 'articles':
                    $schema[] = $this->get_article_schema( $post );
                    break;
                case 'jobs':
                    $schema[] = $this->get_job_posting_schema( $post );
                    break;
                case 'courses':
                    $schema[] = $this->get_course_schema( $post );
                    break;
                case 'directory':
                    $schema[] = $this->get_local_business_schema( $post );
                    break;
                case 'post':
                    $schema[] = $this->get_blog_posting_schema( $post );
                    break;
            }
        }
        
        // BreadcrumbList
        if ( ! is_front_page() ) {
            $breadcrumbs = $this->get_breadcrumb_schema();
            if ( $breadcrumbs ) {
                $schema[] = $breadcrumbs;
            }
        }
        
        // Output JSON-LD
        foreach ( $schema as $item ) {
            if ( ! empty( $item ) ) {
                echo '<script type="application/ld+json">' . wp_json_encode( $item, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
            }
        }
    }

    /**
     * Get meta description
     *
     * @param WP_Post $post Post object.
     * @return string Description.
     */
    private function get_meta_description( $post ) {
        if ( has_excerpt( $post ) ) {
            return wp_strip_all_tags( get_the_excerpt( $post ) );
        }
        
        $content = $post->post_content;
        $content = wp_strip_all_tags( $content );
        $content = preg_replace( '/\s+/', ' ', $content );
        
        return wp_trim_words( $content, 30, '...' );
    }

    /**
     * Get Organization schema
     *
     * @return array Schema data.
     */
    private function get_organization_schema() {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => get_bloginfo( 'name' ),
            'url'      => home_url( '/' ),
            'logo'     => $this->get_site_logo_url(),
        ];
    }

    /**
     * Get Website schema
     *
     * @return array Schema data.
     */
    private function get_website_schema() {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => get_bloginfo( 'name' ),
            'url'             => home_url( '/' ),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => home_url( '/?s={search_term_string}' ),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Get NewsArticle schema
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_news_article_schema( $post ) {
        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'NewsArticle',
            'headline'         => get_the_title( $post ),
            'description'      => $this->get_meta_description( $post ),
            'datePublished'    => get_the_date( 'c', $post ),
            'dateModified'     => get_the_modified_date( 'c', $post ),
            'author'           => $this->get_author_schema( $post ),
            'publisher'        => $this->get_organization_schema(),
            'mainEntityOfPage' => get_permalink( $post ),
            'image'            => $this->get_featured_image_url( $post ),
        ];
    }

    /**
     * Get Article schema
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_article_schema( $post ) {
        $schema = $this->get_news_article_schema( $post );
        $schema['@type'] = 'Article';
        return $schema;
    }

    /**
     * Get BlogPosting schema
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_blog_posting_schema( $post ) {
        $schema = $this->get_news_article_schema( $post );
        $schema['@type'] = 'BlogPosting';
        return $schema;
    }

    /**
     * Get JobPosting schema
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_job_posting_schema( $post ) {
        // Get custom fields (implement based on your CPT meta)
        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'JobPosting',
            'title'         => get_the_title( $post ),
            'description'   => $this->get_meta_description( $post ),
            'datePosted'    => get_the_date( 'c', $post ),
            'hiringOrganization' => $this->get_organization_schema(),
            // Add more fields based on CPT meta
        ];
    }

    /**
     * Get Course schema
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_course_schema( $post ) {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            'name'        => get_the_title( $post ),
            'description' => $this->get_meta_description( $post ),
            'provider'    => $this->get_organization_schema(),
            // Add more fields based on CPT meta
        ];
    }

    /**
     * Get LocalBusiness schema for directory
     *
     * @param WP_Post $post Post object.
     * @return array Schema data.
     */
    private function get_local_business_schema( $post ) {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'LocalBusiness',
            'name'        => get_the_title( $post ),
            'description' => $this->get_meta_description( $post ),
            // Add more fields based on CPT meta
        ];
    }

    /**
     * Get author schema
     *
     * @param WP_Post $post Post object.
     * @return array Author schema.
     */
    private function get_author_schema( $post ) {
        $author_id = $post->post_author;
        return [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name', $author_id ),
            'url'   => get_author_posts_url( $author_id ),
        ];
    }

    /**
     * Get breadcrumb schema
     *
     * @return array|null Breadcrumb schema.
     */
    private function get_breadcrumb_schema() {
        $items = [];
        $position = 1;
        
        // Home
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __( 'Home', 'greenergy' ),
            'item'     => home_url( '/' ),
        ];
        
        // Add more breadcrumb items based on current page
        // This is a simplified version - expand as needed
        
        if ( empty( $items ) ) {
            return null;
        }
        
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Get site logo URL
     *
     * @return string Logo URL.
     */
    private function get_site_logo_url() {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            return wp_get_attachment_image_url( $custom_logo_id, 'full' );
        }
        return '';
    }

    /**
     * Get featured image URL
     *
     * @param WP_Post $post Post object.
     * @return string Image URL.
     */
    private function get_featured_image_url( $post ) {
        if ( has_post_thumbnail( $post ) ) {
            return get_the_post_thumbnail_url( $post, 'large' );
        }
        return '';
    }
}
