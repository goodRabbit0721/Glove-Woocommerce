<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$sale_value = etheme_get_option( 'sale_percentage' );

?>
<?php if ( $product->is_on_sale() && ( etheme_get_option( 'sale_icon' ) || $sale_value ) ) : ?>
<div class="sale-wrapper <?php if( $sale_value ) echo 'with-percentage'; ?>">
	<?php if ( etheme_get_option( 'sale_icon' ) ) : ?>

		<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'xstore' ) . '</span>', $post, $product ); ?>

	<?php endif; ?>

	<?php if ( $sale_value && $product->product_type == 'simple' ): ?>
		<?php 
			$regular_price = $product->regular_price;
			$sale_price = $product->sale_price;
			$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
		    echo '<span class="sale-value">-'. $percentage . '%' .'</span>';
	   ?>
	<?php endif ?>
</div>
<?php endif ?>
