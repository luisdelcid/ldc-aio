<?php

class LDC_AIO_Roles_and_Capabilities {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Roles and Capabilities';
        LDC_AIO_One::add_setting('hide_dashboard', array(
        	'name' => 'Hide the Dashboard?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_dashboard_capability', array(
        	'name' => '— Minimum capability required to access the Dashboard:',
        	'std' => 'edit_posts',
        	'type' => 'text',
            'visible' => array('hide_dashboard', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_dashboard')){
            add_action('admin_init', function(){
				if(wp_doing_ajax()){
					return;
				}
                if(current_user_can(LDC_AIO_One::get_setting('hide_dashboard_capability'))){
                    return;
                }
                wp_safe_redirect(home_url());
                exit;
			});
        }
        LDC_AIO_One::add_setting('hide_toolbar', array(
        	'name' => 'Hide the Toolbar?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_toolbar_capability', array(
        	'name' => '— Minimum capability required to view the Toolbar:',
        	'std' => 'edit_posts',
        	'type' => 'text',
            'visible' => array('hide_toolbar', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_toolbar')){
            add_filter('show_admin_bar', function($show){
				if(!current_user_can(LDC_AIO_One::get_setting('hide_toolbar_capability'))){
					return false;
				}
				return $show;
			});
        }
        LDC_AIO_One::add_setting('hide_media', array(
        	'name' => 'Hide others Media?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_media_capability', array(
        	'name' => '— Minimum capability required to view others attachments:',
        	'std' => 'edit_posts',
        	'type' => 'text',
            'visible' => array('hide_media', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_media')){
            add_filter('ajax_query_attachments_args', function($query){
				if(!current_user_can(LDC_AIO_One::get_setting('hide_media_capability'))){
					$query['author'] = get_current_user_id();
				}
				return $query;
			});
        }
        LDC_AIO_One::add_setting('hide_posts', array(
        	'name' => 'Hide others Posts?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_posts_capability', array(
        	'name' => '— Minimum capability required to view others posts:',
        	'std' => 'edit_posts',
        	'type' => 'text',
            'visible' => array('hide_posts', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_posts')){
            add_action('current_screen', function(){
                global $pagenow;
            	if($pagenow != 'edit.php'){
            		return;
            	}
            	$current_screen = get_current_screen();
            	add_filter('views_' . $current_screen->id, function($views){
            		if(!current_user_can(LDC_AIO_One::get_setting('hide_posts_capability'))){
            			foreach($views as $index => $view){
            				$views[$index] = preg_replace('/ <span class="count">\([0-9]+\)<\/span>/', '', $view);
            			}
            		}
            		return $views;
            	});
            });
            add_filter('pre_get_posts', function($query){
                global $pagenow;
                if('edit.php' != $pagenow or !$query->is_admin){
                    return $query;
                }
                if(!current_user_can(LDC_AIO_One::get_setting('hide_posts_capability'))){
                    $query->set('author', get_current_user_id());
                }
                return $query;
            });
        }
        LDC_AIO_One::add_setting('hide_site', array(
        	'name' => 'Hide the front end?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_site_capability', array(
        	'name' => '— Minimum capability required to view the front end:',
        	'std' => 'read',
        	'type' => 'text',
            'visible' => array('hide_site', true),
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_site_excluded', array(
            'multiple' => true,
			'name' => '— Excluded pages:',
			'placeholder' => 'Select pages',
			'post_type' => 'page',
			'type' => 'post',
			'visible' => array('hide_site', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_site')){
            add_action('template_redirect', function(){
				if(!current_user_can(LDC_AIO_One::get_setting('hide_site'))){
                    if(!in_array(get_the_ID(), LDC_AIO_One::get_setting('hide_site_excluded'))){
                        auth_redirect();
                    }
				}
			});
        }
        LDC_AIO_One::add_setting('hide_rest_api', array(
        	'name' => 'Hide the REST API?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('hide_rest_api_capability', array(
        	'name' => '— Minimum capability required to access the REST API:',
        	'std' => 'read',
        	'type' => 'text',
            'visible' => array('hide_rest_api', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('hide_rest_api')){
            add_filter('rest_authentication_errors', function($error){
				if($error){
					return $error;
				}
				if(!current_user_can(LDC_AIO_One::get_setting('hide_rest_api_capability'))){
					return new WP_Error('rest_not_logged_in', __('You are not currently logged in.'), array(
						'status' => 401,
					));
				}
				return null;
			});
        }
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Roles_and_Capabilities::init();
