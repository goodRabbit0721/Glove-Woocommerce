<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Register all 8theme Widgets
// **********************************************************************// 
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'recent-posts.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'recent-comments.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'twitter.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'flickr.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'wp-instagram-widget.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'static-block.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'qr-code.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'brands.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'about-author.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'socials.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'featured-posts.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'posts-tabs.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_WIDGETS . 'products.php') );

if(!function_exists('etheme_register_general_widgets')) {
	add_action( 'widgets_init', 'etheme_register_general_widgets' );
	function etheme_register_general_widgets() {
	    register_widget('ETheme_Twitter_Widget');
	    register_widget('ETheme_Recent_Posts_Widget');
	    register_widget('ETheme_Recent_Comments_Widget');
	    register_widget('ETheme_Flickr_Widget');
	    register_widget('ETheme_Instagram_Widget');
	    register_widget('ETheme_StatickBlock_Widget');
	    register_widget('ETheme_QRCode_Widget');
	    if( class_exists('WooCommerce') ) {
			register_widget('ETheme_Brands_Widget');
			register_widget('ETheme_Products_Widget');
	    }
	    register_widget('ETheme_About_Author_Widget');
	    register_widget('ETheme_Socials_Widget');
	    register_widget('ETheme_Featured_Posts_Widget');
	    register_widget('ETheme_Posts_Tabs_Widget');
	}
}

/*
*  Forms for Widgets
* ******************************************************************* */

if(!function_exists('etheme_widget_label')) {
	function etheme_widget_label( $label, $id ) {
	    echo "<label for='{$id}'>{$label}</label>";
	}
}

if(!function_exists('etheme_widget_input_checkbox')) {
	function etheme_widget_input_checkbox( $label, $id, $name, $checked, $value = 1 ) {
	    echo "\n\t\t\t<p>";
	    echo "<label for='{$id}'>";
	    echo "<input type='checkbox' id='{$id}' value='{$value}' name='{$name}' {$checked} /> ";
	    echo "{$label}</label>";
	    echo '</p>';
	}
}

if(!function_exists('etheme_widget_textarea')) {
	function etheme_widget_textarea( $label, $id, $name, $value ) {
	    echo "\n\t\t\t<p>";
	    etheme_widget_label( $label, $id );
	    echo "<textarea id='{$id}' name='{$name}' rows='3' cols='10' class='widefat'>" . ( $value ) . "</textarea>";
	    echo '</p>';
	}
}

if(!function_exists('etheme_widget_input_text')) {
	function etheme_widget_input_text( $label, $id, $name, $value ) {
	    echo "\n\t\t\t<p>";
	    etheme_widget_label( $label, $id );
	    echo "<input type='text' id='{$id}' name='{$name}' value='" . strip_tags( $value ) . "' class='widefat' />";
	    echo '</p>';
	}
}

if(!function_exists('etheme_widget_input_image')) {
	function etheme_widget_input_image( $label, $id, $name, $value ) {
	    echo "\n\t\t\t<p>";
	    etheme_widget_label( $label, $id );
	    echo "<input type='text' id='{$id}' name='{$name}' value='" . strip_tags( $value ) . "' class='widefat' />";
	    echo "<input type='button' value='Upload Image' class='button upload_image_button' />";
	    echo '</p>';
	}
}

if(!function_exists('etheme_widget_input_dropdown')) {
	function etheme_widget_input_dropdown( $label, $id, $name, $value, $options ) {
	    echo "\n\t\t\t<p>";
	    etheme_widget_label( $label, $id );
	    echo "<select id='{$id}' name='{$name}' class='widefat'>";
		$val = current( array_flip( $options ) );
    	if( ! empty( $val ) ) echo '<option value=""></option>';
	    foreach ($options as $key => $option) {
	    	echo '<option value="' . $key . '" ' . selected( strip_tags( $value ), $key ) . '>' . $option . '</option>';
	    }
	    echo "</select>";
	    echo '</p>';
	}
}