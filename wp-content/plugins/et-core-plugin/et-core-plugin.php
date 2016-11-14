<?php
/*
Plugin Name: XStore Core
Plugin URI: http://8theme.com
Description: 8theme Core Plugin for Xstore theme
Version: 1.0.7
Author: 8theme
Text Domain: xstore-core
Author URI: http://8theme.com
*/

if(!defined('WPINC')) die();


include 'inc/functions.php';
include 'inc/post-types.php';
include 'inc/shortcodes.php';
include 'inc/support-panel.php';
include 'inc/twitteroauth/twitteroauth.php';
include 'inc/testimonials/woothemes-testimonials.php';
include 'inc/soundcloud/soundcloud-shortcode.php';

add_action('plugins_loaded', 'xstore_load_importers');

function xstore_load_importers() {
	if ( is_admin() && ! defined( 'IMPORT_DEBUG' ) ) {
		include 'inc/wordpress-importer/wordpress-importer.php';
		include 'inc/import.php';
	}
}

add_action( 'plugins_loaded', 'xstore_core_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function xstore_core_load_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'xstore-core' );

	load_textdomain( 'xstore-core', WP_LANG_DIR . '/xstore-core/xstore-core-' . $locale . '.mo' );
	load_plugin_textdomain( 'xstore-core', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}


?>