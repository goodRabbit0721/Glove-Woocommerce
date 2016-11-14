<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Banner With mask
// **********************************************************************// 

function etheme_banner_shortcode($atts, $content) {
    $image = $mask = $output = $custom_class = '';
    
	if( empty( $atts['subtitle_google_fonts'] ) ) {
		$atts['subtitle_google_fonts'] = 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal';
	}

	if( empty( $atts['title_google_fonts'] ) ) {
		$atts['title_google_fonts'] = 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal';
	}

    extract(shortcode_atts(array(
        'align'  => 'left',
        'valign'  => 'top',
        'class'  => '',
        'link'  => '',
        'hover'  => '',
		'title'  => '',
		'subtitle'  => '',
		'font_style'  => '',
        'type'  => 1,  
        'img' => '',
		'responsive_fonts' => 1,
        'banner_color_bg' => 'transparent',
        'img_size' => '270x170',
		'image_opacity' => '1',
		'image_opacity_on_hover' => '1',
		'css' => ''
    ), $atts));

    $id = rand(1000,9999);

    $banner_id = 'banner-' . $id;

	$image = etheme_get_image($img, $img_size);

    if ($type != '') {
      $class .= ' banner-type-'.$type;
    }

	if ($align != '') {
		$class .= ' align-'.$align;
	}

	if ($responsive_fonts == 1) {
		$class .= ' responsive-fonts';
	}

    if ($valign != '') {
      $class .= ' valign-'.$valign;
    }

    if ($font_style != '') {
      $class .= ' font-style-'.$font_style;
    }

    $onclick = '';

	//parse link
	$link = ( '||' === $link ) ? '' : $link;
	$link = vc_build_link( $link );
	$use_link = false;
	if ( strlen( $link['url'] ) > 0 ) {
		$use_link = true;
		$a_href = $link['url'];
		//$a_title = $link['title'];
		//$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
	}

    if( $use_link ) {
        $class .= ' cursor-pointer';
        $onclick = 'onclick="window.location=\''. esc_url( $a_href ).'\'"';
    }
	if( empty( $subtitle ) && empty( $title ) ) {
		$class .= ' no-titles';
	}

	if( ! empty($css) && function_exists( 'vc_shortcode_custom_css_class' )) {
		$custom_class = ' ' . vc_shortcode_custom_css_class( $css );
	}

    $output .= '<div id="' . $banner_id . '" class="banner '. esc_attr( $class ).'" '. $onclick .'>';
	    $output .= $image;
	    $output .= '<div class="banner-content ' . esc_attr( $custom_class ) . '">';
			if( ! empty( $subtitle ) ) {
				$output .= etheme_getHeading('subtitle', $atts, 'banner-subtitle');
			}
			if( ! empty( $title ) ) {
				$output .= etheme_getHeading('title', $atts, 'banner-title');
			}
		    $output .= '<div class="content-inner">' . do_shortcode($content) . '</div>';
	    $output .= '</div>';
    $output .= '</div>';

    $output .= '<style type="text/css">';
	#$output .= $css;
    $output .= '#' . $banner_id . ' {';
    $output .= 'background-color: ' . $banner_color_bg . ';';
    $output .= '}';
    $output .= '#' . $banner_id . ' img {';
    $output .= 'opacity: ' . $image_opacity . ';';
    $output .= '}';
    $output .= '#' . $banner_id . ':hover img {';
    $output .= 'opacity: ' . $image_opacity_on_hover . ';';
    $output .= '}';
    $output .= '</style>';

    
    return $output;
}

function etheme_getHeading( $tag, $atts, $class = '' ) {
	$inline_css = '';
	if ( isset( $atts[ $tag ] ) && '' !== trim( $atts[ $tag ] ) ) {
		if ( isset( $atts[ 'use_custom_fonts_' . $tag ] ) && 'true' === $atts[ 'use_custom_fonts_' . $tag ] ) {
			$custom_heading = visual_composer()->getShortCode( 'vc_custom_heading' );
			$data = vc_map_integrate_parse_atts( 'banner', 'vc_custom_heading', $atts, $tag . '_' );
			$data['el_class'] = $class;
			$data['text'] = $atts[ $tag ]; // provide text to shortcode
			return $custom_heading->render( ( $data ) );
		} else {
			if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
				if ( ! empty( $atts['custom_text'] ) ) {
					$inline_css[] = vc_get_css_color( 'color', $atts['custom_text'] );
				}
			}
			if ( ! empty( $inline_css ) ) {
				$inline_css = ' style="' . implode( '', $inline_css ) . '"';
			}

			return '<h2 class="' . $class . '" ' . $inline_css . '>' . $atts[ $tag ] . '</h2>';
		}
	}

	return '';
}
// **********************************************************************// 
// ! Register New Element: Banner with mask
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_banner');
if(!function_exists('etheme_register_vc_banner')) {
	function etheme_register_vc_banner() {
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
		$params = array_merge( array(
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
				"type" => "textarea_html",
				"holder" => "div",
				"heading" => "Banner Mask Text",
				"param_name" => "content",
				"value" => "Some promo words"
			),
			array(
				"type" => "vc_link",
				"heading" => esc_html__("Link", 'xstore'),
				"param_name" => "link"
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Horizontal align", 'xstore'),
				"param_name" => "align",
				"value" => array( "", esc_html__("Left", 'xstore') => "left", esc_html__("Center", 'xstore') => "center", esc_html__("Right", 'xstore') => "right")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Vertical align", 'xstore'),
				"param_name" => "valign",
				"value" => array( esc_html__("Top", 'xstore') => "top", esc_html__("Middle", 'xstore') => "middle", esc_html__("Bottom", 'xstore') => "bottom")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Banner design", 'xstore'),
				"param_name" => "type",
				"value" => array( "",
					__("Diagonal", 'xstore') => 1,
					__("Zoom", 'xstore') => 2,
				)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Font style", 'xstore'),
				"param_name" => "font_style",
				"value" => array( "", esc_html__("light", 'xstore') => "light", esc_html__("dark", 'xstore') => "dark")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Responsive fonts", 'xstore'),
				"param_name" => "responsive_fonts",
				"value" => array( "",
					__("Yes", 'xstore') => 1,
					__("No", 'xstore') => 0,
				)
			),
			/*array(
              "type" => "dropdown",
              "heading" => esc_html__("Hover effect", 'xstore'),
              "param_name" => "hover",
              "value" => array( "", esc_html__("zoom", 'xstore') => "zoom", esc_html__("fade", 'xstore') => "fade")
            ),*/
			array(
				'type' => 'attach_image',
				"heading" => esc_html__("Banner Image", 'xstore'),
				"param_name" => "img",
				"group" => "Image",
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Banner size", 'xstore' ),
				"param_name" => "img_size",
				"description" => esc_html__("Enter image size. Example in pixels: 200x100 (Width x Height).", 'xstore'),
				"group" => "Image",
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => esc_html__("Image Opacity", 'xstore'),
				"param_name" => "image_opacity",
				"value" => 1,
				"min" => 0.0,
				"max" => 1.0,
				"step" => 0.1,
				"suffix" => "",
				"description" => esc_html__("Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)", 'xstore'),
				"group" => "Image",
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => esc_html__("Image Opacity on Hover", 'xstore'),
				"param_name" => "image_opacity_on_hover",
				"value" => 1,
				"min" => 0.0,
				"max" => 1.0,
				"step" => 0.1,
				"suffix" => "",
				"description" => esc_html__("Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)", 'xstore'),
				"group" => "Image",
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => esc_html__("Background Color", 'xstore'),
				"param_name" => "banner_color_bg",
				"value" => "",
				"description" => "",
				"group" => "Image",
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
				  'group' => esc_html__( 'Design for banner content', 'xstore' )
			  ),
		), $title_custom_heading, $subtitle_custom_heading);

	    $banner_params = array(
	      'name' => '[8THEME] Banner with mask',
	      'base' => 'banner',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-banner.png',
	      'category' => 'Eight Theme',
	      'params' => $params
	    );
	
	    vc_map($banner_params);
	}
}
