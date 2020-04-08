<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Must be refactored
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

class LDC_AIO_Confirm_User_Email {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//
	// CONSTANTS
	//
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	const ACTION = 'wpt-confirm-user-email';
	const PREFIX = 'wpt_confirm_user_email_';

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//
	// INIT
	//
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function init(){
        $meta_box_and_tab = 'Confirm User Email';
        LDC_AIO_One::add_setting('confirm_user_email', array(
            'name' => 'Confirm User Email?',
            'on_label' => '<i class="dashicons dashicons-yes"></i>',
            'style' => 'square',
            'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('confirm_user_email_capability', array(
        	'name' => '— Minimum capability required to bypass the User Email Confirmation:',
        	'std' => 'manage_options',
        	'type' => 'text',
            'visible' => array('confirm_user_email', true),
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('confirm_user_email')){
            add_action('user_request_action_confirmed', array(__CLASS__, 'user_request_action_confirmed'));
    		add_action('validate_password_reset', array(__CLASS__, 'validate_password_reset'), 10, 2);
    		add_filter('authenticate', array(__CLASS__, 'authenticate'), 100, 2);
    		add_filter('shake_error_codes', array(__CLASS__, 'shake_error_codes'));
    		add_filter('user_request_action_confirmed_message', array(__CLASS__, 'user_request_action_confirmed_message'), 10, 2);
    		add_filter('user_request_action_description', array(__CLASS__, 'user_request_action_description'), 10, 2);
        }
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//
	// ACTIONS
	//
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function user_request_action_confirmed($request_id = 0){
		if(function_exists('wp_get_user_request')){
			$request = wp_get_user_request($request_id);
		} elseif(function_exists('wp_get_user_request_data')){
			$request = wp_get_user_request_data($request_id);
		} else {
			$request = false;
		}
		if(!$request){
			return new WP_Error('invalid_request', __('Invalid user request.'));
		}
		update_post_meta($request_id, '_wp_user_request_completed_timestamp', time());
		$result = wp_update_post(array(
			'ID' => $request_id,
			'post_status' => 'request-completed',
		));
		return $result;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function validate_password_reset($errors, $user){
		if(!$errors->has_errors() && $user instanceof WP_User && isset($_POST['pass1']) && !empty($_POST['pass1']) && !user_can($user->ID, LDC_AIO_One::get_setting('confirm_user_email_capability')) && !self::completed_user_requests($user)){
			self::fake($user);
		}
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//
	// FILTERS
	//
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function authenticate($user, $username){
		if($user instanceof WP_User){
			if(!user_can($user->ID, LDC_AIO_One::get_setting('confirm_user_email_capability'))){
				if(!self::completed_user_requests($user)){
					return self::tmp($user);
				}
			}
		} elseif(is_wp_error($user)) {
			if(username_exists($username)){
				$tmp_user = get_user_by('login', $username);
				if(!self::completed_user_requests($tmp_user)){
					return self::tmp($tmp_user);
				}
			} elseif(email_exists($username)){
				$tmp_user = get_user_by('email', $username);
				if(!self::completed_user_requests($tmp_user)){
					return self::tmp($tmp_user);
				}
			}
		}
		return $user;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function shake_error_codes($shake_error_codes){
		$shake_error_codes[] = self::PREFIX . 'pending';
		return $shake_error_codes;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function user_request_action_confirmed_message($message = '', $request_id = 0){
		if(function_exists('wp_get_user_request')){
			$request = wp_get_user_request($request_id);
		} elseif(function_exists('wp_get_user_request_data')){
			$request = wp_get_user_request_data($request_id);
		} else {
			$request = false;
		}
		$redirect_to = '';
		if($request){
			$request_data = $request->request_data;
			if(!empty($request_data['redirect_to'])){
				$redirect_to = $request_data['redirect_to'];
			}
		}
		$message  = '<p class="success">Gracias por confirmar tu dirección de correo electrónico. <a href="' . esc_url(wp_login_url($redirect_to)) . '" alt="' . __('Log in') . '">' . __('Log in') . '</a></p>';
		return $message;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function user_request_action_description($description, $action_name){
		if($action_name == self::ACTION){
			$description = __('Confirma tu dirección de correo electrónico');
		}
		return $description;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	//
	// PRIVATE METHODS
	//
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function cleanup_user_requests($user_id = 0){
		$user = self::get_user($user_id);
		$expires = (int) apply_filters('user_request_key_expiration', DAY_IN_SECONDS);
		$ids = get_posts(array(
			'author' => $user->ID,
			'date_query' => array(
				array(
					'before' => $expires . ' seconds ago',
					'column' => 'post_modified_gmt',
				),
			),
			'fields' => 'ids',
			'post_name__in' => array(self::ACTION),
			'post_status' => 'request-pending',
			'post_type' => 'user_request',
			'posts_per_page' => -1,
		));
		if($ids){
			foreach($ids as $id){
				wp_update_post(array(
					'ID' => $id,
					'post_password' => '',
					'post_status' => 'request-failed',
				));
			}
		}
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function completed_user_requests($user_id = 0){
		$user = self::get_user($user_id);
		return get_posts(array(
			'author' => $user->ID,
			'fields' => 'ids',
			'post_name__in' => array(self::ACTION),
			'post_status' => 'request-completed',
			'post_type' => 'user_request',
			'posts_per_page' => -1,
		));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function create_user_request($user_id = 0, $redirect_to = ''){
		$user = self::get_user($user_id);
		self::cleanup_user_requests($user->ID);
		return wp_create_user_request($user->user_email, self::ACTION, array(
			'redirect_to' => $redirect_to,
		));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function fake($user_id = 0){
		$user = self::get_user($user_id);
		$request_id = self::create_user_request($user);
		if(is_wp_error($request_id)){
			return $request_id;
		}
		update_post_meta($request_id, '_wp_user_request_completed_timestamp', time());
		$result = wp_update_post(array(
			'ID' => $request_id,
			'post_status' => 'request-completed',
		));
		return $result;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function get_user($user_id = 0){
		if(empty($user_id) && function_exists('wp_get_current_user')){
			return wp_get_current_user();
		} elseif($user_id instanceof WP_User){
			return $user_id;
		} elseif($user_id && is_numeric($user_id)){
			return get_user_by('id', $user_id);
		}
		return false;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function incompleted_user_requests($user_id = 0){
		$user = self::get_user($user_id);
		$expires = (int) apply_filters('user_request_key_expiration', DAY_IN_SECONDS);
		return get_posts(array(
			'author' => $user->ID,
			'date_query' => array(
				array(
					'after' => $expires . ' seconds ago',
					'column' => 'post_modified_gmt',
					'inclusive' => true,
				),
			),
			'fields' => 'ids',
			'post_name__in' => array(self::ACTION),
			'post_status'   => array(
                'request-pending',
                'request-confirmed',
            ),
			'post_type' => 'user_request',
			'posts_per_page' => -1,
		));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function tmp($user_id = 0){
		$user = self::get_user($user_id);
		if(self::incompleted_user_requests($user)){
			$message = '<strong>' . __('Warning:') . '</strong> ' . sprintf(__('Your email address has not been updated yet. Please check your inbox at %s for a confirmation email.'), '<code>' . esc_html($user->user_email) . '</code>');
			$message = str_replace('updated', 'confirmed', $message); // en fix
			$message = str_replace('actualizado', 'confirmado', $message); // es fix
			return new WP_Error(self::PREFIX . 'pending', $message, 'message');
		} else {
			$redirect_to = '';
			if(!empty($_REQUEST['redirect_to'])){
				$redirect_to = $_REQUEST['redirect_to'];
			}
			$request_id = self::create_user_request($user, $redirect_to);
			if(is_wp_error($request_id)){
				return $request_id;
			}
			$result = self::send_user_request($request_id);
			if(is_wp_error($result)){
				return $result;
			}
			$message = '<strong>' . __('Warning:') . '</strong> Confirma tu dirección de correo electrónico. ' . __('Check your email for the confirmation link.');
			return new WP_Error(self::PREFIX . 'pending', $message, 'message');
		}
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static private function send_user_request($request_id = 0){
		return wp_send_user_request($request_id);
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Confirm_User_Email::init();
