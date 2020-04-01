<?php

class LDC_AIO_Beaver_Builder_Plugin {


    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Beaver Builder Plugin';
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
