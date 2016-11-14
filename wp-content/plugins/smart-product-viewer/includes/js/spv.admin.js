(function($){
	
	SmartProduct.Plugin = {
			
		init: function() {
			
			this._initSortable();
			this._initUpload();
			
		},
		
		_initSortable: function() {

			var self = this;
			
			var product_images = $( "#smart-product-sortable" ).sortable({
				cursor: 'move',
				stop: function( event, ui ) {
					// Update order in DB
					self.orderUpdate(this);
				}
			});
			$( "#smart-product-sortable" ).disableSelection();
			
			// Reorder button
			$( "#smart-product-reorder" ).click( function(){
				
				// Get your list items
				var items = $('#smart-product-sortable').find('li').toArray().reverse();
				
				// Clear the old list items and insert the newly ordered ones
				$('#smart-product-sortable').empty().html(items);

				// Update order in DB
				self.orderUpdate(product_images);

			});
			
		},

		orderUpdate: function( product_images ) {

			var ids = $(product_images).sortable('toArray', { attribute: 'data-id' });
					
			// call ThreeSixtyPlugin::updateImages function via AJAX
			var data = {
					action: 	'update_smart_product_images',
					post_id: 	SmartProduct.post_id,
					images_ids: ids,
				};
			
			$.post(SmartProduct.ajax_url, data, function( response ) {
				// new order returned
				//console.log(response);
			});

		},
		
		_initUpload: function() {
			
			var file_frame;
			
			$('#smart-product-upload-photos').on('click', function( event ) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}
				
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Smart Product Images',
					button: {
						text: 'Use for Smart Product',
					},
					library : { 
						type : 'image' 
					},
					multiple: true
				});
				
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
				
					// Get all attachments
					var attachments = file_frame.state().get('selection').toJSON();
					
					// Create attachment's ids array
					var attachment_ids = new Array();
					
					$(attachments).each( function() {
						attachment_ids.push(this.id);
					});
					
					// Call SmartProductPlugin::updateImages function via AJAX
					var data = {
							action: 	'update_smart_product_images',
							post_id: 	SmartProduct.post_id,
							images_ids: attachment_ids
						};
				
					$.post(SmartProduct.ajax_url, data, function( photoIds ) {
						// Update
						$('#smart-product-images-wrap').html(photoIds);
				    });
				});
				
				// Finally, open the modal
				file_frame.open();
				
			});	
		}	
	}
	
	$(document).ready(function() {
		SmartProduct.Plugin.init();
	});
	
})(jQuery);