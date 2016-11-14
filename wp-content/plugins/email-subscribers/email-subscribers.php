<?php
/**
 * Plugin Name: Email Subscribers & Newsletters
 * Plugin URI: http://www.storeapps.org/product/email-subscribers/
 * Description: Add subscription forms on website, send HTML newsletters & automatically notify subscribers about new blog posts once it gets published.
 * Version: 3.2.2
 * Author: StoreApps
 * Author URI: http://www.storeapps.org/
 * Requires at least: 3.4
 * Tested up to: 4.6.1
 * Text Domain: email-subscribers
 * Domain Path: /languages/
 * License: GPLv3
 * Copyright (c) 2015, 2016 StoreApps
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'es-defined.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'es-stater.php');

add_action( 'admin_menu', array( 'es_cls_registerhook', 'es_adminmenu' ) );
add_action( 'admin_init', array( 'es_cls_registerhook', 'es_welcome' ) );
add_action( 'admin_enqueue_scripts', array( 'es_cls_registerhook', 'es_load_scripts' ) );
add_action( 'wp_enqueue_scripts', array( 'es_cls_registerhook', 'es_load_widget_scripts_styles' ) );
add_action( 'widgets_init', array( 'es_cls_registerhook', 'es_widget_loading' ) );

// Action to Upgrade Email Subscribers database
add_action( 'admin_init', array( 'es_cls_registerhook', 'sa_email_subscribers_db_update' ) );

// Admin Notices
add_action( 'admin_notices', array( 'es_cls_registerhook', 'es_add_admin_notices' ) );
add_action( 'admin_init', array( 'es_cls_registerhook', 'dismiss_admin_notice' ) );

add_shortcode( 'email-subscribers', 'es_shortcode' );

add_action( 'wp_ajax_es_klawoo_subscribe', array( 'es_cls_registerhook', 'klawoo_subscribe' ) );

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'es-directly.php');

function es_textdomain() {
	load_plugin_textdomain( 'email-subscribers' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'es_textdomain' );
add_action( 'transition_post_status', array( 'es_cls_sendmail', 'es_prepare_notification' ), 10, 3 );

add_action( 'user_register', 'es_sync_registereduser' );

register_activation_hook( ES_FILE, array( 'es_cls_registerhook', 'es_activation' ) );
register_deactivation_hook( ES_FILE, array( 'es_cls_registerhook', 'es_deactivation' ) );
