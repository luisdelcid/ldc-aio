<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_generate_jwt')){
	function ldc_generate_jwt($api_key = '', $api_secret = ''){
		if($api_key and $api_secret){
			require_once(LDC_AIO_DIR . 'in/php-jwt-5.2.0/vendor/autoload.php');
			$token = array(
				'exp' => time() + MINUTE_IN_SECONDS, // GMT time
				'iss' => $api_key,
			);
			return \Firebase\JWT\JWT::encode($token, $api_secret);
		}
		return '';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('ldc_zoom_request')){
	function ldc_zoom_request($api = '', $point = '', $arguments = array()){
		if($api and $point){
			$endpoints = array(
	            'dashboards' => array(
	                'list_meetings' => array(
	                    'endpoint' => '/metrics/meetings',
	                    'method' => 'GET',
	                    'parameters' => 0,
	                ),
	            ),
	        );
			$endpoints = apply_filters('ldc_zoom_endpoints', $endpoints);
	        if(!empty($endpoints[$api][$point])){
				$endpoint = $endpoints[$api][$point]['endpoint'];
		        $method = $endpoints[$api][$point]['method'];
		        $parameters = $endpoints[$api][$point]['parameters'];
		        if(count($arguments) >= $parameters){
					if($parameters){
						global $wpdb;
			            $args = array_splice($arguments, 0, $parameters);
			            $endpoint = $wpdb->prepare($endpoint, $args);
			            $offset = count($arguments) - $parameters;
			            $arguments = array_splice($arguments, -$offset);
			        }
					$api_key = LDC_AIO_One::get_setting('zoom_api_key');
					$api_key = apply_filters('ldc_zoom_api_key', $api_key);
					$api_secret = LDC_AIO_One::get_setting('zoom_api_secret');
					$api_secret = apply_filters('ldc_zoom_api_secret', $api_secret);
			        $jwt = ldc_generate_jwt($api_key, $api_secret);
			        if($jwt){
						$url = 'https://api.zoom.us/v2';
						$url = apply_filters('ldc_zoom_url', $url);
						$url = $url . $endpoint;
						$response = wp_remote_request($url, array(
							'body' => $arguments,
				            'headers' => array(
				                'Accept' => 'application/json',
				                'Authorization' => 'Bearer ' . $jwt,
				                'Content-Type' => 'application/json',
				            ),
				            'method' => $method,
				            'timeout' => 30,
				        ));
				        $response = ldc_parse_response($response);
				        $response = ldc_maybe_json_decode_response($response);
						return $response;
			        }
		        }
	        }
		}
		return ldc_response_error();
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
