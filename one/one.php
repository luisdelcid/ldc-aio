<?php

require_once(LDC_AIO_DIR . 'in/plugin-update-checker-4.9/plugin-update-checker.php');
Puc_v4_Factory::buildUpdateChecker('https://github.com/luisdelcid/' . LDC_AIO_SLUG, LDC_AIO_FILE, LDC_AIO_SLUG);

require_once(LDC_AIO_DIR . 'one/class-one.php');
LDC_AIO_One::init();

foreach(glob(LDC_AIO_DIR . 'all/*', GLOB_ONLYDIR) as $dir){
    $slug = basename($dir);
    $file = $dir . '/' . $slug . '.php';
    if(file_exists($file)){
        require_once($file);
    }
}
