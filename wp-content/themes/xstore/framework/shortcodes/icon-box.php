<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Icon Box
// **********************************************************************// 

function etheme_icon_box_shortcode($atts, $content) {
    $output = $btn = $style = $style_hover = '';
	if( ! function_exists( 'vc_icon_element_fonts_enqueue' ) ) return;
    extract(shortcode_atts(array(
    	'title' => '',
		'icon' => '',
		'type' => 'fontawesome',
		'icon_fontawesome' => '',
		'icon_openiconic' => '',
		'icon_typicons' => '',
		'icon_entypo' => '',
		'icon_linecons' => '',
		'icon_monosocial' => '',
		'color' => '',
		'bg_colour' => '',
		'color_hover' => '',
		'bg_color_hover' => '',
    	'size' => '',
    	'position' => '',
    	'link' => '',
		'btn_text' => '',
		'btn_style' => 'default',
		'btn_size' => 'default',
		'btn_align' => 'center',
    	'design' => '',
    	'animation' => '',
        'class'  => '',
		'css' => ''
    ), $atts));

    if($title != '') {
	    $title = '<h3>' . $title . '</h3>';
    }

	if($color != '') {
		$style .= 'color:' . $color . ';';
	}

	if($bg_colour != '') {
		$style .= 'background-color:' . $bg_colour . ';';
	}

	if($color_hover != '') {
		$style_hover .= 'color:' . $color_hover . ';';
	}

	if($bg_color_hover != '') {
		$style_hover .= 'background-color:' . $bg_color_hover . ';';
	}
    
    if($size != '') {
	    $style .= 'font-size:' . $size . 'px;';
    }

	vc_icon_element_fonts_enqueue( $type );
    
	$iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
	$icon = '<i class="' . $iconClass . '"></i>';

    $class .= ' ' . $position . '-icon';
     
    if($design != '') {
	    $class .= ' design-' . $design;
    }
    
    if($animation != '') {
	    $class .= ' animation-' . $animation;
    }

    if($link != '' && $btn_text != '') {
		$btn_class = "btn style-" . $btn_style . " size-" . $btn_size;
	    $btn = '<div class="button-wrap align-' . $btn_align . '"><a href="' . $link . '" class="' . $btn_class . '">' . $btn_text . '</a></div>';
    }

	if( ! empty($css) && function_exists( 'vc_shortcode_custom_css_class' )) {
		$class .= ' ' . vc_shortcode_custom_css_class( $css );
	}

    $box_id = rand(1000,10000);

	$output .= '<div class="ibox-block box-' . $box_id . ' ' . $class . '">';
		$output .= '<div class="ibox-symbol">';
			$output .= $icon;
		$output .= '</div>';
		$output .= '<div class="ibox-content">';
			$output .= $title;
			$output .= '<hr class="divider short">';
			$output .= '<div class="ibox-text">'. do_shortcode( $content ) .'</div>';
		$output .= '</div>';
		$output .= $btn;
	$output .= '</div>';
	$output .= '<style>';
	$output .= $css;
	$output .= '.ibox-block.box-' . $box_id . ' .ibox-symbol i { ' . $style . ' }';
	$output .= '.ibox-block.box-' . $box_id . ':hover .ibox-symbol i { ' . $style_hover . ' }';
	$output .= '</style>';

    return $output;
}

// **********************************************************************// 
// ! Register New Element: Icon Box
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_icon_box');
if(!function_exists('etheme_register_vc_icon_box')) {
	function etheme_register_vc_icon_box() {
		if(!function_exists('vc_map')) return;
	    $params = array(
	      'name' => '[8THEME] Icon Box',
	      'base' => 'icon_box',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-icon.png',
	      'category' => 'Eight Theme',
	      'params' => array(
	        array(
	          "type" => "textfield",
	          "heading" => "Title",
	          "param_name" => "title"
	        ),
			  array(
				  'type' => 'dropdown',
				  'heading' => esc_html__( 'Icon library', 'xstore' ),
				  'value' => array(
					  esc_html__( 'Font Awesome', 'xstore' ) => 'fontawesome',
					  esc_html__( 'Open Iconic', 'xstore' ) => 'openiconic',
					  esc_html__( 'Typicons', 'xstore' ) => 'typicons',
					  esc_html__( 'Entypo', 'xstore' ) => 'entypo',
					  esc_html__( 'Linecons', 'xstore' ) => 'linecons',
					  esc_html__( 'Mono Social', 'xstore' ) => 'monosocial',
				  ),
				  'admin_label' => true,
				  'param_name' => 'type',
				  'description' => esc_html__( 'Select icon library.', 'xstore' ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_fontawesome',
				  'value' => 'fa fa-adjust', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false,
					  // default true, display an "EMPTY" icon?
					  'iconsPerPage' => 4000,
					  // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'fontawesome',
				  ),
				  'description' => esc_html__( 'Select icon from library.', 'xstore' ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_openiconic',
				  'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false, // default true, display an "EMPTY" icon?
					  'type' => 'openiconic',
					  'iconsPerPage' => 4000, // default 100, how many icons per/page to display
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'openiconic',
				  ),
				  'description' => esc_html__( 'Select icon from library.', 'xstore' ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_typicons',
				  'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false, // default true, display an "EMPTY" icon?
					  'type' => 'typicons',
					  'iconsPerPage' => 4000, // default 100, how many icons per/page to display
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'typicons',
				  ),
				  'description' => esc_html__( 'Select icon from library.', 'xstore' ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_entypo',
				  'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false, // default true, display an "EMPTY" icon?
					  'type' => 'entypo',
					  'iconsPerPage' => 4000, // default 100, how many icons per/page to display
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'entypo',
				  ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_linecons',
				  'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false, // default true, display an "EMPTY" icon?
					  'type' => 'linecons',
					  'iconsPerPage' => 4000, // default 100, how many icons per/page to display
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'linecons',
				  ),
				  'description' => esc_html__( 'Select icon from library.', 'xstore' ),
			  ),
			  array(
				  'type' => 'iconpicker',
				  'heading' => esc_html__( 'Icon', 'xstore' ),
				  'param_name' => 'icon_monosocial',
				  'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
				  'settings' => array(
					  'emptyIcon' => false, // default true, display an "EMPTY" icon?
					  'type' => 'monosocial',
					  'iconsPerPage' => 4000, // default 100, how many icons per/page to display
				  ),
				  'dependency' => array(
					  'element' => 'type',
					  'value' => 'monosocial',
				  ),
				  'description' => esc_html__( 'Select icon from library.', 'xstore' ),
			  ),
	        array(
	          'type' => 'colorpicker',
	          "heading" => esc_html__("Icon color", 'xstore'),
	          "param_name" => "color"
	        ),
	        array(
	          'type' => 'colorpicker',
	          "heading" => esc_html__("Icon background color", 'xstore'),
	          "param_name" => "bg_colour"
	        ),
			  array(
				  'type' => 'colorpicker',
				  "heading" => esc_html__("Icon color (hover)", 'xstore'),
				  "param_name" => "color_hover"
			  ),
			  array(
				  'type' => 'colorpicker',
				  "heading" => esc_html__("Icon background color (hover)", 'xstore'),
				  "param_name" => "bg_color_hover"
			  ),
	        array(
	          "type" => "textfield",
	          "heading" => "Icon size (in pixels)",
	          "param_name" => "size"
	        ),
	        array(
	          "type" => "textarea_html",
	          "holder" => "div",
	          "heading" => "Content text",
	          "param_name" => "content"
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Position of the icon", 'xstore'),
	          "param_name" => "position",
	          "value" => array( 
	          	__("Left", 'xstore') => 'left', 
	          	__("Top", 'xstore') => 'top',
	          	__("Right", 'xstore') => 'right'
          	 )
	        ),
	        /*array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Design", 'xstore'),
	          "param_name" => "design",
	          "value" => array( 
	          	"",
	          	__("Design 1", 'xstore') => 1, 
	          	__("Design 2", 'xstore') => 2,
	          	__("Design 3", 'xstore') => 3
          	 )
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Animation", 'xstore'),
	          "param_name" => "animation",
	          "value" => array( 
	          	"",
	          	__("Animation 1", 'xstore') => 1, 
	          	__("Animation 2", 'xstore') => 2,
	          	__("Animation 3", 'xstore') => 3
          	 )
	        ),*/
			  array(
				  "type" => "textfield",
				  "heading" => "Button text",
				  "param_name" => "btn_text",
				  "group" => "Button"
			  ),
			  array(
				  "type" => "textfield",
				  "heading" => "Button link",
				  "param_name" => "link",
					"group" => "Button"
			  ),
			  array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Style", 'xstore'),
				  "param_name" => "btn_style",
				  "value" => array(
					  "",
					  esc_html__("Default", 'xstore') => 'default',
					  esc_html__("Active", 'xstore') => 'active',
					  esc_html__("Border", 'xstore') => 'border',
					  esc_html__("White", 'xstore') => 'white',
					  esc_html__("Black", 'xstore') => 'black'
				  ),
				  "group" => "Button"
			  ),
			  array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Size", 'xstore'),
				  "param_name" => "btn_size",
				  "value" => array(
					  "",
					  esc_html__("Default", 'xstore') => 'default',
					  esc_html__("Small", 'xstore') => 'small',
					  esc_html__("Large", 'xstore') => 'large'
				  ),
				  "group" => "Button"
			  ),
			  array(
				  "type" => "dropdown",
				  "heading" => esc_html__("Align", 'xstore'),
				  "param_name" => "btn_align",
				  "value" => array(
					  "",
					  esc_html__("Left", 'xstore') => 'left',
					  esc_html__("Center", 'xstore') => 'center',
					  esc_html__("Right", 'xstore') => 'right'
				  ),
				  "group" => "Button"
			  ),
				array(
				  "type" => "textfield",
				  "heading" => esc_html__("Extra Class", 'xstore'),
				  "param_name" => "class",
				  "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xstore')
				),
			  array(
				  'type' => 'css_editor',
				  'heading' => esc_html__( 'CSS box', 'xstore' ),
				  'param_name' => 'css',
				  'group' => esc_html__( 'Design', 'xstore' )
			  ),
	      )
	
	    );  
	
	    vc_map($params);
	}
}