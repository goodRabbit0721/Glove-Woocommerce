<?php  if ( ! defined('ABSPATH')) exit('No direct script access allowed');
// **********************************************************************// 
// ! Product Video
// **********************************************************************//

add_action('admin_init', 'etheme_product_meta_boxes');

function etheme_product_meta_boxes() {
	add_meta_box( 'woocommerce-product-videos', esc_html__( 'Product Video', 'xstore' ), 'etheme_woocommerce_product_video_box', 'product', 'side' );
}

if(!function_exists('etheme_woocommerce_product_video_box')) {
	function etheme_woocommerce_product_video_box() {
		global $post;
		?>
		<div id="product_video_container">
			<?php esc_html_e('Upload your Video in 3 formats: MP4, OGG and WEBM', 'xstore') ?>
			<ul class="product_video">
				<?php
					
					$product_video_code = get_post_meta( $post->ID, '_product_video_code', true );


					if ( metadata_exists( 'post', $post->ID, '_product_video_gallery' ) ) {
						$product_image_gallery = get_post_meta( $post->ID, '_product_video_gallery', true );
					} 
					
					$video_attachments = false;
					
					if(isset($product_image_gallery) && $product_image_gallery != '') {
						$video_attachments = get_posts( array(
							'post_type' => 'attachment',
							'include' => $product_image_gallery
						) ); 
					}
					
					
					
					//$attachments = array_filter( explode( ',', $product_image_gallery ) );
	
					if ( $video_attachments )
						foreach ( $video_attachments as $attachment ) {
							echo '<li class="video" data-attachment_id="' . $attachment->id . '">
								Format: ' . $attachment->post_mime_type . '
								<ul class="actions">
									<li><a href="#" class="delete" title="' . esc_html__( 'Delete image', 'xstore' ) . '">' . esc_html__( 'Delete', 'xstore' ) . '</a></li>
								</ul>
							</li>';
						}
				?>
			</ul>
	
			<input type="hidden" id="product_video_gallery" name="product_video_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />
	
		</div>
		<p class="add_product_video hide-if-no-js">
			<a href="#"><?php esc_html_e( 'Add product gallery video', 'xstore' ); ?></a>
		</p>
		<p>
			<?php esc_html_e('Or you can use YouTube or Vimeo iframe code', 'xstore'); ?>
		</p>
		<div class="product_iframe_video">
			
			<textarea name="et_video_code" id="et_video_code" rows="7"><?php echo esc_attr( $product_video_code ); ?></textarea>
			
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($){
	
				// Uploading files
				var product_gallery_frame;
				var $image_gallery_ids = $('#product_video_gallery');
				var $product_images = $('#product_video_container ul.product_video');
	
				jQuery('.add_product_video').on( 'click', 'a', function( event ) {
	
					var $el = $(this);
					var attachment_ids = $image_gallery_ids.val();
	
					event.preventDefault();
	
					// If the media frame already exists, reopen it.
					if ( product_gallery_frame ) {
						product_gallery_frame.open();
						return;
					}
	
					// Create the media frame.
					product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
						// Set the title of the modal.
						title: '<?php esc_html_e( 'Add Images to Product Gallery', 'xstore' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Add to gallery', 'xstore' ); ?>',
						},
						multiple: true,
						library : { type : 'video'}
					});
	
					// When an image is selected, run a callback.
					product_gallery_frame.on( 'select', function() {
	
						var selection = product_gallery_frame.state().get('selection');
	
						selection.map( function( attachment ) {
	
							attachment = attachment.toJSON();
	
							if ( attachment.id ) {
								attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
	
								$product_images.append('\
									<li class="video" data-attachment_id="' + attachment.id + '">\
										Video\
										<ul class="actions">\
											<li><a href="#" class="delete" title="<?php esc_html_e( 'Delete video', 'xstore' ); ?>"><?php esc_html_e( 'Delete', 'xstore' ); ?></a></li>\
										</ul>\
									</li>');
							}
	
						} );
	
						$image_gallery_ids.val( attachment_ids );
					});
	
					// Finally, open the modal.
					product_gallery_frame.open();
				});
	
				// Image ordering
				$product_images.sortable({
					items: 'li.video',
					cursor: 'move',
					scrollSensitivity:40,
					forcePlaceholderSize: true,
					forceHelperSize: false,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'wc-metabox-sortable-placeholder',
					start:function(event,ui){
						ui.item.css('background-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
					},
					update: function(event, ui) {
						var attachment_ids = '';
	
						$('#product_video_container ul li.video').css('cursor','default').each(function() {
							var attachment_id = jQuery(this).attr( 'data-attachment_id' );
							attachment_ids = attachment_ids + attachment_id + ',';
						});
	
						$image_gallery_ids.val( attachment_ids );
					}
				});
	
				// Remove images
				$('#product_video_container').on( 'click', 'a.delete', function() {
	
					$(this).closest('li.video').remove();
	
					var attachment_ids = '';
	
					$('#product_video_container ul li.video').css('cursor','default').each(function() {
						var attachment_id = jQuery(this).attr( 'data-attachment_id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});
	
					$image_gallery_ids.val( attachment_ids );
	
					return false;
				} );
	
			});
		</script>
		<?php
	}
}

add_action( 'woocommerce_process_product_meta', 'etheme_save_video_meta');

if(!function_exists('etheme_save_video_meta')) {
	function etheme_save_video_meta($post_id) {
		// Gallery Images
		$video_ids =  explode( ',',  $_POST['product_video_gallery']  ) ;
		update_post_meta( $post_id, '_product_video_gallery', implode( ',', $video_ids ) );
		update_post_meta( $post_id, '_product_video_code',  $_POST['et_video_code']  );
	}
}

if(!function_exists('etheme_get_external_video')) {
	function etheme_get_external_video($post_id) {
		if(!$post_id) return false;
		$product_video_code = get_post_meta( $post_id, '_product_video_code', true );
		
		return $product_video_code;
	}
}

if(!function_exists('etheme_get_attach_video')) {
	function etheme_get_attach_video($post_id) {
		if(!$post_id) return false;
		$product_video_code = get_post_meta( $post_id, '_product_video_gallery', false );
		
		return $product_video_code;
	}
}