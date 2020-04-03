<?php

class LDC_AIO_Media {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        $meta_box_and_tab = 'Media';
        LDC_AIO_One::add_setting('remove_filename_accents', array(
        	'name' => 'Remove filename accents?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $remove_filename_accents = LDC_AIO_One::get_setting('remove_filename_accents');
        if($remove_filename_accents){
            add_filter('sanitize_file_name', array(__CLASS__, 'sanitize_file_name'));
        }
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function sanitize_file_name($filename){
        return remove_accents($filename);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Media::init();
