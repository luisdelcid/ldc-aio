<?php

class LDC_AIO_Admin_Search_Meta {

	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function after_setup_theme(){
        $meta_box_and_tab = 'Admin Search Meta';
        LDC_AIO_One::add_setting('search_post_metadata', array(
        	'name' => 'Search Post Metadata?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        LDC_AIO_One::add_setting('search_user_metadata', array(
        	'name' => 'Search User Metadata?',
        	'on_label' => '<i class="dashicons dashicons-yes"></i>',
        	'style' => 'square',
        	'type' => 'switch',
        ), $meta_box_and_tab);
        $search_post_metadata = LDC_AIO_One::get_setting('search_post_metadata');
        if($search_post_metadata){
            add_filter('posts_groupby', array(__CLASS__, 'posts_groupby'));
            add_filter('posts_join', array(__CLASS__, 'posts_join'));
            add_filter('posts_where', array(__CLASS__, 'posts_where'));
        }
        $search_user_metadata = LDC_AIO_One::get_setting('search_user_metadata');
        if($search_user_metadata){
            add_filter('users_pre_query', array(__CLASS__, 'users_pre_query'), 10, 2);
        }
    }

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function init(){
        add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function posts_groupby($groupby){
        global $pagenow, $wpdb;
    	if(is_admin() and $pagenow === 'edit.php' and is_search()){
    		$g = $wpdb->posts . '.ID';
    		if(!$groupby){
    			$groupby = $g;
    		} else {
    			$groupby = trim($groupby) . ', ' . $g;
    		}
    	}
    	return $groupby;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function posts_join($join){
        global $pagenow, $wpdb;
    	if(is_admin() and $pagenow === 'edit.php' and is_search()){
    		$j = 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id';
    		if(!$join){
    			$join = $j;
    		} else {
    			$join = trim($join) . ' ' . $j;
    		}
    	}
    	return $join;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function posts_where($where){
        global $pagenow, $wpdb;
    	if(is_admin() and $pagenow === 'edit.php' and is_search()){
    		$s = get_query_var('s');
    		$s = $wpdb->esc_like($s);
    		$s = '%' . $s . '%';
    		$str = '(' . $wpdb->posts . '.post_title LIKE %s)';
    		$sql = $wpdb->prepare($str, $s);
    		$search = $sql;
    		$str = '(' . $wpdb->postmeta . '.meta_value LIKE %s)';
    		$sql = $wpdb->prepare($str, $s);
    		$replace = $search . ' OR ' . $sql;
    		$where = str_replace($search, $replace, $where);
    	}
    	return $where;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    static public function users_pre_query($results, $user_query){
        global $pagenow, $wpdb;
    	if(is_admin() and $pagenow === 'users.php' and $user_query->get('search') and is_null($user_query->results)){
    		$j = 'LEFT JOIN ' . $wpdb->usermeta . ' ON ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id';
    		$user_query->query_from .= ' ' . $j;
    		$s = $user_query->get('search');
    		$s = str_replace('*', '%', $s);
    		$str = 'user_login LIKE %s';
    		$sql = $wpdb->prepare($str, $s);
    		$search = $sql;
    		$str = 'meta_value LIKE %s';
    		$sql = $wpdb->prepare($str, $s);
    		$replace = $search . ' OR ' . $sql;
    		$user_query->query_where = str_replace($search, $replace, $user_query->query_where);
    		$user_query->query_where .= ' GROUP BY ID';
    	}
    	return $results;
	}

    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

}

LDC_AIO_Admin_Search_Meta::init();
