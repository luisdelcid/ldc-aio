<?php

class LDC_AIO_Cloudinary {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    public static function add_image_size($name = '', $args = array()){
        $name = sanitize_title($name);
        $args = shortcode_atts(array(
            'name' => $name,
            'options' => array(),
        ), $args);
        ksort($args['options']);
        $args['options_md5'] = md5(ldc_base64_urlencode(wp_json_encode($args['options'])));
        self::$image_sizes[$name] = $args;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function fl_builder_photo_sizes_select($sizes){
        if(isset($sizes['full'])){
			$id = ldc_attachment_url_to_postid($sizes['full']['url']);
			if($id){
				if(self::$image_sizes and self::$config){
					foreach(self::$image_sizes as $name => $args){
						$image = get_post_meta($id, '_ldc_cl_image_' . $args['options_md5'], true);
						if($image and !isset($sizes[$name])){
							 $url = (isset($image['secure_url']) ? $image['secure_url'] : (isset($image['url']) ? $image['url'] : ''));
							 $width = (isset($image['width']) ? $image['width'] : 0);
							 $height = (isset($image['height']) ? $image['height'] : 0);
							 $sizes[$name] = array(
								'url' => $url,
								'filename' => $image['public_id'],
								'width' => $width,
								'height' => $height,
							 );
						}
					}
				}
			}
		}
		return $sizes;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function image_downsize($out, $id, $size){
        if(wp_attachment_is_image($id) and is_string($size) and isset(self::$image_sizes[$size]) and self::$config){
            $image = self::image_get_intermediate_size($id, $size);
            if($image){
                return $image;
            }
        }
		return $out;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function image_size_names_choose($sizes){
        if(self::$image_sizes and self::$config){
            foreach(self::$image_sizes as $name => $args){
                if(!isset($sizes[$name])){
                    $sizes[$name] = $args['name'];
                }
            }
        }
        return $sizes;
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        add_filter('fl_builder_photo_sizes_select', array(__CLASS__, 'fl_builder_photo_sizes_select'));
        add_filter('image_downsize', array(__CLASS__, 'image_downsize'), 10, 3);
        add_filter('image_size_names_choose', array(__CLASS__, 'image_size_names_choose'));
        $meta_box_and_tab = 'Cloudinary';
        LDC_AIO_One::add_setting('cloudinary_api_key', array(
        	'name' => 'API Key',
        	'type' => 'text',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('cloudinary_api_secret', array(
        	'name' => 'API Secret',
        	'type' => 'text',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('cloudinary_cloud_name', array(
        	'name' => 'Cloud Name',
        	'type' => 'text',
        ), $meta_box_and_tab);
        $api_key = apply_filters('ldc_cloudinary_api_key', LDC_AIO_One::get_setting('cloudinary_api_key'));
        $api_secret = apply_filters('ldc_cloudinary_api_secret', LDC_AIO_One::get_setting('cloudinary_api_secret'));
        $cloud_name = apply_filters('ldc_cloudinary_cloud_name', LDC_AIO_One::get_setting('cloudinary_cloud_name'));
        if($api_key and $api_secret and $cloud_name){
            require_once(LDC_AIO_DIR . 'in/cloudinary-php/vendor/autoload.php');
            \Cloudinary::config(array(
                'api_key' => $api_key,
                'api_secret' => $api_secret,
                'cloud_name' => $cloud_name,
            ));
            self::$config = true;
        }
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static private $config = false, $image_sizes = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static private function image_get_intermediate_size($id = 0, $size = ''){
        $image_size = self::$image_sizes[$size];
        $image = get_post_meta($id, '_ldc_cl_image_' . $image_size['options_md5'], true);
        if(!$image){
            $image = \Cloudinary\Uploader::upload(get_attached_file($id), $image_size['options']);
            if($image instanceof \Cloudinary\Error){
                return false;
            }
    		update_post_meta($id, '_ldc_cl_image_' . $image_size['options_md5'], $image);
        }
        $url = (isset($image['secure_url']) ? $image['secure_url'] : (isset($image['url']) ? $image['url'] : ''));
        $width = (isset($image['width']) ? $image['width'] : 0);
        $height = (isset($image['height']) ? $image['height'] : 0);
        if(!$url or !$width or !$height){
            return false;
        }
        return array($url, $width, $height, true);
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Cloudinary::init();
