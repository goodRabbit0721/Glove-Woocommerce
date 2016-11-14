<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.mailmunch.co
 * @since      2.0.0
 *
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/includes
 * @author     MailMunch <info@mailmunch.co>
 */
class Constantcontact_Mailmunch_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {
    update_option('cc_mm_activation_redirect', 'true');
	}

}
