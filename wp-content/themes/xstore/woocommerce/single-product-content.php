<?php 
	global $layout, $class, $image_class, $infor_class, $product;
 ?>
<div class="<?php echo esc_attr( $image_class ); ?> product-images product-scroll-down">
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

<?php 
    if($layout == 'large') {
        ?>
        </div>
        <div class="row">
        <?php
    } 
?>
<div class="<?php echo esc_attr( $infor_class ); ?> product-information">
    <div class="product-information-inner">
        <div class="fixed-content">
            <div class="product-detail-title" style="background-color: <?php the_field('product_title_background_color'); ?>">
                <span class="product-title-style"><?php echo $product->post->post_title; ?></span>
            </div>
        
            <div class="product-title-div">
                <span class="product-title-description"><?php the_field('product_title_description'); ?></span>
            </div>
        
        <?php if (get_field('product_description1_title') != ''): ?>
            <div class="construction">
                <div class="product-detail-page">
                    <label><?php the_field('product_description1_title');?></label>
                </div>
                <?php the_field('product_description1_content');?>
            </div>
        <?php endif ?>
        <?php if (get_field('product_description2_title') != ''): ?>
            <div class="ideal-for">
                <div class="product-detail-page">
                    <label><?php the_field('product_description2_title');?></label>
                </div>
                <?php the_field('product_description2_content');?>
            </div>
        <?php endif ?>
            <div class="safety-info">
                <div class="product-detail-page"><label>Safety info</label></div>
                <?php if (get_field('safety_info') != ''):?>
                    <img class="safety-info" src="<?php the_field('safety_info'); ?>" alt="" />
                <?php endif; ?> 
            </div>
           
            <div class="purchase">
                <div class="product-detail-page"><label>Purchase:</label></div>
                <?php if (get_field('safety_info') != ''):?>
                    <img class="purchase-field-image" src="<?php the_field('purchase'); ?>" alt="" />
                <?php endif; ?> 
            </div>
            <!-- <?php /*if(!etheme_get_option('product_name_signle')):  ?>
                <h4 class="product-title-style"><?php esc_html_e('Product Information', 'xstore'); ?></h4>
             <?php endif; */?>-->
            
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
            ?>
            <div class="single-product-price-cart">
                <?php
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        			do_action( 'woocommerce_single_product_summary' );
                    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
                    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        		?>
            </div>
            <?php /*if(etheme_get_option('share_icons')): ?>
                <div class="product-share">
                    <?php echo do_shortcode('[share title="'.__('Share Social', 'xstore').'" text="'.get_the_title().'"]'); ?>
                </div>
            <?php endif; ?>
            
            <?php if(etheme_get_option('product_posts_links')): ?>
                <?php etheme_project_links(array()); ?>
            <?php endif; */?>
         </div>
    </div>
</div><!-- Product information/ END -->
<?php 
    if($layout == 'large') {
        ?>
            <div class="<?php echo esc_attr( $infor_class ); ?>">
                <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
            </div>
        <?php
    } 
?>
