// JavaScript Document
var sbwis_admin;
;(function($){
	var w = $(window);
	sbwis_admin = {
		init: function() {
			sbwis_admin.accordion_init();
			sbwis_admin.infinite_scroll_setting_form();
			sbwis_admin.sb_media_uploader_init();
			sbwis_admin.import_export_popup();
			sbwis_admin.import_settings();
			sbwis_admin.animations();
			sbwis_admin.mobile_settings_box();
		},
		accordion_init: function() {
			$('body').on('click', '.handlediv, .hndle span, .sb-button-group a.edit', function() {
				sbwis_admin.accordion($(this));
			});
		},
		accordion: function($this) {
			$this.closest('.postbox').children('.inside').slideToggle('fast', function(){
				$this.closest('.postbox').toggleClass('closed');
			});
		},
		animations: function() {
			$('#animation').change(function() {
				var animation = $(this).val();
				$('#animate-img').attr('class', '');
				$('#animate-img').addClass('animated '+animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					$('#animate-img').attr('class', '');
				});
			});
		},
		infinite_scroll_setting_form: function() {
			$('body').on('submit', 'form.infinite_scroll_setting_form', function(e) {
				var error = 0;
				var focusfield = '';
				var content_selector 	= $(this).find("input[name='settings[content_selector]']");
				var navigation_selector = $(this).find("input[name='settings[navigation_selector]']");
				var next_selector		= $(this).find("input[name='settings[next_selector]']");
				var item_selector		= $(this).find("input[name='settings[item_selector]']");
				
				$('.field-wrapper .error').removeClass('error');
				
				if($.trim(content_selector.val()) == '') {
					content_selector.addClass('error');
					error = 1;
					if(focusfield == '')
						focusfield = content_selector;
				}
				
				if($.trim(item_selector.val()) == '') {
					item_selector.addClass('error');
					error = 1;
					if(focusfield == '')
						focusfield = item_selector;
				}
				
				if($.trim(navigation_selector.val()) == '') {
					navigation_selector.addClass('error');
					error = 1;
					if(focusfield == '')
						focusfield = navigation_selector;
				}
				
				if($.trim(next_selector.val()) == '') {
					next_selector.addClass('error');
					error = 1;
					if(focusfield == '')
						focusfield = next_selector;
				}
				
				if(error == 1) {
					sbwis_admin.sb_growl('Please fill all required fields.');
					sbwis_admin.sb_growl_close(1000);
					$('html, body').animate({
						'scrollTop': focusfield.offset().top - 50
					}, 'fast', function() {
						focusfield.focus();
					});
					return false;
				}
				sbwis_admin.setting_form_ajax($(this), e);
			});
		},
		setting_form_ajax: function($this, e) {
			e.preventDefault();
			var data = $this.serialize();
			var url = $this.attr('action');
			sbwis_admin.sb_growl('Saving...');
			$('.btn-save-settings').attr('disabled', true);
			$this.find('.ajax-loader').show();
			$.ajax({
				type 	 :	'POST',
				url	 	 : 	url,
				data 	 : 	data,
				dataType :	'json',
				success	 :	function(response) {
					$('.btn-save-settings').attr('disabled', false);
					$this.find('.ajax-loader').hide();
					sbwis_admin.sb_growl('Settings Saved.');
					sbwis_admin.sb_growl_close(1000);
				}
			});
		},
		sb_growl: function($msg) {
			$('.sb-message').fadeIn('fast');
			$('.sb-message').html($msg);
		},
		sb_growl_close: function(time) {
			setTimeout(function() {
				$('.sb-message').fadeOut('fast', function() {
					$('.sb-message').removeClass('fail');
				});
			}, time);
		},
		sb_media_uploader_init: function() {
			var custom_uploader;
			$('body').on('blur', '.loading_image', function() {
				var src = $(this).val();
				$(this).next().next().attr('src', src);
			});
			$('body').on('click', '.upload_image', function() {
				$this = $(this);
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
				
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false
				});
				
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$this.prev().val(attachment.url);
					$this.next('img.loading_image_preview').attr('src', attachment.url);
				});
				
				custom_uploader.open();
			});

		},
		import_settings: function() {
			$('body').on('submit', '#frm-import-settings', function(e) {
				e.preventDefault();
				$this = $(this);
				$this.find('.ajax-loader').show();
				$('.import-is-setting').attr('disabled', true);
				sbwis_admin.sb_growl('Importing...');
				$(this).ajaxSubmit({
					type 	 :	'POST',
					url	 	 : 	SB.AJAX,
					success	 :	function(response) {
						$('.import-is-setting').attr('disabled', false);
						$this.find('.ajax-loader').hide();
						sbwis_admin.sb_growl(response);
						sbwis_admin.sb_growl_close(3000);
						$this[0].reset();
					}
				});
			});
		},
		import_export_popup: function() {
			var $popup = $("#import-export");
			$popup.dialog({                   
				dialogClass   	:	'wp-dialog',
				modal         	: 	true,
				title			:	'Import / Export Settings',
				width			:	400,
				height			:	500,
				draggable     	: 	false,
				resizable     	: 	false,
				autoOpen      	: 	false,
				closeOnEscape 	: 	false,
				open			: 	function() {
					$('.ui-dialog-buttonpane').find('button:contains("Select")').addClass('button-primary');
					$('.ui-dialog-titlebar-close').replaceWith('<button class="ui-button ui-widget ui-dialog-titlebar-close" onclick="jQuery(\'#import-export\').html(\'\').addClass(\'popup-ajax-loader\'); location.reload();"></button>');
				},
				close			:	function() {
					location.reload();
				},
				buttons       	:	{
					"Close": function() { $popup.html('').addClass('popup-ajax-loader'); location.reload(); }
				}
			});
			
			$('body').on('click','#import-export-link',function(event) {
				event.preventDefault();
				$popup.addClass('popup-ajax-loader');
				$popup.html('');
				$popup.dialog('open');
				$.ajax({
					type 	 :	'POST',
					url	 	 : 	SB.AJAX,
					data 	 : 	{action: 'import_export_settings'},
					success	 :	function(response) {
						$popup.html(response);
						$popup.removeClass('popup-ajax-loader');
					}
				});
			});
		},
		mobile_settings_box: function() {
			$('body').on('click', '#mobile_pagination_settings', function() {
				$('.small-device-settings-box').slideToggle();
			});
		}
	}
	sbwis_admin.init();
})(jQuery);