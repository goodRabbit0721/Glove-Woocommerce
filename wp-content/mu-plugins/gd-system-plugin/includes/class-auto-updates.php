<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Auto_Updates {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_filter( 'user_has_cap',                   [ $this, 'no_update_core_cap' ],  PHP_INT_MAX, 3 );
		add_filter( 'pre_site_transient_update_core', [ $this, 'no_update_core_nags' ], PHP_INT_MAX );

		add_filter( 'auto_update_core',                   '__return_false', PHP_INT_MAX );
		add_filter( 'automatic_updates_send_email',       '__return_false', PHP_INT_MAX );
		add_filter( 'enable_auto_upgrade_email',          '__return_false', PHP_INT_MAX );
		add_filter( 'automatic_updates_send_debug_email', '__return_false', PHP_INT_MAX );
		add_filter( 'auto_core_update_send_email',        '__return_false', PHP_INT_MAX );

	}

	/**
	 * Prevent users from having the `update_core` capability.
	 *
	 * @filter user_has_cap
	 *
	 * @param  array $allcaps
	 * @param  array $cap
	 * @param  array $args
	 *
	 * @return array
	 */
	public function no_update_core_cap( array $allcaps, array $cap, array $args ) {

		$allcaps['update_core'] = false;

		return $allcaps;

	}

	/**
	 * Prevent update core nags and notifications.
	 *
	 * @filter pre_site_transient_update_core
	 *
	 * @return object
	 */
	public function no_update_core_nags() {

		return (object) [
			'last_checked'    => time(),
			'version_checked' => get_bloginfo( 'version' ),
		];

	}

}
