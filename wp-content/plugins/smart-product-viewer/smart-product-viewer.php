<?php
/*
Plugin Name: Smart Product Viewer
Plugin URI: http://plugins.topdevs.net/smart-product-viewer/
Description: Smart Product Viewer is a 360º viewer and product animation plugin for any WordPress e-Commerce website that help customers see even more details of your product with a full 360° spin view and understand the workflow with step-by-step animation.
Version: 1.4.5
Author: topdevs
Author URI: http://codecanyon.net/user/topdevs/portfolio?ref=topdevs
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define constants
define( 'SPV_AJAX', false ); // set to true if using from AJAX insereted content

// Include all files needed
require_once 'includes/ThreeSixtySlider.php';
require_once 'includes/SmartProductWidget.php';
require_once 'includes/SmartProductPlugin.php';
// TinyMCE
require_once 'includes/tinymce/SmartProductViewerTinyMCE.php';
require_once 'includes/tinymce/tinymce-options.php';

// Start everything
add_action( 'init', array( 'SmartProductPlugin', 'init' ) );

// Register widget
add_action( 'widgets_init', array( 'SmartProductPlugin', 'registerWidget' ) );
?>