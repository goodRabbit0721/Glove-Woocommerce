<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$hover = etheme_get_option('product_img_hover');
$view = etheme_get_option('product_view');
$view_color = etheme_get_option('product_view_color');
$size = apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
$view_mode = etheme_get_view_mode();

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

$show_counter = false;

if ( ! empty( $woocommerce_loop['show_counter'] ) )
    $show_counter = $woocommerce_loop['show_counter'];

if ( ! empty( $woocommerce_loop['hover'] ) )
    $hover = $woocommerce_loop['hover'];

if ( ! empty( $woocommerce_loop['product_view'] ) )
    $view = $woocommerce_loop['product_view'];

if ( ! empty( $woocommerce_loop['product_view_color'] ) )
    $view_color = $woocommerce_loop['product_view_color'];

if ( ! empty( $woocommerce_loop['size'] ) )
    $size = $woocommerce_loop['size'];

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 === ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 === $woocommerce_loop['columns'] ) {
    $classes[] = 'first';
	$classes[] = 'grid-sizer';
}
if ( 0 === $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}

if(!class_exists('YITH_WCWL')) {
	$classes[] = 'wishlist-disabled';
}

$classes[] = etheme_get_product_class( $woocommerce_loop['columns'] );

if ( ! empty( $woocommerce_loop['isotope'] ) && $woocommerce_loop['isotope'] || etheme_get_option( 'products_masonry' ) ) {
    $classes[] = 'et-isotope-item';
}

$classes[] = 'product-hover-' . $hover;
$classes[] = 'product-view-' . $view;
$classes[] = 'view-color-' . $view_color;

if( etheme_get_option( 'hide_buttons_mobile' ) ) {
    $classes[] = 'hide-hover-on-mobile';
}


?>

<div <?php post_class( $classes ); ?>>
    <div class="content-product">
        <?php etheme_loader(); ?>
		<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * woocommerce_before_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );
		?>

        <?php if ( $view == 'booking' && etheme_get_option('product_page_productname')): ?>
            <p class="product-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </p>
        <?php endif ?>

		<div class="product-image-wrapper hover-effect-<?php echo esc_attr( $hover ); ?>">

            <?php etheme_product_availability(); ?>

			<a class="product-content-image" href="<?php the_permalink(); ?>" data-images="<?php echo etheme_get_image_list( $size ); ?>">
				<?php if( $hover == 'swap' ) etheme_get_second_image( $size ); ?>
				<?php echo woocommerce_get_product_thumbnail( $size ); ?>
			</a>

            <?php if ($view == 'info' && $view != 'booking'): ?>
                <div class="product-mask">

                    <?php if (etheme_get_option('product_page_productname')): ?>
                        <p class="product-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </p>
                    <?php endif ?>

                    <?php
                        /**
                         * woocommerce_after_shop_loop_item_title hook
                         *
                         * @hooked woocommerce_template_loop_rating - 5
                         * @hooked woocommerce_template_loop_price - 10
                         */
                        if (etheme_get_option('product_page_price')) {
                            do_action( 'woocommerce_after_shop_loop_item_title' );
                        }
                    ?>
                </div>
            <?php endif ?>

            <?php if ( $view == 'booking' ): ?>
                <?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
                <div class="product-excerpt">
                    <?php echo do_shortcode(get_the_excerpt()); ?>
                </div>
                <div class="product-excerpt">
                    <?php $product->list_attributes(); ?>
                </div>
                <?php 
                    if (etheme_get_option('product_page_addtocart') ) {
                        do_action( 'woocommerce_after_shop_loop_item' ); 
                    }
                ?>
            <?php endif ?>
            
            <?php if ($view == 'mask' || $view == 'mask2' || $view == 'default' || $view == 'info'): ?>
    			<footer class="footer-product">
                    <?php if (etheme_get_option('quick_view')): ?>
                        <span class="show-quickly" data-prodid="<?php echo $post->ID;?>"><?php esc_html_e('Quick View', 'xstore') ?></span>
                    <?php endif ?>
                    <?php //if (etheme_get_option('product_page_addtocart')) {
                        do_action( 'woocommerce_after_shop_loop_item' );
                    //}?>
                    <?php echo etheme_wishlist_btn(__('Wishlist', 'xstore')); ?>
    			</footer>
            <?php endif ?>
		</div>

        <?php if ( ($view != 'info' || $view_mode == 'list') && $view != 'booking' ): ?>
    		<div class="text-center product-details">
    	        <?php if (etheme_get_option('product_page_cats')): ?>
                    
                    <?php
                        etheme_product_cats();
                    ?>
    	        <?php endif ?>

    	        <?php if (etheme_get_option('product_page_productname')): ?>
                    <p class="product-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </p>
    	        <?php endif ?>

                <?php
                    /**
                     * woocommerce_after_shop_loop_item_title hook
                     *
                     * @hooked woocommerce_template_loop_rating - 5
                     * @hooked woocommerce_template_loop_price - 10
                     */
                    if (etheme_get_option('product_page_price')) {
                        do_action( 'woocommerce_after_shop_loop_item_title' );
                	}
                ?>

    	        <div class="product-excerpt">
                    <?php echo do_shortcode(get_the_excerpt()); ?>
    	        </div>

                <?php if( $show_counter ) etheme_product_countdown(); ?>

    			<?php
    				if (etheme_get_option('product_page_addtocart') && $view != 'mask') {
    					do_action( 'woocommerce_after_shop_loop_item' );
    				}
    			?>
    		</div>
        <?php endif ?>
	</div><!-- .content-product -->
</div>