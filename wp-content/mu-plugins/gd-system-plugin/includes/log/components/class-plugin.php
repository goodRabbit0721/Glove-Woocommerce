<?php

namespace WPaaS\Log\Components;

use \WPaaS\Log\Timer;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Plugin extends Component {

	/**
	 * Array of cached plugin data (before actions).
	 *
	 * @var array
	 */
	private $plugins = [];

	/**
	 * Wrapper for get_plugins().
	 *
	 * @return array
	 */
	private function get_plugins() {

		if ( ! function_exists( 'get_plugins' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		}

		$plugins = get_plugins();

		foreach ( $plugins as $plugin => $data ) {

			$plugins[ $plugin ]['Slug'] = ( false === strpos( $plugin, '/' ) ) ? basename( $plugin, '.php' ) : dirname( $plugin );

		}

		return $plugins;

	}

	/**
	 * Return data for a specified plugin.
	 *
	 * @param  string $plugin
	 * @param  string $data    (optional)
	 *
	 * @return mixed
	 */
	private function get_plugin_data( $plugin, $data = '' ) {

		$plugins = $this->get_plugins();

		if ( ! isset( $plugins[ $plugin ] ) ) {

			return;

		}

		if ( ! $data ) {

			return (array) $plugins[ $plugin ];

		}

		if ( ! isset( $plugins[ $plugin ][ $data ] ) ) {

			return;

		}

		return $plugins[ $plugin ][ $data ];

	}

	/**
	 * Plugin > Activate
	 *
	 * @action activate_plugin
	 *
	 * @param string $plugin
	 * @param bool   $network_wide
	 */
	public function callback_activate_plugin( $plugin, $network_wide ) {

		Timer::stop();

		$summary = _x(
			'"%s" plugin activated',
			'Plugin name',
			'gd-system-plugin'
		);

		if ( $network_wide ) {

			$summary = _x(
				'"%s" plugin activated network wide',
				'Plugin name',
				'gd-system-plugin'
			);

		}

		$this->log_metric( 'publish' );

		$this->log(
			'activate',
			$summary,
			[
				'name'         => $this->get_plugin_data( $plugin, 'Name' ),
				'version'      => $this->get_plugin_data( $plugin, 'Version' ),
				'slug'         => $this->get_plugin_data( $plugin, 'Slug' ),
				'basename'     => $plugin,
				'network_wide' => (bool) $network_wide,
			]
		);

	}

	/**
	 * Plugin > Deactivate
	 *
	 * @action deactivate_plugin
	 *
	 * @param string $plugin
	 * @param bool   $network_wide
	 */
	public function callback_deactivate_plugin( $plugin, $network_wide ) {

		Timer::stop();

		$summary = _x(
			'"%s" plugin deactivated',
			'Plugin name',
			'gd-system-plugin'
		);

		if ( $network_wide ) {

			$summary = _x(
				'"%s" plugin deactivated network wide',
				'Plugin name',
				'gd-system-plugin'
			);

		}

		$this->log_metric( 'publish' );

		$this->log(
			'deactivate',
			$summary,
			[
				'name'         => $this->get_plugin_data( $plugin, 'Name' ),
				'version'      => $this->get_plugin_data( $plugin, 'Version' ),
				'slug'         => $this->get_plugin_data( $plugin, 'Slug' ),
				'basename'     => $plugin,
				'network_wide' => (bool) $network_wide,
			]
		);

	}

	/**
	 * Plugin > Delete
	 *
	 * @action delete_plugin
	 *
	 * @param string $plugin
	 */
	public function callback_delete_plugin( $plugin ) {

		Timer::stop();

		$this->log(
			'delete',
			_x(
				'"%s" plugin deleted',
				'Plugin name',
				'gd-system-plugin'
			),
			[
				'name'     => $this->get_plugin_data( $plugin, 'Name' ),
				'version'  => $this->get_plugin_data( $plugin, 'Version' ),
				'slug'     => $this->get_plugin_data( $plugin, 'Slug' ),
				'basename' => $plugin,
			]
		);

	}

	/**
	 * Before plugin upgrades.
	 *
	 * @param  array $options
	 *
	 * @return array
	 */
	public function callback_upgrader_package_options( $options ) {

		if ( ! $this->plugins && isset( $options['hook_extra']['plugin'] ) ) {

			$this->plugins = $this->get_plugins();

		}

		return $options;

	}

	/**
	 * Plugin > Install
	 * Plugin > Update
	 *
	 * @param \Plugin_Upgrader $upgrader
	 * @param array            $data
	 */
	public function callback_upgrader_process_complete( $upgrader, $data ) {

		if (
			! is_a( $upgrader, 'Plugin_Upgrader' )
			||
			'plugin' !== $data['type']
			||
			! in_array( $data['action'], [ 'install', 'update' ] )
		) {

			return;

		}

		if ( 'install' === $data['action'] ) {

			$this->plugin_install( $upgrader );

			return;

		}

		wp_clean_plugins_cache();

		$bulk    = ( ! empty( $data['bulk'] ) && true === $data['bulk'] );
		$plugins = ( $bulk ) ? $data['plugins'] : [ $upgrader->result['destination_name'] ];

		foreach ( $plugins as $plugin ) {

			$this->plugin_update( $plugin, $bulk );

		}

	}

	/**
	 * Plugin > Install
	 *
	 * @param \Plugin_Upgrader $upgrader
	 */
	private function plugin_install( $upgrader ) {

		Timer::stop();

		unset( $this->plugins );

		$plugin      = $upgrader->plugin_info();
		$plugin_data = get_plugin_data( trailingslashit( $upgrader->result['local_destination'] ) . $plugin );

		$this->log(
			'install',
			_x(
				'"%s" plugin installed',
				'Plugin name',
				'gd-system-plugin'
			),
			[
				'name'     => $plugin_data['Name'],
				'version'  => $plugin_data['Version'],
				'slug'     => $upgrader->result['destination_name'],
				'basename' => $plugin,
			]
		);

	}

	/**
	 * Plugin > Update
	 *
	 * @param string $plugin
	 * @param bool   $bulk
	 */
	private function plugin_update( $plugin, $bulk ) {

		Timer::stop();

		$version = $this->get_plugin_data( $plugin, 'Version' );

		$meta = [
			'name'        => $this->get_plugin_data( $plugin, 'Name' ),
			'old_version' => empty( $this->plugins[ $plugin ]['Version'] ) ? $version : $this->plugins[ $plugin ]['Version'],
			'new_version' => $version,
			'slug'        => $this->get_plugin_data( $plugin, 'Slug' ),
			'basename'    => $plugin,
			'bulk'        => (bool) $bulk,
		];

		if ( empty( $meta['old_version'] ) || $meta['old_version'] === $meta['new_version'] ) {

			$summary = _x(
				'"%1$s" plugin updated to %2$s',
				'1: Plugin name, 2: New plugin version',
				'gd-system-plugin'
			);

		} else {

			$summary = _x(
				'"%1$s" plugin updated from %2$s to %3$s',
				'1: Plugin name, 2: Old plugin version, 3: New plugin version',
				'gd-system-plugin'
			);

		}

		$this->log( 'update', $summary, $meta );

	}

}
