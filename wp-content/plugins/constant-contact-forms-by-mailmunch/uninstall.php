<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://www.mailmunch.co
 * @since      2.0.0
 *
 * @package    Constantcontact_Mailmunch
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('cc_mm_user_token');
delete_option('cc_mm_site_id');
delete_option('cc_mm_constantcontact_access_token');
delete_option('cc_mm_constantcontact_list_id');
delete_option('cc_mm_skip_onboarding');
delete_option('cc_mm_activation_redirect');
delete_option('cc_mm_activation_date');
delete_option('cc_mm_dismiss_review_notice');