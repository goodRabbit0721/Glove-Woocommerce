<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.mailmunch.co
 * @since      2.0.0
 *
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/includes
 * @author     MailMunch <info@mailmunch.co>
 */
class Constantcontact_Mailmunch_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
    update_option('cc_mm_activation_redirect', 'true');
	}

}
