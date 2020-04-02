<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_google_client')){
	function ldc_google_client($config = array()){
        if(class_exists('Google_Client')){
            return new Google_Client($config);
        }
		return null;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_google_application_credentials')){
	function ldc_google_application_credentials($file = ''){
		if(file_exists($file)){
			putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $file);
			return true;
		}
		return false;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
