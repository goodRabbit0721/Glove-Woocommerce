<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************//
// ! Brands
// **********************************************************************//

function etheme_carousel_shortcode($atts = array(), $content) {
    extract( shortcode_atts( array(
        'space' => 'yes',
        'large' => 4,
        'notebook' => 3,
        'tablet_land' => 2,
        'tablet_portrait' => 2,
        'mobile' => 1,
        'slider_autoplay' => false,
        'slider_speed' => 10000,
        'hide_pagination' => false,
        'hide_buttons' => false,
        'el_class' => '',
    ), $atts ) );

    $box_id = rand(1000,10000);

    ob_start();

    if( $space == 'yes' ) {
        $el_class .= ' slider-with-space';
    }

    echo '<div class="slider-' . $box_id . ' custom-carousel ' . $el_class . '">';
        etheme_override_shortcodes();
        echo do_shortcode($content);
        etheme_restore_shortcodes();
    echo '</div>';


    echo '
        <script type="text/javascript">
            (function() {
                var options = {
                    items:5,
                    autoPlay: ' . (($slider_autoplay == "yes") ? $slider_speed : "false" ). ',
                    pagination: ' . (($hide_pagination == "yes") ? "false" : "true") . ',
                    navigation: ' . (($hide_buttons == "yes") ? "false" : "true" ). ',
                    navigationText:false,
                    rewindNav: ' . (($slider_autoplay == "yes") ? "true" : "false" ). ',
                    itemsCustom: [[0, ' . esc_js($mobile) . '], [479, ' . esc_js($tablet_portrait) . '], [619, ' . esc_js($tablet_portrait) . '], [768, ' . esc_js($tablet_land) . '],  [1200, ' . esc_js($notebook) . '], [1600, ' . esc_js($large) . ']]
                };

                jQuery(".slider-'.$box_id.'").owlCarousel(options);

                var owl = jQuery(".slider-'.$box_id.'").data("owlCarousel");

                jQuery( window ).bind( "vc_js", function() {
                    owl.reinit(options);
                } );
            })();
        </script>
    ';

    return ob_get_clean();
}


if(!function_exists('etheme_override_shortcodes')){
    function etheme_override_shortcodes() {
        global $shortcode_tags, $_shortcode_tags;
        // Let's make a back-up of the shortcodes
        $_shortcode_tags = $shortcode_tags;
        // Add any shortcode tags that we shouldn't touch here
        $disabled_tags = array( '' );
        foreach ( $shortcode_tags as $tag => $cb ) {
            if ( in_array( $tag, $disabled_tags ) ) {
                continue;
            }
            // Overwrite the callback function
            $shortcode_tags[ $tag ] = 'etheme_wrap_shortcode_in_div';
        }
    }
}
// Wrap the output of a shortcode in a div with class "ult-item-wrap"
// The original callback is called from the $_shortcode_tags array
if(!function_exists('etheme_wrap_shortcode_in_div')){
    function etheme_wrap_shortcode_in_div( $attr, $content = null, $tag ) {
        global $_shortcode_tags;
        return '<div class="ult-item-wrap">' . call_user_func( $_shortcode_tags[ $tag ], $attr, $content, $tag ) . '</div>';
    }
}
if(!function_exists('etheme_restore_shortcodes')){
    function etheme_restore_shortcodes() {
        global $shortcode_tags, $_shortcode_tags;
        // Restore the original callbacks
        if ( isset( $_shortcode_tags ) ) {
            $shortcode_tags = $_shortcode_tags;
        }
    }
}


// **********************************************************************//
// ! Register New Element: scslug
// **********************************************************************//
add_action( 'init', 'etheme_register_custom_carousel');
if(!function_exists('etheme_register_custom_carousel')) {
    function etheme_register_custom_carousel() {
        if(!function_exists('vc_map')) return;
        $params = array(
            'name' => '[8theme] Custom carousel',
            'base' => 'etheme_carousel',
            'icon' => 'icon-wpb-etheme',
            'icon' => ETHEME_CODE_IMAGES . 'vc/el-categories.png',
            'content_element' => true,
            'is_container' => true,
            'js_view' => 'VcColumnView',
            'show_settings_on_create' => false,
            'as_parent' => array(
                'only' => 'banner,vc_column_text',
            ),
            'category' => 'Eight Theme',
            'params' => array_merge(array(
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Add space between slides", 'xstore'),
                    "param_name" => "space",
                    "value" => array( "",
                        esc_html__("Yes", 'xstore') => 'yes',
                        esc_html__("No", 'xstore') => 'no',
                    )
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Extra Class", 'xstore'),
                    "param_name" => "el_class"
                ),
            ), etheme_get_slider_params())
        );

        vc_map($params);
    }
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Etheme_Carousel extends WPBakeryShortCodesContainer {
    }
}
