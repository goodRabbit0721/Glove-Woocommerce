<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

$empty_cart_content = etheme_get_option('empty_cart_content');

?>

<?php do_action('woocommerce_cart_is_empty'); ?>

<div class="cart-empty empty-cart-block">
	<i class="icon-shopping-cart"></i>

	<?php if( empty( $empty_cart_content ) ): ?>
		<h1 style="text-align: center;"><?php _e('YOUR SHOPPING CART IS EMPTY', 'xstore') ?></h1>
		<p style="text-align: center;"><?php _e('We invite you to get acquainted with an assortment of our shop. Surely you can find something for yourself!', 'xstore') ?></p>
	<?php else: ?>
		<?php echo do_shortcode( $empty_cart_content ); ?>
	<?php endif; ?>
	<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<p><a class="btn" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><span><?php esc_html_e('Return To Shop', 'xstore') ?></span></a></p>
	<?php endif; ?>

</div>