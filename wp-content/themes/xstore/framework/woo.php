<?php
// **********************************************************************// 
// ! Remove Default STYLES
// **********************************************************************//

add_filter( 'woocommerce_enqueue_styles', '__return_false' );
add_filter( 'pre_option_woocommerce_enable_lightbox', 'return_no'); // Remove woocommerce prettyphoto 

function return_no($option) {
	return 'no';
}

// **********************************************************************// 
// ! Template hooks
// **********************************************************************// 

add_action('wp', 'etheme_template_hooks', 60);
if(!function_exists('etheme_template_hooks')) {
	function etheme_template_hooks() {
		add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 40 ); // add pagination above the products
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 5 );

		//add_action( 'woocommerce_single_product_summary', 'etheme_email_btn', 36 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

		/* Remove link open and close on product content */
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );


		// Change price position on the single product page
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

		if(etheme_get_option('tabs_location') == 'after_image' && etheme_get_option('tabs_type') != 'disable' && etheme_get_option('single_layout') != 'large') {
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 61 );
			add_filter('et_option_tabs_type', create_function('', 'return "accordion";'));
			if(etheme_get_option('reviews_position') == 'outside') {
				add_action( 'woocommerce_single_product_summary', 'comments_template', 110 );
			}
		}

		if( etheme_get_option('single_layout') == 'fixed' || etheme_get_custom_field('single_layout') == 'fixed' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		} else if( etheme_get_option('single_layout') == 'center' || etheme_get_custom_field('single_layout') == 'center' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			add_action( 'woocommerce_single_product_summary', 'etheme_product_cats', 8 );
		} else if( etheme_get_option('single_layout') == 'wide' || etheme_get_custom_field('single_layout') == 'wide' || etheme_get_option('single_layout') == 'right' || etheme_get_custom_field('single_layout') == 'right' ) {
			if(is_singular('product')) remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );
		} else if( etheme_get_option('single_layout') == 'booking' || etheme_get_custom_field('single_layout') == 'booking' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		} else {
			// Add product categories after title
			//add_action( 'woocommerce_single_product_summary', 'etheme_product_cats', 8 );
			add_action( 'woocommerce_single_product_summary', 'etheme_size_guide', 21 );
		}

		if(etheme_get_option('reviews_position') == 'outside') {
			add_filter( 'woocommerce_product_tabs', 'etheme_remove_reviews_from_tabs', 98 );
			add_action( 'woocommerce_after_single_product_summary', 'comments_template', 30 );
		}

		if( get_option('yith_wcwl_button_position') == 'shortcode' ) {
			add_action( 'woocommerce_after_add_to_cart_button', 'etheme_wishlist_btn', 30 );
		}

		add_action('woocommerce_account_navigation', 'etheme_my_account_title', 5);

		remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );

		/* Increase avatar size for reviews on product page */

		add_filter( 'woocommerce_review_gravatar_size', function() {
			return 80;
		}, 30 );

		// 360 view plugin
		if( class_exists( 'SmartProductPlugin' ) ) {
			remove_filter('woocommerce_single_product_image_html', array('SmartProductPlugin', 'wooCommerceImage'), 999, 2 );
		}


	}
}

if( ! function_exists('etheme_additional_information')) {
	function etheme_additional_information() {
		global $product;
		?>
	        <div class="product-attributes">
	            <?php $product->list_attributes(); ?>
	        </div>
		<?php
	}
}

if(!function_exists('etheme_get_single_product_class')) {
	function etheme_get_single_product_class( $layout ) {
	    $class = 'tabs-'.etheme_get_option('tabs_location');
	    $class .= ' single-product-'.$layout;
	    $class .= ' reviews-position-'.etheme_get_option('reviews_position');
	    if(etheme_get_option('ajax_addtocart')) $class .= ' ajax-cart-enable';
	    if(etheme_get_option('single_product_hide_sidebar')) $class .= ' sidebar-mobile-hide';

		if(!etheme_get_option('product_name_signle')) {
		    $class .= ' hide-product-name';
		}

		if( etheme_get_option('fixed_images') && $layout != 'fixed' ) {
		    $class .= ' product-fixed-images';
		} else if(etheme_get_option('fixed_content')) {
		    $class .= ' product-fixed-content';
		}

		$image_class = 'col-lg-6 col-md-6 col-sm-12';
		$infor_class = 'col-lg-6 col-md-6 col-sm-12';

		if($layout == 'small') {
		    $image_class = 'col-lg-4 col-md-5 col-sm-12';
		    $infor_class = 'col-lg-8 col-md-7 col-sm-12';
		}

		if($layout == 'large') {
		    $image_class = 'col-sm-12';
		    $infor_class = 'col-lg-6 col-md-6 col-sm-12';
		}

		if($layout == 'xsmall') {
		    $image_class = 'col-lg-9 col-md-8 col-sm-12';
		    $infor_class = 'col-lg-3 col-md-4 col-sm-12';
		}

		if($layout == 'fixed') {
		    $image_class = 'col-sm-6'; 
		    $infor_class = 'col-lg-3 col-md-3 col-sm-12'; 
		}


		if($layout == 'center') {
		    $image_class = 'col-lg-4 col-md-4 col-sm-12';
		    $infor_class = 'col-lg-4 col-md-4 col-sm-12';
		}

		return array($class, $image_class, $infor_class);
	}
}



if(!function_exists('etheme_my_account_title')) {
	function etheme_my_account_title() {
		the_title( '<h3 class="woocommerce-MyAccount-title entry-title">', '</h3>' );
	}
}

if(! function_exists('etheme_360_view_block')) {
	function etheme_360_view_block() {
			global $post;
			$post_id = $post->ID;

			$smart_product = get_post_meta( $post_id, "smart_product_meta", true );

			// Check if show options is turn on
			if ( ! isset( $smart_product['show'] ) || $smart_product['show'] != 'true' )
				return '';

			// Check if id set
			if ( ! isset( $smart_product['id'] ) || $smart_product['id'] == "" )
				return '';

			// Create slider instance
			$slider = new ThreeSixtySlider( $smart_product );
			
			ob_start();
			?>
				<a href="#product-360-popup" class="open-360-popup"><?php esc_html_e('Open 360 view', 'xstore'); ?></a>
				<div id="product-360-popup" class="product-360-popup mfp-hide">
					<?php echo $slider->show(); ?>
				</div>
			<?php
	}
}

// **********************************************************************//
// ! After products widget area
// **********************************************************************//

if( ! function_exists( 'etheme_after_products_widgets' ) ) {
	function etheme_after_products_widgets() {
		echo '<div class="after-products-widgets">';
		dynamic_sidebar('shop-after-products');
		echo '</div>';
	}
}


// **********************************************************************//
// ! Product sale countdown
// **********************************************************************//

if(!function_exists('etheme_product_countdown')) {
	function etheme_product_countdown() {
		$date = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
		if( ! $date ) return false;
		echo etheme_countdown_shortcode( array(
			'year' => date( 'Y', $date ),
			'month' => date( 'M', $date ),
			'day' => date( 'd', $date ),
			'scheme' => 'dark',
			'class' => 'product-sale-counter'
		) );
	}
}



// **********************************************************************//
// ! Wishlist
// **********************************************************************//

if(!function_exists('etheme_wishlist_btn')) {
	function etheme_wishlist_btn() {
		if(!class_exists('YITH_WCWL_Shortcode')) return;

		echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
	}
}

// Allow HTML in term (category, tag) descriptions
foreach ( array( 'pre_term_description' ) as $filter ) {
	remove_filter( $filter, 'wp_filter_kses' );
}

foreach ( array( 'term_description' ) as $filter ) {
	remove_filter( $filter, 'wp_kses_data' );
}

if(!function_exists('etheme_remove_reviews_from_tabs')) {
	function etheme_remove_reviews_from_tabs($tabs ) {
		unset( $tabs['reviews'] ); 			// Remove the reviews tab
		return $tabs;

	}
}


if( ! function_exists( 'etheme_compare_css' ) ) {
	add_action( 'wp_print_styles', 'etheme_compare_css', 200 );
	function etheme_compare_css() {
		if( ! class_exists( 'YITH_Woocompare' ) ) return;
		if ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'yith-woocompare-view-table' ) ) return;
		wp_enqueue_style( 'parent-style' );
	}
}

// **********************************************************************// 
// ! Catalog setup
// **********************************************************************// 

add_action( 'after_setup_theme', 'etheme_catalog_setup', 50 );

if(!function_exists('etheme_catalog_setup')) {
	function etheme_catalog_setup() {
		if(is_admin()) return;
		$just_catalog = etheme_get_option('just_catalog');

		if($just_catalog) {
			#remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

			add_filter( 'woocommerce_loop_add_to_cart_link', function() {
				return sprintf( '<a rel="nofollow" href="%s" class="button show-product">%s</a>',
					esc_url( get_the_permalink() ),
					__('Show details', 'xstore')
				);
			}, 50 );

		}
		// **********************************************************************// 
		// ! Set number of products per page
		// **********************************************************************// 
		$products_per_page = etheme_get_products_per_page();
		add_filter( 'loop_shop_per_page', function() use($products_per_page) { return $products_per_page; }, 50 );
	}
}

// **********************************************************************// 
// ! Define image sizes
// **********************************************************************//
if(!function_exists('etheme_woocommerce_image_dimensions')) {
	function etheme_woocommerce_image_dimensions() {
		global $pagenow;

		if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
			return;
		}

		$catalog = array(
			'width' 	=> '555',	// px
			'height'	=> '760',	// px
			'crop'		=> 0 		// true
		);

		$single = array(
			'width' 	=> '720',	// px
			'height'	=> '961',	// px
			'crop'		=> 0 		// true
		);

		$thumbnail = array(
			'width' 	=> '205',	// px
			'height'	=> '272',	// px
			'crop'		=> 0 		// false
		);

		// Image sizes
		update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
		update_option( 'shop_single_image_size', $single ); 		// Single product image
		update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
	}
}

add_action( 'after_switch_theme', 'etheme_woocommerce_image_dimensions', 1 );

// **********************************************************************// 
// ! AJAX Quick View
// **********************************************************************//

add_action('wp_ajax_etheme_product_quick_view', 'etheme_product_quick_view');
add_action('wp_ajax_nopriv_etheme_product_quick_view', 'etheme_product_quick_view');
if(!function_exists('etheme_product_quick_view')) {
	function etheme_product_quick_view() {
		if(empty($_POST['prodid'])) {
			echo 'Error: Absent product id';
			die();
		}

		$args = array(
			'p' => (int) $_POST['prodid'],
			'post_type' => 'product'
		);

		if( class_exists('SmartProductPlugin') )
			remove_filter('woocommerce_single_product_image_html', array('SmartProductPlugin', 'wooCommerceImage'), 999, 2 );


		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) : $the_query->the_post();
				woocommerce_get_template('product-quick-view.php');
			endwhile;
			wp_reset_query();
			wp_reset_postdata();
		} else {
			echo 'No posts were found!';
		}
		die();
	}
}


if(!function_exists('etheme_email_btn')) {
	function etheme_email_btn($label = '') {
		global $post;
		$html = '';
		$permalink = get_permalink($post->ID);
		$post_title = rawurlencode(get_the_title($post->ID));
		if($label == '') {
			$label = esc_html__('Email to a friend', 'xstore');
		}
		$html .= '
            <a href="mailto:enteryour@addresshere.com?subject='.$post_title.'&amp;body=Check%20this%20out:%20'.$permalink.'" target="_blank" class="email-link">'.$label.'</a>';
		echo $html;
	}
}

if(!function_exists('etheme_size_guide')) {
	function etheme_size_guide() {
		$image = etheme_get_custom_field('size_guide_img');
		if ( ! empty( $image ) ) : ?>
			<div class="size-guide">
				<a href="<?php echo esc_url( $image ); ?>" rel="lightbox"><?php esc_html_e('Sizing guide', 'xstore'); ?></a>
			</div>
		<?php endif;
	}
}

if(!function_exists('etheme_product_cats')) {
	function etheme_product_cats() {
		global $post, $product;
		$cat = etheme_get_custom_field('primary_category');
		?>
		<div class="products-page-cats 123456789">
			<?php
		        if(!empty($cat) && $cat != 'auto') {
		            $primary = get_term_by( 'slug', $cat, 'product_cat' );
		            $term_link = get_term_link( $primary );
		            echo '<a href="' . esc_url( $term_link ) . '">' . $primary->name . '</a>';
		        } else {
					echo $product->get_categories( ', ' );
		        }
			?>
		</div>
		<?php
	}
}

// **********************************************************************// 
// ! Get list of all product images
// **********************************************************************// 

if(!function_exists('etheme_get_image_list')) {
	function etheme_get_image_list($size = 'shop_catalog' ) {
		global $post, $product, $woocommerce;
		$images_string = '';

		$attachment_ids = $product->get_gallery_attachment_ids();

		$_i = 0;
		if(count($attachment_ids) > 0) {
			$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size );
			$images_string .= $image[0];
			foreach($attachment_ids as $id) {
				$_i++;
				$image = wp_get_attachment_image_src($id, $size);
				if($image == '') continue;
				if($_i == 1)
					$images_string .= ',';


				$images_string .= $image[0];

				if($_i != count($attachment_ids))
					$images_string .= ',';
			}

		}

		return $images_string;
	}
}


// **********************************************************************// 
// ! Display second image in the gallery
// **********************************************************************// 

if(!function_exists('etheme_get_second_image')) {
	function etheme_get_second_image($size = 'shop_catalog' ) {
		global $product, $woocommerce_loop;
		$attachment_ids = $product->get_gallery_attachment_ids();

		$image = '';

		if ( ! empty( $attachment_ids[0] ) ) {
			$image = wp_get_attachment_image( $attachment_ids[0], $size );
		}

		if( $image != ''): ?>
			<div class="image-swap">
				<?php echo ($image); ?>
			</div>
			<?php
		endif;
	}
}


// **********************************************************************// 
// ! Get column class bootstrap
// **********************************************************************// 

if(!function_exists('etheme_get_product_class')) {
	function etheme_get_product_class($colums = 3 ) {
		$cols = 12 / $colums;

		$small = 6;
		$extra_small = 6;

		$class = 'col-md-' . $cols;
		$class .= ' col-sm-' . $small;
		$class .= ' col-xs-' . $extra_small;

		return $class;
	}
}

// **********************************************************************//
// ! Get product availability
// **********************************************************************//

if( ! function_exists('etheme_product_availability') ) {
	function etheme_product_availability() {
		if( ! etheme_get_option( 'out_of_icon' ) ) return;
		global $product;
		// Availability
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}

// **********************************************************************// 
// ! Grid/List switcher
// **********************************************************************// 

add_action('woocommerce_before_shop_loop', 'etheme_grid_list_switcher',35);
if(!function_exists('etheme_grid_list_switcher')) {
	function etheme_grid_list_switcher() {
		global $wp;
		$current_url = etheme_shop_page_link(true);

		$view_mode = etheme_get_option('view_mode');

		if($view_mode == 'grid' || $view_mode == 'list') return;

		$url_grid = add_query_arg( 'view_mode', 'grid', remove_query_arg( 'view_mode', $current_url ) );
		$url_list = add_query_arg( 'view_mode', 'list', remove_query_arg( 'view_mode', $current_url ) );

		$current = etheme_get_view_mode();
		?>
		<div class="view-switcher hidden-tablet hidden-phone">
			<label><?php esc_html_e('View as:', 'xstore'); ?></label>
			<?php if($view_mode == 'grid_list'): ?>
				<div class="switch-grid <?php if( $current == 'grid' ) echo 'switcher-active'; ?>">
					<a href="<?php echo esc_url( $url_grid ); ?>"><?php esc_html_e('Grid', 'xstore'); ?></a>
				</div>
				<div class="switch-list <?php if( $current == 'list' ) echo 'switcher-active'; ?>">
					<a href="<?php echo esc_url( $url_list ); ?>"><?php esc_html_e('List', 'xstore'); ?></a>
				</div>
			<?php elseif($view_mode == 'list_grid'): ?>
				<div class="switch-list <?php if( $current == 'list' ) echo 'switcher-active'; ?>">
					<a href="<?php echo esc_url( $url_list ); ?>"><?php esc_html_e('List', 'xstore'); ?></a>
				</div>
				<div class="switch-grid <?php if( $current == 'grid' ) echo 'switcher-active'; ?>">
					<a href="<?php echo esc_url( $url_grid ); ?>"><?php esc_html_e('Grid', 'xstore'); ?></a>
				</div>
			<?php endif ;?>
		</div>
		<?php
	}
}


if(!function_exists('etheme_get_view_mode')) {
	function etheme_get_view_mode() {
		if( ! class_exists('WC_Session_Handler') ) return;
		$s = new WC_Session_Handler(); // WC()->session

		$mode = etheme_get_option('view_mode');
		$current = 'grid';

		if ( isset( $_REQUEST['view_mode'] ) ) :
			$current = ( $_REQUEST['view_mode'] );
		elseif ( $s->__isset( 'view_mode' ) && is_shop() || is_product_category() || is_product_tag()) :
			$current = ( $s->__get( 'view_mode' ) );
		elseif ($mode == 'list_grid' || $mode == 'list') :
			$current = 'list';
		endif;

		return $current;
	}
}


if(!function_exists('etheme_view_mode_action')) {
	add_action( 'init', 'etheme_view_mode_action', 100 );
	function etheme_view_mode_action() {
		if ( isset( $_REQUEST['view_mode'] ) ) :
			if( ! class_exists('WC_Session_Handler')) return;
			$s = new WC_Session_Handler(); // WC()->session
			$s->set( 'view_mode', ( $_REQUEST['view_mode'] ) );
		endif;
	}
}

// **********************************************************************// 
// ! Filters button
// **********************************************************************// 

add_action('woocommerce_before_shop_loop', 'etheme_filters_btn', 11);
if(!function_exists('etheme_filters_btn')) {
	function etheme_filters_btn() {
		if( is_active_sidebar( 'shop-filters-sidebar' ) ) {
			?>
			<div class="open-filters-btn"><a href="#" class="btn<?php echo (etheme_get_option('filter_opened')) ? ' active' : ''; ?>"><?php esc_html_e('Filters', 'xstore'); ?></a></div>
			<?php
		}
	}
}

// **********************************************************************// 
// ! Productes per page dropdown
// **********************************************************************// 

add_action('woocommerce_before_shop_loop', 'etheme_products_per_page_select',37);
if(!function_exists('etheme_products_per_page_select')) {
	function etheme_products_per_page_select() {
		global $wp_query;

		$action = '';
		$method = 'post';
		$cat 	= '';
		$cat 	= $wp_query->get_queried_object();
		$et_ppp_options = etheme_get_option('et_ppp_options');

		$products_per_page_options = (!empty($et_ppp_options)) ? explode(',', $et_ppp_options) : array(12,24,36,-1);

		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . add_query_arg( array( 'et_ppp' => false ), $_SERVER['QUERY_STRING'] ) : null;

		?>

		<div class="products-per-page">
			<span><?php esc_html_e('Show', 'xstore'); ?></span>
			<form method="<?php echo esc_attr( $method ); ?>" action="<?php echo esc_url( $action ); ?>"><?php

				?><select name="et_ppp" onchange="this.form.submit()" class="et-per-page-select"><?php

					foreach( $products_per_page_options as $key => $value ) :

						?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, etheme_get_products_per_page() ); ?>><?php
						$text = '%s';
						esc_html( printf( $text, $value == -1 ? esc_html__( 'All', 'xstore' ) : $value ) );
						?></option><?php

					endforeach;

					?></select><?php

				// Keep query string vars intact
				foreach ( $_GET as $key => $val ) :

					if ( 'et_ppp' === $key || 'submit' === $key ) :
						continue;
					endif;

					if ( is_array( $val ) ) :
						foreach( $val as $inner_val ) :
							?><input type="hidden" name="<?php echo esc_attr( $key ); ?>[]" value="<?php echo esc_attr( $inner_val ); ?>" /><?php
						endforeach;
					else :
						?><input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" /><?php
					endif;
				endforeach;

				?></form>
			<span><?php esc_html_e('Per page', 'xstore'); ?></span>
		</div>
		<?php
	}
}

if(!function_exists('etheme_get_products_per_page')) {
	function etheme_get_products_per_page() {
		if( ! class_exists('WC_Session_Handler') ) return;
		$s = new WC_Session_Handler(); // WC()->session

		if ( isset( $_REQUEST['et_ppp'] ) ) :
			return intval( $_REQUEST['et_ppp'] );
		elseif ( $s->__isset( 'products_per_page' ) ) :
			return intval( $s->__get( 'products_per_page' ) );
		else :
			return intval( etheme_get_option('products_per_page') );
		endif;
	}
}


if(!function_exists('ehemet_products_per_page_action')) {
	add_action( 'init', 'ehemet_products_per_page_action', 100 );
	function ehemet_products_per_page_action() {
		if ( isset( $_REQUEST['et_ppp'] ) ) :
			if( ! class_exists('WC_Session_Handler')) return;
			$s = new WC_Session_Handler(); // WC()->session
			$s->set( 'products_per_page', intval( $_REQUEST['et_ppp'] ) );
		endif;
	}
}

if(!function_exists('etheme_set_customer_session')) {
	add_action( 'init', 'etheme_set_customer_session', 10 );
	function etheme_set_customer_session() {
		if ( WC()->version > '2.1' && ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) :
			WC()->session->set_customer_session_cookie( true );
		endif;
	}
}

// **********************************************************************// 
// ! Category thumbnail
// **********************************************************************// 
if(!function_exists('etheme_category_header')){
	function etheme_category_header() {
		global $wp_query;
		$cat = $wp_query->get_queried_object();
		if(!property_exists($cat, "term_id") && !is_search() && etheme_get_option('product_bage_banner') != ''){
			echo '<div class="category-description">';
			echo do_shortcode(etheme_get_option('product_bage_banner'));
			echo '</div>';
		} else if( property_exists($cat, "taxonomy") && $cat->taxonomy == 'brand' ) {
			echo '<div class="category-description">';
			echo do_shortcode( $cat->description );
			echo '</div>';
		}
	}
}

// **********************************************************************// 
// ! Wishlist Widget
// **********************************************************************// 

if(!function_exists('etheme_wishlist_widget')) {
	function etheme_wishlist_widget() {
		if( class_exists( 'YITH_WCWL' ) ):
			$products = YITH_WCWL()->get_products( array(
				#'wishlist_id' => 'all',
				'is_default' => true
			) );

			$limit = 3;

			$icon_label = etheme_get_option('cart_icon_label');
			$class = ' ico-label-' . $icon_label;

			$products = array_reverse($products);

			$wl_count = YITH_WCWL()->count_products();
			?>
			<div class="et-wishlist-widget <?php echo esc_attr( $class ); ?>">
				<a href="<?php echo esc_url(YITH_WCWL()->get_wishlist_url()); ?>"><i class="icon-like_outline"></i></a>
				<?php if( $wl_count > 0 ) : ?>
					<span class="wishlist-count"><?php echo (int) $wl_count; ?></span>
				<?php endif; ?>
				<div class="wishlist-dropdown product_list_widget">

					<?php if ( ! empty($products) ) : ?>

						<p><?php esc_html_e('Recently added item(s)', 'xstore'); ?></p>
						<ul class="cart-widget-products">
							<?php
							$i = 0;
							foreach( $products as $item ) {
								$i++;
								if( $i > $limit) break;
								if( function_exists( 'wc_get_product' ) ) {
									$_product = wc_get_product( $item['prod_id'] );
								}
								else{
									$_product = get_product( $item['prod_id'] );
								}

								if( ! $_product ) continue;

								$product_name  = $_product->get_title();
								$thumbnail     = $_product->get_image();
								$product_price = WC()->cart->get_product_price( $_product );
								?>
								<li class="">
									<?php if ( ! $_product->is_visible() ) : ?>
										<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . '&nbsp;'; ?>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="product-mini-image">
											<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . '&nbsp;'; ?>
										</a>
									<?php endif; ?>

									<h4 class="product-title"><a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo $product_name; ?></a></h4>

									<div class="descr-box">
										<?php echo $product_price; ?>
									</div>
								</li>
								<?php
							}
							?>
						</ul>

						<p class="buttons">
							<a href="<?php echo esc_url(YITH_WCWL()->get_wishlist_url()); ?>" class="button btn-view-wishlist"><?php _e( 'View Wishlist', 'xstore' ); ?></a>
						</p>

					<?php else : ?>

						<p class="empty"><?php esc_html_e( 'No products in the wishlist.', 'xstore' ); ?></p>

					<?php endif; ?>

				</div><!-- end product list -->
			</div>
			<?php
		endif;
	}
}


if(!function_exists('etheme_support_multilingual_ajax')) {
	add_filter('wcml_multi_currency_is_ajax', 'etheme_support_multilingual_ajax');
	function etheme_support_multilingual_ajax($functions) {
		$functions[] = 'etheme_wishlist_fragments';
		return $functions;
	}
}

if( ! function_exists('etheme_wishlist_fragments') ) {
	add_action( 'wp_ajax_etheme_wishlist_fragments', 'etheme_wishlist_fragments');
	add_action( 'wp_ajax_nopriv_etheme_wishlist_fragments', 'etheme_wishlist_fragments');

	function etheme_wishlist_fragments() {
		if(! function_exists('wc_setcookie') || ! function_exists('YITH_WCWL') ) return;
		$products = YITH_WCWL()->get_products( array(
			#'wishlist_id' => 'all',
			'is_default' => true
		) );

		// Get mini cart
		ob_start();

		etheme_wishlist_widget();

		$wishlist = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'wishlist' => $wishlist,
			'wishlist_hash' =>  md5( json_encode( $products ) )
		);

		wp_send_json( $data );
	}
}

if( ! function_exists('etheme_maybe_set_wishlist_cookies') ) {
	add_action( 'wp', 'etheme_maybe_set_wishlist_cookies', 99 );
	function etheme_maybe_set_wishlist_cookies() {
		if(! function_exists('wc_setcookie') || ! function_exists('YITH_WCWL') ) return;
		$products = YITH_WCWL()->get_products( array(
			#'wishlist_id' => 'all',
			'is_default' => true
		) );

		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
			if ( ! empty( $products ) ) {
				etheme_set_wishlist_cookies( true );
			} elseif ( isset( $_COOKIE['et_items_in_wishlist'] ) ) {
				etheme_set_wishlist_cookies( false );
			}
		}
	}
}

if( ! function_exists('etheme_set_wishlist_cookies') ) {
	function etheme_set_wishlist_cookies($set = true ) {
		if(! function_exists('wc_setcookie') || ! function_exists('YITH_WCWL') ) return;
		$products = YITH_WCWL()->get_products( array(
			#'wishlist_id' => 'all',
			'is_default' => true
		) );
		if ( $set ) {
			wc_setcookie( 'et_items_in_wishlist', 1 );
			wc_setcookie( 'et_wishlist_hash', md5( json_encode( $products ) ) );
		} elseif ( isset( $_COOKIE['et_items_in_wishlist'] ) ) {
			wc_setcookie( 'et_items_in_wishlist', 0, time() - HOUR_IN_SECONDS );
			wc_setcookie( 'et_wishlist_hash', '', time() - HOUR_IN_SECONDS );
		}
		do_action( 'etheme_set_wishlist_cookies', $set );
	}
}

// **********************************************************************//
// ! Get current shop link
// **********************************************************************// 

if(!function_exists('etheme_shop_page_link')) {
	function etheme_shop_page_link($keep_query = false ) {
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id('shop') ) ) {
			$link = get_post_type_archive_link( 'product' );
		} else {
			$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
		}

		if( $keep_query ) {
			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) {
				if ( 'orderby' === $key || 'submit' === $key ) {
					continue;
				}
				$link = add_query_arg( $key, $val, $link );

			}
		}

		return $link;
	}
}

// **********************************************************************// 
// ! Is zoom plugin activated
// **********************************************************************// 

if( ! function_exists('etheme_is_zoom_activated') ) {
	function etheme_is_zoom_activated() {
		return class_exists( 'YITH_WCMG_Frontend' );
	}
}

// **********************************************************************// 
// ! Top Cart Widget
// **********************************************************************// 

if(!function_exists('etheme_top_cart')) {
	function etheme_top_cart($load_cart = false) {
		global $woocommerce;

		$icon_design = etheme_get_option('shopping_cart_icon');
		$icon_bg = (etheme_get_option('shopping_cart_icon_bg')) ? 'yes' : 'no';
		$icon_label = etheme_get_option('cart_icon_label');

		$class = 'ico-design-' . $icon_design;
		$class .= ' ico-bg-' . $icon_bg;
		$class .= ' ico-label-' . $icon_label;

		?>
		<div class="shopping-container <?php echo esc_attr( $class ); ?>" data-fav-badge="<?php echo (etheme_get_option('favicon_label')) ? 'enable' : 'disable'; ?>">
			<div class="shopping-cart-widget" id='basket'>
				<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="cart-summ">
						<span class="cart-bag">
							<i class='ico-sum'></i>
							<?php etheme_cart_number(); ?>
						</span>
					<?php etheme_cart_total(); ?>
				</a>
			</div>

			<div class="cart-popup-container">
				<div class="cart-popup">
					<div class="widget woocommerce widget_shopping_cart">
						<?php
						if($load_cart) {
							woocommerce_mini_cart();
						} else {
							echo '<div class="widget_shopping_cart_content"></div>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if(!function_exists('etheme_cart_total')) {
	function etheme_cart_total() {
		global $woocommerce;
		?>
		<span class="shop-text"><span class="cart-items"><?php esc_html_e('Cart', 'xstore') ?>:</span> <span class="total"><?php echo $woocommerce->cart->get_cart_subtotal(); ?></span></span>
		<?php
	}
}


if(!function_exists('etheme_cart_number')) {
	function etheme_cart_number() {
		global $woocommerce;
		?>
			<span class="badge-number number-value-<?php echo $woocommerce->cart->cart_contents_count; ?>" data-items-count="<?php echo $woocommerce->cart->cart_contents_count; ?>"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
		<?php
	}
}

if(!function_exists('etheme_cart_items')) {
	function etheme_cart_items ($limit = 3) {
		?>
		<?php if ( ! WC()->cart->is_empty() ) : ?>

			<p><?php esc_html_e('Recently added item(s)', 'xstore'); ?></p>
			<ul class="cart-widget-products">
				<?php
				$i = 0;
				$cart = array_reverse( WC()->cart->get_cart() );
				foreach ( $cart as $cart_item_key => $cart_item ) {
					$i++;
					if( $i > $limit ) continue;
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

						$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
						<li class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
							<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'xstore' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
							?>
							<?php if ( ! $_product->is_visible() ) : ?>
								<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . ''; ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>" class="product-mini-image">
									<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . ''; ?>
								</a>
							<?php endif; ?>
							<div class="product-item-right">
								<h4 class="product-title"><a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>"><?php echo $product_name; ?></a></h4>

								<div class="descr-box">
									<?php echo WC()->cart->get_item_data( $cart_item ); ?>
									<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
								</div>
							</div>

						</li>
						<?php
					}
				}
				?>
			</ul>

		<?php else : ?>

			<p class="empty"><?php esc_html_e( 'No products in the cart.', 'xstore' ); ?></p>

		<?php endif; ?>


		<?php if ( ! WC()->cart->is_empty() ) : ?>

			<div class="cart-widget-subtotal">
				<span class="small-h"><?php echo esc_html__('Cart Subtotal:', 'xstore'); ?></span>
				<span class="big-coast">
					<?php echo WC()->cart->get_cart_subtotal(); ?>
				</span>
			</div>

			<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

			<p class="buttons">
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button btn-checkout wc-forward"><?php esc_html_e( 'Checkout', 'xstore' ); ?></a>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button btn-view-cart wc-forward"><?php esc_html_e( 'View Cart', 'xstore' ); ?></a>
			</p>

		<?php endif; ?>

		<?php
	}
}



if(!function_exists('etheme_get_fragments')) {
	add_filter('woocommerce_add_to_cart_fragments', 'etheme_get_fragments', 30);
	function etheme_get_fragments($array = array()) {
		ob_start();
		etheme_cart_total();
		$cart_total = ob_get_clean();

		ob_start();
		etheme_cart_number();
		$cart_number = ob_get_clean();


		$array['span.shop-text'] = $cart_total;
		$array['span.badge-number'] = $cart_number;

		return $array;
	}
}
