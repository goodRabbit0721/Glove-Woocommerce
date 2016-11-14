<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! twitter
// **********************************************************************// 

function etheme_twitter_shortcode($atts, $content) {
		extract( shortcode_atts( array(
			'title' => '',
			'username' => '',
			'consumer_key' => '',
			'consumer_secret' => '',
			'user_token' => '',
			'user_secret' => '',
			'limit' => 10,
			'design' => 'slider',
			'class' => 10
		), $atts ) );

		$id = rand(100,999);
		
		if(empty($consumer_key) || empty($consumer_secret) || empty($user_token) || empty($user_secret) || empty($username)) {
			return esc_html__('Not enough information', 'xstore');
		}
		
		$tweets_array = etheme_get_tweets($consumer_key, $consumer_secret, $user_token, $user_secret, $username, $limit, 100, 'slider');
		
		$output = '';
		
		$output .= '<div class="et-twitter-'.$design.' '.$class.'">';
		if($title != '') {
			$output .= '<h2 class="twitter-slider-title"><span>'.$title.'</span></h2>';
		}
		
		
		$output .= '<ul class="et-tweets ' . $design.$id . '">';
		
		
		foreach($tweets_array as $tweet) {
			$output .= '<li class="et-tweet">';
			$output .= etheme_tweet_linkify($tweet['text']);
			$output .= '<div class="twitter-info">';
                            $output .= '<a href="'.$tweet['user']['url'].'" class="active" target="_blank">@'.$tweet['user']['screen_name'].'</a> '.date("l M j \- g:ia",strtotime($tweet['created_at']));
			$output .= '</div>';
			$output .= '</li>';
		}
		
		$output .= '</ul>';
			
		$output .= '</div>';

		if( $design == 'slider' ) {
			$items = '[[0, 1], [479,1], [619,1], [768,1],  [1200, 1], [1600, 1]]';
			$output .=  '<script type="text/javascript">';
			$output .=  '     jQuery(".'.$design.$id.'").owlCarousel({';
			$output .=  '         items:1, ';
			$output .=  '         navigation: true,';
			$output .=  '         navigationText:false,';
			$output .=  '         rewindNav: false,';
			$output .=  '         itemsCustom: '.$items.'';
			$output .=  '    });';
			$output .=  ' </script>';
		}
		
		
		return $output;
}




// **********************************************************************// 
// ! Register New Element: twitter
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_twitter');
if(!function_exists('etheme_register_vc_twitter')) {
	function etheme_register_vc_twitter() {
		if(!function_exists('vc_map')) return;
	    $params = array(
	      'name' => '[8THEME] Twitter',
	      'base' => 'twitter',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-twitter.png',
	      'category' => 'Eight Theme',
	      'params' => array(
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Title", 'xstore'),
	          "param_name" => "title"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Username", 'xstore'),
	          "param_name" => "username"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Customer Key", 'xstore'),
	          "param_name" => "consumer_key"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Customer Secret", 'xstore'),
	          "param_name" => "consumer_secret"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Access Token", 'xstore'),
	          "param_name" => "user_token"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Access Token Secret", 'xstore'),
	          "param_name" => "user_secret"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Number of tweets", 'xstore'),
	          "param_name" => "limit"
	        ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Design", 'xstore'),
              "param_name" => "design",
              "value" => array( 
                  esc_html__("Slider", 'xstore') => 'slider',
                  esc_html__("Grid", 'xstore') => 'grid',
                )
            ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Extra Class", 'xstore'),
	          "param_name" => "class",
	          "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xstore')
	        )
	      )
	
	    );  
	
	    vc_map($params);
	}
}
