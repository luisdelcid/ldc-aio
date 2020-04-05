<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_attachment_url_to_postid')){
	function ldc_attachment_url_to_postid($url = ''){
		if($url){
			/** original */
			$post_id = ldc_guid_to_postid($url);
			if($post_id){
				return $post_id;
			}
            /** resized */
			preg_match('/^(.+)(-\d+x\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches);
			if($matches){
				$url = $matches[1];
				if(isset($matches[3])){
					$url .= $matches[3];
				}
                $post_id = ldc_guid_to_postid($url);
				if($post_id){
					return $post_id;
				}
			}
			/** scaled */
			/*preg_match('/^(.+)(-scaled)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches);
			if($matches){
				$url = $matches[1];
				if(isset($matches[3])){
					$url .= $matches[3];
				}
                $post_id = ldc_guid_to_postid($url);
				if($post_id){
					return $post_id;
				}
			}*/
			/** edited */
			preg_match('/^(.+)(-e\d+)(\.' . substr($url, strrpos($url, '.') + 1) . ')?$/', $url, $matches);
			if($matches){
				$url = $matches[1];
				if(isset($matches[3])){
					$url .= $matches[3];
				}
                $post_id = ldc_guid_to_postid($url);
				if($post_id){
					return $post_id;
				}
			}
		}
		return 0;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_guid_to_postid')){
	function ldc_guid_to_postid($guid = ''){
        global $wpdb;
		if($guid){
			$str = "SELECT ID FROM $wpdb->posts WHERE guid = %s";
			$sql = $wpdb->prepare($str, $guid);
			$post_id = $wpdb->get_var($sql);
			if($post_id){
				return (int) $post_id;
			}
		}
		return 0;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_maybe_require_media_functions')){
	function ldc_maybe_require_media_functions(){
		if(!is_admin()){
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/media.php');
		}
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_sideload_file')){
    function ldc_sideload_file($url = '', $name = '', $post = null){
        ldc_maybe_require_media_functions();
    	$file_array = array(
    		'tmp_name' => download_url($url)
    	);
		if(is_wp_error($file_array['tmp_name'])){
			return $file_array['tmp_name'];
		}
    	$file_array['name'] = $name ? $name : basename($url);
		$ext = pathinfo($file_array['name'], PATHINFO_EXTENSION);
		if(!$ext and extension_loaded('fileinfo')){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$real_mime = finfo_file($finfo, $file_array['tmp_name']);
			finfo_close($finfo);
			foreach(wp_get_mime_types() as $exts => $mime){
				if($mime == $real_mime){
					$exts = explode('|', $exts);
					$file_array['name'] .= '.' . $exts[0];
					break;
				}
			}
		}
		$post = get_post($post);
		$post_id = $post ? $post->ID : 0;
		$attachment_id = media_handle_sideload($file_array, $post_id);
		if(is_wp_error($attachment_id)){
			@unlink($file_array['tmp_name']);
			return $attachment_id;
		} else {
			return $attachment_id;
		}
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
