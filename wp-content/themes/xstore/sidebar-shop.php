<?php

$l = etheme_page_config();

if(!$l['sidebar'] || $l['sidebar'] == 'without' || $l['sidebar'] == 'no_sidebar') return;

?>

<div class="<?php echo esc_attr( $l['sidebar-class'] ); ?> sidebar sidebar-<?php echo esc_attr( $l['sidebar'] ); ?> <?php echo (etheme_get_option('shop_sidebar_hide_mobile')) ? 'hidden-xs' : '' ; ?>">
    <?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('shop-sidebar')):

        if(!class_exists('WooCommerce')) return;
        // **********************************************************************//
        // ! Categories
        // **********************************************************************//
        $args = array(
            'widget_id' => 'woocommerce_product_categories',
            'before_widget' => '<div id="product_categories-1" class="sidebar-widget widget_product_categories widget_onsale">',
            'after_widget' => '</div><!-- //sidebar-widget -->',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>'
        );

        $instance = array(
            'title' => 'Categories',
            'hierarchical' => true,
            'count' => false,
            'dropdown' => false,
            'orderby' => ''
        );

        $widget = new WC_Widget_Product_Categories();
        $widget->widget($args, $instance);

    endif; ?>
</div>