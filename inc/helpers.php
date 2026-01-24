<?php
/**
 * Theme Helper Functions
 *
 * Utility functions used across the theme.
 *
 * @package Greenergy
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get template part with data passing
 *
 * @param string $slug   Template slug.
 * @param string $name   Template name.
 * @param array  $args   Arguments to pass to template.
 */
function greenergy_get_template( $slug, $name = '', $args = [] ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args );
    }

    $template = '';

    if ( $name ) {
        $template = locate_template( [ "{$slug}-{$name}.php", "{$slug}.php" ] );
    } else {
        $template = locate_template( [ "{$slug}.php" ] );
    }

    if ( $template ) {
        include $template;
    }
}

/**
 * Get post thumbnail with fallback
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @param array  $attr    Image attributes.
 * @return string Image HTML.
 */
function greenergy_get_thumbnail( $post_id = null, $size = 'card-thumbnail', $attr = [] ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    if ( has_post_thumbnail( $post_id ) ) {
        $default_attr = [
            'loading'  => 'lazy',
            'decoding' => 'async',
            'class'    => 'w-full h-full object-cover',
        ];
        $attr = wp_parse_args( $attr, $default_attr );
        
        return get_the_post_thumbnail( $post_id, $size, $attr );
    }

    // Return placeholder
    $placeholder = GREENERGY_ASSETS_URI . '/images/placeholders/default.svg';
    return '<img src="' . esc_url( $placeholder ) . '" alt="" class="w-full h-full object-cover" loading="lazy">';
}

/**
 * Get reading time estimate
 *
 * @param int $post_id Post ID.
 * @return int Minutes to read.
 */
function greenergy_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( strip_tags( $content ) );
    $reading_time = ceil( $word_count / 200 ); // Average 200 words per minute

    return max( 1, $reading_time );
}

/**
 * Get formatted date
 *
 * @param int    $post_id Post ID.
 * @param string $format  Date format.
 * @return string Formatted date.
 */
function greenergy_get_date( $post_id = null, $format = '' ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    if ( ! $format ) {
        $format = get_option( 'date_format' );
    }

    return get_the_date( $format, $post_id );
}

/**
 * Get author info
 *
 * @param int $post_id Post ID.
 * @return array Author data.
 */
function greenergy_get_author( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $author_id = get_post_field( 'post_author', $post_id );

    return [
        'id'     => $author_id,
        'name'   => get_the_author_meta( 'display_name', $author_id ),
        'url'    => get_author_posts_url( $author_id ),
        'avatar' => get_avatar_url( $author_id, [ 'size' => 80 ] ),
        'bio'    => get_the_author_meta( 'description', $author_id ),
    ];
}

/**
 * Truncate text
 *
 * @param string $text   Text to truncate.
 * @param int    $length Maximum length.
 * @param string $suffix Suffix to add.
 * @return string Truncated text.
 */
function greenergy_truncate( $text, $length = 150, $suffix = '...' ) {
    $text = wp_strip_all_tags( $text );
    
    if ( mb_strlen( $text ) <= $length ) {
        return $text;
    }

    return mb_substr( $text, 0, $length ) . $suffix;
}

/**
 * Get SVG icon
 *
 * @param string $name   Icon name.
 * @param int    $width  Icon width.
 * @param int    $height Icon height.
 * @param string $class  Additional CSS class.
 * @return string SVG markup.
 */
function greenergy_icon( $name, $width = 24, $height = 24, $class = '' ) {
    $icon_path = GREENERGY_ASSETS_DIR . "/images/icons/{$name}.svg";
    
    if ( ! file_exists( $icon_path ) ) {
        return '';
    }

    $svg = file_get_contents( $icon_path );
    
    // Add dimensions and class
    $svg = preg_replace(
        '/<svg/',
        sprintf( '<svg width="%d" height="%d" class="%s"', $width, $height, esc_attr( $class ) ),
        $svg,
        1
    );

    return $svg;
}

/**
 * Get social share URLs
 *
 * @param int $post_id Post ID.
 * @return array Share URLs.
 */
function greenergy_share_urls( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $url = urlencode( get_permalink( $post_id ) );
    $title = urlencode( get_the_title( $post_id ) );

    return [
        'facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$url}",
        'twitter'   => "https://twitter.com/intent/tweet?url={$url}&text={$title}",
        'linkedin'  => "https://www.linkedin.com/shareArticle?mini=true&url={$url}&title={$title}",
        'whatsapp'  => "https://wa.me/?text={$title}%20{$url}",
        'telegram'  => "https://t.me/share/url?url={$url}&text={$title}",
        'email'     => "mailto:?subject={$title}&body={$url}",
    ];
}

/**
 * Check if current page is blog
 *
 * @return bool
 */
function greenergy_is_blog() {
    return ( is_archive() || is_author() || is_category() || is_home() || is_tag() ) 
           && 'post' === get_post_type();
}

/**
 * Pagination
 *
 * @param WP_Query $query Query object.
 */
function greenergy_pagination( $query = null ) {
    if ( ! $query ) {
        global $wp_query;
        $query = $wp_query;
    }

    $big = 999999999;

    $pages = paginate_links( [
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'total'     => $query->max_num_pages,
        'type'      => 'array',
        'prev_text' => greenergy_icon( 'arrow-left', 20, 20 ),
        'next_text' => greenergy_icon( 'arrow-right', 20, 20 ),
    ] );

    if ( $pages ) {
        echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'greenergy' ) . '">';
        foreach ( $pages as $page ) {
            // Add Tailwind classes
            $page = str_replace( 'page-numbers', 'pagination-link', $page );
            $page = str_replace( 'current', 'pagination-link active', $page );
            echo $page;
        }
        echo '</nav>';
    }
}

/**
 * Breadcrumbs
 */
function greenergy_breadcrumbs() {
    if ( is_front_page() ) {
        return;
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'greenergy' ) . '">';
    echo '<span class="breadcrumbs-item">';
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="breadcrumbs-link">' . esc_html__( 'Home', 'greenergy' ) . '</a>';
    echo '</span>';

    if ( is_singular() ) {
        $post_type = get_post_type();
        $post_type_obj = get_post_type_object( $post_type );
        
        if ( $post_type_obj && $post_type !== 'page' ) {
            echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
            echo '<span class="breadcrumbs-item">';
            echo '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '" class="breadcrumbs-link">';
            echo esc_html( $post_type_obj->labels->name );
            echo '</a>';
            echo '</span>';
        }

        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        echo esc_html( get_the_title() );
        echo '</span>';
    } elseif ( is_archive() ) {
        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        echo esc_html( get_the_archive_title() );
        echo '</span>';
    } elseif ( is_search() ) {
        echo '<span class="breadcrumbs-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumbs-item breadcrumbs-current" aria-current="page">';
        /* translators: %s: search query */
        printf( esc_html__( 'Search: %s', 'greenergy' ), get_search_query() );
        echo '</span>';
    }

    echo '</nav>';
}

/**
 * Check if we're in development mode
 *
 * @return bool
 */
function greenergy_is_dev() {
    return defined( 'WP_DEBUG' ) && WP_DEBUG;
}
