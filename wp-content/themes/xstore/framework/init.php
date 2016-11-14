<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Define base constants
// **********************************************************************//

define('ETHEME_FW', '1.0');
define('ETHEME_BASE', get_template_directory() .'/');
define('ETHEME_CHILD', get_stylesheet_directory() .'/');
define('ETHEME_BASE_URI', get_template_directory_uri() .'/');

define('ETHEME_CODE', 'framework/');
define('ETHEME_CODE_DIR', ETHEME_BASE.'framework/');
define('ETHEME_TEMPLATES', ETHEME_CODE . 'templates/');
define('ETHEME_THEME', 'theme/');
define('ETHEME_THEME_DIR', ETHEME_BASE . 'theme/');
define('ETHEME_TEMPLATES_THEME', ETHEME_THEME . 'templates/');
define('ETHEME_CODE_3D', ETHEME_CODE .'thirdparty/');
define('ETHEME_CODE_3D_URI', ETHEME_BASE_URI.ETHEME_CODE .'thirdparty/');
define('ETHEME_CODE_WIDGETS', ETHEME_CODE .'widgets/');
define('ETHEME_CODE_POST_TYPES', ETHEME_CODE .'post-types/');
define('ETHEME_CODE_SHORTCODES', ETHEME_CODE .'shortcodes/');
define('ETHEME_CODE_CSS', ETHEME_BASE_URI . ETHEME_CODE .'assets/admin-css/');
define('ETHEME_CODE_JS', ETHEME_BASE_URI . ETHEME_CODE .'assets/js/');
define('ETHEME_CODE_IMAGES', ETHEME_BASE_URI . ETHEME_THEME .'assets/images/');
define('ETHEME_API', 'http://8theme.com/api/v1/');

define('ETHEME_PREFIX', '_et_');

// **********************************************************************// 
// ! Helper Framework functions
// **********************************************************************//
require_once( ETHEME_BASE . ETHEME_CODE . 'helpers.php' );

/*
* Theme f-ns
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'theme-functions.php') );

/*
* Theme template elements
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'template-elements.php') );

/*
* Menu walkers
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'walkers.php') );

// **********************************************************************// 
// ! Framework setup
// **********************************************************************//
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'theme-init.php') );

/*
* Shortcodes
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'shortcodes.php') );

/*
* Widgets
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'widgets.php') );

/*
* Post types
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE_POST_TYPES . 'static-blocks.php') );
require_once( apply_filters('etheme_file_url', ETHEME_CODE_POST_TYPES . 'portfolio.php') );

/*
* Configure Visual Composer
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'vc.php') );

/*
* Plugins activation
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE_3D . 'tgm-plugin-activation/class-tgm-plugin-activation.php') );


/*
* Video parse from url
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE_3D . 'parse-video/VideoUrlParser.class.php') );

/*
* Composer autoloader
* ******************************************************************* */
require_once apply_filters('etheme_file_url', ETHEME_CODE_3D . 'vendor/autoload.php');


/*
* WooCommerce f-ns
* ******************************************************************* */
if(etheme_woocommerce_installed() && current_theme_supports('woocommerce') ) {
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'woo.php') );
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'woo/brands.php') );
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'woo/video.php') );
}

/*
* Options Framework 
* ******************************************************************* */

/* extension loader */
require_once( apply_filters('etheme_file_url', ETHEME_CODE_3D . 'options-framework/loader.php') );


/* load base framework options */
if ( !isset( $redux_demo ) && file_exists( apply_filters('etheme_file_url', ETHEME_CODE . 'theme-options.php') ) ) {
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'theme-options.php') );
}

	

/*
* Sidebars
* ******************************************************************* */
require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'sidebars.php') );


/*
* Custom Metaboxes for pages
* ******************************************************************* */

require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'custom-metaboxes.php') );


/*
* Admin panel setup
* ******************************************************************* */

if ( is_admin() ) {
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'admin.php') );

	require_once( apply_filters('etheme_file_url', ETHEME_CODE_3D . 'menu-images/nav-menu-images.php'));
	
	/*
	* Check theme version
	* ******************************************************************* */
	require_once( apply_filters('etheme_file_url', ETHEME_CODE . 'version-check.php') );

}