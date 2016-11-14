<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	get_header( 'shop' ); 

	$l = etheme_page_config();

	$full_width = etheme_get_option('shop_full_width');

	if($full_width) {
		$content_span = 'col-md-12';
	}
	
	/**
	 * woocommerce_before_main_content hook
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action( 'woocommerce_before_main_content' );
?>
<div class="<?php echo ($full_width) ? 'container' : ''; ?>">
	<?php etheme_category_header();?>
	<?php do_action( 'woocommerce_archive_description' ); ?>
</div>
<div class="<?php echo (!$full_width) ? 'container' : 'shop-full-width'; ?> sidebar-mobile-<?php etheme_option( 'sidebar_for_mobile' ); ?> content-page">
	<div class="sidebar-position-<?php echo esc_attr( $l['sidebar'] ); ?>">
		
		<div class="row product-page-title">
			<a href="#" id="gallery"></a>
			<div class="col-md-3"></div>
			<div class="col-md-9">Products</div>
		</div>
		<div class="row" style="height: 30px;"></div>
		<div class="row">
			
			<div class="content main-products-loop <?php echo esc_attr( $l['content-class'] ); ?>">

				<?php if ( have_posts() ) : ?>

					<?php wc_print_notices(); ?>

                    <!-- this is part. -->
					<?php if (woocommerce_products_will_display() && etheme_get_option( 'top_toolbar' ) ): ?>
	                    <!-- <div class="filter-wrap">
	                    	<div class="filter-content">
		                    	<?php
		                    		/**
		                    		 * woocommerce_before_shop_loop hook
		                    		 *
		                    		 * @hooked woocommerce_result_count - 20
		                    		 * @hooked woocommerce_catalog_ordering - 30
		                             * @hooked etheme_grid_list_switcher - 35
		                    		 */
		                    		do_action( 'woocommerce_before_shop_loop' );
		                    	?>
	                    	</div>
	                    </div> -->
					<?php endif ?>
	                    
					<div class="shop-filters widget-columns-<?php echo etheme_get_option( 'filters_columns' ); ?><?php echo (etheme_get_option('filter_opened')) ? ' filters-opened' : ''; ?>">
						<div class="shop-filters-area">
						  	<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('shop-filters-sidebar')): ?>
							<?php endif; ?>	
						</div>
					</div>

					<?php woocommerce_product_loop_start(); ?>
		
						<?php woocommerce_product_subcategories(); ?>
		
						<?php while ( have_posts() ) : the_post(); ?>
							
							<?php wc_get_template_part( 'content', 'product' ); ?>
		
						<?php endwhile; // end of the loop. ?>
		
					<?php woocommerce_product_loop_end(); ?>
		
                    <div class="after-shop-loop"><?php
							/**
							 * woocommerce_after_shop_loop hook
							 *
							 * @hooked woocommerce_pagination - 10
							 */
							do_action( 'woocommerce_after_shop_loop' );
						?></div>
		
				<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		
					<?php wc_get_template( 'loop/no-products-found.php' ); ?>
		
				<?php endif; ?>
		
			<?php
				/**
				 * woocommerce_after_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				 */
				etheme_after_products_widgets();
				do_action( 'woocommerce_after_main_content' );
			?>

			</div>

			<?php get_sidebar('shop'); ?>
		</div>

	</div>
</div>

<?php get_footer( 'shop' ); ?>