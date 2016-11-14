<?php do_action( 'before_smart_product_html', $this->postID ); ?>
<div id="threesixty-slider-<?php $this->ID(); ?>" class="threesixty-loading threesixty-image <?php $this->classes(); ?>" style="width: <?php echo $this->width; ?>px;">
	<div class="threesixty-preview-<?php $this->ID(); ?>"><?php $this->previewImage(); ?></div>
	<div class="threesixty-spinner threesixty-spinner-<?php $this->ID(); ?>">
		<span>0%</span>
	</div>
	<ol class="threesixty-images threesixty-images-<?php $this->ID(); ?>"></ol>
	<?php if ( $this->useScrollbar() ) : ?>
	<div class="threesixty-scrollbar">
		<div id="nouislider-<?php $this->ID(); ?>"></div>
	</div>
	<?php endif; ?>
	<?php if ( $this->fullscreen == 'true' ) : ?>
	<a href="#threesixty-slider-<?php $this->ID(); ?>" class="threesixty-mfp-anchor"></a>
	<?php endif; ?>
</div>
<?php do_action( 'after_smart_product_html', $this->postID ); ?>
<!--SPV--><script type="text/javascript">//SPV
(function($) {
	<?php if ( $this->fullscreen == 'true' ) : ?>
	$('#threesixty-slider-<?php $this->ID(); ?> .threesixty-mfp-anchor').magnificPopup({
		closeOnBgClick: true,
		midClick: true,
		//removalDelay: 300,
  		//mainClass: 'mfp-fade',
		callbacks: {
			 resize: function() {
			 	var originalHeight 	= <?php echo $this->heightOrigianl; ?>,
			 		originalWidth 	= <?php echo $this->widthOrigianl; ?>,
			 		mfpHeight 		= <?php echo $this->heightOrigianl; ?>,
			 		mfpWidth 		= <?php echo $this->widthOrigianl; ?>,
			 		ratio 			= originalWidth / originalHeight,
			 		winHeight 		= $(window).height(),
			 		winWidth 		= $(window).width();
			 	
			 	if ( originalHeight > winHeight * 0.9 ) {
			 		mfpHeight 		= winHeight * 0.9;
			 		mfpWidth 		= mfpHeight * ratio;
				}
				if ( mfpWidth > winWidth * 0.9 ) {
			 		mfpWidth 		= winWidth * 0.9;
			 		mfpHeight 		= mfpWidth / ratio;
				}
			 	if ( mfpHeight < originalHeight && mfpWidth < originalWidth ) {
				    $('#threesixty-slider-<?php $this->ID(); ?>').height(mfpHeight);
				    $('#threesixty-slider-<?php $this->ID(); ?>').width(mfpWidth);
				} 
				else {
				    $('#threesixty-slider-<?php $this->ID(); ?>').height(originalHeight);
				    $('#threesixty-slider-<?php $this->ID(); ?>').width(originalWidth);
				}
			},
			close: function() {
			    $('#threesixty-slider-<?php $this->ID(); ?>').height('auto');
			    $('#threesixty-slider-<?php $this->ID(); ?>').width(<?php echo $this->width; ?>);
			}
		},
		closeMarkup :"<button class=\"mfp-close\"></button>"
	});
	<?php endif; ?>
	var product_<?php $this->ID(); ?> = $('#threesixty-slider-<?php $this->ID(); ?>').ThreeSixty({
		totalFrames: <?php echo $this->getImagesCount(); ?>,
		endFrame: 0,
		currentFrame: <?php echo $this->getImagesCount(); ?>,
		imgList: '.threesixty-images-<?php $this->ID(); ?>',
		progress: '.threesixty-spinner-<?php $this->ID(); ?>',
		preview: '.threesixty-preview-<?php $this->ID(); ?>',
		images: <?php $this->imagesJSArray() ?>,
		height: 0,
		width: <?php echo $this->width; ?>,
		navigation: <?php echo $this->navigation; ?>,
		drag: <?php echo $this->drag; ?>,
		showCursor: true,
		interval: <?php echo $this->interval; ?>,
		speedMultiplier: <?php echo $this->speedMultiplier; ?>,
		<?php if ( $this->autoplay == 'true' ) : ?>
		startAutoplay: true,
		<?php endif; ?>
		onReady: function() {
			$("#threesixty-slider-<?php $this->ID(); ?>").removeClass('threesixty-loading');
			<?php if ( $this->autoplay == 'true' ) : ?>
			product_<?php $this->ID(); ?>.play();
			<?php endif; ?>
			<?php if ( $this->useScrollbar() ) : ?>
			$('#nouislider-<?php $this->ID(); ?>').noUiSlider({
				range: {
					'min': 0, 
					'max': <?php echo $this->getImagesCount() - 1; ?>
				},
				start: 0,
				step: 1,
				serialization: {
					lower: [
						$.Link({
							target: function( val ) {
								product_<?php $this->ID(); ?>.gotoAndPlay( val );
							}
						})
					]
				}		
			});
			<?php endif; ?>
		}
		<?php do_action( 'after_smart_product_js_arguments', $this->postID ); ?>
	});
	<?php if ( $this->moveOnScroll == 'true' ) : ?>
	$(window).scroll(function(event) {
		var page_percentage, frame_value, page_offset;
		page_offset = $(window)[0].pageYOffset;
		if(page_offset) {
			frame_value = Math.abs(Math.floor(page_offset / <?php echo $this->interval; ?> * 2));
			if(frame_value > <?php echo $this->getImagesCount(); ?>){
				frame_value = frame_value % <?php echo $this->getImagesCount(); ?>;
			};
			product_<?php $this->ID(); ?>.gotoAndPlay(frame_value);
		}
	});
	<?php endif; ?>
	<?php if ( $this->moveOnHover == 'true' ) : ?>
	$("#threesixty-slider-<?php $this->ID(); ?>").mouseover(function(){
		product_<?php $this->ID(); ?>.play();
	});
	$("#threesixty-slider-<?php $this->ID(); ?>").mouseleave(function(){
		product_<?php $this->ID(); ?>.stop();
	});
	<?php endif; ?>
}(jQuery));
</script>