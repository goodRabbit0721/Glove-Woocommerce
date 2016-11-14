<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Brands Filter Widget
// **********************************************************************// 
class ETheme_Brands_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'etheme_widget_brands', 'description' => esc_html__( "Products brands list", 'xstore') );
		parent::__construct('etheme-brands', '8theme - '.__('Brands list', 'xstore'), $widget_ops);
		$this->alt_option_name = 'etheme_widget_brans';
	}

	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? false : $instance['title']);
		echo $before_widget;
		if(!$title == '' ){
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		$current_term = get_queried_object();
		$args = array( 'hide_empty' => false);
		$terms = get_terms('brand', $args);
		$count = count($terms); $i=0;
		if ( ! is_wp_error( $terms ) && $count > 0) {
			?>
			<ul>
				<?php
				foreach ($terms as $term) {
					$i++;
					$curr = false;
					$thumbnail_id 	= absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
					if(isset($current_term->term_id) && $current_term->term_id == $term->term_id) {
						$curr = true;
					}
					?>
					<li>
						<a href="<?php echo get_term_link( $term ); ?>" title="<?php echo sprintf(__('View all products from %s', 'xstore'), $term->name); ?>"><?php if($curr) echo '<strong>'; ?><?php echo $term->name; ?><?php if($curr) echo '</strong>'; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : '';

		?>
		<?php etheme_widget_input_text(__('Title', 'xstore'), $this->get_field_id('title'),$this->get_field_name('title'), $title); ?>

		<?php
	}
}