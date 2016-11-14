<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists('SmartProductPlugin') ) {
	
	class SmartProductPlugin {
		
		static public function init() {
			
			self::_createCustomPostType();
			
			// Add actions
			add_action('wp_enqueue_scripts', 	array('SmartProductPlugin', 'enqueueScripts') );
			add_action('admin_enqueue_scripts',	array('SmartProductPlugin', 'enqueueAdminScripts') );
			add_action('add_meta_boxes', 		array('SmartProductPlugin', 'addMetaBoxes') );
			add_action('save_post', 			array('SmartProductPlugin', 'postSave') );
			
			// AJAX
			add_action('wp_ajax_update_smart_product_images', array('SmartProductPlugin', 'updateImages') );
			
			// Create shortcode
			add_shortcode('smart-product', array('SmartProductPlugin', 'shortcode') );
			
			// Image in metabox
			add_image_size('smart-product-thumb', 100, 100 );

			// Woo Commerce Product Image
			add_filter('woocommerce_single_product_image_html', array('SmartProductPlugin', 'wooCommerceImage'), 999, 2 );

		}
		
		/**
		 * Register 'Three Sixty' Custom Post Type
		 *
		 * @method _createCustomPostType
		 * @author Ilya K.
		 */
		
		static private function _createCustomPostType()
		{
			$labels = array(
					'name' 			=> _x('Smart Product', 'post type general name'),
					'singular_name' => _x('Smart Product', 'post type singular name'),
					'add_new' 		=> __('Add New', 			'topdevs'),
					'add_new_item' 	=> __('Add New Smart Product', 'topdevs'),
					'edit_item' 	=> __('Edit Smart Product', 	'topdevs'),
					'new_item' 		=> __('New Smart Product', 	'topdevs'),
					'all_items' 	=> __('All Smart Products',	'topdevs'),
					'view_item' 	=> __('View Smart Product',	'topdevs'),
					'search_items' 	=> __('Search Smart Products',  'topdevs'),
					'not_found' 	=> __('No Smart Products found','topdevs'),
					'not_found_in_trash'=> __('No Smart Products found in Trash', 'topdevs'),
					'parent_item_colon'	=> '',
					'menu_name' 		=> __('Smart Product', 'topdevs')
			);
			$args = array(
					'labels' 			=> $labels,
					'public' 			=> false,
					'publicly_queryable' => false,
					'show_ui' 			=> true,
					'show_in_menu' 		=> true,
					'query_var' 		=> true,
					'rewrite' 			=> true,
					'capability_type' 	=> 'post',
					'has_archive' 		=> false,
					'hierarchical' 		=> false,
					'menu_position' 	=> null,
					'menu_icon' 		=> 'dashicons-visibility',
					'supports' 			=> array('title')
			);
			register_post_type('smart-product', $args );
		}
		
		
		/**
		 * Front end scripts
		 * 
		 */
		
		static public function enqueueScripts() {
			
			// Make sure jQuery migrate added
			wp_enqueue_script( 'jquery-migrate', "http://code.jquery.com/jquery-migrate-1.2.1.min.js", array('jquery') );
			
			// Add Threesixty files
			// Included in smart.product.min.js, since v.1.2, use for development only
			//wp_enqueue_script( 'threesixty', 			plugins_url( '/js/threesixty.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style ( 'threesixty', 			plugins_url( '/css/360.css', __FILE__ ) );
			
			// Add Magnific pop-up files
			wp_enqueue_script( 'magnific-popup', 		plugins_url( '/js/jquery.magnific-popup.min.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_style ( 'magnific-popup', 		plugins_url( '/css/magnific-popup.css', __FILE__ ) );

			// Add compiled and minified version, since v.1.2
			wp_enqueue_script( 'smart-product', plugins_url( '/js/smart.product.min.js', __FILE__ ), array( 'jquery', 'magnific-popup' ) );
			
			// Enable AJAX support only if defined by developer
			if ( defined( "SPV_AJAX" ) && SPV_AJAX === true )
				wp_enqueue_script( 'spv.ajax', plugins_url( '/js/spv.ajax.js', __FILE__ ), array( 'jquery' ) );
			
		}
		
		/**
		 * Dashboard scripts
		 *
		 */
		
		static public function enqueueAdminScripts() {
			
			global $post;

			wp_enqueue_style ('threesixty', plugins_url('/css/admin.css', __FILE__ ) );

			if ( is_object( $post ) ) {
			
				wp_enqueue_media();
				
				wp_enqueue_script('jquery-ui-sortable');		
				wp_enqueue_script('threesixty', plugins_url('/js/spv.admin.js', __FILE__ ), array('jquery') );

				wp_localize_script( 'threesixty', 'SmartProduct',	array( 
						'ajax_url' 	=> admin_url('admin-ajax.php'),
						'post_id'	=> $post->ID
						) );
			}
		
		}
		
		/**
		 * Show shortcode view
		 * 
		 * @method shortcode
		 * @author Ilya K.
		 */
		
		static function shortcode( $atts ) {
			
			$slider = new ThreeSixtySlider( $atts );
			
			ob_start();
			
			$slider->show();
		
			return ob_get_clean();
		}
		
		/**
		 * @method addMetaBox
		 * @author Ilya K.
		 */
		
		static public function addMetaBoxes() {
			
			// Shortcodes Examples
			add_meta_box( 
				'smart-product-shortcodes-meta-box', 
				'Smart Product Shortcodes', 
				array( 'SmartProductPlugin', 'metaBoxShortcodes' ), 
				'smart-product' 
				);

			// Dragable Images
			add_meta_box(
				'smart-product-images-meta-box', 
				'Smart Product Images', 
				array( 'SmartProductPlugin', 'metaBoxImages' ), 
				'smart-product' 
				);

			// Woo Commerce Product
			add_meta_box(
				'smart-product-meta-box', 
				'Smart Product', 
				array( 'SmartProductPlugin', 'wooProductMetaBox' ), 
				'product',
				'side'
				);
			
		}
		
		/**
		 * @method registerWidget
		 * @author Ilya K.
		 */
		
		static public function registerWidget() {
		
			register_widget('SmartProductWidget');
		
		}
		
		/**
		 * @method metaBoxImages
		 * @author Ilya K.
		 */
		
		static public function metaBoxImages( $post ) {
			
			$images = get_post_meta( $post->ID, '360_images', true );
			if ( $images == "") $images = array();
			
			require_once 'views/metabox-images.php';
		
		}

		/**
		 * @method metaBoxShortcodes
		 * @author Ilya K.
		 */
		
		static public function metaBoxShortcodes( $post ) {

			$images = get_post_meta( $post->ID, '360_images', true );
			require_once 'views/metabox-shortcodes.php';
		
		}

		/**
		 * @method wooProductMetaBox
		 * @author Ilya K.
		 */
		
		static public function wooProductMetaBox( $post ) {

			$smart_product = get_post_meta( $post->ID, "smart_product_meta", true );
			
			// Defaults
			extract( shortcode_atts( array(
					'show' 		=> 'false',
					'id' 		=> '',
					'nav' 		=> 'true',
					'border' 	=> 'true',
					'scrollbar'	=> '',
					'width' 	=> '',
					'style' 	=> 'flat',
					'color' 	=> 'gray',
					'autoplay' 	=> 'false',
					'interval'	=> '40',
					'fullscreen' => 'false',
					'move_on_scroll' => 'false',
			), $smart_product ) );

			// Get all Smart Products
			$threesxity_sliders = get_posts( array(
							'posts_per_page'  => -1,
							'post_type'       => 'smart-product'
					) );

			require_once 'views/metabox-woo.php';
		
		}

		/**
		 * Call on post submit to save meta
		 * 
		 */
		static public function postSave( $post_id ) {
	
			// Check if our nonce is set.
			if ( ! isset( $_POST['smart_product_woo'] ) )
				return $post_id;

			$nonce = $_POST['smart_product_woo'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'smart_product_woo_metabox' ) )
				return $post_id;

			// If this is an autosave, our form has not been submitted,
			// so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				return $post_id;

			// Check post type.
			if ( 'product' != $_POST['post_type'] )
				return $post_id;

			/* OK, its safe for us to save the data now. */

			$smart_product = array();

			$smart_product['id'] 			= strip_tags( $_POST['smart_product_id'] );
			$smart_product['width'] 		= strip_tags( $_POST['smart_product_width'] );
			$smart_product['scrollbar'] 	= strip_tags( $_POST['smart_product_scrollbar'] );
			$smart_product['color'] 		= strip_tags( $_POST['smart_product_color'] );
			$smart_product['style'] 		= strip_tags( $_POST['smart_product_style'] );
			$smart_product['interval'] 		= strip_tags( $_POST['smart_product_interval'] );
			$smart_product['nav'] 			= ( isset( $_POST['smart_product_nav'] ) && $_POST['smart_product_nav'] == "true" ) ? "true" : "false";
			$smart_product['border'] 		= ( isset( $_POST['smart_product_border'] ) && $_POST['smart_product_border'] == "true" ) ? "true" : "false";
			$smart_product['show'] 			= ( isset( $_POST['smart_product_show'] ) && $_POST['smart_product_show'] == "true" ) ? "true" : "false";
			$smart_product['autoplay'] 		= ( isset( $_POST['smart_product_autoplay'] ) && $_POST['smart_product_autoplay'] == "true" ) ? "true" : "false";
			$smart_product['fullscreen'] 	= ( isset( $_POST['smart_product_fullscreen'] ) && $_POST['smart_product_fullscreen'] == "true" ) ? "true" : "false";
			$smart_product['move_on_scroll']= ( isset( $_POST['smart_product_move_on_scroll'] ) && $_POST['smart_product_move_on_scroll'] == "true" ) ? "true" : "false";

			update_post_meta( $post_id, "smart_product_meta", $smart_product );

		}

		/**
		 * Update images order when drag
		 */
		
		static public function updateImages() {

			global $wpdb;
			
			$images  = $_POST['images_ids'];
			$post_id = $_POST['post_id'];
			
			update_post_meta( $post_id, '360_images', $images );
			
			echo '<ul id="smart-product-sortable">';
			foreach ( $images as $id ) { echo '<li>' . wp_get_attachment_image( $id, 'smart-product-thumb' ) . '</li>'; }
			echo '</ul>';
			
			die();
		}

		/**
		 * Show Smart Product view as Woo product image
		 * @author Ilya K.
		 */

		static public function wooCommerceImage( $html, $post_id = null ) {

			if ( is_null( $post_id ) ) {
				
				global $post;
				$post_id = $post->ID;
			}

			$smart_product = get_post_meta( $post_id, "smart_product_meta", true );

			// Check if show options is turn on
			if ( ! isset( $smart_product['show'] ) || $smart_product['show'] != 'true' )
				return $html;

			// Check if id set
			if ( ! isset( $smart_product['id'] ) || $smart_product['id'] == "" )
				return $html;

			// Create slider instance
			$slider = new ThreeSixtySlider( $smart_product );
			
			ob_start();
			
			$slider->show();
		
			return ob_get_clean();
		}

	}

}

?>