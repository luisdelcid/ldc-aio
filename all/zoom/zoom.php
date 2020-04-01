<?php

class LDC_AIO_Zoom {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        require_once(LDC_AIO_DIR . 'all/zoom/zoom-api.php');
        $meta_box_and_tab = 'Zoom';
        LDC_AIO_One::add_setting('zoom_api_key', array(
        	'name' => 'Zoom API Key',
        	'type' => 'text',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('zoom_api_secret', array(
        	'name' => 'Zoom API Secret',
        	'type' => 'text',
        ), $meta_box_and_tab);
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Zoom::init();
