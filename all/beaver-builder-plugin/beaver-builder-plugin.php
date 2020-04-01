<?php

class LDC_AIO_Beaver_Builder_Plugin {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function fl_builder_color_presets($colors){
        $b4_colors = array(
            '007bff', // primary
            '6c757d', // secondary
            '28a745', // success
            '17a2b8', // info
            'ffc107', // warning
            'dc3545', // danger
            'f8f9fa', // light
            '343a40', // dark
        );
        $colors = array_merge($b4_colors, $colors);
        return $colors;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Beaver Builder Plugin';
        LDC_AIO_One::add_setting('add_b4_color_presets', array(
        	'name' => 'Add Bootstrap 4 color presets?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $add_b4_color_presets = LDC_AIO_One::get_setting('add_b4_color_presets');
        if($add_b4_color_presets){
            add_filter('fl_builder_color_presets', array(__CLASS__, 'fl_builder_color_presets'));
        }
        LDC_AIO_One::add_setting('disable_inline_editing', array(
        	'name' => 'Disable inline editing?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $disable_inline_editing = LDC_AIO_One::get_setting('disable_inline_editing');
        if($disable_inline_editing){
            add_filter('fl_inline_editing_enabled', '__return_false');
        }
        LDC_AIO_One::add_setting('expand_templates_into_navigation_mega_menus', array(
        	'name' => 'Expand templates into navigation mega menus?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $expand_templates_into_navigation_mega_menus = LDC_AIO_One::get_setting('expand_templates_into_navigation_mega_menus');
        if($expand_templates_into_navigation_mega_menus){
            add_filter('walker_nav_menu_start_el', array(__CLASS__, 'walker_nav_menu_start_el'), 10, 4);
        }
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function walker_nav_menu_start_el($item_output, $item, $depth, $args){
        if($item->object == 'fl-builder-template'){
            $item_output = $args->before;
            $item_output .= do_shortcode('[fl_builder_insert_layout id="' . $item->object_id . '"]');
            $item_output .= $args->after;
        }
        return $item_output;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Beaver_Builder_Plugin::init();
