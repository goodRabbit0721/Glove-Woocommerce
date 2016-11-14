<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Portfolio Post Type
// **********************************************************************// 


if(!function_exists('etheme_portfolio_shortcode')) {
	function etheme_portfolio_shortcode($atts) {
		$a = shortcode_atts( array(
	       'title' => 'Recent Works',
	       'limit' => 12
	   ), $atts );
	   
	   
	   return etheme_get_recent_portfolio($a['limit'], $a['title']);
	}
}

if(!function_exists('etheme_get_recent_portfolio')) {
	function etheme_get_recent_portfolio($limit, $title = 'Recent Works', $not_in = 0) {
		$args = array(
			'post_type' => 'etheme_portfolio',
			'order' => 'DESC',
			'orderby' => 'date',
			'posts_per_page' => $limit,
			'post__not_in' => array( $not_in )
		);
		
		return etheme_create_portfolio_slider($args, $title);
	}
}

if(!function_exists('etheme_create_portfolio_slider')) {
	function etheme_create_portfolio_slider($args, $title = false, $width = 540, $height = 340, $crop = true){
		global $wpdb;
	    $box_id = rand(1000,10000);
	    $multislides = new WP_Query( $args );
	    $sliderHeight = etheme_get_option('default_blog_slider_height');
	    $class = '';
	    
		ob_start();
	        if ( $multislides->have_posts() ) :
	            $title_output = '';
	            if ($title) {
	                $title_output = '<h3 class="title"><span>'.$title.'</span></h3>';
	            }   
	              echo '<div class="slider-container carousel-area '.$class.'">';
		              echo $title_output;
		              echo '<div class="items-slide slider-'.$box_id.'">';
		                    echo '<div class="slider recentCarousel">';
		                    $_i=0;
		                    while ($multislides->have_posts()) : $multislides->the_post();
		                        $_i++;
		                        get_template_part( 'portfolio', 'slide' );

		                    endwhile; 
		                    echo '</div><!-- slider -->'; 
		              echo '</div><!-- products-slider -->';
	              echo '</div><!-- slider-container -->'; 

	           
	            echo '
	                <script type="text/javascript">
	                    jQuery(".slider-'.$box_id.' .slider").owlCarousel({
	                        items:4, 
	                        lazyLoad : true,
	                        navigation: true,
	                        navigationText:false,
	                        rewindNav: false,
	                        itemsCustom: [[0, 1], [479,2], [619,2], [768, 2],  [1200, 3], [1600, 3]]
	                    });

	                </script>
	            ';
	        endif;
	        wp_reset_query();

		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
}

if(!function_exists('etheme_project_categories')) {
	function etheme_project_categories($id) {
		$term_list = wp_get_post_terms($id, 'portfolio_category');
		$_i = 0;
		foreach ($term_list as $value) { 
			$_i++;
			echo '<a href="'.get_term_link($value, 'portfolio_category').'">';
				echo $value->name;
			echo '</a>';
			if($_i != count($term_list)) 
				echo ', ';
		}
	}
}



if(!function_exists('etheme_portfolio_grid_shortcode')) {

	function etheme_portfolio_grid_shortcode() {
		$a = shortcode_atts( array(
	       'categories' => '',
	       'limit' => -1,
	   		'show_pagination' => 1
	   ), $atts );
	   
	   
	   return etheme_portfolio($a['categories'], $a['limit'], $a['show_pagination']);
	    
	}
}

if(!function_exists('etheme_portfolio')) {
	function etheme_portfolio($categories = false, $limit = false, $show_pagination = true) {

			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$cat = get_query_var('portfolio_category');
			
			$tax_query = array();

			if(!$limit) {
				$limit = etheme_get_option('portfolio_count');
			}

			$spacing = etheme_get_option('portfolio_margin');

			if(is_array($categories) && !empty($categories)) {
				$tax_query = array(
					array(
						'taxonomy' => 'portfolio_category',
						'field' => 'term_id',
						'terms' => $categories,
						'operator' => 'IN'
					)
				);
			} else if(!empty($cat)) {
				$tax_query = array(
					array(
						'taxonomy' => 'portfolio_category',
						'field' => 'slug',
						'terms' => $cat
					)
				);
			}

			$args = array(
				'post_type' => 'etheme_portfolio',
				'paged' => $paged,	
				'posts_per_page' => $limit,
				'tax_query' => $tax_query
			);

			$loop = new WP_Query($args);
			
			if ( $loop->have_posts() ) : ?>
				<?php if( ! is_tax( 'portfolio_category' ) ) : ?>
					<ul class="portfolio-filters">
						<li><a href="#" data-filter="*" class="btn-filter active"><?php esc_html_e('Show All', 'xstore'); ?></a></li>
							<?php
							$categories = get_terms('portfolio_category', array('include' => $categories));
							$catsCount = count($categories);
							$_i=0;
							foreach($categories as $category) {
								$_i++;
								?>
									<li><a href="#" data-filter=".portfolio_category-<?php echo $category->slug; ?>" class="filter-btn"><?php echo $category->name; ?></a></li>
								<?php
							}

							?>
					</ul>
				<?php endif; ?>
				<div class="portfolio spacing-<?php echo esc_attr( $spacing ); ?>">
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

						<?php
							get_template_part( 'content', 'portfolio' );
						?>

					<?php endwhile; ?>
				</div>

			<?php if ($show_pagination): ?>
				<div class="portfolio-pagination">
					<?php etheme_pagination($loop, $paged); ?>
				</div>
			<?php endif ?>
			
		<?php else: ?>

			<h3><?php esc_html_e('No projects were found!', 'xstore') ?></h3>

		<?php endif;
	}
}
