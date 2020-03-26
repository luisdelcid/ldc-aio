<?php

class LDC_AIO_One {

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function add_admin_notice($admin_notice = '', $class = 'error', $is_dismissible = false){
        if($admin_notice){
			if(!in_array($class, array('error', 'warning', 'success', 'info'))){
				$class = 'warning';
			}
			if($is_dismissible){
				$class .= ' is-dismissible';
			}
			$admin_notice = '<div class="notice notice-' . $class . '"><p>' . $admin_notice . '</p></div>';
            self::$admin_notices[] = $admin_notice;
		}
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function add_setting($field_id = '', $field = array(), $meta_box_and_tab = ''){
        if($field_id){
			$field_id = sanitize_title($field_id);
            $meta_box_id = self::maybe_add_meta_box($meta_box_and_tab);
            $tab_id = self::maybe_add_tab($meta_box_and_tab);
            if(empty($field['columns'])){
                $field['columns'] = 12;
            }
            $field['id'] = $field_id;
            self::$meta_boxes[$meta_box_id]['fields'][$field_id] = $field;
            return true;
        }
        return false;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	static public function admin_notices(){
        if(self::$admin_notices){
            foreach(self::$admin_notices as $admin_notice){
                echo $admin_notice;
            }
        }
	}

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function after_setup_theme(){
        add_action('admin_notices', array(__CLASS__, 'admin_notices'));
        add_filter('mb_settings_pages', array(__CLASS__, 'mb_settings_pages'));
        add_filter('rwmb_meta_boxes', array(__CLASS__, 'rwmb_meta_boxes'));
		self::add_setting('powered_by', array(
            'std' => '<p><img style="max-width: 150px; height: auto;" src="data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA3OTQuMjIgNDQ4LjExIj48cGF0aCBkPSJNOTA3LjE3LDU0NC4xMUE5Niw5NiwwLDEsMSw5NzQuOCwzODBsNDUuMjYtNDUuMjZhMTYwLDE2MCwwLDEsMCwuNSwyMjYuMjdMOTc1LjMsNTE1Ljc0QTk1LjczLDk1LjczLDAsMCwxLDkwNy4xNyw1NDQuMTFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjM1IC0xNjApIi8+PHBvbHlnb24gcG9pbnRzPSI3NzguNDkgNDE3LjcyIDc3OC40OCA0MTcuNzMgNzc4LjQ5IDQxNy43MiA3NzguNDkgNDE3LjcyIi8+PGNpcmNsZSBjeD0iNzYyLjIyIiBjeT0iMTk3LjgxIiByPSIzMiIvPjxjaXJjbGUgY3g9Ijc2Mi4yMiIgY3k9IjM3OC44MyIgcj0iMzIiLz48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNDQ4IiByeD0iMzIiLz48cGF0aCBkPSJNNTIzLDI4Ny43NWExNjAsMTYwLDAsMSwwLDE2MCwxNjBBMTYwLDE2MCwwLDAsMCw1MjMsMjg3Ljc1Wm0wLDI1NmE5Niw5NiwwLDEsMSw5Ni05NkE5Niw5NiwwLDAsMSw1MjMsNTQzLjc1WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTIzNSAtMTYwKSIvPjxyZWN0IHg9IjM4NCIgd2lkdGg9IjY0IiBoZWlnaHQ9IjQ0OCIgcng9IjMyIi8+PC9zdmc+" alt="' . LDC_AIO_NAME . '"></p><p>' . LDC_AIO_NAME . ' is proudly powered by <a href="https://luisdelcid.com" target="_blank">Luis del Cid</a>.</p>',
            'type' => 'custom_html',
        ));
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function get_setting($field_id = ''){
        $option_name = str_replace('-', '_', LDC_AIO_SLUG);
		$settings = get_option($option_name);
		if(isset($settings[$field_id])){
			return $settings[$field_id];
		}
        return false;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
		require_once(LDC_AIO_DIR . 'in/plugin-update-checker-4.9/plugin-update-checker.php');
		Puc_v4_Factory::buildUpdateChecker('https://github.com/luisdelcid/' . LDC_AIO_SLUG, LDC_AIO_FILE, LDC_AIO_SLUG);
        add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function is_current_screen(){
        if(is_admin()){
            $current_screen = get_current_screen();
            if($current_screen){
                if(str_replace('toplevel_page_', '', $current_screen->id) === LDC_AIO_SLUG or strpos($current_screen->id, LDC_AIO_SLUG . '_page_') === 0){
                    return true;
                }
            }
        }
        return false;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function mb_settings_pages($settings_pages){
        $tabs = self::$tabs;
        if(count($tabs) > 1){
            ksort($tabs);
            $general_id = LDC_AIO_SLUG . '-' . sanitize_title(__('General'));
            if(!empty($tabs[$general_id])){
                $general = $tabs[$general_id];
                unset($tabs[$general_id]);
                $tabs = array_merge(array(
                    $general_id => $general,
                ), $tabs);
            }
        }
        $settings_pages[] = array(
            'columns' => 1,
            'icon_url' => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA3OTQuMjIgNDQ4LjExIj48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPmxkYy00czwvdGl0bGU+PHBhdGggY2xhc3M9ImNscy0xIiBkPSJNOTA3LjE3LDU0NC4xMUE5Niw5NiwwLDEsMSw5NzQuOCwzODBsNDUuMjYtNDUuMjZhMTYwLDE2MCwwLDEsMCwuNSwyMjYuMjdMOTc1LjMsNTE1Ljc0QTk1LjczLDk1LjczLDAsMCwxLDkwNy4xNyw1NDQuMTFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjM1IC0xNjApIi8+PHBvbHlnb24gY2xhc3M9ImNscy0xIiBwb2ludHM9Ijc3OC40OSA0MTcuNzIgNzc4LjQ4IDQxNy43MyA3NzguNDkgNDE3LjcyIDc3OC40OSA0MTcuNzIiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMSIgY3g9Ijc2Mi4yMiIgY3k9IjE5Ny44MSIgcj0iMzIiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMSIgY3g9Ijc2Mi4yMiIgY3k9IjM3OC44MyIgcj0iMzIiLz48cmVjdCBjbGFzcz0iY2xzLTEiIHdpZHRoPSI2NCIgaGVpZ2h0PSI0NDgiIHJ4PSIzMiIvPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTUyMywyODcuNzVhMTYwLDE2MCwwLDEsMCwxNjAsMTYwQTE2MCwxNjAsMCwwLDAsNTIzLDI4Ny43NVptMCwyNTZhOTYsOTYsMCwxLDEsOTYtOTZBOTYsOTYsMCwwLDEsNTIzLDU0My43NVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0yMzUgLTE2MCkiLz48cmVjdCBjbGFzcz0iY2xzLTEiIHg9IjM4NCIgd2lkdGg9IjY0IiBoZWlnaHQ9IjQ0OCIgcng9IjMyIi8+PC9zdmc+',
            'id' => LDC_AIO_SLUG,
            'menu_title' => LDC_AIO_NAME,
            'option_name' => str_replace('-', '_', LDC_AIO_SLUG),
            'page_title' => __('General Settings'),
            'style' => 'no-boxes',
            'submenu_title' => __('General'),
            'tabs' => $tabs,
            'tab_style' => 'left',
        );
        return $settings_pages;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function rwmb_meta_boxes($meta_boxes){
        if(is_admin()){
            if(self::$meta_boxes){
                foreach(self::$meta_boxes as $meta_box){
                    $meta_box['fields'] = array_values($meta_box['fields']);
                    $meta_boxes[] = $meta_box;
                }
            }
        }
        return $meta_boxes;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    //
    // Private
    //
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static private function maybe_add_meta_box($meta_box = ''){
        if(!$meta_box){
            $meta_box = __('General');
        }
        $meta_box = wp_strip_all_tags($meta_box);
        $meta_box_id = LDC_AIO_SLUG . '-' . sanitize_title($meta_box);
        if(empty(self::$meta_boxes[$meta_box_id])){
            self::$meta_boxes[$meta_box_id] = array(
                'fields' => array(),
                'id' => $meta_box_id,
                'settings_pages' => LDC_AIO_SLUG,
                'tab' => $meta_box_id,
                'title' => $meta_box,
            );
        }
        return $meta_box_id;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static private function maybe_add_tab($tab = ''){
        if(!$tab){
            $tab = __('General');
        }
        $tab = wp_strip_all_tags($tab);
        $tab_id = LDC_AIO_SLUG . '-' . sanitize_title($tab);
        if(empty(self::$tabs[$tab_id])){
            self::$tabs[$tab_id] = $tab;
        }
        return $tab_id;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static private $admin_notices = array(), $meta_boxes = array(), $tabs = array();

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_One::init();
