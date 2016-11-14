<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');

define('ETHEME_SUPPORT_LINK', 'http://www.8theme.com/forums/classico-wordpress-support-forum/');
define('ETHEME_DOCS_LINK', 'http://www.8theme.com/classico-theme-documentation/');
define('ETHEME_CHANGELOG_LINK', 'http://8theme.com/demo/docs/classico/classico-changelog.txt');
define('ETHEME_TF_LINK', 'http://themeforest.net/item/classico-responsive-woocommerce-wordpress-theme/11024192');
define('ETHEME_THEME_NAME', 'XStore');
define('ETHEME_THEME_SLUG', 'xstore');
define('ETHEME_RATE_LINK', 'http://themeforest.net/downloads');

// **********************************************************************// 
// ! Specific functions only for this theme
// **********************************************************************//

if(!function_exists('etheme_theme_setup')) {

	add_action('after_setup_theme', 'etheme_theme_setup', 1);
    add_theme_support( 'woocommerce' );
	
	
	function etheme_theme_setup(){
        add_theme_support( 'post-formats', array( 'video', 'quote', 'gallery', 'audio' ) );
        add_theme_support( 'post-thumbnails', array('post') );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( "title-tag" );
	}
}

// **********************************************************************// 
// ! Menus
// **********************************************************************// 

if(!function_exists('etheme_register_menus')) {
    function etheme_register_menus() {
        register_nav_menus(array(
            'main-menu' => esc_html__('Main menu', 'xstore'),
            'main-menu-right' => esc_html__('Main menu right', 'xstore'),
            'mobile-menu' => esc_html__('Mobile menu', 'xstore'),
            'my-account' => esc_html__('My account', 'xstore')
        ));
    }
    add_action('init', 'etheme_register_menus');
}