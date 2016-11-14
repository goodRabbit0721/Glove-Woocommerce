<?php
/**
 * Single Product title
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if(!etheme_get_option('product_name_signle')) {
    return;
}
?>

<h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
