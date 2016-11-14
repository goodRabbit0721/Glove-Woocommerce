<div id="<?php echo $this->prefix; ?>shortcode-form">
	<div class="<?php echo $this->prefix; ?>form-wrap">
		<?php if ( $saved_meta ) : ?>
		<p><input id="<?php echo $this->prefix; ?>default-switch" type="checkbox" checked="checked" /> <em>Show parameters saved previously</em></p>
		<?php endif; ?>
		<table id="<?php echo $this->prefix; ?>table" class="<?php echo $this->prefix; ?>table">
		<?php //var_dump( $saved_meta ); ?>
		<?php foreach ( $params as $id => $section ) : ?>
			<tr><td colspan="2"><h2><?php echo $section['title']; ?></h2></td></tr>
			<?php foreach ( $section['params'] as $key => $param ) :
				
				$id 		= $this->prefix . $key;
				$value 		= ( isset( $saved_meta[$key] ) ) ? $saved_meta[$key] : $param['value']; 
				$display 	= "";

				if ( isset ( $param['condition'] ) ) {
					
					$conditions = $param['condition'];

					foreach ( $conditions as $key => $values ) {
						
						$values = explode(",", $values);
						
						$condition_value = $section['params'][$key]['value'];
						$condition_value = ( isset( $saved_meta[$key] ) ) ? $saved_meta[$key] : $condition_value; 

						$display = "display:none";

						foreach ( $values as $num => $val ) {
							if ( $val == $condition_value )
								$display = "";
						}								

					}
				} ?>

				<tr id="<?php echo $id; ?>-row" style="<?php echo $display; ?>">
				<?php switch ( $param['type'] ) {
					
					case 'post': 
						$threesxity_sliders = get_posts( array(
							'posts_per_page'  => -1,
							'post_type'       => $param['post_type']
						)); ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><select id="<?php echo $id; ?>" name="<?php echo $id; ?>">
							<?php foreach ( $threesxity_sliders as $slider ) : ?>
							<option <?php echo selected( $slider->ID, $value ); ?> value="<?php echo $slider->ID; ?>"><?php echo get_the_title( $slider->ID ); ?></option>
							<?php endforeach; ?>
							</select>
					<?php break;

					case 'text': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><input type="text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
					<?php break;
					
					case 'select': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><select id="<?php echo $id; ?>" name="<?php echo $id; ?>">
							<?php foreach ( $param['options'] as $option_value => $title ) : ?>
							<option <?php selected( $option_value, $value ) ?> value="<?php echo $option_value; ?>"><?php echo $title; ?></option>	
							<?php endforeach; ?>
							</select>
					<?php break;

					case 'color': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><input size="8" class="<?php echo $this->prefix; ?>color" type="text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
					<?php break;
				
				} ?>

				<?php echo ( isset( $param['units'] ) ) ? $param['units'] : ""; ?><br />
					<em><?php echo $param['desc']; ?></em></td>
				</tr>

			<?php endforeach; ?>
		<?php endforeach; ?>
		</table>

		<table id="<?php echo $this->prefix; ?>table-defaults" style="display:none">
		<?php foreach ( $params as $id => $section ) : ?>
			<tr><td colspan="2"><h2><?php echo $section['title']; ?></h2></td></tr>
			<?php foreach ( $section['params'] as $key => $param ) :
				
				$id 		= $this->prefix . $key;
				$value 		= $param['value']; 
				$display 	= "";

				if ( isset ( $param['condition'] ) ) {
					
					$conditions = $param['condition'];

					foreach ( $conditions as $key => $values ) {
						
						$values = explode(",", $values);
						
						$condition_value = $section['params'][$key]['value'];
						
						$display = "display:none";

						foreach ( $values as $num => $val ) {
							if ( $val == $condition_value )
								$display = "";
						}								

					}
				} ?>

				<tr id="<?php echo $id; ?>-row" style="<?php echo $display; ?>">
				<?php switch ( $param['type'] ) {

					case 'post': 
						$threesxity_sliders = get_posts( array(
							'posts_per_page'  => -1,
							'post_type'       => $param['post_type']
						)); ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><select id="<?php echo $id; ?>" name="<?php echo $id; ?>">
							<?php foreach ( $threesxity_sliders as $slider ) : ?>
							<option <?php echo selected( $slider->ID, $value ); ?> value="<?php echo $slider->ID; ?>"><?php echo get_the_title( $slider->ID ); ?></option>
							<?php endforeach; ?>
							</select>
					<?php break;
					
					case 'text': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><input type="text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
					<?php break;
					
					case 'select': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><select id="<?php echo $id; ?>" name="<?php echo $id; ?>">
							<?php foreach ( $param['options'] as $option_value => $title ) : ?>
							<option <?php selected( $option_value, $value ) ?> value="<?php echo $option_value; ?>"><?php echo $title; ?></option>	
							<?php endforeach; ?>
							</select>
					<?php break;

					case 'color': ?>
						<td><label for="<?php echo $id; ?>"><?php echo $param['title']; ?></label></td>
						<td><input size="8" class="<?php echo $this->prefix; ?>color" type="text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
					<?php break;
				
				} ?>

				<?php echo ( isset( $param['units'] ) ) ? $param['units'] : ""; ?><br />
					<em><?php echo $param['desc']; ?></em></td>
				</tr>

			<?php endforeach; ?>
		<?php endforeach; ?>
		</table>

		<p><input id="<?php echo $this->prefix; ?>save-params" type="checkbox" checked="checked" /> <em>Save chosen parameters for next time</em></p>
		<input type="button" id="<?php echo $this->prefix; ?>gallery-submit" class="button button-primary" value="Insert Shortcode" name="submit" />
		
	</div>
</div>