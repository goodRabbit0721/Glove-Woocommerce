<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists('SmartProductWidget') ) {

	class SmartProductWidget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 * 
		 */
		public function __construct() {
			parent::__construct(
		 		'360_widget', // Base ID
				'Smart Product Widget', // Name
				array( 'description' => __( 'Show Smart Product View', '360_image' ), ) // Args
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			echo $before_widget;
			if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			
			$slider = new ThreeSixtySlider( $instance );
			$slider->show();
			
			echo $after_widget;
			
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] 		= strip_tags( $new_instance['title'] );
			$instance['id'] 		= strip_tags( $new_instance['id'] );
			$instance['width'] 		= strip_tags( $new_instance['width'] );
			$instance['scrollbar'] 	= strip_tags( $new_instance['scrollbar'] );
			$instance['color'] 		= strip_tags( $new_instance['color'] );
			$instance['style'] 		= strip_tags( $new_instance['style'] );
			$instance['interval'] 	= strip_tags( $new_instance['interval'] );
			$instance['nav'] 		= ( $new_instance['nav'] == "true" ) ? "true" : "false";
			$instance['border'] 	= ( $new_instance['border'] == "true" ) ? "true" : "false";
			$instance['autoplay'] 	= ( $new_instance['autoplay'] == "true" ) ? "true" : "false";
			$instance['fullscreen'] 	= ( $new_instance['fullscreen'] == "true" ) ? "true" : "false";
			$instance['move_on_scroll'] 	= ( $new_instance['move_on_scroll'] == "true" ) ? "true" : "false";

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			
			extract( shortcode_atts( array(
					'title' 	=> '',
					'id' 		=> '',
					'nav' 		=> 'true',
					'border' 	=> 'true',
					'scrollbar'	=> '',
					'width' 	=> '',
					'style' 	=> 'flat',
					'color' 	=> 'gray',
					'autoplay'	=> 'false',
					'fullscreen'	=> 'false',
					'move_on_scroll'	=> 'false',
					'interval'	=> '40'
			), $instance ) );

			$threesxity_sliders = get_posts( array(
							'posts_per_page'  => -1,
							'post_type'       => 'smart-product'
					) );
			?>
			
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>	
				<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Smart Product:'); ?></label>
				<select class="widefat" name="<?php echo $this->get_field_name('id'); ?>">
					<?php foreach ( $threesxity_sliders as $slider ) : ?>
					<option <?php echo selected( $slider->ID, esc_attr( $id )); ?> value="<?php echo $slider->ID; ?>"><?php echo get_the_title( $slider->ID ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<table>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('nav'); ?>"><?php _e('Show navigation:'); ?></label>
					</td>
					<td>
						<input type="checkbox" <?php checked('true', esc_attr( $nav )) ?> id="<?php echo $this->get_field_id('nav'); ?>" name="<?php echo $this->get_field_name('nav'); ?>" value="true"/>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('border'); ?>"><?php _e('Show border:'); ?></label>
					</td>
					<td>
						<input type="checkbox" <?php checked('true', esc_attr( $border )) ?> id="<?php echo $this->get_field_id('border'); ?>" name="<?php echo $this->get_field_name('border'); ?>" value="true"/>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:'); ?></label> 
					</td>
					<td>
						<input size="5" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />px
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('scrollbar'); ?>"><?php _e('Show scrollbar:'); ?></label>
					</td>
					<td>
						<select class="widefat" name="<?php echo $this->get_field_name('scrollbar'); ?>">
							<option <?php echo selected('', esc_attr( $scrollbar )); ?> value="">No</option>
							<option <?php echo selected('top', esc_attr( $scrollbar )); ?> value="top">On Top</option>
							<option <?php echo selected('bottom', esc_attr( $scrollbar )); ?> value="bottom">In the Bottom</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('color'); ?>"><?php _e('Color:'); ?></label>
					</td>
					<td>
						<select class="widefat" name="<?php echo $this->get_field_name('color'); ?>">
							<option <?php echo selected('dark-blue', 	esc_attr( $color )); ?> value="dark-blue">Dark Blue</option>
							<option <?php echo selected('light-blue', 	esc_attr( $color )); ?> value="light-blue">Light Blue</option>
							<option <?php echo selected('red', 			esc_attr( $color )); ?> value="red">Red</option>
							<option <?php echo selected('brown', 		esc_attr( $color )); ?> value="brown">Brown</option>
							<option <?php echo selected('purple', 		esc_attr( $color )); ?> value="purple">Purple</option>
							<option <?php echo selected('gray', 		esc_attr( $color )); ?> value="gray">Gray</option>
							<option <?php echo selected('yellow', 		esc_attr( $color )); ?> value="yellow">Yellow</option>
							<option <?php echo selected('green', 		esc_attr( $color )); ?> value="green">Green</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:'); ?></label>
					</td>
					<td>
						<select class="widefat" name="<?php echo $this->get_field_name('style'); ?>">
							<option <?php echo selected('glow', 		esc_attr( $style )); ?> value="glow">Glow</option>
							<option <?php echo selected('fancy', 		esc_attr( $style )); ?> value="fancy">Fancy</option>
							<option <?php echo selected('wave', 		esc_attr( $style )); ?> value="wave">Wave</option>
							<option <?php echo selected('flat-round', 	esc_attr( $style )); ?> value="flat-round">Flat round</option>
							<option <?php echo selected('flat-square', 	esc_attr( $style )); ?> value="flat-square">Flat square</option>
							<option <?php echo selected('vintage', 		esc_attr( $style )); ?> value="vintage">Vintage</option>
							<option <?php echo selected('arrows', 		esc_attr( $style )); ?> value="arrows">Arrows</option>
							<option <?php echo selected('leather', 		esc_attr( $style )); ?> value="leather">Leather</option>
						</select>		
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:'); ?></label>
					</td>
					<td>
						<input type="checkbox" <?php checked('true', esc_attr( $autoplay )) ?> id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>" value="true"/>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('fullscreen'); ?>"><?php _e('Fullscreen Lightbox:'); ?></label>
					</td>
					<td>
						<input type="checkbox" <?php checked('true', esc_attr( $fullscreen )) ?> id="<?php echo $this->get_field_id('fullscreen'); ?>" name="<?php echo $this->get_field_name('fullscreen'); ?>" value="true"/>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('move_on_scroll'); ?>"><?php _e('Move on Page Scroll:'); ?></label>
					</td>
					<td>
						<input type="checkbox" <?php checked('true', esc_attr( $move_on_scroll )) ?> id="<?php echo $this->get_field_id('move_on_scroll'); ?>" name="<?php echo $this->get_field_name('move_on_scroll'); ?>" value="true"/>
					</td>
				</tr>
				<tr>
					<td>	
						<label for="<?php echo $this->get_field_id('interval'); ?>"><?php _e('Frames Interval:'); ?></label> 
					</td>
					<td>
						<input size="5" id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>" type="text" value="<?php echo esc_attr( $interval ); ?>" />ms
					</td>
				</tr>
			</table>
			<?php 
		}
	}
}
?>