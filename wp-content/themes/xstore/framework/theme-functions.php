<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Add classes to body
// **********************************************************************//

add_filter('body_class', 'etheme_add_body_classes');
if(!function_exists('etheme_add_body_classes')) {
    function etheme_add_body_classes($classes) {
        $post_template  = etheme_get_post_template();
        $l = etheme_page_config();

        $post_id = etheme_get_page_id();

        $id = $post_id['id'];

        if(etheme_get_option('fixed_nav') != 'disable') $classes[] = 'fixed-' . etheme_get_option('fixed_nav');
        if(etheme_get_option('promo_auto_open')) $classes[] = 'open-popup ';
        if(etheme_get_option('promo_open_scroll')) $classes[] = 'scroll-popup ';
        $classes[] = 'breadcrumbs-type-'.$l['breadcrumb'];
        $classes[] = etheme_get_option('main_layout');
        $classes[] = (etheme_get_option('cart_widget')) ? 'cart-widget-on' : 'cart-widget-off';
        $classes[] = (etheme_get_option('search_form')) ? 'search-widget-on' : 'search-widget-off';
        $classes[] = (etheme_get_option('header_full_width')) ? 'et-header-full-width' : 'et-header-boxed';
        $classes[] = (etheme_get_option('header_overlap') || etheme_get_custom_field('header_overlap', $id)) ? 'et-header-overlap' : 'et-header-not-overlap';
        $classes[] = (etheme_get_option('fixed_header')) ? 'et-header-fixed' : 'et-fixed-disable';
        $classes[] = (etheme_get_option('top_panel')) ? 'et-toppanel-on' : 'et-toppanel-off';
        $classes[] = (etheme_get_option('site_preloader')) ? 'et-preloader-on' : 'et-preloader-off';
        $classes[] = (etheme_get_option('just_catalog')) ? 'et-catalog-on' : 'et-catalog-off';
        $classes[] = (etheme_get_option('footer_fixed')) ? 'et-footer-fixed-on' : 'et-footer-fixed  -off';

        $classes[] = 'global-post-template-' . $post_template;

        $ht = etheme_get_header_type();

        $classes[] = "global-header-" . $ht;

        $header_bg = etheme_get_option('header_bg_color');

        if( !empty($header_bg['background-color']) && $header_bg['background-color'] == 'transparent' ) {
            $classes[] = "body-header-transparent";
        }

        if( etheme_iphone_detect() ) $classes[] = 'iphone-browser';

        return $classes;
    }
}

if( ! function_exists('etheme_iphone_detect')) {
    function etheme_iphone_detect($user_agent=NULL) {
        if(!isset($user_agent)) {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        return (strstr($user_agent, " AppleWebKit/") && strstr($user_agent, " Safari/") && !strstr($user_agent, " CriOS"));//(strpos($user_agent, 'iPhone') !== FALSE);
    } 
}

if(!function_exists('etheme_bordered_layout')) {
    function etheme_bordered_layout() {

        if(etheme_get_option('main_layout') != 'bordered') return;

        ?>
            <div class="body-border-left"></div>
            <div class="body-border-top"></div>
            <div class="body-border-right"></div>
            <div class="body-border-bottom"></div> 
        <?php
    }
    add_action('et_after_body', 'etheme_bordered_layout');
}

if(!function_exists('etheme_page_background')) {
    function etheme_page_background() {

        $post_id = etheme_get_page_id();

        $bg_image = etheme_get_custom_field('bg_image', $post_id['id']);
        $bg_color = etheme_get_custom_field('bg_color', $post_id['id']);

        if( ! empty( $bg_image ) || ! empty( $bg_color ) ) {
            ?>
                <style type="text/css">
                    body {
                        <?php if( ! empty( $bg_color ) ): ?>
                            background-color: <?php echo $bg_color; ?>!important;
                        <?php endif; ?>
                        <?php if( ! empty( $bg_image ) ): ?>
                            background-image: url(<?php echo $bg_image; ?>)!important;
                        <?php endif; ?>
                    }
                </style>
            <?php
        }
    }
    add_action('wp_head', 'etheme_page_background');
}


if( ! function_exists('etheme_woocommerce_installed') ) {
    function etheme_woocommerce_installed() {
        return class_exists('WooCommerce');
    }
}


// **********************************************************************// 
// ! Heade color
// **********************************************************************// 

if( ! function_exists('etheme_get_header_color') ) {
    function etheme_get_header_color() {
        global $post;
        $color = etheme_get_option('header_color');

        $post_id = etheme_get_page_id();

        $id = $post_id['id'];

        $custom = etheme_get_custom_field('header_color', $id);

        if( ! empty( $custom ) && $custom != 'inherit' ) {
            $color = $custom;
        }

        return $color;
    }
}


// **********************************************************************// 
// ! Wp title
// **********************************************************************// 
if(!function_exists('etheme_wp_title')) {
    function etheme_wp_title($title, $sep ) {
        global $paged, $page;

        if ( is_feed() ) {
            return $title;
        }

        // Add the site name.
        $title .= get_bloginfo( 'name', 'display' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description', 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) ) {
            $title = "$title $sep $site_description";
        }

        // Add a page number if necessary.
        if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
            $title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'xstore' ), max( $paged, $page ) );
        }

        return $title;
    }
    add_filter( 'wp_title', 'etheme_wp_title', 10, 2 );
}

if(!function_exists('etheme_get_the_title')) {
    function etheme_get_the_title() {

        $post_page = get_option( 'page_for_posts' );

        if( is_404() ) {
            return esc_html__('Page not found', 'xstore');
        }

        if(is_home()) {
            if( empty( $post_page) && ! is_single() && ! is_page() ) {
                return esc_html__('Blog', 'xstore');
            }
            return get_the_title( $post_page );
        }

        // Homepage and Single Page
        if ( is_home() || is_single() || is_404() ) {
            return get_the_title();
        }

        // Search Page
        if ( is_search() ) {
            return sprintf( esc_html__( 'Search Results for: %s', 'xstore' ), get_search_query() );
        }

        // Archive Pages
        if ( is_archive() ) {
            if ( is_author() ) {
                return sprintf( esc_html__( 'All posts by %s', 'xstore' ), get_the_author() );
            }
            elseif ( is_day() ) {
                return sprintf( esc_html__( 'Daily Archives: %s', 'xstore' ), get_the_date() );
            }
            elseif ( is_month() ) {
                return sprintf( esc_html__( 'Monthly Archives: %s', 'xstore'), get_the_date( _x( 'F Y', 'monthly archives date format', 'xstore' ) ) );
            }
            elseif ( is_year() ) {
                return sprintf( esc_html__( 'Yearly Archives: %s', 'xstore' ), get_the_date( _x( 'Y', 'yearly archives date format', 'xstore' ) ) );
            }
            elseif ( is_tag() ) {
                return sprintf( esc_html__( 'Tag Archives: %s', 'xstore' ), single_tag_title( '', false ) );
            }
            elseif ( is_category() ) {
                return sprintf( esc_html__( 'Category Archives: %s', 'xstore' ), single_cat_title( '', false ) );
            }
            elseif ( is_tax( 'post_format', 'post-format-aside' ) ) {
                return esc_html__( 'Asides', 'xstore' );
            }
            elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
                return esc_html__( 'Videos', 'xstore' );
            }
            elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
                return esc_html__( 'Audio', 'xstore' );
            }
            elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
                return esc_html__( 'Quotes', 'xstore' );
            }
            elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
                return esc_html__( 'Galleries', 'xstore' );
            }
            elseif ( is_tax( 'portfolio_category' ) ) {
                return single_term_title();
            }
            elseif( function_exists('is_bbpress') && is_bbpress() ){
                return esc_html__('Forums', 'xstore');
            }
            else {
                return esc_html__( 'Archives', 'xstore' );
            }
        }

        return get_the_title();
    }
}



// **********************************************************************// 
// ! Header Type
// **********************************************************************// 
if(!function_exists('etheme_get_header_type')) {
    function etheme_get_header_type() {
        $ht = etheme_get_option('header_type');
        apply_filters('custom_header_filter', $ht );
        return $ht;
    }

}

// **********************************************************************// 
// ! Get logo
// **********************************************************************// 

if(!function_exists('etheme_get_logo_data')) {
    function etheme_get_logo_data() {
        $return = array(
            'logo' => array(),
            'fixed_logo' => array()
        );

        $logo_fixed = etheme_get_option('logo_fixed');
        if(!is_array($logo_fixed)) {
            $logo_fixed = array('url' => $logo_fixed);
        }

        $logoimg = etheme_get_option('logo');


        if(empty($logo_fixed['url'])) {
            $logo_fixed = $logoimg;
        }

        $page = etheme_get_page_id();

        $custom_logo = etheme_get_custom_field('custom_logo', $page['id'] );

        if($custom_logo != '') {
            $logoimg['url'] = $custom_logo;
        }

        $return['logo']['src'] = (!empty($logoimg['url'])) ? $logoimg['url'] : ETHEME_BASE_URI.'theme/assets/images/logo.png';
        $return['fixed_logo']['src'] = (!empty($logo_fixed['url'])) ? $logo_fixed['url'] : ETHEME_BASE_URI.'theme/assets/images/logo-fixed.png';

        $return['logo']['width'] = (!empty($logoimg['width'])) ? $logoimg['width'] : 259;
        $return['logo']['height'] = (!empty($logoimg['height'])) ? $logoimg['height'] : 45;
        $return['fixed_logo']['width'] = (!empty($logo_fixed['width'])) ? $logo_fixed['width'] : 259;
        $return['fixed_logo']['height'] = (!empty($logo_fixed['height'])) ? $logo_fixed['height'] : 45;

        return $return;
    }
}

// **********************************************************************// 
// ! Get top links
// **********************************************************************// 

if(!function_exists('etheme_get_links')) {
    function etheme_get_links($args) {
        extract(shortcode_atts(array(
            'short'  => false,
            'popups'  => true,
        ), $args));
        $links = array();

        $reg_id = etheme_tpl2id('et-registration.php');

        $login_link = wp_login_url( get_permalink() );

        if( class_exists('WooCommerce')) {
            $login_link = get_permalink( get_option('woocommerce_myaccount_page_id') );
        }

        if(etheme_get_option('promo_popup')) {
            $links['popup'] = array(
                'class' => 'popup_link',
                'link_class' => 'etheme-popup',
                'href' => '#etheme-popup',
                'title' => etheme_get_option('promo-link-text'),
            );
            if(!etheme_get_option('promo_link')) {
                $links['popup']['class'] .= ' hidden';
            }
            if(etheme_get_option('promo_auto_open')) {
                $links['popup']['link_class'] .= ' open-click';
            }
        }

        if( etheme_get_option('top_links') ) {
            if ( is_user_logged_in() ) {
                if( class_exists('WooCommerce')) {
                    if ( has_nav_menu( 'my-account' ) ) { 
                        $submenu = wp_nav_menu(array(
                            'theme_location' => 'my-account',
                            'before' => '',
                            'container_class' => 'menu-main-container',
                            'after' => '',
                            'link_before' => '',
                            'link_after' => '',
                            'depth' => 100,
                            'fallback_cb' => false,
                            'walker' => new ETheme_Navigation,
                            'echo' => false
                        ));
                    } else {
                        $submenu = '<ul>';
                        $permalink = wc_get_page_permalink( 'myaccount' );

                        foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
                            $url = ( $endpoint != 'dashboard' ) ? wc_get_endpoint_url( $endpoint, '', $permalink ) : $permalink ;
                            $submenu .= '<li class="' . wc_get_account_menu_item_classes( $endpoint ) . '"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
                        }

                        $submenu .= '</ul>';
                    }

                    $links['my-account'] = array(
                        'class' => 'my-account-link',
                        'link_class' => '',
                        'href' => get_permalink( get_option('woocommerce_myaccount_page_id') ),
                        'title' => esc_html__( 'My Account', 'xstore' ),
                        'submenu' => $submenu
                    );

                }
                // $links['logout'] = array(
                //     'class' => 'logout-link',
                //     'link_class' => '',
                //     'href' => wp_logout_url(home_url()),
                //     'title' => esc_html__( 'Logout', 'xstore' )
                // );
            } else {

                $login_text = ($short) ? esc_html__( 'Sign In', 'xstore' ): esc_html__( 'Sign In or Create an account', 'xstore' );

                $links['login'] = array(
                    'class' => 'login-link',
                    'link_class' => '',
                    'href' => $login_link,
                    'title' => $login_text
                );

                if(!empty($reg_id)) {
                    $links['register'] = array(
                        'class' => 'register-link',
                        'link_class' => '',
                        'href' => get_permalink($reg_id),
                        'title' => esc_html__( 'Register', 'xstore' )
                    );
                }

            }
        }

        return apply_filters('etheme_get_links', $links);
    }
}

// **********************************************************************// 
// ! Get gallery from content
// **********************************************************************//
if(!function_exists('etheme_gallery_from_content')) {
    function etheme_gallery_from_content($content) {

        $result = array(
            'ids' => array(),
            'filtered_content' => ''
        );

        preg_match('/\[gallery.*ids=.(.*).\]/', $content, $ids);
        if(!empty($ids)) {
            $result['ids'] = explode(",", $ids[1]);
            $content =  str_replace($ids[0], "", $content);
            $result['filtered_content'] = apply_filters( 'the_content', $content);
        }

        return $result;

    }
}

// **********************************************************************// 
// ! Get post classes
// **********************************************************************//
if(!function_exists('etheme_post_class')) {
    function etheme_post_class($cols = false, $layout = false ) {
        global $et_loop;

        $classes = array();

        if($cols) {
            $classes[] = 'post-grid';
            $classes[] = 'isotope-item';
            $classes[] = 'col-md-' . $cols;
        } else {
            $classes[] = 'blog-post';
        }

        if(etheme_get_option('blog_byline')) {
            $classes[] = ' byline-on';
        } else {
            $classes[] = ' byline-off';
        }

        if( ! $layout ) {
            $classes[] = ' content-'.etheme_get_option('blog_layout');
        } else {
            $classes[] = ' content-'.$layout;
        }

        if( ! empty( $et_loop['slide_view'] ) ) {
            $classes[] = 'slide-view-' . $et_loop['slide_view'];
        }

        if( ! empty( $et_loop['blog_align'] ) ) {
            $classes[] = ' blog-align-' . $et_loop['blog_align'];
        }

        return $classes;
    }
}


// **********************************************************************// 
// ! Get post template
// **********************************************************************//
if(!function_exists('etheme_get_post_template')) {
    function etheme_get_post_template() {
        $template = etheme_get_option('post_template');

        $custom = etheme_get_custom_field('post_template');

        if( ! empty( $custom ) ) {
            $template = $custom;
        }

        return $template;
    }
}


// **********************************************************************// 
// ! Get grid cols
// **********************************************************************//
if(!function_exists('etheme_get_cols')) {
    function etheme_get_cols($columns ) {

        if( $columns < 1 ) {
            $columns = 1;
        }

        $cols = 12/$columns;

        return $cols;
    }
}

// **********************************************************************// 
// ! Get read more button text
// **********************************************************************//
if(!function_exists('etheme_get_read_more')) {
    function etheme_get_read_more() {
        return '<span class="read-more">'.__('Continue reading', 'xstore').'</span>';
    }
}


// **********************************************************************// 
// ! Init owl carousel gallery
// **********************************************************************//
if(!function_exists('etheme_owl_init')) {
    function etheme_owl_init($el, $atts = array() ) {
        extract( shortcode_atts( array(
            'singleItem' => 'true',
            'itemsCustom' => '[1600, 1]',
            'has_nav' => false,
            'nav_for' => false,
            'echo' => true
        ), $atts ));
        if( ! $echo ) ob_start();
        ?>
            jQuery('<?php echo $el; ?>').owlCarousel({
                items:1,
                navigation: true,
                lazyLoad: false,
                rewindNav: false,
                addClassActive: true,
                singleItem : <?php echo $singleItem; ?>,
                autoHeight : true,
                itemsCustom: <?php echo $itemsCustom; ?>,
                <?php if ($has_nav): ?>
                    afterMove: function(args) {
                        var owlMain = jQuery("<?php echo $el; ?>").data('owlCarousel');
                        var owlThumbs = jQuery("<?php echo $has_nav; ?>").data('owlCarousel');

                        jQuery('.active-thumbnail').removeClass('active-thumbnail')
                        jQuery("<?php echo $has_nav; ?>").find('.owl-item').eq(owlMain.currentItem).addClass('active-thumbnail');
                        if(typeof owlThumbs != 'undefined') {
                            owlThumbs.goTo(owlMain.currentItem-1);
                        }
                    }
                <?php endif ?>
            });
        <?php

        if ( $nav_for ) {
            ?>
                jQuery('<?php echo $el; ?> .owl-item').click(function(e) {
                    var owlMain = jQuery("<?php echo $nav_for; ?>").data('owlCarousel');
                    var owlThumbs = jQuery("<?php echo $el; ?>").data('owlCarousel');
                    owlMain.goTo(jQuery(e.currentTarget).index());
                });
            <?php
        }

        if( ! $echo ) return ob_get_clean();
    }
}


// **********************************************************************// 
// ! Views coutner
// **********************************************************************//

if(!function_exists('etheme_get_views')) {
    function etheme_get_views($id = false) {
        if( ! $id ) {
            $id = get_the_ID();
        }
        $number = get_post_meta( $id, '_et_views_count', true );
        if( empty($number) ) $number = 0;
        return $number;
    }
}

add_action( 'wp', 'etheme_update_views');

if(!function_exists('etheme_update_views')) {
    function etheme_update_views() {
        if( ! is_single() || ! is_singular( 'post' ) ) return;

        $id = get_the_ID();

        $number = etheme_get_views( $id );
        if( empty($number) ) {
            $number = 1;
            add_post_meta( $id, '_et_views_count', $number );
        } else {
            $number++;
            update_post_meta( $id, '_et_views_count', $number );
        }
    }
}


if(!function_exists('etheme_has_post_audio')) {
    function etheme_has_post_audio() {
        $post_audio = etheme_get_custom_field('post_audio');
        if( ! empty( $post_audio ) ) {
            return true;
        }
        return false;
    }
}

if(!function_exists('etheme_the_post_audio')) {
    function etheme_the_post_audio() {
        $audio = etheme_get_custom_field('post_audio');

        if(!empty($audio)) {
            echo do_shortcode( $audio );
        }

    }
}

if(!function_exists('etheme_the_post_quote')) {
    function etheme_the_post_quote($id = false ) {
        if( ! $id ) $id = get_the_ID();
        $quote = etheme_get_custom_field('post_quote', $id);

        if(!empty($quote)) {
            echo do_shortcode( $quote );
        }

    }
}

if(!function_exists('etheme_has_post_video')) {
    function etheme_has_post_video() {
        $post_video = etheme_get_custom_field('post_video');
        if( ! empty( $post_video ) ) {
            return true;
        }
        return false;
    }
}

if(!function_exists('etheme_the_post_video')) {
    function etheme_the_post_video() {
        $url = etheme_get_custom_field('post_video');

        $embed =  VideoUrlParser::get_url_embed($url);
        if(!empty($embed)) {
            ?>
                <iframe width="100%" height="560" src="<?php echo $embed; ?>" frameborder="0" allowfullscreen></iframe>
            <?php
        }

    }
}

if(!function_exists('etheme_get_primary_category')) {
    function etheme_get_primary_category() {
        $primary = false;
        $cat = etheme_get_custom_field('primary_category');
        if(!empty($cat) && $cat != 'auto') {
            $primary = get_term_by( 'slug', $cat, 'category' );
        } else {
            $cats = wp_get_post_categories(get_the_ID());
            if( isset($cats[0]) ) {
                $primary = get_term_by( 'id', $cats[0], 'category' );
            }
        }
        if( $primary ) {
            $term_link = get_term_link( $primary );
            echo '<a href="' . esc_url( $term_link ) . '">' . $primary->name . '</a>';
        }
    }
}

// **********************************************************************// 
// ! Custom Comment Form
// **********************************************************************// 

if(!function_exists('etheme_custom_comment_form')) {
    function etheme_custom_comment_form($defaults) {
        $defaults['comment_notes_before'] = '';
        $defaults['comment_notes_after'] = '';
        $dafaults['id_form'] = 'comments_form';

        $defaults['comment_field'] = '<div class="form-group"><label for="comment" class="control-label">'.__('Your Comment', 'xstore').'</label><textarea placeholder="' . esc_html__('Comment', 'xstore') . '" class="form-control required-field"  id="comment" name="comment" cols="45" rows="12" aria-required="true"></textarea></div>';

        return $defaults;
    }
}

add_filter('comment_form_defaults', 'etheme_custom_comment_form');

if(!function_exists('etheme_custom_comment_form_fields')) {
    function etheme_custom_comment_form_fields() {
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $reqT = '<span class="required">*</span>';
        $aria_req = ($req ? " aria-required='true'" : ' ');

        $fields = array(
            'author' => '<div class="form-group comment-form-author">'.
                            '<label for="author" class="control-label">'.__('Name', 'xstore').' '.($req ? $reqT : '').'</label>'.
                            '<input id="author" name="author" placeholder="' . esc_html__('Your name (required)', 'xstore') . '" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30" ' . $aria_req . '>'.
                        '</div>',
            'email' => '<div class="form-group comment-form-email">'.
                            '<label for="email" class="control-label">'.__('Email', 'xstore').' '.($req ? $reqT : '').'</label>'.
                            '<input id="email" name="email" placeholder="' . esc_html__('Your email (required)', 'xstore') . '" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" ' . $aria_req . '>'.
                        '</div>',
            'url' => '<div class="form-group comment-form-url">'.
                            '<label for="url" class="control-label">'.__('Website', 'xstore').'</label>'.
                            '<input id="url" name="url" placeholder="' . esc_html__('Your website', 'xstore') . '" type="text" class="form-control" value="' . esc_attr($commenter['comment_author_url']) . '" size="30">'.
                        '</div>'
        );

        return $fields;
    }
}

add_filter('comment_form_default_fields', 'etheme_custom_comment_form_fields');

// **********************************************************************// 
// ! Set exerpt 
// **********************************************************************//
if(!function_exists('etheme_excerpt_length')) {
    function etheme_excerpt_length( $length ) {
        return etheme_get_option('excerpt_length');
    }
}

add_filter( 'excerpt_length', 'etheme_excerpt_length', 999 );

if(!function_exists('etheme_excerpt_more')) {
    function etheme_excerpt_more( $more ) {
        return '...';
    }
}

add_filter('excerpt_more', 'etheme_excerpt_more');


// **********************************************************************// 
// ! Enable shortcodes in text widgets
// **********************************************************************// 
add_filter('widget_text', 'do_shortcode');


// **********************************************************************// 
// ! Add Facebook Open Graph Meta Data
// **********************************************************************// 

//Adding the Open Graph in the Language Attributes
if(!function_exists('etheme_add_opengraph_doctype')) {
    function etheme_add_opengraph_doctype($output ) {
        return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
}
add_filter('language_attributes', 'etheme_add_opengraph_doctype');


if(!function_exists('etheme_excerpt')) {
    function etheme_excerpt($text, $excerpt){
        if ($excerpt) return $excerpt;

        $text = strip_shortcodes( $text );

        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
        $words = preg_split("/[\n
         ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
        } else {
                $text = implode(' ', $words);
        }

        return apply_filters('wp_trim_excerpt', $text, $excerpt);
        }
}


// **********************************************************************// 
// ! AJAX search
// **********************************************************************// 
add_action( 'wp_ajax_et_ajax_search', 'etheme_ajax_search_action');
add_action( 'wp_ajax_nopriv_et_ajax_search', 'etheme_ajax_search_action');
if(!function_exists('etheme_ajax_search_action')) {
    function etheme_ajax_search_action() {
        global $woocommerce;
        $result = array(
            'status' => 'error',
            'html' => ''
        );
        if(isset($_REQUEST['s'])) {

            $wc_get_template = function_exists( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';

            $s = sanitize_text_field($_REQUEST['s']);

            $ordering_args = $woocommerce->query->get_catalog_ordering_args( 'title', 'asc' );

            $args = array(
                's'                   => $s,
                'post_type'           => 'product',
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
                'orderby'             => $ordering_args['orderby'],
                'order'               => $ordering_args['order'],
                'posts_per_page'      => 8,
                'suppress_filters'    => false,
                'meta_query'          => array(
                    array(
                        'key'     => '_visibility',
                        'value'   => array( 'search', 'visible' ),
                        'compare' => 'IN'
                    )
                )
            );

            if ( isset( $_REQUEST['product_cat'] ) ) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => $_REQUEST['product_cat']
                    ) );
            }


            $products = get_posts( $args );

            if ( ! empty($products) ) {
                ob_start();

                echo '<h3 class="search-results-title">' . esc_html__('Products found', 'xstore') . '<a href="' . esc_url( home_url() ) . '/?s='. $s .'&post_type=product">' . esc_html__('View all', 'xstore' ) . '</a></h3>';

                foreach ( $products as $post ) {
                    setup_postdata( $post );
                    $wc_get_template( 'content-widget-product.php' );
                }

                $result['status'] = 'success';
                $result['html'] .= '<ul class="product-ajax-list">' . ob_get_clean() . '</ul>';
            }

            wp_reset_postdata();

            /* get posts results */

            $args = array(
                's'                   => $s,
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => 8,
            );

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {
                ob_start();

                echo '<h3 class="search-results-title">' . esc_html__('Posts found', 'xstore') . '<a href="' . esc_url( home_url() ) . '/?s='. $s .'&post_type=post">' . esc_html__('View all', 'xstore' ) . '</a></h3>';

                foreach ( $posts as $post ) {
                    setup_postdata( $post );
                    ?>
                        <li>
                            <?php if ( has_post_thumbnail($post->ID) ): ?>
                                <a href="<?php the_permalink($post->ID); ?>" class="post-list-image">
                                    <?php echo get_the_post_thumbnail( $post->ID, 'small' ); ?>
                                </a>
                            <?php endif ?>
                            <h4><a href="<?php the_permalink($post->ID) ?>"><?php echo get_the_title($post->ID); ?></a></h4>
                            <span class="post-date"><?php echo get_the_time(get_option('date_format'), $post); ?></span>
                        </li>
                    <?php
                }

                $result['status'] = 'success';
                $result['html'] .= '<ul class="posts-ajax-list">' . ob_get_clean() . '</ul>';
            }

            wp_reset_postdata();

            if ( empty( $products ) && empty( $posts ) ) {
                $result['status'] = 'error';
                $result['html'] = esc_html__( 'No results', 'xstore' );
            }
        }

        echo json_encode($result);

        die();
    }
}

// **********************************************************************// 
// ! Footer Type
// **********************************************************************// 
if(!function_exists('etheme_footer_type')) {
    function etheme_footer_type() {
        return etheme_get_option('footer_type');
    }

    add_filter('custom_footer_filter', 'etheme_footer_type',10);
}

// **********************************************************************// 
// ! Footer widgets class
// **********************************************************************// 
if(!function_exists('etheme_get_footer_widget_class')) {
    function etheme_get_footer_widget_class($n) {

        $class = 'col-md-';

        switch ($n) {
            case 1:
                $class .= 12;
                break;
            case 2:
                $class .= 6;
                break;
            case 3:
                $class .= 4;
                break;
            case 4:
                $class .= 3;
                break;

            default:
                $class .= 3;
                break;
        }

        if( $n == 4 ) {
            $class .= ' col-sm-6';
        }

        return $class;

    }
}



// **********************************************************************//
// ! Implement Opauth Facebook login
// **********************************************************************//

if( ! function_exists('etheme_login_facebook') ) {
    add_action('init', 'etheme_login_facebook', 20);
    function etheme_login_facebook() {
        if( empty( $_GET['facebook'] ) && empty( $_GET['code'] ) ) {
            return;
        }

        $account_url    = wc_get_page_permalink('myaccount');
        $security_salt  = apply_filters('et_facebook_salt', '2NlBUibcszrVtNmDnxqDbwCOpLWq91eatIz6O1O');
        $app_id         = etheme_get_option('facebook_app_id');
        $app_secret     = etheme_get_option('facebook_app_secret');

        if( empty( $app_secret ) || empty( $app_id ) ) return;

        $config = array(
            'security_salt' => $security_salt,
            'host' => $account_url,
            'path' => '/',
            'callback_url' => $account_url,
            'callback_transport' => 'get',
            'strategy_dir' => ETHEME_CODE_3D . 'vendor/opauth/',
            'Strategy' => array(
                'Facebook' => array(
                    'app_id' => $app_id,
                    'app_secret' => $app_secret,
                    'scope' => 'email'
                ),
            )
        );

        if( empty( $_GET['code'] ) ) {
            $config['request_uri'] = '/facebook/';
        } else {
            $config['request_uri'] = '/facebook/int_callback?code=' . $_GET['code'];
        }

        new Opauth( $config );
    }
}

if( ! function_exists('etheme_process_facebook_callback') ) {
    add_action('init', 'etheme_process_facebook_callback', 30);
    function etheme_process_facebook_callback() {
        if( empty( $_GET['opauth'] ) ) return;

        $opauth = unserialize(etheme_decoding($_GET['opauth']));

        if( empty( $opauth['auth']['info'] ) ) {
            wc_add_notice( esc_html__( 'Can\'t login with Facebook. Please, try again later.', 'xstore' ), 'error' );
            return;
        }

        $info = $opauth['auth']['info'];

        if( empty( $info['email'] ) ) {
            wc_add_notice( esc_html__( 'Facebook doesn\'t provide your email. Try to register manually.', 'xstore' ), 'error' );
            return;
        }

        add_filter('pre_option_woocommerce_registration_generate_username', 'etheme_generate_username_option', 10);

        $password = wp_generate_password();
        $customer = wc_create_new_customer( $info['email'], '', $password);

        $user = get_user_by('email', $info['email']);

        if( is_wp_error( $customer ) ) {
            if( isset( $customer->errors['registration-error-email-exists'] ) ) {
                wc_set_customer_auth_cookie( $user->ID );
            }
        } else {
            wc_set_customer_auth_cookie( $customer );
        }

        wc_add_notice( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'xstore' ), $user->display_name ) );

        remove_filter('pre_option_woocommerce_registration_generate_username', 'etheme_generate_username_option', 10);
    }
}

if( ! function_exists('etheme_generate_username_option') ) {
    function etheme_generate_username_option() {
        return 'yes';
    }
}

// **********************************************************************//
// ! Facebook login button
// **********************************************************************//

if( ! function_exists('etheme_faceboook_login_button') ) {
    add_action( 'woocommerce_before_customer_login_form', 'etheme_faceboook_login_button');
    function etheme_faceboook_login_button() {
        $app_id         = etheme_get_option('facebook_app_id');
        $app_secret     = etheme_get_option('facebook_app_secret');

        if( empty( $app_secret ) || empty( $app_id ) ) return;

        $facebook_login_url = add_query_arg('facebook', 'login', wc_get_page_permalink('myaccount'));
        echo '<div class="et-facebook-login-wrapper"><a href="' . esc_url( $facebook_login_url ) . '" class="et-facebook-login-button"><i class="fa fa-facebook"></i> ' . esc_html__('Login / Register with Facebook', 'xstore') . '</a></div>';
    }
}



// **********************************************************************// 
// ! http://codex.wordpress.org/Function_Reference/wp_nav_menu#How_to_add_a_parent_class_for_menu_item
// **********************************************************************// 

add_filter( 'wp_nav_menu_objects', 'etheme_add_menu_parent_class');
function etheme_add_menu_parent_class($items ) {

    $parents = array();
    foreach ( $items as $item ) {
        if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
            $parents[] = $item->menu_item_parent;
        }
    }

    foreach ( $items as $item ) {
        if ( in_array( $item->ID, $parents ) ) {
            $item->classes[] = 'menu-parent-item';
        }
    }

    return $items;
}

// **********************************************************************// 
// ! Change WP coockie notice position
// **********************************************************************//
if( class_exists('Cookie_Notice') ) {
    remove_action( 'wp_footer', array( $cookie_notice, 'add_cookie_notice' ), 1000 );
    add_action( 'et_after_body', array( $cookie_notice, 'add_cookie_notice' ), 1000 );
}


// **********************************************************************// 
// ! Twitter API functions
// **********************************************************************// 
if(!function_exists('etheme_capture_tweets')) {
    function etheme_capture_tweets($consumer_key,$consumer_secret,$user_token,$user_secret,$user, $count) {

        $connection = etheme_connection_with_access_token($consumer_key,$consumer_secret,$user_token, $user_secret);
        $params = array(
            'screen_name' => $user,
            'count' => $count
        );

        $content = $connection->get("statuses/user_timeline",$params);

        //prar($content);

        return json_encode($content);
    }
}

if(!function_exists('etheme_connection_with_access_token')) {
    function etheme_connection_with_access_token($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret) {
        $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
        return $connection;
    }
}


if(!function_exists('etheme_tweet_linkify')) {
    function etheme_tweet_linkify($tweet) {
        $tweet = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $tweet);
        $tweet = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $tweet);
        $tweet = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $tweet);
        $tweet = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $tweet);
        return $tweet;
    }
}
if(!function_exists('etheme_store_tweets')) {
    function etheme_store_tweets($file, $tweets) {
        ob_start(); // turn on the output buffering 
        $fo = etheme_fo($file, 'w'); // opens for writing only or will create if it's not there
        if (!$fo) return etheme_print_tweet_error(error_get_last());
        $fr = etheme_fw($fo, $tweets); // writes to the file what was grabbed from the previous function
        if (!$fr) return etheme_print_tweet_error(error_get_last());
        etheme_fc($fo); // closes
        ob_end_flush(); // finishes and flushes the output buffer; 
    }
}

if(!function_exists('etheme_pick_tweets')) {
    function etheme_pick_tweets($file) {
        ob_start(); // turn on the output buffering 
        $fo = etheme_fo($file, 'r'); // opens for reading only 
        if (!$fo) return etheme_print_tweet_error(error_get_last());
        $fr = etheme_fr($fo, filesize($file));
        if (!$fr) return etheme_print_tweet_error(error_get_last());
        etheme_fc($fo);
        ob_end_flush();
        return $fr;
    }
}

if(!function_exists('etheme_print_tweet_error')) {
    function etheme_print_tweet_error($errorsArray) {
        $html = '';
        if( count($errorsArray) > 0 ){
            foreach ($errorsArray as $key => $error) {
                $html .= '<p class="warning">Error: ' . $error['message']  . '</p>';
            }
        }
        return $html;
    }
}

if(!function_exists('etheme_twitter_cache_enabled')) {
    function etheme_twitter_cache_enabled(){
        return apply_filters('etheme_twitter_cache_enabled', true);
    }
}

if(!function_exists('etheme_get_tweets')) {
    function etheme_get_tweets($consumer_key, $consumer_secret, $user_token, $user_secret, $user, $count, $cachetime=50, $key = 'widget') {
        if(etheme_twitter_cache_enabled()){
            //setting the location to cache file
            $cachefile = ETHEME_CODE_DIR . 'cache/cache-twitter-' . $key . '.json';

            // the file exitsts but is outdated, update the cache file
            if (file_exists($cachefile) && ( time() - $cachetime > filemtime($cachefile)) && filesize($cachefile) > 0) {
                //capturing fresh tweets
                $tweets = etheme_capture_tweets($consumer_key,$consumer_secret,$user_token,$user_secret,$user, $count);
                $tweets_decoded = json_decode($tweets, true);
                //if get error while loading fresh tweets - load outdated file
                if(isset($tweets_decoded['errors'])) {
                    $tweets = etheme_pick_tweets($cachefile);
                }
                //else store fresh tweets to cache
                else
                    etheme_store_tweets($cachefile, $tweets);
            }
            //file doesn't exist or is empty, create new cache file
            elseif (!file_exists($cachefile) || filesize($cachefile) == 0) {
                $tweets = etheme_capture_tweets($consumer_key,$consumer_secret,$user_token,$user_secret,$user, $count);
                $tweets_decoded = json_decode($tweets, true);
                //if request fails, and there is no old cache file - print error
                if(isset($tweets_decoded['errors'])) {
                    echo etheme_print_tweet_error($tweets['errors']);
                    return array();
                }
                //make new cache file with request results
                else
                    etheme_store_tweets($cachefile, $tweets);
            }
            //file exists and is fresh
            //load the cache file
            else {
               $tweets = etheme_pick_tweets($cachefile);
            }
        } else{
           $tweets = etheme_capture_tweets($consumer_key,$consumer_secret,$user_token,$user_secret,$user, $count);
        }

        $tweets = json_decode($tweets, true);

        if(isset($tweets['errors'])) {
            echo etheme_print_tweet_error($tweets['errors']);
            return array();
        }

        return $tweets;
    }
}



// **********************************************************************// 
// ! Related posts 
// **********************************************************************// 

if(!function_exists('etheme_get_related_posts')) {
    function etheme_get_related_posts($postId = false, $limit = 5){
        global $post;
        if(!$postId) {
            $postId = $post->ID;
        }

        $query_type = etheme_get_option('related_query');
        $atts = array(
            'large' => 3,
            'notebook' => 3,
            'tablet_land' => 2,
            'tablet_portrait' => 2,
            'mobile' => 1,
            'size' => etheme_get_option('blog_related_images_size')
        );
        if($query_type == 'tags') {
            $tags = get_the_tags($postId);
            if ($tags) {
                $tags_ids = array();
                foreach($tags as $tag) $tags_ids[] = $tag->term_id;

                $args = array(
                    'tag__in' => $tags_ids,
                    'post__not_in' => array($postId),
                    'showposts'=>$limit, // Number of related posts that will be shown.
                );
            }
        } else {
            $categories = get_the_category($postId);
            if ($categories) {
                $category_ids = array();
                foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

                $args = array(
                    'category__in' => $category_ids,
                    'post__not_in' => array($postId),
                    'showposts'=>$limit, // Number of related posts that will be shown.
                );
            }
        }
        etheme_create_posts_slider($args, esc_html__('Related posts', 'xstore'), $atts);
    }
}



if(!function_exists('etheme_get_menus_options')) {
    function etheme_get_menus_options() {
        $menus = array();
        $menus = array(""=>"Default");
        $nav_terms = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        foreach ( $nav_terms as $obj ) {
            $menus[$obj->slug] = $obj->name;
        }
        return $menus;
    }
}


// **********************************************************************// 
// ! Get image by size function
// ! TODO: echo param. Show full image tag param
// **********************************************************************// 
if( ! function_exists('etheme_get_image') ) {
    function etheme_get_image($attach_id, $size) {
        if (function_exists('wpb_getImageBySize')) {
            $image = wpb_getImageBySize( array(
                    'attach_id' => $attach_id,
                    'thumb_size' => $size
                ) );
            $image = $image['thumbnail'];
        } else {
            $image = wp_get_attachment_image( $attach_id, $size );
        }

        return $image;
    }
}

// **********************************************************************// 
// ! Hook photoswipe tempalate to the footer
// **********************************************************************// 
add_action('after_page_wrapper', 'etheme_photoswipe_template', 30);
if(!function_exists('etheme_photoswipe_template')) {
    function etheme_photoswipe_template() {
        ?>
<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe.
         It's a separate element as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides.
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don't modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                <button class="pswp__button pswp__button--share" title="Share"></button>

                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

        </div>

    </div>

</div>
        <?php
    }
}

?>
