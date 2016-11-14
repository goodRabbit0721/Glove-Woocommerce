<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product_cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop;
$styles_default = array(
	'style' => etheme_get_option('cat_style'),
	'text_color' => etheme_get_option('cat_text_color'),
	'valign' => etheme_get_option('cat_valign'),
);

if ( ! empty( $styles ) ) {
	$styles_default = wp_parse_args( $styles, $styles_default  );
}

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

if ( empty( $woocommerce_loop['categories_columns'] ) )
	$woocommerce_loop['categories_columns'] = etheme_get_option('prodcuts_per_row');

$classes = array();

$classes[] = 'category-grid';

if( !empty($woocommerce_loop['display_type']) && $woocommerce_loop['display_type'] == 'slider' ) {
	$classes[] = 'slide-item';
} else {
	$col_sm = 12 / $woocommerce_loop['categories_columns'];
	$classes[] = 'col-xs-12 col-sm-' . $col_sm . ' columns-' . $woocommerce_loop['categories_columns'];
}

$classes[] = 'text-color-' . $styles_default['text_color'];
$classes[] = 'valign-' . $styles_default['valign'];
$classes[] = 'style-' . $styles_default['style'];

// Increase loop count
$woocommerce_loop['loop'] ++;
?>
<div <?php wc_product_cat_class( $classes, $category ); ?>>
	<?php
	/**
	 * woocommerce_before_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_open - 10
	 */
	do_action( 'woocommerce_before_subcategory', $category );


	/**
	 * woocommerce_before_subcategory_title hook
	 *
	 * @hooked woocommerce_subcategory_thumbnail - 10
	 */
	do_action( 'woocommerce_before_subcategory_title', $category );
	?>
	
	<div class="categories-mask">
		<span><?php esc_html_e('Category', 'xstore'); ?></span>
		<h4><?php echo $category->name; ?></h4>
		<?php
			if ( $category->count > 0 )
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">' . sprintf( _n( '%s product', '%s products', $category->count, 'xstore' ), $category->count ). '</mark>', $category );
		?>
		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			
			do_action( 'woocommerce_after_subcategory_title', $category );
		?>
		<span class="more"><?php esc_html_e('Read More', 'xstore'); ?></span>
	</div>

	<?php
	/**
	 * woocommerce_after_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_close - 10
	 */
	do_action( 'woocommerce_after_subcategory', $category ); ?>
</div>