<?php
/**
 * Custom Nav Walker
 *
 * @package Greenergy
 */

class Greenergy_Nav_Walker extends Walker_Nav_Menu {
    /**
     * Start Level
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        // Sub-menus not implemented in this design, but good to have empty method
    }

    /**
     * Start Element
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $is_active = in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-parent', $classes );

        // Base classes
        $li_classes = 'h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer transition-all duration-300 hover:scale-105';
        
        // Active/Inactive specific classes
        if ( $is_active ) {
            $li_classes .= ' bg-green-700/10 hover:bg-green-700/20'; // Active background
            $text_classes = 'text-green-700 font-medium';
        } else {
            $li_classes .= ' hover:bg-green-50'; // Inactive hover
            $text_classes = 'text-neutral-950 font-normal';
        }

        $output .= '<div class="' . esc_attr( $li_classes ) . '">';
        
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        $atts['class']  = 'justify-start text-base leading-6 ' . $text_classes;

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= $item_output;
    }

    /**
     * End Element
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</div>\n";
    }
}
