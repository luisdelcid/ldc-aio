<?php

class LDC_AIO_Beaver_Builder_Theme {

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
                    $('#reboot_default_styles').on('click', function(e){
                        e.preventDefault();
						if(confirm('Are you sure?')){
							$('#reboot_default_styles').text('Wait...');
							$.ajax({
								beforeSend: function(xhr){
									xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
								},
								method: 'GET',
								url: wpApiSettings.root + 'ldc-aio/v1/reboot-default-styles',
							}).done(function(response){
								$('#reboot_default_styles').text('Done.');
								setTimeout(function(){
									$('#reboot_default_styles').text('Reboot default styles');
								}, 1000);
							});
						}
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
        LDC_AIO_One::add_setting('reboot_default_styles', array(
            'std' => '<button id="reboot_default_styles" class="button">Reboot default styles</button>',
            'type' => 'custom_html',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('remove_default_styles', array(
            'desc' => 'You must <a href="' . admin_url('options-general.php?page=fl-builder-settings#tools') . '" target="_blank">clear cache</a> for new settings to take effect.',
        	'name' => 'Remove default styles?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $remove_default_styles = LDC_AIO_One::get_setting('remove_default_styles');
        if($remove_default_styles){
            add_filter('fl_theme_compile_less_paths', array(__CLASS__, 'fl_theme_compile_less_paths'));
        }
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

    static public function reboot_default_styles(){
        $mods = get_theme_mods();
		$mods['fl-scroll-to-top'] = 'enable';
		$mods['fl-framework'] = 'bootstrap-4';
		$mods['fl-awesome'] = 'fa5';
        $mods['fl-body-bg-color'] = '#ffffff';
        $mods['fl-accent'] = '#007bff';
        $mods['fl-accent-hover'] = '#007bff';
		$mods['fl-heading-text-color'] = '#343a40';
		$mods['fl-heading-font-family'] = 'Open Sans';
        $mods['fl-h1-font-size'] = 40;
        $mods['fl-h1-font-size_medium'] = 33;
        $mods['fl-h1-font-size_mobile'] = 28;
        $mods['fl-h1-line-height'] = 1.2;
        $mods['fl-h1-line-height_medium'] = 1.2;
        $mods['fl-h1-line-height_mobile'] = 1.2;
        $mods['fl-h2-font-size'] = 32;
        $mods['fl-h2-font-size_medium'] = 28;
        $mods['fl-h2-font-size_mobile'] = 24;
        $mods['fl-h2-line-height'] = 1.2;
        $mods['fl-h2-line-height_medium'] = 1.2;
        $mods['fl-h2-line-height_mobile'] = 1.2;
        $mods['fl-h3-font-size'] = 28;
        $mods['fl-h3-font-size_medium'] = 25;
        $mods['fl-h3-font-size_mobile'] = 22;
        $mods['fl-h3-line-height'] = 1.2;
        $mods['fl-h3-line-height_medium'] = 1.2;
        $mods['fl-h3-line-height_mobile'] = 1.2;
        $mods['fl-h4-font-size'] = 24;
        $mods['fl-h4-font-size_medium'] = 22;
        $mods['fl-h4-font-size_mobile'] = 20;
        $mods['fl-h4-line-height'] = 1.2;
        $mods['fl-h4-line-height_medium'] = 1.2;
        $mods['fl-h4-line-height_mobile'] = 1.2;
        $mods['fl-h5-font-size'] = 20;
        $mods['fl-h5-font-size_medium'] = 19;
        $mods['fl-h5-font-size_mobile'] = 16;
        $mods['fl-h5-line-height'] = 1.2;
        $mods['fl-h5-line-height_medium'] = 1.2;
        $mods['fl-h5-line-height_mobile'] = 1.2;
        $mods['fl-h6-font-size'] = 16;
        $mods['fl-h6-font-size_medium'] = 16;
        $mods['fl-h6-font-size_mobile'] = 16;
        $mods['fl-h6-line-height'] = 1.2;
        $mods['fl-h6-line-height_medium'] = 1.2;
        $mods['fl-h6-line-height_mobile'] = 1.2;
        $mods['fl-body-text-color'] = '#6c757d';
        $mods['fl-body-font-family'] = 'Open Sans';
        $mods['fl-body-font-size'] = 16;
        $mods['fl-body-font-size_medium'] = 16;
        $mods['fl-body-font-size_mobile'] = 16;
        $mods['fl-body-line-height'] = 1.5;
        $mods['fl-body-line-height_medium'] = 1.5;
        $mods['fl-body-line-height_mobile'] = 1.5;
        update_option('theme_mods_' . get_option('stylesheet'), $mods);
		return array(
			'success' => true,
		);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rest_api_init(){
        register_rest_route('ldc-aio/v1', '/reboot-default-styles', array(
            'callback' => array(__CLASS__, 'reboot_default_styles'),
            'methods' => 'GET',
            'permission_callback' => function(){
                return current_user_can('manage_options');
            },
        ));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Beaver_Builder_Theme::init();
