<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

add_action( 'init', 'etheme_register_vc_tabs');
if(!function_exists('etheme_register_vc_tabs')) {
	function etheme_register_vc_tabs() {
		if(!function_exists('vc_map')) return;
	    $tab_id_1 = time().'-1-'.rand(0, 100);
	    $tab_id_2 = time().'-2-'.rand(0, 100);
	    $setting_vc_tabs = array(
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Tabs type", 'xstore' ),
	          "param_name" => "type",
	          "value" => array(__("Default", 'xstore' ) => '',
	              esc_html__("Products Tabs", 'xstore' ) => 'products-tabs',
	              esc_html__("Left bar", 'xstore' ) => 'left-bar',
	              esc_html__("Right bar", 'xstore' ) => 'right-bar')
	        ),
	    );
	    vc_add_params('vc_tabs', $setting_vc_tabs);

	    vc_map( array(
	      "name" => esc_html__("Tab", 'xstore' ),
	      "base" => "vc_tab",
	      "allowed_container_element" => 'vc_row',
	      "is_container" => true,
	      "content_element" => false,
	      "params" => array(
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Title", 'xstore' ),
	          "param_name" => "title",
	          "description" => esc_html__("Tab title.", 'xstore' )
	        ),
	        array(
	          'type' => 'icon',
	          "heading" => esc_html__("Icon", 'xstore'),
	          "param_name" => "icon"
	        ),
	        array(
	          "type" => "tab_id",
	          "heading" => esc_html__("Tab ID", 'xstore' ),
	          "param_name" => "tab_id"
	        )
	      ),
	      'js_view' => 'VcTabView'
	    ) );
	}
}
