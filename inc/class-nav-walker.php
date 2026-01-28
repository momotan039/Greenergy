<?php
/**
 * Custom Nav Walker for Tailwind CSS
 *
 * @package Greenergy
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Greenergy_Nav_Walker
 */
class Greenergy_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Start Level
     */
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $classes = array( 'dropdown-menu' );
        $class_names = implode( ' ', $classes );
        $output .= "\n<ul class=\"$class_names\">\n";
    }

    /**
     * Start Element
     */
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Add group class for dropdown hover
        if ( in_array( 'menu-item-has-children', $classes ) ) {
            $classes[] = 'group relative';
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        // Active state
        $active_class = '';
        if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-parent', $classes ) ) {
            $active_class = ' active';
        }

        // Depth-based link classes
        if ( $depth === 0 ) {
            $atts['class'] = 'nav-link' . $active_class;
        } else {
            $atts['class'] = 'dropdown-item' . $active_class;
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        // Add chevron for dropdowns
        $dropdown_icon = '';
        if ( in_array( 'menu-item-has-children', $classes ) && $depth === 0 ) {
            $dropdown_icon = '<span class="ml-1 opacity-50 group-hover:opacity-100 transition-opacity">' . greenergy_icon( 'chevron-down', 16, 16 ) . '</span>';
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= $dropdown_icon;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
