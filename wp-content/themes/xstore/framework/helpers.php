<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');


/*
* Function include template file
* firstly look in framework/templates/
* then theme/templates/
* ******************************************************************* */

if(!function_exists('etheme_load_template')) {
    function etheme_load_template($name, $data = array()) {
        $file_name = $name . '.php';

        extract( $data );

        $framework_file = apply_filters('etheme_file_url', ETHEME_TEMPLATES . $file_name ) ;
        $in_theme_file = apply_filters('etheme_file_url', ETHEME_TEMPLATES_THEME . $file_name ) ;

        if( file_exists( $in_theme_file ) ) {
            include $in_theme_file;
        } else if( file_exists( $framework_file ) ) {
            include $framework_file;
        } else {
            echo 'can\'t find the file ' . $file_name;
        }
    }
}

/*
* Load Shortcode file
* ******************************************************************* */

if(!function_exists('etheme_load_shortcode')) {
	function etheme_load_shortcode($name) {
		$file = ETHEME_CODE_SHORTCODES . $name.'.php';
		if ( ( ETHEME_BASE != ETHEME_CHILD ) && file_exists(trailingslashit(ETHEME_CHILD).$file) ) {
			$path = trailingslashit(ETHEME_CHILD).$file;
			require_once($path) ;
		}
		elseif(file_exists(trailingslashit(ETHEME_BASE).$file)) {
			$path = trailingslashit(ETHEME_BASE).$file;
			require_once($path) ;
		}
	}
}


/*
* Get theme option 
* ******************************************************************* */

if(!function_exists('etheme_get_option')) {
	function etheme_get_option($key, $setting = null,$doshortcode = false) {
	  	global $et_options;
  		$result = '';
  		
  		if(!empty($et_options[$key])) {
	    	if($doshortcode){
	        	$result = do_shortcode($et_options[$key]);
	    	}else{
	        	$result =  $et_options[$key];
	    	}
  		}
    	return apply_filters('et_option_'.$key, $result);
	}
}

if(!function_exists('etheme_option')) {
	function etheme_option($key, $setting = null,$doshortcode = true) {
		echo etheme_get_option($key, $setting, $doshortcode);
	}
}

/*
* Get custom meta for posts
* ******************************************************************* */

if(!function_exists('etheme_get_custom_field')) {
	function etheme_get_custom_field($field, $postid = false) {
		global $post;

		if ( null === $post && !$postid) return FALSE;

		if(!$postid) {
			$postid = $post->ID;
		} 

		$custom_field = get_post_meta($postid, ETHEME_PREFIX . $field, true);
		
		if(is_array($custom_field)) {
			$custom_field = $custom_field[0];
		}
		if ( $custom_field ) {
			return stripslashes( wp_kses_decode_entities( $custom_field ) );
		}
		else {
			return FALSE;
		}
	}
}

if(!function_exists('etheme_custom_field')) {
	function etheme_custom_field($field) {
		echo etheme_get_custom_field($field);
	}
}

/*
* Get page by template
* ******************************************************************* */

if(!function_exists('etheme_tpl2id')) {
	function etheme_tpl2id($tpl){
		global $wpdb;
		
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $tpl
		));
		foreach($pages as $page){
			return $page->ID;
		}
	}
}

/*
* Get file from child theme
* ******************************************************************* */

if(!function_exists('etheme_childtheme_file')) {
	add_filter('etheme_file_url', 'etheme_childtheme_file', 10, 1);
	
	function etheme_childtheme_file($file) {
		if ( ( ETHEME_BASE != ETHEME_CHILD ) && file_exists(trailingslashit(ETHEME_CHILD).$file) )
			$url = trailingslashit(ETHEME_CHILD).$file;
		else 
			$url = trailingslashit(ETHEME_BASE).$file;
		return $url;
	}
}


/*
* Get sidebars list for options
* ******************************************************************* */

if(!function_exists('etheme_get_sidebars')) {
	function etheme_get_sidebars() {
		global $wp_registered_sidebars;
		$sidebars[] = '--Choose--';
		foreach( $wp_registered_sidebars as $id=>$sidebar ) {
			$sidebars[ $id ] = $sidebar[ 'name' ];
        }
        return $sidebars;
	}
}

/*
* Get revolution sliders list for options
* ******************************************************************* */

if(!function_exists('etheme_get_revsliders')) {
	function etheme_get_revsliders() {
		global $wpdb;
	    if(class_exists('RevSliderAdmin')) {
	    	
	    	$rs = $wpdb->get_results( 
	    		"
	    		SELECT id, title, alias
	    		FROM ".$wpdb->prefix."revslider_sliders
	    		ORDER BY id ASC LIMIT 100
	    		"
	    	);
	    	$revsliders = array(
	    		'no_slider' => 'No Slider'
	    	);
	    	if ($rs) {
		    	$_ri = 1;
		    	foreach ( $rs as $slider ) {
		    	  	$revsliders[$slider->alias] = $slider->title;
		    		$_ri++;
		    	}
	    	}
	    	
	        return $revsliders;
	    } else {
		    return array('' => 'You need to install Revolution Slider plugin');
	    }
	}
}

/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB options array
 */
 
if(!function_exists('etheme_get_post_options')) {
	function etheme_get_post_options($query_args ) {
	
	    $args = wp_parse_args( $query_args, array(
	        'post_type' => 'post',
	        'numberposts' => 10,
	    ) );

	    $post_options = array();
	    $post_options[''] = "Inherit";
	    $post_options['without'] = "Without";  
	
	    $posts = get_posts( $args );
	
	    if ( $posts ) {
	        foreach ( $posts as $post ) {
				$post_options[$post->ID] = $post->post_title;
	        }
	    }
	
	    return $post_options;
	}
}

/*
* Styled print array
* ******************************************************************* */

if(!function_exists('pr')) {
    function pr($arr) {
        echo '<pre>';
            print_r($arr);
        echo '</pre>';   
    }
}

/*
* Get HTTP or HTTPS
* ******************************************************************* */

if(!function_exists('etheme_http')) {
	function etheme_http() {
		return (is_ssl())?"https://":"http://";
	}
}

/*
* Trunc string for some words number
* ******************************************************************* */

if(!function_exists('etheme_trunc')) {
    function etheme_trunc($phrase, $max_words) {
       $phrase_array = explode(' ',$phrase);
       if(count($phrase_array) > $max_words && $max_words > 0)
          $phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).' ...';
       return $phrase;
    }
}

if(! function_exists('etheme_strip_shortcodes')) {
	function etheme_strip_shortcodes($content ) {
		if ( false === strpos( $content, '[' ) ) {
			return $content;
		}

		$content = preg_replace("/\[[^\]]*\]/", '', $content);  # strip shortcodes, keep shortcode content

		return $content;
	}
}

/*
* Convert CSS/JS code to string code
* ******************************************************************* */

if(!function_exists('etheme_js2tring')) {
    function etheme_js2tring($str='') {
        return trim(preg_replace("/('|\"|\r?\n)/", '', $str)); 
    } 
}