<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Set Content Width
// **********************************************************************//  
if (!isset( $content_width )) $content_width = 1170;

// **********************************************************************// 
// ! Include CSS and JS
// **********************************************************************// 
if(!function_exists('etheme_enqueue_scripts')) {
    function etheme_enqueue_scripts() {
        if ( !is_admin() ) {

            $script_depends = array();

            if(class_exists('WooCommerce')) {
                $script_depends = array('wc-add-to-cart-variation');
            }

            if ( is_singular() && get_option( 'thread_comments' ) )
                wp_enqueue_script( 'comment-reply' );

            wp_enqueue_script('jquery');
            wp_enqueue_script('head', get_template_directory_uri().'/js/head.min.js');
            wp_enqueue_script('plugins', get_template_directory_uri().'/js/plugins.min.js',array(),false,true);
            wp_enqueue_script('hoverIntent', get_template_directory_uri().'/js/jquery.hoverIntent.js',array(),false,true);

            wp_enqueue_script('etheme', get_template_directory_uri().'/js/etheme.min.js',$script_depends,false,true);
            wp_enqueue_script('product_detail', get_template_directory_uri().'/js/product_detail.js');

            $etConf = array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'noresults' => esc_html__('No results were found!', 'xstore'),
                'successfullyAdded' => esc_html__('Product added.', 'xstore'),
                'checkCart' => esc_html__('Please check your cart.', 'xstore'),
                'catsAccordion' => etheme_get_option('cats_accordion'),
                'contBtn' => esc_html__('Continue shopping', 'xstore'),
                'checkBtn' => esc_html__('Checkout', 'xstore'),
                'menuBack' => esc_html__('Back', 'xstore'),
                'woocommerce' => (class_exists('Woocommerce') && current_theme_supports('woocommerce')),
            );

            if (class_exists('WooCommerce')) {
                $etConf['checkoutUrl'] = esc_url( WC()->cart->get_checkout_url() );
            }

            wp_localize_script( 'etheme', 'etConfig', $etConf);
            wp_dequeue_script('prettyPhoto');
            wp_dequeue_script('prettyPhoto-init');
        }
    }
}

add_action( 'wp_enqueue_scripts', 'etheme_enqueue_scripts', 30);

// **********************************************************************// 
// ! Add new images size
// **********************************************************************// 

if(!function_exists('etheme_image_sizes')) {
    function etheme_image_sizes() {
        add_image_size( 'shop_catalog_alt', 600, 600, true );
    }
    add_action( 'after_setup_theme', 'etheme_image_sizes');
}

// **********************************************************************// 
// ! Screet chat fix
// **********************************************************************// 

define('SC_CHAT_LICENSE_KEY', '69e13e4c-3dfd-4a70-83c8-3753507f5ae8');
if(!function_exists('etheme_chat_init')) {
    function etheme_chat_init () {
        update_option('sc_chat_validate_license', 1);
    }
}

add_action( 'after_setup_theme', 'etheme_chat_init');


// **********************************************************************// 
// ! Theme 3d plugins
// **********************************************************************// 
add_action( 'init', 'etheme_3d_plugins' );
if(!function_exists('etheme_3d_plugins')) {
    function etheme_3d_plugins() {
        if(function_exists( 'set_revslider_as_theme' )){
            set_revslider_as_theme();
        }
        if(function_exists( 'set_ess_grid_as_theme' )){
            set_ess_grid_as_theme();
        }
    }
}

if(!function_exists('etheme_vcSetAsTheme')) {
    add_action( 'vc_before_init', 'etheme_vcSetAsTheme' );
    function etheme_vcSetAsTheme() {
        if(function_exists( 'vc_set_as_theme' )){
            vc_set_as_theme();
        }
    }
}

if(!defined('YITH_REFER_ID')) {
    define('YITH_REFER_ID', '1028760');
}

define('BSF_6892199_NOTICES', false);

// **********************************************************************// 
// ! Ititialize theme css configuration
// **********************************************************************// 
add_action('wp_head', 'etheme_init');
if(!function_exists('etheme_init')) {
    function etheme_init() {
        global $post;
        ?>
        <style type="text/css">
            <?php
                $bread_bg = etheme_get_option('breadcrumb_bg');
                $post_id = etheme_get_page_id();

                if( is_singular('page') && has_post_thumbnail($post_id['id']) ) {
                    $bread_bg['background-image'] = wp_get_attachment_url( get_post_thumbnail_id($post_id['id']), 'large');
                }
            ?>

            .page-heading {
            <?php if(!empty($bread_bg['background-color'])): ?>  background-color: <?php echo $bread_bg['background-color']; ?>;<?php endif; ?>
            <?php if(!empty($bread_bg['background-image'])): ?>  background-image: url(<?php echo $bread_bg['background-image']; ?>) ; <?php endif; ?>
            <?php if(!empty($bread_bg['background-attachment'])): ?>  background-attachment: <?php echo $bread_bg['background-attachment']; ?>;<?php endif; ?>
            <?php if(!empty($bread_bg['background-size'])): ?>  background-size: <?php echo $bread_bg['background-size']; ?>;<?php endif; ?>
            <?php if(!empty($bread_bg['background-repeat'])): ?>  background-repeat: <?php echo $bread_bg['background-repeat']; ?>;<?php  endif; ?>
            <?php if(!empty($bread_bg['background-position'])): ?>  background-position: <?php echo $bread_bg['background-position']; ?>;<?php endif; ?>
            }

            <?php
                $custom_css = etheme_get_option('custom_css');
                $custom_css_desktop = etheme_get_option('custom_css_desktop');
                $custom_css_tablet = etheme_get_option('custom_css_tablet');
                $custom_css_wide_mobile = etheme_get_option('custom_css_wide_mobile');
                $custom_css_mobile = etheme_get_option('custom_css_mobile');
                if($custom_css != '') {
                    echo $custom_css;
                }
                if($custom_css_desktop != '') {
                    echo '@media (min-width: 992px) { ' . $custom_css_desktop . ' }';
                }
                if($custom_css_tablet != '') {
                    echo '@media (min-width: 768px) and (max-width: 991px) {' . $custom_css_tablet . ' }';
                }
                if($custom_css_wide_mobile != '') {
                    echo '@media (min-width: 481px) and (max-width: 767px) { ' . $custom_css_wide_mobile . ' }';
                }
                if($custom_css_mobile != '') {
                    echo '@media (max-width: 480px) { ' . $custom_css_mobile . ' }';
                }
             ?>

            <?php
               $background_img = etheme_get_option('background_img');
             ?>

            .bordered .body-border-left,
            .bordered .body-border-top,
            .bordered .body-border-right,
            .bordered .body-border-bottom {
            <?php if(!empty($background_img['background-color'])): ?>  background-color: <?php echo $background_img['background-color']; ?>;<?php endif; ?>
            }

            <?php
               $logo_width = etheme_get_option('logo_width');
             ?>

            .header-logo img {
                max-width: <?php echo esc_html( $logo_width ); ?>px;
            }

            <?php
               $tab_height = etheme_get_option('tab_height');
             ?>

            <?php if( etheme_get_option('tabs_scroll') ) : ?>
            .tabs.accordion .tab-content .tab-content-inner {
                height: <?php echo esc_html( $tab_height ); ?>px!important;
            }
            <?php endif; ?>
        </style>
        <?php
    }
}


if(!function_exists('etheme_load_textdomain')) {

    add_action('after_setup_theme', 'etheme_load_textdomain');

    function etheme_load_textdomain(){
        /**
         * Load theme translations
         */
        load_theme_textdomain( 'xstore', get_template_directory() . '/languages' );

        $locale = get_locale();
        $locale_file = get_template_directory() . "/languages/$locale.php";
        if ( is_readable( $locale_file ) )
            require_once( $locale_file );
    }
}
