<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $layout, $class, $image_class, $infor_class;

$l = etheme_page_config();

$layout = $l['product_layout'];

list($class, $image_class, $infor_class) = etheme_get_single_product_class($layout);

/**
 * woocommerce_before_single_product hook
 *
 * @hooked wc_print_notices - 10
 */
 do_action( 'woocommerce_before_single_product' );

 if ( post_password_required() ) {
 	echo get_the_password_form();
 	return;
 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class($class); ?>>

    <div class="row">
        <div class="<?php echo esc_attr( $l['content-class'] ); ?> product-content sidebar-position-<?php echo esc_attr( $l['sidebar'] ); ?>">
            <div class="row">
                
                <?php wc_get_template_part( 'single-product-content', $layout ); ?>
            </div>
            
        </div> <!-- CONTENT/ END -->

		<?php if($l['sidebar'] != '' && $l['sidebar'] != 'without' && $l['sidebar'] != 'no_sidebar'): ?>
            <div class="<?php echo esc_attr( $l['sidebar-class'] ); ?> single-product-sidebar sidebar-<?php echo esc_attr( $l['sidebar'] ); ?>">
				<?php etheme_product_brand_image(); ?>
				<?php if(etheme_get_option('upsell_location') == 'sidebar') woocommerce_upsell_display(); ?>
				<?php dynamic_sidebar('single-sidebar'); ?>
			</div>
		<?php endif; ?>
    </div>
    <?php
        /**
         * woocommerce_after_single_product_summary hook
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_output_related_products - 20 [REMOVED in woo.php]
         */
         if(etheme_get_option('tabs_location') == 'after_content' && $layout != 'large') {
             //do_action( 'woocommerce_after_single_product_summary' );
         }
    ?>
    
    <div class="row product-profile-description">
        <div class="container">
            <div class="col-md-12 seamless-proprietary">
                <div class="col-md-6">
                    <label>SEAMLESS DEXTERITY COMBINED WITH INJECTION MOLDED IMPACT PROTECTION AND MULTIPLE LEVELS OF PROTECTION</label>
                    <div> <?php the_field('left_short_detail');?></div>
                </div>
                <div class="col-md-6">
                    <label>OUR PROPRIETARY INJECTION MOLDING PROCESS BRINGS TO LIFE A GLOVE WITH GREAT DEXTERITY, HEAVY IMPACT PROTECTION, AND REINFORCEMENT IN THE FINGERTIPS</label>
                    <div><?php the_field('right_short_detail');?></div>
                </div>
            </div>
            
            <div class="col-md-12 retangle-detail">
                <?php if (get_field('box1_title') !='') :?>
                    <div class="retangle col-md-4">
                        <div>
                           <div><label><?php the_field('box1_title');?></label></div>
                            <div> <?php the_field('box1-content');?></div> 
                        </div>
                    </div>
                <?php endif ?> 
                <?php if (get_field('box2_title') !='') : ?>
                    <div class="retangle col-md-4">
                        <div>
                            <div><label><?php the_field('box2_title');?></label></div>
                            <div> <?php the_field('box2-content');?></div> 
                        </div>
                    </div>
                <?php endif ?>
                <?php if (get_field('box3-title') !='') : ?>
                    <div class="retangle col-md-4">
                        <div>
                            <div><label><?php the_field('box3-title');?></label></div>
                            <div> <?php the_field('box3-content');?></div> 
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <div class="col-md-12 retangle-detail">
                <?php if (get_field('box4_title') !='') : ?>
                    <div class="retangle col-md-4">
                        <div>
                           <div><label><?php the_field('box4_title');?></label></div>
                            <div> <?php the_field('box4-content');?></div> 
                        </div>
                    </div>
                <?php endif ?> 
                <?php if (get_field('box5_title') !='') : ?>
                    <div class="retangle col-md-4">
                        <div>
                            <div><label><?php the_field('box5_title');?></label></div>
                            <div> <?php the_field('box5-content');?></div> 
                        </div>
                    </div>
                <?php endif ?>
                <?php if (get_field('box6_title') !='') : ?>
                    <div class="retangle col-md-4">
                        <div>
                            <div><label><?php the_field('box6_title');?></label></div>
                            <div> <?php the_field('box6-content');?></div> 
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    
    <div class="row image-couple-glove">
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?php if (get_field('left-glove-image') != ''):?>
                <img class="left-glove" src="<?php the_field('left-glove-image'); ?>" alt="" />
            <?php endif; ?> 
        </div>
        <div class="col-md-5">
             <?php if (get_field('right_glove_image') != ''):?>
                <img class="right-glove" src="<?php the_field('right_glove_image'); ?>" alt="" />
            <?php endif; ?> 
        </div>
        <div class="col-md-1"></div>
    </div>
    
    <?php if(etheme_get_option('upsell_location') == 'after_content') woocommerce_upsell_display(); ?>

    <?php
		if(etheme_get_custom_field('additional_block') != '') {
			echo '<div class="product-extra-content">';
				etheme_show_block(etheme_get_custom_field('additional_block'));
			echo '</div>';
		}     
    ?>

    <?php if(etheme_get_option('show_related')) woocommerce_output_related_products(); ?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

