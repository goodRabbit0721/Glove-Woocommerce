<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $woocommerce_loop;
// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', etheme_get_option('prodcuts_per_row') );

    $view_mode = etheme_get_view_mode();
    if( !empty($woocommerce_loop['view_mode'])) {
        $view_mode = $woocommerce_loop['view_mode'];
    } else {
        $woocommerce_loop['view_mode'] = $view_mode;
    }
    
    if($view_mode == 'list') {
        $view_class = 'products-list';
        if( $woocommerce_loop['columns'] > 3 ) $woocommerce_loop['columns'] = 3;
    }else{
        $view_class = 'products-grid';
    }
        
    if ( ! empty( $woocommerce_loop['isotope'] ) && $woocommerce_loop['isotope'] || etheme_get_option( 'products_masonry' ) ) {
        $view_class .= ' et-isotope';
    }
?>
<div class="row products-loop <?php echo esc_attr( $view_class ); ?> row-count-<?php echo esc_attr( $woocommerce_loop['columns'] ); ?>">