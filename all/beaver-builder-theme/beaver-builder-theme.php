<?php

class LDC_AIO_Beaver_Builder_Theme {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function add_default_styles(){
        $mods = get_theme_mods();
        $mods['fl-accent'] = '#428bca';
        update_option('theme_mods_' . get_option('stylesheet'), $mods);
		return array(
			'success' => true,
		);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function admin_enqueue_scripts($hook){
        if(str_replace('toplevel_page_', '', $hook) === LDC_AIO_SLUG){
            wp_enqueue_script('wp-api');
        }
    }

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function admin_footer(){
        if(LDC_AIO_One::is_current_screen()){ ?>
            <script>
        		jQuery(function($){
                    $('#add_default_styles').on('click', function(e){
                        e.preventDefault();
    					$('#add_default_styles').text('Wait...');
                        $.ajax({
                            beforeSend: function(xhr){
                                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                            },
                            method: 'GET',
                            url: wpApiSettings.root + 'ldc-aio/v1/add-default-styles',
                        }).done(function(response){
                            $('#add_default_styles').text('Done.');
    						setTimeout(function(){
    							$('#add_default_styles').text('Add default styles');
    						}, 1000);
                        });
                    });
        		});
        	</script><?php
        }
    }

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function after_setup_theme(){
        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));
		add_action('admin_footer', array(__CLASS__, 'admin_footer'));
		add_action('rest_api_init', array(__CLASS__, 'rest_api_init'));
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
        LDC_AIO_One::add_setting('add_default_styles', array(
            'std' => '<button id="add_default_styles" class="button">Add default styles</button>',
            'type' => 'custom_html',
        ), $meta_box_and_tab);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function customize_register($wp_customize){
		$wp_customize->remove_section('fl-presets');
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

    static public function init(){
        add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rest_api_init(){
        register_rest_route('ldc-aio/v1', '/add-default-styles', array(
            'callback' => array(__CLASS__, 'add_default_styles'),
            'methods' => 'GET',
            'permission_callback' => function(){
                return current_user_can('manage_options');
            },
        ));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Beaver_Builder_Theme::init();
