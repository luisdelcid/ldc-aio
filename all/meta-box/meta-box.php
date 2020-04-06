<?php

class LDC_AIO_Meta_Box {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function format_single_value($field, $value, $args, $post_id){
        if($field['timestamp']){
            $value = self::from_timestamp($value, $field);
        } else {
            $value = array(
                'timestamp' => strtotime($value),
                'formatted' => $value,
            );
        }
        return empty($args['format']) ? $value['formatted'] : date_i18n($args['format'], $value['timestamp']);
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function format_value($field, $value, $args, $post_id){
        if(!$field['multiple']){
           return self::format_single_value($field, $value, $args, $post_id);
        }
        $output = '<ul>';
        foreach($value as $single){
           $output .= '<li>' . self::format_single_value($field, $single, $args, $post_id) . '</li>';
        }
        $output .= '</ul>';
        return $output;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function from_timestamp($meta, $field){
        return array(
            'timestamp' => $meta ? $meta : null,
            'formatted' => $meta ? date_i18n($field['php_format'], intval($meta)) : '',
        );
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Meta Box';
        LDC_AIO_One::add_setting('add_custom_fields', array(
        	'name' => 'Add custom fields?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('add_custom_fields')){
            add_action('init', function(){
                require_once(LDC_AIO_DIR . 'all/meta-box/custom-fields.php');
            });
            add_filter('rwmb_row_open_outer_html', array(__CLASS__, 'rwmb_row_open_outer_html'), 20, 2);
            add_filter('rwmb_row_close_outer_html', array(__CLASS__, 'rwmb_row_close_outer_html'), 20, 2);
            add_filter('rwmb_col_open_outer_html', array(__CLASS__, 'rwmb_col_open_outer_html'), 20, 2);
            add_filter('rwmb_col_close_outer_html', array(__CLASS__, 'rwmb_col_close_outer_html'), 20, 2);
            add_filter('rwmb_raw_html_outer_html', array(__CLASS__, 'rwmb_raw_html_outer_html'), 20, 2);
        }
        LDC_AIO_One::add_setting('fix_conditional_logic', array(
        	'name' => 'Fix conditional logic?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('fix_conditional_logic')){
            add_action('init', function(){
                if(!defined('MB_FRONTEND_SUBMISSION_DIR') and defined('MB_USER_PROFILE_DIR')){
    				define('MB_FRONTEND_SUBMISSION_DIR', MB_USER_PROFILE_DIR);
    			}
            }, 21);
        }
        LDC_AIO_One::add_setting('fix_validation', array(
        	'name' => 'Fix validation?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('fix_validation')){
            add_action('rwmb_enqueue_scripts', array(__CLASS__, 'rwmb_enqueue_scripts'));
        }
        LDC_AIO_One::add_setting('use_date_i18n', array(
        	'name' => 'Use date_i18n instead of date on date and datetime fields?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('use_date_i18n')){
            add_filter('rwmb_the_value', array(__CLASS__, 'rwmb_the_value'), 20, 4);
        }
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_enqueue_scripts($object){
        if(!empty($object->meta_box['validation'])){
            $url = LDC_AIO_DIR . 'all/meta-box/validate.js';
            wp_dequeue_script('rwmb-validate');
            wp_deregister_script('rwmb-validate');
            wp_enqueue_script('rwmb-validate', $url, array('jquery-validation', 'jquery-validation-additional-methods'), RWMB_VER . '-fixed.' . LDC_AIO_VERSION, true);
            if(is_callable(array('RWMB_Helpers_Field', 'localize_script_once'))){
                RWMB_Helpers_Field::localize_script_once('rwmb-validate', 'rwmbValidate', array(
                    'summaryMessage' => esc_html__('Please correct the errors highlighted below and try again.', 'meta-box'),
                ));
            } elseif(is_callable(array('RWMB_Helpers_Field', 'localize_script_once'))){
                RWMB_Field::localize_script('rwmb-validate', 'rwmbValidate', array(
                    'summaryMessage' => esc_html__('Please correct the errors highlighted below and try again.', 'meta-box'),
                ));
            }
        }
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_the_value($value, $field, $args, $object_id){
        $types = array(
           'date' => 'RWMB_Date_Field',
           'datetime' => 'RWMB_Datetime_Field',
       );
       if(array_key_exists($field['type'], $types)){
           $value = call_user_func(array($types[$field['type']], 'get_value'), $field, $args, $object_id);
           if(false === $value){
               return '';
           }
           return self::format_value($field, $value, $args, $object_id);
       }
       return $value;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_row_open_outer_html($outer_html, $field){
        if(is_admin()){
    		return '';
    	}
        $classes = 'form-row';
        if(!empty($field['class'])){
    		$classes .= ' ' . $field['class'];
    	}
    	return '<div class="' . $classes . '">';
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_row_close_outer_html($outer_html, $field){
        if(is_admin()){
    		return '';
    	}
    	return '</div>';
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_col_open_outer_html($outer_html, $field){
        if(is_admin()){
    		return '';
    	}
    	$classes = array();
    	foreach(array('col', 'col-sm', 'col-md', 'col-lg', 'col-xl', 'offset', 'offset-sm', 'offset-md', 'offset-lg', 'offset-xl') as $class){
    		if(isset($field[$class])){
    			if(is_numeric($field[$class])){
    				if(intval($field[$class]) >= 1 and intval($field[$class]) <= 12){
    					$classes[] = $class . '-' . $field[$class];
    				}
    			}
    		}
    	}
    	if(!$classes){
    		$classes[] = 'col';
    	}
        $classes = implode(' ', $classes);
        if(!empty($field['class'])){
    		$classes .= ' ' . $field['class'];
    	}
    	return '<div class="' . $classes . '">';
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_col_close_outer_html($outer_html, $field){
        if(is_admin()){
    		return '';
    	}
    	return '</div>';
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_raw_html_outer_html($outer_html, $field){
        if(is_admin()){
    		return '';
    	}
    	if(!empty($field['hide_on_mobile']) and wp_is_mobile()){
    		return '';
    	}
    	if(!isset($field['std'])){
    		$field['std'] = '';
    	}
    	return $field['std'];
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Meta_Box::init();
