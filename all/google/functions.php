<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_google_client')){
	function ldc_google_client($config = array()){
		require_once(LDC_AIO_DIR . 'in/google-api-php-client/vendor/autoload.php');
		return new Google_Client($config);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
