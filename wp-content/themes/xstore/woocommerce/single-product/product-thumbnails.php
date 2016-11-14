<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post_id, $product, $woocommerce, $main_slider_on, $attachment_ids, $main_attachment_id;

$zoom_plugin = etheme_is_zoom_activated();

$gallery_slider = etheme_get_option('thumbs_slider');

if( etheme_get_custom_field('disable_gallery', $product->id) ) {
	$gallery_slider = false;
}

if( defined('DOING_AJAX') && DOING_AJAX ) {
	$gallery_slider = true;
}

$thums_size = apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' );

if( ! $gallery_slider ) {
	//$thums_size = apply_filters( 'single_product_large_thumbnail_size', 'shop_single' );
}

$ul_class = 'thumbnails-list';

if( $zoom_plugin ) {
	$ul_class .= ' yith_magnifier_gallery';
}

if( empty( $attachment_ids ) ) {
	$attachment_ids = $product->get_gallery_attachment_ids();
}

if ( $attachment_ids ) {
	$loop 		= 0;
	$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
	?>
	<div class="thumbnails <?php echo 'columns-' . $columns; ?> <?php echo $gallery_slider ? 'slider' : 'noslider' ?>">
		<?php etheme_loader(); ?>
		<ul class="<?php echo esc_attr( $ul_class ); ?>">
		<?php

			if( count( $attachment_ids ) > 0 && ! in_array( $main_attachment_id, $attachment_ids ) ) {

				$classes = array( 'zoom' );

	            $image       = wp_get_attachment_image( $main_attachment_id, $thums_size );
	            $image_class = esc_attr( implode( ' ', $classes ) );
	            $image_title = esc_attr( get_the_title( $main_attachment_id ) );

	            list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $main_attachment_id, "shop_single" );
	            list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src( $main_attachment_id, "shop_magnifier" );

	            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li class="thumbnail-item %s"><a href="%s" class="%s" title="%s" data-small="%s">%s</a></li>', $image_class, $magnifier_url, $image_class, $image_title, $thumbnail_url, $image ), $post_id, $post_id, $image_class );

			}

			foreach ( $attachment_ids as $attachment_id ) {

				$classes = array( 'zoom' );

				$image_link = wp_get_attachment_url( $attachment_id );

				if ( ! $image_link )
					continue;

	            $image       = wp_get_attachment_image( $attachment_id, $thums_size );
	            $image_class = esc_attr( implode( ' ', $classes ) );
	            $image_title = esc_attr( get_the_title( $attachment_id ) );
				$image_link  = wp_get_attachment_image_src( $attachment_id, 'full' );

	            list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $attachment_id, "shop_single" );
	            list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src( $attachment_id, "shop_magnifier" );

	            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li class="thumbnail-item %s"><a href="%s" data-large="%s" data-width="%s" data-height="%s" class="pswp-additional %s" title="%s" data-small="%s">%s</a></li>', $image_class, $magnifier_url, $image_link[0], $image_link[1], $image_link[2], $image_class, $image_title, $thumbnail_url, $image ), $attachment_id, $post_id, $image_class );

				$loop++;
			}
		?>
		</ul>
		<?php if( $gallery_slider ) : ?>
	        <script type="text/javascript">
	        	<?php
	        		$items = '[[0, 3], [479,3], [619,3], [768,3], [1200, 4], [1600, 4]]';
	        	 ?>
			    jQuery('.thumbnails-list').owlCarousel({
			        items : 4,
			        transitionStyle:"fade",
			        navigation: true,
			        navigationText: ["",""],
			        addClassActive: true,
			        itemsCustom: <?php echo esc_js( $items ); ?>,
					afterInit : function(el){
						el.find(".owl-item").eq(0).addClass("active-thumbnail");
					}
			    });

			   <?php if( $main_slider_on ) : ?>

			    jQuery('.thumbnails-list .owl-item').click(function(e) {
		            var owlMain = jQuery(".main-images").data('owlCarousel');
		            var owlThumbs = jQuery(".product-thumbnails").data('owlCarousel');
		            owlMain.goTo(jQuery(e.currentTarget).index());
			    });

			    jQuery('.thumbnails-list a').click(function(e) {
				    e.preventDefault();
			    });

			    <?php endif; ?>

	        </script>
	    <?php endif; ?>
	</div>
	<?php
}