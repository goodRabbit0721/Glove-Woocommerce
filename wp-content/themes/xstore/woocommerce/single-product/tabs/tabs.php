<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) && etheme_get_option('tabs_type') != 'disable' ) : $i=0; ?>

<?php if (etheme_get_option( 'single_layout' ) == 'center' ) : ?>
<div data-vc-full-width="true" data-vc-full-width-init="false" class="vc_row wpb_row tabs-full-width">
<?php endif ?>

	<div class="woocommerce-tabs wc-tabs-wrapper tabs <?php etheme_option('tabs_type'); ?> <?php echo (etheme_get_option('tabs_scroll')) ? 'tabs-with-scroll' : ''; ?>">
        <ul class="wc-tabs tabs-nav">
            <?php foreach ( $tabs as $key => $tab ) : $i++; ?>
                <li>
                    <a href="#tab_<?php echo $key ?>" id="tab_<?php echo $key ?>" class="tab-title <?php if($i == 1) echo 'opened'; ?>"><span><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></span></a>
                </li>
            <?php endforeach; ?>

            
            <?php if (etheme_get_custom_field('custom_tab1_title') && etheme_get_custom_field('custom_tab1_title') != '' ) : ?>
                <li>
                    <a href="#tab_7" id="tab_7" class="tab-title"><span><?php etheme_custom_field('custom_tab1_title'); ?></span></a>
                </li>
            <?php endif; ?>  
        
            <?php if (etheme_get_option('custom_tab_title') && etheme_get_option('custom_tab_title') != '' ) : ?>
                <li>
                    <a href="#tab_9" id="tab_9" class="tab-title"><span><?php etheme_option('custom_tab_title'); ?></span></a>
                </li>                
            <?php endif; ?> 
        </ul>

        <?php $i = 0; foreach ( $tabs as $key => $tab ) : $i++; ?>
            <div class="accordion-title"><a href="#tab_<?php echo $key ?>" id="tab_<?php echo $key ?>" class="tab-title <?php if($i == 1) echo 'opened'; ?>"><span><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></span></a></div>
            <div class="tab-content tab-<?php echo $key ?>" id="content_tab_<?php echo $key ?>" <?php if($i == 1) echo 'style="display:block;"'; ?>>
                <div class="tab-content-inner">
                    <div class="tab-content-scroll">
                        <?php call_user_func( $tab['callback'], $key, $tab ) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
		
        <?php if (etheme_get_custom_field('custom_tab1_title') && etheme_get_custom_field('custom_tab1_title') != '' ) : ?>
            <div class="accordion-title"><a href="#tab_7" id="tab_7" class=" tab-title"><span><?php etheme_custom_field('custom_tab1_title'); ?></span></a></div>
            <div id="content_tab_7" class="tab-content">
            	<div class="tab-content-inner">
                    <div class="tab-content-scroll">
	        		    <?php echo do_shortcode(etheme_get_custom_field('custom_tab1')); ?>
                    </div>
	            </div>
            </div>
        <?php endif; ?>	 
        
        <?php if (etheme_get_option('custom_tab_title') && etheme_get_option('custom_tab_title') != '' ) : ?>
            <div class="accordion-title"><a href="#tab_9" id="tab_9" class="tab-title"><span><?php etheme_option('custom_tab_title'); ?></span></a></div>
            <div id="content_tab_9" class="tab-content">
            	<div class="tab-content-inner">
                    <div class="tab-content-scroll">
                        <?php echo do_shortcode(etheme_get_option('custom_tab')); ?>
                    </div>
	            </div>
            </div>
        <?php endif; ?>	
	</div>
<?php if (etheme_get_option( 'single_layout' ) == 'center' ) : ?>
</div>
<div class="vc_row-full-width vc_clearfix"></div>
<?php endif ?>
<?php endif; ?>