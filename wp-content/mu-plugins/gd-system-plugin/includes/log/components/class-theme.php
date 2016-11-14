<?php

namespace WPaaS\Log\Components;

use \WPaaS\Log\Timer;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Theme extends Component {

	/**
	 * {Theme} > Activate
	 *
	 * @action switch_theme
	 *
	 * @param string    $new_name
	 * @param \WP_Theme $new_theme
	 * @param \WP_Theme $old_theme
	 */
	public function callback_switch_theme( $new_name, $new_theme, $old_theme ) {

		if ( ! is_a( $new_theme, 'WP_Theme' ) || ! is_a( $new_theme, 'WP_Theme' ) ) {

			return;

		}

		Timer::stop();

		$this->log_metric( 'publish' );

		$this->log(
			'activate',
			_x(
				'"%s" theme activated',
				'Theme name',
				'gd-system-plugin'
			),
			[
				'name'           => $new_name,
				'version'        => $new_theme->Version,
				'stylesheet'     => $new_theme->Stylesheet,
				'template'       => $new_theme->Template,
				'author'         => $new_theme->Author,
				'old_name'       => $old_theme->Name,
				'old_version'    => $old_theme->Version,
				'old_stylesheet' => $old_theme->Stylesheet,
				'old_template'   => $old_theme->Template,
				'old_author'     => $old_theme->Author,
			]
		);

	}

	/**
	 * Theme > Install
	 * Theme > Update
	 *
	 * @param \Theme_Upgrader $upgrader
	 * @param array           $data
	 */
	public function callback_upgrader_process_complete( $upgrader, $data ) {

		if (
			! is_a( $upgrader, 'Theme_Upgrader' )
			||
			'theme' !== $data['type']
			||
			! in_array( $data['action'], [ 'install', 'update' ] )
		) {

			return;

		}

		if ( 'install' === $data['action'] ) {

			$this->theme_install( $upgrader );

			return;

		}

		$bulk   = ( ! empty( $data['bulk'] ) && true === $data['bulk'] );
		$themes = ( $bulk ) ? $data['themes'] : [ $upgrader->result['destination_name'] ];

		foreach ( $themes as $stylesheet ) {

			$this->theme_update( $stylesheet, $bulk );

		}

	}

	/**
	 * Theme > Install
	 *
	 * @param \Theme_Upgrader $upgrader
	 */
	private function theme_install( $upgrader ) {

		Timer::stop();

		$theme = $upgrader->theme_info();

		$this->log(
			'install',
			_x(
				'"%s" theme installed',
				'Theme name',
				'gd-system-plugin'
			),
			[
				'name'       => $theme->Name,
				'version'    => $theme->Version,
				'stylesheet' => $theme->Stylesheet,
				'template'   => $theme->Template,
				'author'     => $theme->Author,
			]
		);

	}

	/**
	 * Theme > Update
	 *
	 * @param string $stylesheet
	 * @param bool   $bulk
	 */
	private function theme_update( $stylesheet, $bulk ) {

		Timer::stop();

		$theme = wp_get_theme( $stylesheet );
		$new   = get_file_data( $theme->get_stylesheet_directory() . '/style.css', [ 'Version' => 'Version' ] );

		$meta = [
			'name'        => $theme->Name,
			'old_version' => $theme->Version,
			'new_version' => empty( $new['Version'] ) ? $theme->Version : $new['Version'],
			'stylesheet'  => $theme->Stylesheet,
			'template'    => $theme->Template,
			'author'      => $theme->Author,
			'bulk'        => (bool) $bulk,
		];

		if ( empty( $meta['old_version'] ) || $meta['old_version'] === $meta['new_version'] ) {

			$summary = _x(
				'"%1$s" theme updated to %2$s',
				'1: theme name, 2: New theme version',
				'gd-system-plugin'
			);

		} else {

			$summary = _x(
				'"%1$s" theme updated from %2$s to %3$s',
				'1: Theme name, 2: Old theme version, 3: New theme version',
				'gd-system-plugin'
			);

		}

		$this->log( 'update', $summary, $meta );

	}

}
