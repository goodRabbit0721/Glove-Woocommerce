<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Testimonials Widget
// **********************************************************************// 


// **********************************************************************// 
// ! Register New Element: Testimonials Widget
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_testimonials');
if(!function_exists('etheme_register_vc_testimonials')) {
	function etheme_register_vc_testimonials() {
		if(!function_exists('vc_map')) return;
	    $testimonials_params = array(
	      'name' => 'Testimonials widget',
	      'base' => 'testimonials',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-testimonials.png',
	      'category' => 'Eight Theme',
	      'params' => array(
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Limit", 'xstore'),
	          "param_name" => "limit",
	          "description" => esc_html__('How many testimonials to show? Enter number.', 'xstore')
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Display type", 'xstore' ),
	          "param_name" => "type",
	          "value" => array( 
	              "", 
	              esc_html__("Slider", 'xstore') => 'slider',
	              esc_html__("Slider with grid", 'xstore') => 'slider-grid',
	              esc_html__("Grid", 'xstore') => 'grid'
	            )
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Interval", 'xstore'),
	          "param_name" => "interval",
	          "description" => esc_html__('Interval between slides. In milliseconds. Default: 10000', 'xstore'),
	          "dependency" => Array('element' => "type", 'value' => array('slider', 'slider-grid'))
	        ),
			  array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Show Control Navigation", 'xstore' ),
				  "param_name" => "navigation",
				  "dependency" => Array('element' => "type", 'value' => array('slider', 'slider-grid')),
				  "value" => array(
					  "",
					  esc_html__("Hide", 'xstore') => false,
					  esc_html__("Show", 'xstore') => true
				  )
			  ),
			  array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Color scheme", 'xstore' ),
				  "param_name" => "color_scheme",
				  "value" => array(
					  esc_html__("Dark", 'xstore') => 'dark',
					  esc_html__("Light", 'xstore') => 'light'
				  )
			  ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Category", 'xstore'),
	          "param_name" => "category",
	          "description" => esc_html__('Display testimonials from category.', 'xstore')
	        ),
	      )
	
	    );  
	
	    vc_map($testimonials_params);
	}
}