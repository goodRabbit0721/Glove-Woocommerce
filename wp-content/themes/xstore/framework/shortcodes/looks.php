<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! The looks
// **********************************************************************// 

function etheme_looks_shortcode($atts, $content) {
    global $woocommerce_loop;

    if ( !class_exists('Woocommerce') ) return false;

    extract(shortcode_atts(array(
        'class'  => '',
    ), $atts));

    preg_match_all( '/et_the_look([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
    $look_titles = array();
    if ( isset( $matches[1] ) ) {
      $look_titles = $matches[1];
    }

    $tabs_nav = '';
    
    if( count($look_titles) > 1 ) {
      $tabs_nav .= '<ul class="et-looks-nav">';
      $i = 0;
      foreach ( $look_titles as $look ) {
        $i++;
        $look_atts = shortcode_parse_atts( $look[0] );
        $tabs_nav .= '<li><a href="#">' . $i . '</a></li>';
      }
      $tabs_nav .= '</ul>';

    }

    $output = '';

    ob_start();

    echo '<div class="et-looks ' . esc_attr( $class ) . '">';
      echo $tabs_nav;
      echo '<div class="et-looks-content has-no-active-item">';
        echo do_shortcode( $content );
      echo '</div>';
    echo '</div>';

    $output = ob_get_clean();
      
    return $output;
}

// **********************************************************************// 
// ! Register New Element: The Looks
// **********************************************************************//
add_action( 'init', 'etheme_register_looks');
if(!function_exists('etheme_register_looks')) {
	function etheme_register_looks() {
		if(!function_exists('vc_map')) return;

	    $params = array(
	      'name' => '[8THEME] Product looks',
	      'base' => 'et_looks',
	      'icon' => 'icon-wpb-etheme',
	      'category' => 'Eight Theme', 
        'content_element' => true,
        'is_container' => true,
        'icon' => ETHEME_CODE_IMAGES . 'vc/el-lookbook.png',
        'show_settings_on_create' => true,
        'as_parent' => array(
          'only' => 'et_the_look',
        ),
	      'params' => array(

          array(
            "type" => "textfield",
            "heading" => esc_html__("Extra Class", 'xstore'),
            "param_name" => "class",
            "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xstore')
          )
	      ),
        'js_view' => 'VcColumnView'
	
	    );  
	
	    vc_map($params);
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_ET_Looks extends WPBakeryShortCodesContainer {
    }
}
