<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Step_Theme extends Step {

	/**
	 * Max number of header images displayed
	 *
	 * @var int
	 */
	const MAX_HEADER_IMAGES = 5;

	/**
	 * Image offset query arg
	 */
	const IMAGE_OFFSET_ARG = 'images-offset';

	/**
	 * Hold the image api we got by dependency injection
	 *
	 * @var object
	 */
	private $image_api;

	/**
	 * Class constructor
	 *
	 * @param Log       $log
	 * @param Image_API $image_api
	 */
	public function __construct( Log $log, Image_API $image_api ) {

		$this->image_api = $image_api;

		parent::__construct( $log );

		$this->args = [
			'name'       => 'theme',
			'title'      => __( 'Theme', 'wp-easy-mode' ),
			'page_title' => __( 'Choose a Theme', 'wp-easy-mode' ),
			'can_skip'   => false,
		];

		add_action( 'wpem_print_footer_scripts_' . $this->args['name'], [ $this, 'print_footer_scripts' ] );

		$pointer = new Pointer;

		$pos   = 1;
		$count = 2;

		if ( $this->has_header_images() ) {
			$count = 3;

			$pointer->register(
				[
					'id'        => 'wpem_theme_preview_1',
					'target'    => '#wpem-header-images-wrapper',
					'cap'       => 'manage_options',
					'options'   => [
						'content'  => wp_kses_post(
							sprintf(
								'<h3>%s</h3><p>%s</p>',
								sprintf( __( 'Step %1$s of %2$s', 'wp-easy-mode' ), $pos++, $count ),
								__( 'Choose a stylish header image for your website.', 'wp-easy-mode' )
							)
						),
						'position' => [
							'edge'  => 'left',
							'align' => 'right',
						],
					],
					'btn_primary'   => __( 'Next', 'wp-easy-mode' ),
					'close_on_load' => true,
					'next_pointer'  => 'wpem_theme_preview_2',
				]
			);
		}

		$pointer->register(
			[
				'id'        => 'wpem_theme_preview_2',
				'target'    => '.wp-full-overlay-header .next-theme',
				'cap'       => 'manage_options',
				'options'   => [
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							sprintf( __( 'Step %1$s of %2$s', 'wp-easy-mode' ), $pos++, $count ),
							__( 'Preview different website designs using the the left and right arrows.', 'wp-easy-mode' )
						)
					),
					'position' => [
						'at' => 'left bottom',
						'my' => 'left-63 top',
					],
				],
				'btn_primary'   => __( 'Next', 'wp-easy-mode' ),
				'close_on_load' => true,
				'next_pointer'  => 'wpem_theme_preview_3',
			]
		);

		$pointer->register(
			[
				'id'        => 'wpem_theme_preview_3',
				'target'    => '.button-primary.theme-install',
				'cap'       => 'manage_options',
				'options'   => [
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							sprintf( __( 'Step %1$s of %2$s', 'wp-easy-mode' ), $pos, $count ),
							__( "When you've found a design you like, click Select to install it.", 'wp-easy-mode' )
						)
					),
					'position' => [
						'at' => 'left bottom',
						'my' => 'left-34 top+5',
					],
				],
				'btn_primary'       => __( 'Close', 'wp-easy-mode' ),
				'btn_primary_close' => true,
				'close_on_load'     => true,
			]
		);

		$pointer->register_scripts();

	}

	/**
	 * Add pointer script
	 *
	 * @action wpem_print_footer_scripts_theme
	 */
	public function print_footer_scripts() {

		wp_print_styles( 'wp-pointer' );
		wp_print_scripts( 'wp-pointer' );
		wp_print_scripts( 'wpem-pointers' );
		wp_print_scripts( 'wpem-theme' );

	}

	/**
	 * Step init
	 */
	protected function init() {

		add_filter( 'wpem_themes', [ $this, 'themes' ] );

		if ( $images_offset = filter_input( INPUT_GET, static::IMAGE_OFFSET_ARG, FILTER_VALIDATE_INT ) ) {

			$this->header_image_list( $images_offset );

			exit;

		}

	}

	/**
	 * Step content
	 */
	public function content() {

		?>
		<style type="text/css">
			.wp-full-overlay.expanded {
				margin-left: 275px;
			}
			.wp-full-overlay.expanded .wp-full-overlay-sidebar {
				width: 275px;
			}
			.wp-full-overlay.expanded .wp-full-overlay-footer {
				width: 275px;
				height: 47px;
				border-top: none;
			}
			.install-theme-info .theme-details {
				margin-top: 10px;
			}
		</style>

		<p class="lead-text align-center"><?php _e( "Choose a design for your website (don't worry, you can change this later)", 'wp-easy-mode' ) ?></p>

		<div class="theme-browser rendered">

			<div class="themes"></div>

		</div>

		<script type="text/html" id="wpem-template-theme">
			<div class="theme">
				<div class="theme-screenshot"><img src="#"></div>
				<span class="more-details"><?php _e( 'Preview', 'wp-easy-mode' ) ?></span>
				<div class="theme-author"><?php _e( 'By', 'wp-easy-mode' ) ?> <span></span></div>
				<h3 class="theme-name"></h3>
				<a href="#" class="preview-theme"></a>
			</div>
		</script>

		<script type="text/html" id="wpem-template-theme-preview">
			<div class="theme-install-overlay wp-full-overlay expanded">
				<div class="wp-full-overlay-sidebar">
					<div class="wp-full-overlay-header">
						<a href="#" class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="previous-theme"><span class="screen-reader-text"><?php _e( 'Previous', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="next-theme"><span class="screen-reader-text"><?php _e( 'Next', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="button button-primary theme-install"><?php _e( 'Select', 'wp-easy-mode' ) ?></a>
					</div>
					<div class="wp-full-overlay-sidebar-content">
						<div class="install-theme-info">
							<h3 class="theme-name"></h3>
							<span class="theme-by"><?php _e( 'By', 'wp-easy-mode' ) ?> <span></span></span>
							<div class="theme-details">
								<div class="theme-description"></div>
							</div>
							<div id="wpem-header-images-wrapper"></div>
						</div>
					</div>
					<div class="wp-full-overlay-footer">
						<div class="devices">
							<button type="button" class="preview-desktop active" aria-pressed="true" data-device="desktop"><span class="screen-reader-text"><?php _e( 'Enter desktop preview mode' ); ?></span></button>
							<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet"><span class="screen-reader-text"><?php _e( 'Enter tablet preview mode' ); ?></span></button>
							<button type="button" class="preview-mobile" aria-pressed="false" data-device="mobile"><span class="screen-reader-text"><?php _e( 'Enter mobile preview mode' ); ?></span></button>
						</div>
						<button type="button" class="collapse-sidebar button-secondary expanded" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar', 'wp-easy-mode' ) ?>">
							<span class="collapse-sidebar-arrow"></span>
							<span class="collapse-sidebar-label"><?php _e( 'Collapse', 'wp-easy-mode' ) ?></span>
						</button>
					</div>
				</div>
				<div class="wp-full-overlay-main"></div>
			</div>
		</script>

		<?php if ( $this->has_header_images() ) : ?>
			<script type="text/html" id="wpem-template-header-images">
				<h4><?php _e( 'Suggested Header Images', 'wp-easy-mode' ) ?></h4>
				<ul class="wpem-header-images-list">
					<?php $this->header_image_list() ?>
				</ul>
				<p class="description"><?php _e( 'You can change this header image again later, or even upload a custom image from your computer.', 'wp-easy-mode' ) ?></p>
				<p><a href="#" class="image-license"><?php _e( 'About Image Licenses', 'wp-easy-mode' ) ?></a></p>
			</script>
		<?php endif;

	}

	/**
	 * Return an array of header images.
	 *
	 * @return array
	 */
	private function get_header_images() {

		$images = $this->image_api->get_images_by_cat( wpem_get_site_industry() );

		// If we are missing a large format let's skip this image
		$images = array_filter( $images, function( $image ) {

			return ! is_null( $image->large );

		} );


		return $images;

	}

	/**
	 * Check if header images exist.
	 *
	 * @return bool
	 */
	private function has_header_images() {

		$images = $this->get_header_images();

		return ( 'store' !== wpem_get_site_type() && ! empty( $images ) );

	}

	/**
	 * Display a list of header images.
	 *
	 * @param int $offset (optional)
	 */
	private function header_image_list( $offset = 0 ) {

		$images = $this->get_header_images();

		if ( empty( $images ) ) {

			return;

		}

		// Limit the number of images displayed
		$count  = count( $images );
		$images = array_slice( $images, $offset, absint( static::MAX_HEADER_IMAGES ) );

		foreach ( $images as $key => $image ) :

            /**
             * Resize to max 2400 px wide 50% quality
             * Documentation: https://github.com/asilvas/node-image-steam
             */
            $image_api_base_url = is_callable( '\WPaaS\Plugin::config' ) ? \WPaaS\Plugin::config( 'imageApi.url' ) : 'http://isteam.wsimg.com/stock/';
            $image_url          = sprintf( '%s/%s/:/rs=w:2400/qt=q:50', $image_api_base_url, wp_basename( $image->fullsize ) );

			?>
			<li <?php echo ( 0 === $offset && 0 === $key ) ? 'class="selected"' : '' ?> >
				<a href="#"
                   data-image-preview-url="<?php echo esc_url( $image->large ); ?>"
                   data-image-url="<?php echo esc_url( $image_url ); ?>">
					<span class="screen-reader-text"><?php _e( 'Set image' ) ?></span>
					<img src="<?php echo esc_url( $image->preview ) ?>">
				</a>
			</li>
			<?php

		endforeach;

		$new_offset = $offset + static::MAX_HEADER_IMAGES;

		if ( ( $count - $new_offset ) > 0 ) {

			printf(
				'<li class="load-more"><a href="%s">%s</a></li>',
				esc_url( add_query_arg( static::IMAGE_OFFSET_ARG, $new_offset, $this->url ) ),
				__( 'Load more images', 'wp-easy-mode' )
			);

		}

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="hidden" id="wpem_selected_theme" name="wpem_selected_theme" value="">
		<input type="hidden" id="wpem_selected_header_image_url" name="wpem_selected_header_image_url" value="">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$stylesheet       = filter_input( INPUT_POST, 'wpem_selected_theme', FILTER_SANITIZE_STRING );
		$header_image_url = filter_input( INPUT_POST, 'wpem_selected_header_image_url', FILTER_VALIDATE_URL );
		$header_image_url = ( $header_image_url ) ? esc_url_raw( $header_image_url ) : null;

		$this->log->add_step_field( 'wpem_selected_theme', $stylesheet );
		$this->log->add_step_field( 'wpem_selected_header_image_url', $header_image_url );

		new Demo_Importer( $stylesheet, $header_image_url );

	}

	/**
	 * Show certain themes based on site type
	 *
	 * @filter wpem_themes
	 *
	 * @param  array  $themes
	 *
	 * @return array
	 */
	public function themes( $themes ) {

		$response = wp_remote_get(
			Admin::demo_site_url(
				[
					'action' => 'list_themes',
				]
			)
		);

		if ( 200 !== wp_remote_retrieve_response_code( $response ) || is_wp_error( $response ) ) {

			return $themes;

		}

		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_array( $response ) ) {

			$themes = $response;

		}

		shuffle( $themes );

		return $themes;

	}

}
