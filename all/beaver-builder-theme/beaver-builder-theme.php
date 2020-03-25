<?php

class LDC_AIO_Beaver_Builder_Theme {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function after_setup_theme(){
        $meta_box_and_tab = 'Beaver Builder Theme';
        LDC_AIO_One::add_setting('remove_default_styles', array(
        	'name' => 'Remove default styles for HTML buttons and forms?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $remove_default_styles = LDC_AIO_One::get_setting('remove_default_styles');
        if($remove_default_styles){
            add_filter('fl_theme_compile_less_paths', array(__CLASS__, 'fl_theme_compile_less_paths'));
        }
		LDC_AIO_One::add_setting('clear_cache', array(
            'std' => '<a class="button" href="' . esc_url(admin_url('options-general.php?page=fl-builder-settings#tools')) . '" target="_blank">' . __('Clear Cache', 'fl-builder') . '</a>',
            'type' => 'custom_html',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('remove_presets', array(
        	'name' => 'Remove presets?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $remove_presets = LDC_AIO_One::get_setting('remove_presets');
        if($remove_presets){
            add_action('customize_register', array(__CLASS__, 'customize_register'), 20);
        }
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function customize_register($wp_customize){
         $wp_customize->remove_section('fl-presets');
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function fl_theme_compile_less_paths($paths){
        foreach($paths as $index => $path){
            if($path == FL_THEME_DIR . '/less/theme.less'){
                $paths[$index] = LDC_AIO_DIR . 'all/beaver-builder-theme/theme.less';
            }
        }
        return $paths;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Beaver_Builder_Theme::init();
