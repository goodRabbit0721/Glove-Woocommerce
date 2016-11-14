<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Instagram
// **********************************************************************// 

function etheme_instagram_shortcode($atts, $content) {
    $args = shortcode_atts(array(
        'title'  => '',
        'username'  => '',
        'number'  => 9,
        'columns'  => 4,
        'size'  => 'thumbnail',
        'target'  => '',
        'slider'  => 0,
        'spacing'  => 0,
        'link'  => '',
    ), $atts);
    
    ob_start();

    the_widget( 'ETheme_Instagram_Widget', $args );

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}

// **********************************************************************// 
// ! Register New Element: Instagram
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_scslug');
if(!function_exists('etheme_register_vc_scslug')) {
	function etheme_register_vc_scslug() {
		if(!function_exists('vc_map')) return;
	    $params = array(
	      'name' => '[8THEME] Instagram',
	      'base' => 'instagram',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-instagram.png',
	      'category' => 'Eight Theme',
	      'params' => array(
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Title", 'xstore'),
	          "param_name" => "title",
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Username or hashtag", 'xstore'),
	          "param_name" => "username",
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Numer of photos", 'xstore'),
	          "param_name" => "number",
	        ),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Photo size', 'xstore' ),
				'param_name' => 'size',
				'value' => array(
					__( 'Thumbnail', 'xstore' ) => 'thumbnail',
					__( 'Medium', 'xstore' ) => 'medium',
					__( 'Large', 'xstore' ) => 'large',
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Columns', 'xstore' ),
				'param_name' => 'columns',
				'value' => array(
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Open links in', 'xstore' ),
				'param_name' => 'target',
				'value' => array(
					__( 'Current window (_self)', 'xstore' ) => '_self',
					__( 'New window (_blank)', 'xstore' ) => '_blank',
				),
			),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Link text", 'xstore'),
	          "param_name" => "link",
	        ),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Slider', 'xstore' ),
				'param_name' => 'slider',
				'value' => array(
					__( 'Yes', 'xstore' ) => 1,
				),
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Without spacing', 'xstore' ),
				'param_name' => 'spacing',
				'value' => array(
					__( 'Yes', 'xstore' ) => 1,
				),
			),
	      )
	
	    );  
	
	    vc_map($params);
	}
}
