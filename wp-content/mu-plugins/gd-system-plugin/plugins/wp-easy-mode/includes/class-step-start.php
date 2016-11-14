<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Step_Start extends Step {

	/**
	 * Class constructor
	 *
	 * @param Log $log
	 */
	public function __construct( Log $log ) {

		parent::__construct( $log );

		$this->args = [
			'name'       => 'start',
			'title'      => __( 'Start', 'wp-easy-mode' ),
			'page_title' => __( 'New to WordPress?', 'wp-easy-mode' ),
			'can_skip'   => false,
		];

	}

	/**
	 * Step init
	 */
	protected function init() {}

	/**
	 * Step content
	 */
	public function content() {

		update_option( 'wpem_last_viewed', $this->name );

		?>
		<p><?php _e( "Our WordPress setup wizard will help you create a fully-functional website in just a few simple steps so you can get online faster - no code required!", 'wp-easy-mode' ) ?></p>

		<p><?php _e( "It's completely optional and will only take a few minutes.", 'wp-easy-mode' ) ?></p>
		<?php

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="hidden" id="wpem_continue" name="wpem_continue" value="yes">
		<input type="submit" id="wpem_no_thanks" class="button button-secondary" value="<?php esc_attr_e( 'No thanks', 'wp-easy-mode' ) ?>">
		<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Continue', 'wp-easy-mode' ) ?>">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$continue = filter_input( INPUT_POST, 'wpem_continue' );

		$this->log->add_step_field( 'wpem_continue', $continue );

		if ( 'no' === $continue ) {

			wpem_quit();

			return;

		}

		if ( isset( $this->log->geodata ) ) {

			new Smart_Defaults( $this->log->geodata );

		}

		wpem_mark_as_started();

	}

}
