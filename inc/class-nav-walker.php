<?php

/**
 * Custom Nav Walker
 *
 * @package Greenergy
 */

class Greenergy_Nav_Walker extends Walker_Nav_Menu
{
    /**
     * Start Level (Sub-menu container)
     */
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class=\"absolute top-full right-0 mt-2 w-48 bg-white shadow-xl rounded-xl py-2 hidden group-hover:block z-[100] border border-gray-100 transition-all duration-300\">\n";
    }

    /**
     * End Level
     */
    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</div>\n";
    }

    /**
     * Start Element
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $is_active = in_array('current-menu-item', $classes) || in_array('current-menu-parent', $classes) || in_array('current-menu-ancestor', $classes);
        $has_children = in_array('menu-item-has-children', $classes);

        // Base classes
        if ($depth === 0) {
            $li_classes = 'h-9 px-5 py-2 rounded-[102px] flex justify-center items-center gap-2.5 cursor-pointer transition-all duration-300 hover:scale-105 group relative';

            // Active/Inactive specific classes
            if ($is_active) {
                $li_classes .= ' bg-green-700/10 hover:bg-green-700/20'; // Active background
                $text_classes = 'text-green-700 font-medium';
            } else {
                $li_classes .= ' hover:bg-green-50'; // Inactive hover
                $text_classes = 'text-neutral-950 font-normal';
            }
        } else {
            $li_classes = 'px-4 py-2 hover:bg-green-50 transition-colors w-full flex items-center group';
            $text_classes = $is_active ? 'text-green-700 font-bold' : 'text-neutral-700 font-medium';
        }

        $output .= '<div class="' . esc_attr($li_classes) . '">';

        $atts = array();
        $atts['title']  = ! empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = ! empty($item->target)     ? $item->target     : '';
        $atts['rel']    = ! empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = ! empty($item->url)        ? $item->url        : '';
        $atts['class']  = 'justify-start text-base leading-6 w-full ' . $text_classes;

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (! empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $item_output = $args->before;
        $item_output .= '<a ' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;

        if ($has_children && $depth === 0) {
            $item_output .= ' <svg class="w-4 h-4 mr-1 opacity-50 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= $item_output;
    }

    /**
     * End Element
     */
    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        $output .= "</div>\n";
    }
}
