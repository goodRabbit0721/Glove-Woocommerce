<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Title
// **********************************************************************// 

function etheme_title_shortcode($atts, $content) {
    $output = $style1 = $style2 = '';
    
	if( empty( $atts['subtitle_google_fonts'] ) ) {
		$atts['subtitle_google_fonts'] = 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal';
	}

	if( empty( $atts['title_google_fonts'] ) ) {
		$atts['title_google_fonts'] = 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal';
	}

    extract(shortcode_atts(array(
    	'subtitle' => '',
    	'title' => 'Title',
    	'divider' => '',
    	'align' => 'center',
    	'design' => 1,
        'class'  => '',
    ), $atts));
    
    if($subtitle != '') {
	    $subtitle = '<h3'.$style2.'>' . $subtitle . '</h3>';
    }
    
    if($divider != '') {
	    $divider = '<hr class="divider ' . $divider . '">';
    }
    
    if($align != '') {
	    $class .= ' title-' . $align . '';
    }

    $class .= ' design-'.$design;

    $output .= ' <div class="title ' . $class . '">';
			if( ! empty( $subtitle ) ) {
				$output .= etheme_getHeading('subtitle', $atts, 'banner-subtitle');
			}
			if( ! empty( $title ) ) {
				$output .= etheme_getHeading('title', $atts, 'banner-title');
			}
	    $output .= $divider;
    $output .= '</div>';
    
    return $output;
}

// **********************************************************************// 
// ! Register New Element: title
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_title');
if(!function_exists('etheme_register_vc_title')) {
	function etheme_register_vc_title() {
		if(!function_exists('vc_map')) return;		

		require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );

		$title_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'title_', esc_html__( 'Title font', 'xstore' ), array(
			'exclude' => array(
				'vc_link',
				'source',
				'text',
				'css',
				'el_class',
			),
		), array(
			'element' => 'use_custom_fonts_title',
			'value' =>'true',
		) );

		// This is needed to remove custom heading _tag and _align options.
		if ( is_array( $title_custom_heading ) && ! empty( $title_custom_heading ) ) {
			foreach ( $title_custom_heading as $key => $param ) {
				if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
					$title_custom_heading[ $key ]['value'] = '';
					if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
						$sub_key = array_search( 'tag', $param['settings']['fields'] );
						if ( false !== $sub_key ) {
							unset( $title_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
						} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
							unset( $title_custom_heading[ $key ]['settings']['fields']['tag'] );
						}
						$sub_key = array_search( 'text_align', $param['settings']['fields'] );
						if ( false !== $sub_key ) {
							unset( $title_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
						} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
							unset( $title_custom_heading[ $key ]['settings']['fields']['text_align'] );
						}
					}
				}
			}
		}

		$subtitle_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'subtitle_', esc_html__( 'Subtitle font', 'xstore' ), array(
			'exclude' => array(
				'source',
				'vc_link',
				'text',
				'css',
				'el_class',
			),
		), array(
			'element' => 'use_custom_fonts_subtitle',
			'value' =>'true',
		) );


		// This is needed to remove custom heading _tag and _align options.
		if ( is_array( $subtitle_custom_heading ) && ! empty( $subtitle_custom_heading ) ) {
			foreach ( $subtitle_custom_heading as $key => $param ) {
				if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
					$subtitle_custom_heading[ $key ]['value'] = '';
					if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
						$sub_key = array_search( 'tag', $param['settings']['fields'] );
						if ( false !== $sub_key ) {
							unset( $subtitle_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
						} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
							unset( $subtitle_custom_heading[ $key ]['settings']['fields']['tag'] );
						}
						$sub_key = array_search( 'text_align', $param['settings']['fields'] );
						if ( false !== $sub_key ) {
							unset( $subtitle_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
						} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
							unset( $subtitle_custom_heading[ $key ]['settings']['fields']['text_align'] );
						}
					}
				}
			}
		}
	    $params = array(
	      'name' => '[8THEME] Title with text',
	      'base' => 'title',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-title.png',
	      'category' => 'Eight Theme',
	      'params' =>  array_merge( array(
			array(
				"type" => "textfield",
				"heading" => "Title",
				"param_name" => "title",
				'edit_field_class' => 'vc_col-sm-9 vc_column',
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Use custom font?', 'xstore' ),
				'param_name' => 'use_custom_fonts_title',
				'description' => esc_html__( 'Enable Google fonts.', 'xstore' ),
				'edit_field_class' => 'vc_col-sm-3 vc_column',
			),
			array(
				"type" => "textfield",
				"heading" => "Subitle",
				"param_name" => "subtitle",
				'edit_field_class' => 'vc_col-sm-9 vc_column',
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__( 'Use custom font?', 'xstore' ),
				'param_name' => 'use_custom_fonts_subtitle',
				'description' => esc_html__( 'Enable custom font option.', 'xstore' ),
				'edit_field_class' => 'vc_col-sm-3 vc_column',
			),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Divider", 'xstore'),
	          "param_name" => "divider",
	          "value" => array( 
	          	"", 
	          	__("Short", 'xstore') => "short", 
	          	__("Wide", 'xstore') => "wide"
	          )
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Design", 'xstore'),
	          "param_name" => "design",
	          "value" => array( "", 
	          	__("Design 1", 'xstore') => 1, 
	          	__("Design 2", 'xstore') => 2
          		)
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Text align", 'xstore'),
	          "param_name" => "align",
	          "value" => array( 
	          	"", 
	          	__("Left", 'xstore') => "left", 
	          	__("Center", 'xstore') => "center", 
	          	__("Right", 'xstore') => "right"
	          )
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Extra Class", 'xstore'),
	          "param_name" => "class",
	          "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xstore')
	        )
	      ), $title_custom_heading, $subtitle_custom_heading)
	    );
	
	    vc_map($params);
	}
}
