<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
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


global $post, $etheme_global, $post_id, $product, $is_IE, $main_slider_on, $attachment_ids, $main_attachment_id;

$post_id = get_the_ID();

if( defined('DOING_AJAX') && DOING_AJAX && ! isset($etheme_global['quick_view']) ) {
	if( ! empty( $_REQUEST['product_id'] ) ) {
		$post_id = (int) $_REQUEST['product_id'];
		setup_postdata( $post_id );
		$main_attachment_id = get_post_thumbnail_id( $post_id );
		$product = wc_get_product($post_id);
		if( ! empty($_REQUEST['option']) ) {
			$option = esc_attr($_REQUEST['option']);
			$attributes = $product->get_attributes();
			$variations = $product->get_available_variations();
			$images = '';
			$thumb = '';
			$attachment_ids = array();

			foreach($variations as $variation) {
				if( isset( $variation['attributes']['attribute_' . $swatch] ) && $variation['attributes']['attribute_' . $swatch] == $option && has_post_thumbnail( $variation['variation_id'] ) ) {
					$main_attachment_id = get_post_thumbnail_id( $variation['variation_id'] );
				}
			}

		}
	}

} else {
	$attachment_ids = $product->get_gallery_attachment_ids();
	$main_attachment_id = get_post_thumbnail_id( $post_id );
}

$zoom_plugin = etheme_is_zoom_activated();

$zoom_class = 'zoom';

if( $zoom_plugin ) {
	$zoom_class = 'yith_magnifier_zoom';
}

$has_video = false;

$gallery_slider = etheme_get_option('thumbs_slider');

if( etheme_get_custom_field('disable_gallery', $product->id) ) {
	$gallery_slider = false;
}

if( defined('DOING_AJAX') && DOING_AJAX ) {
	$gallery_slider = true;
}

$video_attachments = array();
$videos = etheme_get_attach_video($product->id);
if(isset($videos[0]) && $videos[0] != '') {
	$video_attachments = get_posts( array(
		'post_type' => 'attachment',
		'include' => $videos[0]
	) );
}

if(count($video_attachments)>0 || etheme_get_external_video($product->id) != '') {
	$has_video = true;
}

$main_slider_on = ( ! $zoom_plugin && ( count( $attachment_ids ) > 0 || $has_video ) );

$class = $zoom_btn = '';

if ( $zoom_plugin ) {
	$class .= ' zoom-on';
}

if ( $main_slider_on ) {
	$class .= ' main-slider-on';
}

$product_photoswipe = etheme_get_option( 'product_photoswipe' );

if( $product_photoswipe ) {
	$class .= ' photoswipe-on';
} else {
	$class .= ' photoswipe-off';
}


$class .= ( $gallery_slider ) ? ' gallery-slider-on' : ' gallery-slider-off';

if ( $is_IE ) {
	$class .= ' ie';
}

?>
<?php if ( ! defined('DOING_AJAX') && ! isset($etheme_global['quick_view']) ): ?>
<div class="images-wrapper">
<?php endif; ?>
<div class="images<?php echo esc_attr( $class ); ?>">
	<div class="main-images">
		<?php

			if ( has_post_thumbnail( $post_id ) ) {

				$index = 1;

				$image_title 	= esc_attr( get_the_title( $main_attachment_id ) );
				$image_caption 	= get_post( $main_attachment_id )->post_excerpt;
				$image_link  	= wp_get_attachment_image_src( $main_attachment_id, 'full' );
				$image       	= wp_get_attachment_image( $main_attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
					'title'	=> $image_title,
					'alt'	=> $image_title
					) );

				$attachment_count = count( $product->get_gallery_attachment_ids() );

				if ( $attachment_count > 0 ) {
					$gallery = '[product-gallery]';
				} else {
					$gallery = '';
				}

				if ( $zoom_plugin ) {
					//$zoom_btn = sprintf( '<span href="%s" data-width="%s" data-height="%s" itemprop="image" class="et-enlarge-btn zoom" title="%s">Enlarge</span>', $image_link[0], $image_link[1], $image_link[2], $zoom_class, $image_caption );
				}

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '
						<div>
							<a href="%s" data-width="%s" data-height="%s" itemprop="image" class="woocommerce-main-image pswp-main-image %s" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>
							%s
						</div>',
						$image_link[0],
						$image_link[1],
						$image_link[2],
						$zoom_class,
						$image_caption,
						$image,
						$zoom_btn
					),
					$post_id
				);

				if( $main_slider_on ){
					if( count( $attachment_ids ) > 0 ) {
						foreach ( $attachment_ids as $attachment_id ) {
							$image_title 	= esc_attr( get_the_title( $attachment_id ) );
							$image_caption 	= get_the_title( $attachment_id );
							$image_link  	= wp_get_attachment_image_src( $attachment_id, 'full' );
				            $image          = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

							echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '
									<div>
										<a href="%s" data-large="%s" data-width="%s" data-height="%s" data-index="%d" itemprop="image" class="woocommerce-main-image %s" title="%s" data-width="1000" data-height="1000" data-rel="prettyPhoto' . $gallery . '">%s</a>
									</div>',
									$image_link[0],
									$image_link[0],
									$image_link[1],
									$image_link[2],
									$index,
									$zoom_class,
									$image_caption,
									$image
								),
								$attachment_id
							);

							$index++;
						}
					}

				}

			} else {

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'xstore' ) ), $post_id );

			}
		?>
	</div>
	
	<?php if ($product_photoswipe): ?>
		<a href="#" class="zoom-images-button" data-index="0"><?php esc_html_e('Zoom images', 'xstore'); ?></a>
	<?php endif ?>

	<?php if(etheme_get_external_video($product->id)): ?>
		<a href="#product-video-popup" class="open-video-popup"><?php esc_html_e('Open Video', 'xstore'); ?></a>
		<div id="product-video-popup" class="product-video-popup mfp-hide">
			<?php echo etheme_get_external_video($product->id); ?>
		</div>
	<?php endif; ?>

	<?php etheme_360_view_block(); ?>


	<?php if(count($video_attachments)>0): ?>
		<a href="#product-video-popup" class="open-video-popup"><?php esc_html_e('Open Video', 'xstore'); ?></a>
		<div class="product-video-popup mfp-hide">
			<video controls="controls">
				<?php foreach($video_attachments as $video):  ?>
					<?php $video_ogg = $video_mp4 = $video_webm = false; ?>
					<?php if($video->post_mime_type == 'video/mp4' && !$video_mp4): $video_mp4 = true; ?>
						<source src="<?php echo $video->guid; ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
					<?php endif; ?>
					<?php if($video->post_mime_type == 'video/webm' && !$video_webm): $video_webm = true; ?>
						<source src="<?php echo $video->guid; ?>" type='video/webm; codecs="vp8, vorbis"'>
					<?php endif; ?>
					<?php if($video->post_mime_type == 'video/ogg' && !$video_ogg): $video_ogg = true; ?>
						<source src="<?php echo $video->guid; ?>" type='video/ogg; codecs="theora, vorbis"'>
						<?php esc_html_e('Video is not supporting by your browser', 'xstore'); ?>
						<a href="<?php echo $video->guid; ?>"><?php esc_html_e('Download Video', 'xstore'); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</video>
		</div>
	<?php endif; ?>

	<?php if($gallery_slider) do_action( 'woocommerce_product_thumbnails' ); ?>
</div>

<?php if( $main_slider_on && $gallery_slider ): ?>
    <script type="text/javascript">
        jQuery('.main-images').owlCarousel({
	        items:1,
	        navigation: true,
	        lazyLoad: false,
	        rewindNav: false,
	        addClassActive: true,
	        itemsCustom: [1600, 1],
	        afterMove: function(args) {
	            var owlMain = jQuery(".main-images").data('owlCarousel');
	            var owlThumbs = jQuery(".thumbnails-list").data('owlCarousel');

				jQuery('.zoom-images-button').data('index', owlMain.currentItem);

	            jQuery('.active-thumbnail').removeClass('active-thumbnail')
	            jQuery(".thumbnails-list").find('.owl-item').eq(owlMain.currentItem).addClass('active-thumbnail');
	            if(typeof owlThumbs != 'undefined') {
	            	owlThumbs.goTo(owlMain.currentItem-1);
	            }
	        }
	    });
    </script>
<?php endif; ?>

<?php if( $zoom_plugin ): ?>
	<script type="text/javascript" charset="utf-8">

		var yith_magnifier_options = {
			enableSlider: false,
			showTitle: false,
			zoomWidth: '<?php echo get_option('yith_wcmg_zoom_width') ?>',
			zoomHeight: '<?php echo get_option('yith_wcmg_zoom_height') ?>',
			position: '<?php echo get_option('yith_wcmg_zoom_position') ?>',
			//tint: <?php //echo get_option('yith_wcmg_tint') == '' ? 'false' : "'".get_option('yith_wcmg_tint')."'" ?>,
			//tintOpacity: <?php //echo get_option('yith_wcmg_tint_opacity') ?>,
			lensOpacity: <?php echo get_option('yith_wcmg_lens_opacity') ?>,
			softFocus: <?php echo get_option('yith_wcmg_softfocus') == 'yes' ? 'true' : 'false' ?>,
			//smoothMove: <?php //echo get_option('yith_wcmg_smooth') ?>,
			adjustY: 0,
			disableRightClick: false,
			phoneBehavior: '<?php echo get_option('yith_wcmg_zoom_mobile_position') ?>',
			loadingLabel: '<?php echo stripslashes(get_option('yith_wcmg_loading_label')) ?>'
			<?php if ( defined('DOING_AJAX') ): ?>
			,elements: {
				zoom: jQuery('.yith_magnifier_zoom'),
				zoomImage: jQuery('.yith_magnifier_zoom img'),
				gallery: jQuery('.yith_magnifier_gallery li a')
			}
			<?php endif; ?>
		};

		<?php if ( defined('DOING_AJAX') ): ?>
			var yith_wcmg = jQuery('.images');

			if (yith_wcmg.data('yith_magnifier')) {
				yith_wcmg.yith_magnifier('destroy');
			}

			yith_wcmg.yith_magnifier(yith_magnifier_options);
		<?php endif; ?>
	</script>
<?php endif; ?>

<?php if ( ! defined('DOING_AJAX') && ! isset($etheme_global['quick_view']) ): ?>
</div>
<?php endif; ?>
