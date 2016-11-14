<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

if( ! function_exists('vc_remove_element')) return;

add_action( 'init', 'etheme_VC_setup');

if(!function_exists('etheme_VC_setup')) {
	function etheme_VC_setup() {
		vc_remove_element("vc_tour");
	}
}

if( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	add_filter(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'etheme_vc_custom_css_class', 10, 3);
	if( ! function_exists('etheme_vc_custom_css_class') ) {
		function etheme_vc_custom_css_class( $classes, $base, $atts ) {
			if( ! empty( $atts['fixed_background'] ) ) {
				$classes .= ' et-parallax-' . $atts['fixed_background'];
			}
			if( ! empty( $atts['off_center'] ) ) {
				$classes .= ' off-center-' . $atts['off_center'];
			}
			return $classes;
		}
	}
}

// **********************************************************************//
// ! Add new option to vc_column
// **********************************************************************//
add_action( 'init', 'etheme_columns_options');
if(!function_exists('etheme_columns_options')) {
	function etheme_columns_options() {
		if(!function_exists('vc_map')) return;
		vc_add_param('vc_column', array(
			"type" => "dropdown",
			"heading" => esc_html__("Fixed background position", 'xstore'),
			"param_name" => "fixed_background",
			"group" => esc_html__('Design Options', 'xstore'),
			"edit_field_class" => 'vc_col-sm-5 vc_column',
			"value" => array(
				'' => '',
				__("Left center", 'xstore') => 'left',
				__("Right center", 'xstore') => 'right',
				__("Center center", 'xstore') => 'center',
			)
		));

		vc_add_param('vc_column', array(
			"type" => "dropdown",
			"heading" => esc_html__("Off center", 'xstore'),
			"param_name" => "off_center",
			"value" => array(
				'' => '',
				__("Left", 'xstore') => 'left',
				__("Right", 'xstore') => 'right',
			)
		));

		vc_add_param('vc_row', array(
			"type" => "dropdown",
			"heading" => esc_html__("Fixed background position", 'xstore'),
			"param_name" => "fixed_background",
			"group" => esc_html__('Design Options', 'xstore'),
			"edit_field_class" => 'vc_col-sm-5 vc_column',
			"value" => array(
				'' => '',
				__("Left center", 'xstore') => 'left',
				__("Right center", 'xstore') => 'right',
				__("Center center", 'xstore') => 'center',
			)
		));
	}
}


if( ! function_exists( 'etheme_get_slider_params' ) ) {
	function etheme_get_slider_params() {
		return array(
			array(
				"type" => "textfield",
				"heading" => esc_html__("Slider speed", 'xstore'),
				"param_name" => "slider_speed",
				"group" => esc_html__('Slider settings', 'xstore')
			),
			array(
				"type" => "checkbox",
				"heading" => esc_html__("Slider autoplay", 'xstore'),
				"param_name" => "slider_autoplay",
				"group" => esc_html__('Slider settings', 'xstore'),
				'value' => array( esc_html__( 'Yes, please', 'xstore' ) => 'yes' )

			),
			array(
				"type" => "checkbox",
				"heading" => esc_html__("Hide pagination control", 'xstore'),
				"param_name" => "hide_pagination",
				"group" => esc_html__('Slider settings', 'xstore'),
				'value' => array( esc_html__( 'Yes, please', 'xstore' ) => 'yes' )

			),
			array(
				"type" => "checkbox",
				"heading" => esc_html__("Hide prev/next buttons", 'xstore'),
				"param_name" => "hide_buttons",
				"group" => esc_html__('Slider settings', 'xstore'),
				'value' => array( esc_html__( 'Yes, please', 'xstore' ) => 'yes' )

			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Number of slides on large screens", 'xstore'),
				"param_name" => "large",
				"group" => esc_html__('Slider settings', 'xstore')
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("On notebooks", 'xstore'),
				"param_name" => "notebook",
				"group" => esc_html__('Slider settings', 'xstore')
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("On tablet landscape", 'xstore'),
				"param_name" => "tablet_land",
				"group" => esc_html__('Slider settings', 'xstore')
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("On tablet portrait", 'xstore'),
				"param_name" => "tablet_portrait",
				"group" => esc_html__('Slider settings', 'xstore')
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("On mobile", 'xstore'),
				"param_name" => "mobile",
				"group" => esc_html__('Slider settings', 'xstore')
			),
		);
	}
}