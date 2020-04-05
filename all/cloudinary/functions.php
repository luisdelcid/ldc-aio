<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('add_ldc_cl_image_size')){
    function add_ldc_cl_image_size($name = '', $args = array()){
        LDC_AIO_Cloudinary::add_image_size($name, $args);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
