<?php

class LDC_AIO_Media {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function image_size_names_choose($sizes){
        $sizes['hd'] = 'HD';
        $sizes['full-hd'] = 'Full HD';
		return $sizes;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Media';
        LDC_AIO_One::add_setting('add_hd_and_full_hd_image_size', array(
        	'name' => 'Add HD and Full HD image sizes?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('add_hd_and_full_hd_image_size')){
            add_image_size('hd', 1280, 1280);
            add_image_size('full-hd', 1920, 1920);
            add_filter('image_size_names_choose', array(__CLASS__, 'image_size_names_choose'));
        }
        LDC_AIO_One::add_setting('remove_filename_accents', array(
        	'name' => 'Remove filename accents?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('remove_filename_accents')){
            add_filter('sanitize_file_name', array(__CLASS__, 'sanitize_file_name'));
        }
        LDC_AIO_One::add_setting('solve_audio_video_conflicts', array(
        	'name' => 'Solve audio/video conflicts?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        if(LDC_AIO_One::get_setting('solve_audio_video_conflicts')){
            add_filter('wp_check_filetype_and_ext', array(__CLASS__, 'wp_check_filetype_and_ext'), 10, 5);
        }
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function sanitize_file_name($filename){
        return remove_accents($filename);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function wp_check_filetype_and_ext($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime){
        if($wp_check_filetype_and_ext['ext'] and $wp_check_filetype_and_ext['type']){
    		return $wp_check_filetype_and_ext;
    	}
        if(0 === strpos($real_mime, 'audio/') or 0 === strpos($real_mime, 'video/')){
            $filetype = wp_check_filetype($filename);
            if(in_array(substr($filetype['type'], 0, strcspn($filetype['type'], '/')), array('audio', 'video'))){
                $wp_check_filetype_and_ext['ext'] = $filetype['ext'];
    			$wp_check_filetype_and_ext['type'] = $filetype['type'];
            }
        }
    	return $wp_check_filetype_and_ext;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Media::init();
