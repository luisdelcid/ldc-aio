<?php

class LDC_AIO_Beaver_Builder_Theme {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
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

LDC_AIO_Beaver_Builder_Theme::init();
