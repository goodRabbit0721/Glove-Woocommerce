<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.mailmunch.co
 * @since             2.0.0
 * @package           Constantcontact_Mailmunch
 *
 * @wordpress-plugin
 * Plugin Name:       Constant Contact Forms by MailMunch
 * Plugin URI:        http://connect.constantcontact.com/integrations/mailmunch-email-list-builder
 * Description:       The Constant Contact plugin allows you to quickly and easily add signup forms for your Constant Contact lists. Popup, Embedded, Top Bar and a variety of different options available.
 * Version:           2.0.6
 * Author:            MailMunch
 * Author URI:        http://www.mailmunch.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       constantcontact-mailmunch
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-constantcontact-mailmunch-activator.php
 */
function activate_constantcontact_mailmunch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-constantcontact-mailmunch-activator.php';
	Constantcontact_Mailmunch_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-constantcontact-mailmunch-deactivator.php
 */
function deactivate_constantcontact_mailmunch() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-constantcontact-mailmunch-deactivator.php';
	Constantcontact_Mailmunch_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_constantcontact_mailmunch' );
register_deactivation_hook( __FILE__, 'deactivate_constantcontact_mailmunch' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-constantcontact-mailmunch.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_constantcontact_mailmunch() {

	$plugin = new Constantcontact_Mailmunch();
	$plugin->run();

}
run_constantcontact_mailmunch();
