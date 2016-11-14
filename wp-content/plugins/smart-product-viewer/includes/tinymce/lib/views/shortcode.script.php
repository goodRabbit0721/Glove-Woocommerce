<script type="text/javascript">
(function($) {
	$(document).ready(function() {
		$('body').on('click', '#<?php echo $this->prefix; ?>gallery-submit', function() {
			var options = {
				<?php 
				foreach ( $params as $id => $section ) {
					foreach ( $section['params'] as $id => $param ) {	
						echo $id . ":'" . $param['value'] . "',\r\n";
					}
				}?>
			};

			var table 		= $("#<?php echo $this->prefix; ?>table"),
				shortcode 	= '[smart-product',
				user_params = {};

			for (var index in options) {
				
				var value = table.find('#<?php echo $this->prefix; ?>' + index).val();
				//var value = table.find('input[name=' + index + ']:checked').val();

				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] && value !== undefined ) {
					// add to params
					user_params[index] = value;
					// add to shortcode
					shortcode += ' ' + index + '="' + value + '"';
				}
			}

			shortcode += ']';

			parent.tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);
			parent.tinymce.activeEditor.windowManager.close();

			// Check if user want to save
			var save_params = $("#<?php echo $this->prefix; ?>save-params").is(':checked');

			if ( save_params ) {

				var params 	= JSON.stringify(user_params);
				var data 	= {
					'action': 'save_user_params',
					'params': params,
					'user_ID': <?php echo get_current_user_id(); ?>
				};
				// Send AJAX post to 
				$.post(ajaxurl, data, function(response) {
					if (typeof console == "object") {
						console.log( response );
					}
				});
			}
		});
		
		// Initialize ColorPicker
		function _initColpik() {
			
			$(".<?php echo $this->prefix; ?>color").each(function( i ) {
				var color = $(this).val();
				$(this).css('border-color', color);
			});

			$('.<?php echo $this->prefix; ?>color').colpick({
				layout:'hex',
				submit:0,
				colorScheme:'dark',
				onChange:function(hsb,hex,rgb,el,bySetColor) {
					$(el).css('border-color','#'+hex);
					// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
					if(!bySetColor) $(el).val('#'+hex);
				}
			}).click(function(){
				var color = $(this).val();
				$(this).colpickSetColor(color.substring(1));
			});
		};

		function _initConditions() {
			<?php 
			foreach ( $params as $id => $section ) {
				foreach ( $section['params'] as $key => $param ) {
					
					$id = $this->prefix . $key;
					
					if ( isset ( $param['condition'] ) ) :

						$conditions = $param['condition'];
						
						foreach ( $conditions as $key => $value ) : ?>
					
							$('body').on('change', '#<?php echo $this->prefix . $key; ?>', function() {

								var show = true;
								
								<?php foreach ( $conditions as $key => $values ) : $values = explode(",", $value); ?>
									if ( 
									<?php foreach ( $values as $num => $value ) : ?>
										<?php if ( $num != 0 ) echo "&&"; ?>
										$('#<?php echo $this->prefix . $key; ?>').val() != "<?php echo $value; ?>"
									<?php endforeach; ?>
									) show = false;
								<?php endforeach; ?>

								if ( ! show )
									$("#<?php echo $id; ?>-row").hide('fast');
								else
									$("#<?php echo $id; ?>-row").show('slow');
							});
					
						<?php endforeach;
					endif;
				}
			} ?>
		};

		function _initDefaultsSwitch() {
			$('body').on('change', '#<?php echo $this->prefix; ?>default-switch', function() {
				var defaults 		= $('#<?php echo $this->prefix; ?>table-defaults');
				var current 		= $('#<?php echo $this->prefix; ?>table');

				var defaults_html 	= $(defaults).html();
				var current_html 	= $(current).html();

				$(current).html(defaults_html);
				$(defaults).html(current_html);
			});
		}
		
		_initDefaultsSwitch()
		_initConditions();
		try{
			_initColpik();
		}catch(e){}
	});
}(jQuery));
</script>