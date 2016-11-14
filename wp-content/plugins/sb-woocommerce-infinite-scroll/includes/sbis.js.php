<?php $setting = $this->sb_admin->get_infinite_scroll_setting(); ?>
<style type="text/css">
	img.sb-lazy-img {
		background:url(<?php echo $setting['lazyload_loading_image']; ?>) no-repeat center;
	}
</style>
<script type="text/javascript">
	;(function($) {
		var w = $(window);
		var sbwis;
		sbwis = {
			init: function() {
				var pagination_type = '<?php echo $setting['pagination_type']; ?>';
				<?php
					
					if(!empty($setting)) {
						if($setting['status'] == 1) {
							
							if(isset($setting['mobile_pagination_settings']) && $setting['mobile_pagination_settings'] == '1') { ?>
								if(w.width() <= '<?php echo $setting['break_point']; ?>') {
									var pagination_type = '<?php echo $setting['mobile_pagination_type']; ?>';
								}
								<?php
							}
							?>
							
							if(pagination_type == 'ajax_pagination') {
								$('body').on('click', '<?php echo $setting['navigation_selector'].' a'; ?>', function(e) {
									e.preventDefault();
									var href = $.trim($(this).attr('href'));
									if(href != '') {
										if(!sbwis.msieversion()) {
											history.pushState(null, null, href);
										}
										sbwis.onstart();
										<?php if(trim($setting['loading_image']) != '') { ?>
										$('<?php echo $setting['navigation_selector']; ?>').before('<div id="sb-infinite-scroll-loader" class="sb-infinite-scroll-loader <?php echo $setting['loading_wrapper_class']; ?> "><img src="<?php echo $setting['loading_image']; ?>" alt=" " /><span><?php echo $setting['loading_message']; ?></span></div>');
										<?php } ?>
										$.get(href, function(response) {
											if(!sbwis.msieversion()) {
												document.title = $(response).filter('title').html();
											}
											<?php
												$content_selectors = $setting['content_selector'].','.$setting['navigation_selector'];
												$content_selectors = explode(',', $content_selectors);
												foreach($content_selectors as $content_selector) {
													if(trim($content_selector) == '')
														continue;
													?>
													var html = $(response).find('<?php echo $content_selector; ?>').html();
													$('<?php echo $content_selector; ?>').html(html);
													<?php
												} ?>
												$('.sb-infinite-scroll-loader').remove();
												sbwis.onfinish();
												<?php
												if($setting['scrolltop'] == 1) { ?>
													var scrollto = 0;
													<?php if(trim($setting['scrollto']) != '') { ?>
														if($('<?php echo $setting['scrollto']; ?>').length) {
															var scrollto = $('<?php echo $setting['scrollto']; ?>').offset().top;
														}
													<?php } ?>
													$('html, body').animate({ scrollTop: scrollto }, 500);
												<?php }
											?>
											$('<?php echo $setting['content_selector'].' '.$setting['item_selector']; ?>').addClass('animated <?php echo $setting['animation']; ?>').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
												$(this).removeClass('animated <?php echo $setting['animation']; ?>');
											});
										});
									}
								});
							}
							
							if(pagination_type == 'load_more_button' || pagination_type == 'infinite_scroll') {
								$(document).ready(function() {
									if($('<?php echo $setting['navigation_selector']; ?>').length) {
										$('<?php echo $setting['navigation_selector']; ?>').before('<div id="sb-infinite-scroll-load-more" class="sb-infinite-scroll-load-more <?php echo $setting['load_more_button_class']; ?> "><a sb-processing="0"><?php echo $setting['load_more_button_text']; ?></a><br class="sb-clear" /></div>');
										if(pagination_type == 'infinite_scroll') {
											$('#sb-infinite-scroll-load-more').addClass('sb-hide');
										}
									}
									$('<?php echo $setting['navigation_selector']; ?>').addClass('sb-hide');
									$('<?php echo $setting['content_selector'].' '.$setting['item_selector']; ?>').addClass('sb-added');
								});
								$('body').on('click', '#sb-infinite-scroll-load-more a', function(e) {
									e.preventDefault();
									if($('<?php echo $setting['next_selector']; ?>').length) {
										$('#sb-infinite-scroll-load-more a').attr('sb-processing', 1);
										var href = $('<?php echo $setting['next_selector']; ?>').attr('href');
										sbwis.onstart();
										<?php if(trim($setting['loading_image']) != '') { ?>
											$('#sb-infinite-scroll-load-more').hide();
											$('<?php echo $setting['navigation_selector']; ?>').before('<div id="sb-infinite-scroll-loader" class="sb-infinite-scroll-loader <?php echo $setting['loading_wrapper_class']; ?> "><img src="<?php echo $setting['loading_image']; ?>" alt=" " /><span><?php echo $setting['loading_message']; ?></span></div>');
										<?php } ?>
										$.get(href, function(response) {
											$('<?php echo $setting['navigation_selector']; ?>').html($(response).find('<?php echo $setting['navigation_selector']; ?>').html());
											
											$(response).find('<?php echo $setting['content_selector'].' '.$setting['item_selector']; ?>').each(function() {
												$('<?php echo $setting['content_selector'].' '.$setting['item_selector']; ?>:last').after($(this));
											});
											
											$('#sb-infinite-scroll-loader').remove();
											$('#sb-infinite-scroll-load-more').show();
											$('#sb-infinite-scroll-load-more a').attr('sb-processing', 0);
											sbwis.onfinish();
											$('<?php echo $setting['content_selector'].' '.$setting['item_selector']; ?>').not('.sb-added').addClass('animated <?php echo $setting['animation']; ?>').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
												$(this).removeClass('animated <?php echo $setting['animation']; ?>').addClass('sb-added');
											});
											
											if($('<?php echo $setting['next_selector']; ?>').length == 0) {
												$('#sb-infinite-scroll-load-more').addClass('finished').removeClass('sb-hide');
												$('#sb-infinite-scroll-load-more a').show().html('<?php echo $setting['finished_message']; ?>').css('cursor', 'default');
											}
											
										});
									} else {
										$('#sb-infinite-scroll-load-more').addClass('finished').removeClass('sb-hide');
										$('#sb-infinite-scroll-load-more a').show().html('<?php echo $setting['finished_message']; ?>').css('cursor', 'default');
									}
								});
								
							}
							if(pagination_type == 'infinite_scroll') {
							
								var buffer_pixels = Math.abs(<?php echo $setting['buffer_pixels']; ?>);
								w.scroll(function () {
									if($('<?php echo $setting['content_selector']; ?>').length) {
										var a = $('<?php echo $setting['content_selector']; ?>').offset().top + $('<?php echo $setting['content_selector']; ?>').outerHeight();
										var b = a - w.scrollTop();
										if ((b - buffer_pixels) < w.height()) {
											if($('#sb-infinite-scroll-load-more a').attr('sb-processing') == 0) {
												$('#sb-infinite-scroll-load-more a').trigger('click');
											}
										}
									}
								});
							
							}<?php
						}
					}
				?>
			},
			onstart: function() {
				<?php echo stripslashes($setting['onstart']).';'; ?>
			},
			onfinish: function() {
				sbwis.trigger_load();
				<?php echo stripslashes($setting['onfinish']).';'; ?>
			},
			msieversion: function() {
				var ua = window.navigator.userAgent;
				var msie = ua.indexOf("MSIE ");
	
				if (msie > 0)      // If Internet Explorer, return version number
					return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));

				return false;
			},
			lazyload_init: function() {
				w.scroll(function () {
					sbwis.trigger_load();
				});
				sbwis.trigger_load();
			},
			trigger_load: function() {
				$(".sb-lazy-img").each(function () {
					sbwis.lazyload($(this));
				});
			},
			lazyload: function(e) {
				var threshold = Math.abs(<?php echo $setting['buffer_pixels']; ?>);
				var a = e.offset().top;
				var b = a - w.scrollTop();
				if ((b - threshold) < w.height()) {
					var h = e.attr("sb-lazy-src");
					e.attr("src",h).removeAttr("sb-lazy-src").removeClass('sb-lazy-img');
					
				}
			}
		};
		sbwis.init();
		sbwis.lazyload_init();
		
	})(jQuery);
	
</script>