<?php
/*
Author: Luis del Cid
Author URI: https://luisdelcid.com
Description: A collection of useful functions for your WordPress theme's functions.php.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network:
Plugin Name: LDC AIO
Plugin URI: https://luisdelcid.com/aio/
Text Domain: ldc-aio
Version: 0.3.26.4
*/

// Make sure we don't expose any info if called directly
defined('ABSPATH') or die('Hi there! I\'m just a plugin, not much I can do when called directly.');

define('LDC_AIO_VERSION', '0.3.26.4');
define('LDC_AIO_FILE', __FILE__);
define('LDC_AIO_BASENAME', plugin_basename(LDC_AIO_FILE));
define('LDC_AIO_DIR', plugin_dir_path(LDC_AIO_FILE));
define('LDC_AIO_NAME', 'LDC AIO');
define('LDC_AIO_SLUG', basename(LDC_AIO_BASENAME, '.php'));
define('LDC_AIO_URL', plugin_dir_url(LDC_AIO_FILE));

require_once(LDC_AIO_DIR . 'one/one.php');
require_once(LDC_AIO_DIR . 'one/functions.php');

foreach(glob(LDC_AIO_DIR . 'all/*', GLOB_ONLYDIR) as $dir){
    $file = $dir . '/' . basename($dir) . '.php';
    if(file_exists($file)){
        require_once($file);
    }
    $file = $dir . '/functions.php';
    if(file_exists($file)){
        require_once($file);
    }
}
