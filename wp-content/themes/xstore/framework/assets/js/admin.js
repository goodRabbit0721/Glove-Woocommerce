jQuery(document).ready(function($){

	/* Promo banner in admin panel */
	
	jQuery('.promo-text-wrapper .close-btn').click(function(){
		
		var confirmIt = confirm('Are you sure?');
		
		if(!confirmIt) return;
		
		var widgetBlock = jQuery(this).parent();
	
		var data =  {
			'action':'et_close_promo',
			'close': widgetBlock.attr('data-etag')
		};
		
		widgetBlock.hide();
		
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			success: function(response){
				widgetBlock.remove();
			},
			error: function(data) {
				alert('Error while deleting');
				widgetBlock.show();
			}
		});
	});
	
	/* UNLIMITED SIDEBARS */
	
	var delSidebar = '<div class="delete-sidebar">delete</div>';
	
	jQuery('.sidebar-etheme_custom_sidebar').find('.sidebar-name-arrow').before(delSidebar);
	
	jQuery('.delete-sidebar').click(function(){
		
		var confirmIt = confirm('Are you sure?');
		
		if(!confirmIt) return;
		
		var widgetBlock = jQuery(this).parent().parent();
	
		var data =  {
			'action':'etheme_delete_sidebar',
			'etheme_sidebar_name': jQuery(this).parent().find('h2').text()
		};
		
		widgetBlock.hide();
		
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			success: function(response){
				console.log(response);
				widgetBlock.remove();
			},
			error: function(data) {
				alert('Error while deleting sidebar');
				widgetBlock.show();
			}
		});
	});

	
	/* end sidebars */
    
    $(document).on("click", ".upload_image_button", function() {

        jQuery.data(document.body, 'prevElement', $(this).prev());

        window.send_to_editor = function(html) {
            var imgurl = jQuery('img',html).attr('src');
            var inputText = jQuery.data(document.body, 'prevElement');

            if(inputText != undefined && inputText != '')
            {
                inputText.val(imgurl);
            }

            tb_remove();
        };

        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });

    setTimeout(function() {
    	$('.et-tab-label.vc_tta-section-append').removeClass('vc_tta-section-append').addClass('et-tab-append');
    }, 1000);

    $(document).on('click', '.et-tab-label.et-tab-append', function(event) {

    	if( typeof vc == 'undefined' ) return;

        var newTabTitle = 'Tab', 
        	params, 
        	shortcode,
        	modelId = $(this).parents('.wpb_et_tabs').data('model-id'),
        	prepend = false;

        params = {
            shortcode: "et_tab",
            params: {
                title: newTabTitle
            },
            parent_id: modelId,
            order: _.isBoolean(prepend) && prepend ? vc.add_element_block_view.getFirstPositionIndex() : vc.shortcodes.getNextOrder(),
            prepend: prepend
        }

        shortcode = vc.shortcodes.create(params);

    });

});

