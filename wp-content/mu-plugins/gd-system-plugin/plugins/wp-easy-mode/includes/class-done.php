<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Done {

	/**
	 * Log instance
	 *
	 * @var object
	 */
	private $log;

	/**
	 * Class constructor
	 *
	 * @param Log $log
	 */
	public function __construct( Log $log ) {

		$this->log = $log;

		$this->settings();

		$this->user_meta();

		if ( is_plugin_active( 'contact-widgets/contact-widgets.php' ) ) {

			$this->widget_contact();
			$this->widget_social();

		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			$this->woocommerce();

		}

		$this->flush_transients();

		$this->update_language_packs();

		$this->redirect();

	}

	/**
	 * Settings
	 */
	private function settings() {

		if ( empty( $this->log->steps['settings']['fields'] ) ) {

			return;

		}

		foreach ( $this->log->steps['settings']['fields'] as $option => $value ) {

			if ( 0 === strpos( $option, 'wpem_' ) ) {

				continue;

			}

			remove_all_filters( "option_{$option}" );

			update_option( $option, $value );

		}

	}

	/**
	 * User meta
	 */
	private function user_meta() {

		// Don't display the Sidekick nag
		add_user_meta( get_current_user_id(), 'sk_ignore_notice', true );

	}

	/**
	 * Use Contact step data in Contact widget
	 */
	private function widget_contact() {

		$contact_info = array_filter( (array) get_option( 'wpem_contact_info', [] ) );

		if ( ! $contact_info ) {

			delete_option( 'widget_wpcw_contact' );

			return;

		}

		$widget = (array) get_option( 'widget_wpcw_contact', [] );

		unset( $widget['_multiwidget'] );

		$keys = array_keys( $widget );
		$key  = array_shift( $keys );

		foreach ( $widget[ $key ] as $field => $data ) {

			$value = wpem_get_contact_info( $field );

			if ( isset( $widget[ $key ][ $field ]['value'] ) && false !== $value ) {

				$value = str_replace( [ '<br />', '<br/>', '<br>' ], '', $value );

				$widget[ $key ][ $field ]['value'] = $value;

			}

		}

		// Refresh field order
		$widget[ $key ] = $this->refresh_widget_field_order( $widget[ $key ] );

		$widget['_multiwidget'] = 1;

		update_option( 'widget_wpcw_contact', $widget );

	}

	/**
	 * Use Contact step data in Social widget
	 */
	private function widget_social() {

		$social_profiles = array_filter( (array) get_option( 'wpem_social_profiles', [] ) );

		if ( ! $social_profiles ) {

			delete_option( 'widget_wpcw_social' );

			return;

		}

		$widget = (array) get_option( 'widget_wpcw_social', [] );

		unset( $widget['_multiwidget'] );

		if ( ! $widget ) {

			return;

		}

		$keys = array_keys( $widget );
		$key  = array_shift( $keys );

		include_once wpem()->base_dir . 'includes/social-networks.php';

		// Remove all default social networks from the widget
		foreach ( $social_networks as $network => $data ) {

			if ( isset( $social_networks[ $network ] ) ) {

				unset( $widget[ $key ][ $network ] );

			}

		}

		$fields = [];

		if ( isset( $widget[ $key ]['title'] ) ) {

			// Add the title field to the new list
			$fields['title'] = $widget[ $key ]['title'];

			// Remove the title from the original widget
			unset( $widget[ $key ]['title'] );

		}

		// Prepend new social networks to the fields list
		foreach ( wpem_get_social_profiles() as $network ) {

			$fields[ $network ] = [
				'value' => wpem_get_social_profile_url( $network ),
				'order' => '',
			];

		}

		// Merge updated fields with the original widget
		$widget[ $key ] = $fields + $widget[ $key ];

		// Refresh field order
		$widget[ $key ] = $this->refresh_widget_field_order( $widget[ $key ] );

		$widget['_multiwidget'] = 1;

		update_option( 'widget_wpcw_social', $widget );

	}

	/**
	 * Refresh field order in Contact and Social widgets
	 *
	 * @param  array $instance
	 *
	 * @return array
	 */
	private function refresh_widget_field_order( $instance ) {

		$i = 0;

		foreach ( $instance as $key => $data ) {

			if ( isset( $data['order'] ) ) {

				$instance[ $key ]['order'] = $i;

			}

			$i++;

		}

		return $instance;

	}

	/**
	 * WooCommerce
	 */
	private function woocommerce() {

		// Force secure checkout when SSL is already present
		if ( is_ssl() ) {

			update_option( 'woocommerce_force_ssl_checkout', 'yes' );

		}

		$email = wpem_get_contact_info( 'email' );
		$email = empty( $email ) ? wp_get_current_user()->user_email : $email;

		update_option( 'woocommerce_email_from_address', $email );
		update_option( 'woocommerce_stock_email_recipient', $email );

		$country = ! empty( $this->log->geodata['country_code'] ) ? $this->log->geodata['country_code'] : null;
		$region  = ! empty( $this->log->geodata['region_code'] )  ? $this->log->geodata['region_code']  : null;

		if ( $country ) {

			$this->woocommerce_locale_settings( $country, $region );

		}

	}

	/**
	 * WooCommerce locale settings
	 *
	 * @param string $country
	 * @param string $region
	 */
	private function woocommerce_locale_settings( $country, $region ) {

		$data = include_once WP_PLUGIN_DIR . '/woocommerce/i18n/locale-info.php';

		if ( ! isset( $data[ $country ] ) ) {

			return;

		}

		$default_country = ( $region && isset( $data[ $country ]['tax_rates'][ $region ] ) ) ? sprintf( '%s:%s', $country, $region ) : $country;

		update_option( 'woocommerce_default_country',    $default_country );
		update_option( 'woocommerce_currency',           $data[ $country ]['currency_code'] );
		update_option( 'woocommerce_currency_pos',       $data[ $country ]['currency_pos'] );
		update_option( 'woocommerce_price_decimal_sep',  $data[ $country ]['decimal_sep'] );
		update_option( 'woocommerce_price_thousand_sep', $data[ $country ]['thousand_sep'] );
		update_option( 'woocommerce_dimension_unit',     $data[ $country ]['dimension_unit'] );
		update_option( 'woocommerce_weight_unit',        $data[ $country ]['decimal_sep'] );

	}

	/**
	 * Flush the transients cache
	 *
	 * @return int|bool
	 */
	private function flush_transients() {

		global $wpdb;

		return $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '%_transient_%';" );

	}

	/**
	 * Update language packs for plugins & themes
	 *
	 * @return array|bool
	 */
	private function update_language_packs() {

		if ( 'en_US' === get_locale() ) {

			return false;

		}

		if ( ! function_exists( 'wp_clean_update_cache' ) ) {

			require_once ABSPATH . 'wp-includes/update.php';

		}

		if ( ! class_exists( '\Language_Pack_Upgrader' ) ) {

			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		}

		if ( ! class_exists( '\Automatic_Upgrader_Skin' ) ) {

			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php';

		}

		wp_clean_update_cache();

		wp_update_themes();

		wp_update_plugins();

		$upgrader = new \Language_Pack_Upgrader( new \Automatic_Upgrader_Skin() );

		return $upgrader->bulk_upgrade();

	}

	/**
	 * Mark wizard as done and redirect
	 */
	private function redirect() {

		wpem_mark_as_done();

		wp_safe_redirect(
			wpem_get_customizer_url(
				[
					'return' => admin_url(),
					'wpem'   => 1,
				]
			)
		);

		exit;

	}

}
