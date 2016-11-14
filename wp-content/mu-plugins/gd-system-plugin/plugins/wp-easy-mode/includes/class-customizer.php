<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Customizer {

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( ! filter_input( INPUT_GET, wpem()->page_slug ) ) {

			return;

		}

		$pointer = new Pointer;

		$btn_next = __( 'Next', 'wp-easy-mode' );

		// If beaver builder is installed
		$pagebuilder_installed = ( class_exists( 'FLBuilderLoader' ) ) ? true : false;

		// Step 1
		$pointer->register(
			[
				'id'        => 'wpem_done_step_0',
				'screen'    => 'customize',
				'target'    => '#customize-theme-controls',
				'cap'       => 'manage_options',
				'query_var' => [ wpem()->page_slug => 1 ],
				'options'   => [
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							__( 'Congratulations!', 'wp-easy-mode' ),
							__( 'You\'ve just created your website!  Here are a few quick tips to get you started.', 'wp-easy-mode' )
						)
					),
					'position' => [
						'edge'  => 'left',
						'align' => 'right',
					],
				],
				'btn_primary'   => $btn_next,
                'close_on_load' => true,
				'next_pointer'  => 'wpem_done_step_1',
			]
		);

		// Step 2
		$pointer->register(
			[
				'id'        => 'wpem_done_step_1',
				'screen'    => 'customize',
				'target'    => '#accordion-section-title_tagline',
				'cap'       => 'manage_options',
				'query_var' => [ wpem()->page_slug => 1 ],
				'options'   => [
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							__( 'The Customizer', 'wp-easy-mode' ),
							__( 'This is the Customizer area. Use it to change your site style and edit your site header, footer and sidebar areas.', 'wp-easy-mode' )
						)
					),
					'position' => [
						'edge'  => 'left',
						'align' => 'right',
					],
				],
				'btn_primary'   => $btn_next,
				'close_on_load' => true,
				'next_pointer'  => $pagebuilder_installed ? 'wpem_done_step_2' : 'wpem_done_step_3',
			]
		);

		if ( $pagebuilder_installed ) {

			// Step 3
			$pointer->register(
				[
					'id'        => 'wpem_done_step_2',
					'screen'    => 'customize',
					'target'    => '#bb-launcher-button',
					'cap'       => 'manage_options',
					'query_var' => [ wpem()->page_slug => 1 ],
					'options'   => [
						'content'  => wp_kses_post(
							sprintf(
								'<h3>%s</h3><p>%s</p>',
								__( 'Page Builder', 'wp-easy-mode' ),
								__( 'This launches the Page Builder. Use it to make changes to the content area of your website.', 'wp-easy-mode' )
							)
						),
						'position' => [
							'edge'  => 'left',
							'align' => 'right',
						],
					],
					'btn_primary'   => $btn_next,
					'close_on_load' => true,
					'next_pointer'  => 'wpem_done_step_3',
				]
			);

		}

		// Final Step Button & Content
		if ( $this->is_english() ) {

			$btn_primary = __( 'Watch Video', 'wp-easy-mode' ) . '<span class="dashicons dashicons-video-alt2"></span>';
			$content     = __( 'Click "Watch Video" to view a quick demonstration of how to customize your site with the Customizer.', 'wp-easy-mode' );

		} else {

			$btn_primary = __( 'Learn More', 'wp-easy-mode' );
			$content     = __( 'Click "Learn More" to view some tips on how to customize your site with the Customizer.', 'wp-easy-mode' );

		}

		// Final Step
		$pointer->register(
			[
				'id'        => 'wpem_done_step_3',
				'screen'    => 'customize',
				'target'    => '#customize-theme-controls',
				'cap'       => 'manage_options',
				'query_var' => [ wpem()->page_slug => 1 ],
				'options'   => [
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							__( 'Tutorial', 'wp-easy-mode' ),
							$content
						)
					),
					'position' => [
						'edge'  => 'left',
						'align' => 'right',
					],
				],
				'btn_primary'       => $btn_primary,
				'btn_primary_class' => 'show-overlay',
				'close_on_load'     => true,
				'btn_close'         => true,
			]
		);

		add_action( 'customize_controls_print_styles',         [ $this, 'print_styles' ] );
		add_action( 'customize_controls_enqueue_scripts',      [ $this, 'enqueue_scripts' ] );
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'print_script_templates' ] );

	}

	/**
	 * Print custom styles
	 *
	 * @action customize_controls_print_styles
	 */
	public function print_styles() {

		?>
		<style type="text/css">
			body.wp-customizer .change-theme {
				display: none;
			}
		</style>
		<?php

	}

	/**
	 * Enqueue scripts for the customizer
	 *
	 * @action customize_controls_enqueue_scripts
	 */
	public function enqueue_scripts() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wpem-customizer', wpem()->assets_url . "js/customizer{$suffix}.js", [ 'jquery', 'backbone', 'underscore' ], wpem()->version, true );

		wp_enqueue_style( 'wpem-customizer', wpem()->assets_url . "css/customizer{$suffix}.css", [], wpem()->version );

	}

	/**
	 * Print templates needed by our scripts
	 *
	 * @action customize_controls_print_footer_scripts
	 */
	public function print_script_templates() {

		if ( $this->is_english() ) {

			$content_class = 'video';

			ob_start();

			?>
			<div class="video-wrapper">
				<iframe src="//player.vimeo.com/video/146040077"
				        webkitallowfullscreen=""
				        mozallowfullscreen=""
				        allowfullscreen=""
				        frameborder="0">
				</iframe>
			</div>
			<?php

			$content = ob_get_clean();

		} else {

			$content_class = 'text';

			ob_start();

			?>
			<h3>
				<span class="dashicons dashicons-admin-customizer"></span>
				<?php _e( 'The Customizer', 'wp-easy-mode' ); ?>
			</h3>
			<p><?php _e( 'You are now in the Customizer, a tool that enables you to make changes to your website’s appearance, and preview those changes before publishing them. We’ve created a few commonly-used pages and widgets to help you get started.', 'wp-easy-mode' ); ?></p>
			<p><?php _e( 'The top of the Customizer indicates the name of the active theme you’ve selected. You can change the theme at any time, but doing so will change these options and reset any customizations you might have made here.', 'wp-easy-mode' ); ?></p>
			<p><?php _e( 'The options available in the Customizer will vary, depending on the features supported by your current theme. But most themes include these basic Customizer controls:', 'wp-easy-mode' ); ?></p>
			<ul>
				<li><?php _e( 'Site Identity', 'wp-easy-mode' ); ?></li>
				<li><?php _e( 'Colors', 'wp-easy-mode' ); ?></li>
				<li><?php _e( 'Header and Background Images', 'wp-easy-mode' ); ?></li>
				<li><?php _e( 'Navigation Menus', 'wp-easy-mode' ); ?></li>
				<li><?php _e( 'Widgets', 'wp-easy-mode' ); ?></li>
			</ul>
			<p><?php _e( 'When you’re happy with your changes, click <strong>Save & Publish</strong> to keep these new settings. Or, simply close the Customizer by clicking the “X” in the top left-hand corner to discard your changes.', 'wp-easy-mode' ); ?></p>
			<?php

			$content = wp_kses_post( ob_get_clean() );

		}

		?>
		<script type="text/template" id="wpem-overlay-template">
			<div id="wpem-overlay" class="<?php echo $content_class; //xss ok ?>">
				<div class="wpem-overlay-background"></div>
				<div class="wpem-overlay-foreground">
					<div class="wpem-overlay-control">
						<span class="dashicons dashicons-no-alt"></span>
					</div>
					<div class="wpem-overlay-content">
						<?php echo $content; //xss ok ?>
					</div>
				</div>
			</div>
		</script>
		<?php

	}

	/**
	 * Helper function to see if we are dealing with english
	 *
	 * @return bool
	 */
	private function is_english() {

		return ( 'en' === substr( get_locale(), 0, 2 ) );

	}

}
