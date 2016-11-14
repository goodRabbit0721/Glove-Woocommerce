<?php 
	global $layout, $class, $image_class, $infor_class;
 ?>
 single product content booking
<div class="col-md-4 product-images">
	<?php
		/**
		 * woocommerce_before_single_product_summary hook
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>
</div><!-- Product images/ END -->

<div class="col-md-3 product-side-information">
    <div class="product-side-information-inner">
        <div>
            <span class="product-price-title"><?php _e('Product price', 'xstore'); ?></span>
            <?php
                /**
                 * woocommerce_single_product_summary hook
                 *
                 * @hooked woocommerce_template_single_title - 5 
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 */
                do_action( 'woocommerce_single_product_summary' );
            ?>
        </div>
       
        <?php if(etheme_get_option('share_icons')): ?>
            <div class="product-share">
                <?php echo do_shortcode('[share title="'.__('Share', 'xstore').'" text="'.get_the_title().'"]'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="col-md-5 product-information">
    <div class="product-information-inner">
        <h4 class="title"><?php esc_html_e('Product Information', 'xstore'); ?></h4>
        <?php
            woocommerce_template_single_rating();
            woocommerce_template_single_excerpt();
            etheme_additional_information();
        ?>

        <?php if(etheme_get_option('product_posts_links')): ?>
            <?php etheme_project_links(array()); ?>
        <?php endif; ?>
    </div>
</div><!-- Product information/ END -->
