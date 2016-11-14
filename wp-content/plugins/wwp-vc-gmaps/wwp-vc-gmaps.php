<?php
/*
    Plugin Name: GMAPS for Visual Composer
    Plugin URI: http://workingwithpixels.com/gmaps-for-visual-composer
    Description: A beautiful Google Maps add-on for Visual Composer.
    Version: 1.3
    Author: WWP
    Author URI: http://www.workingwithpixels.com/
    Copyright: WWP, 2016
*/

if (!defined('ABSPATH')) die('-1');

if(!class_exists('WWWP_VC_GMAPS'))
{
    function init_wwp_vc_gmaps()
    {
        if(!defined('WPB_VC_VERSION'))
        {
            add_action('admin_notices', 'wwp_vc_gmaps_notice__error');
        }
    }
    add_action('admin_init', 'init_wwp_vc_gmaps');

    function wwp_vc_gmaps_notice__error()
    {
        $class = 'notice notice-error';
        $message = 'GMAPS for Visual Composer: Please check if Visual Composer is active on your website.';

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }

    class WWWP_VC_GMAPS
    {
        function defines()
        {
            defined('wwp_vc_gmaps_name')  ||  define('wwp_vc_gmaps_name', 'WWP');
            defined('wwp_vc_gmaps_dir')  ||  define('wwp_vc_gmaps_dir', plugin_dir_path( __FILE__ ));
            defined('wwp_vc_gmaps_inc')  ||  define('wwp_vc_gmaps_inc', wwp_vc_gmaps_dir . 'include/');
            defined('wwp_vc_gmaps_inc_dir')  ||  define('wwp_vc_gmaps_inc_dir', plugins_url( 'include/' , __FILE__ ));
            defined('wwp_vc_gmaps_images_path')  ||  define('wwp_vc_gmaps_images_path', plugins_url( 'include/core/img/' , __FILE__ ));
        }

        function __construct()
        {
            $this->defines();

            if(function_exists('add_shortcode_param'))
            {
                add_shortcode_param('marker_icons' , array(&$this, 'marker_icons' ) );
            }

            function wwp_vc_gmaps_init_admin_css()
            {
                wp_enqueue_style('wwp-vc-gmaps-admin', plugins_url( 'include/core/css/wwp-vc-gmaps-admin.css', __FILE__ ));
            }
            add_action('admin_enqueue_scripts', 'wwp_vc_gmaps_init_admin_css');

            add_action('init', array(__CLASS__, 'register_wwp_vc_gmaps_assets'));
            add_action('wp_head', array(__CLASS__, 'print_wwp_vc_gmaps_assets'));

            require_once(wwp_vc_gmaps_inc . 'core/vc_gmaps.php');
            require_once(wwp_vc_gmaps_inc . 'core/vc_gmaps_marker.php');
        }

        static function register_wwp_vc_gmaps_assets()
        {
            // CSS
            wp_register_style('wwp-vc-gmaps', plugins_url( 'include/core/css/wwp-vc-gmaps.css', __FILE__ ));
        }

        static function print_wwp_vc_gmaps_assets()
        {
            global $post;

            if(has_shortcode($post->post_content, 'wwp_vc_gmaps'))
            {
                wp_print_styles('wwp-vc-gmaps');
            }
        }

        function marker_icons($settings, $value)
        {
            $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
            $type = isset($settings['type']) ? $settings['type'] : '';
            $class = isset($settings['class']) ? $settings['class'] : '';

            $pins = array('black', 'blue', 'green', 'purple', 'red', 'gray', 'orange', 'blackv2', 'bluev2', 'greenv2', 'purplev2', 'redv2', 'grayv2', 'orangev2');

            $output = '<input type="hidden" name="'.$param_name.'" class="wpb_vc_param_value '.$param_name.' '.$type.' '.$class.'" value="'.$value.'" id="trace"/>
					<div class="pin-preview"><img src="'.plugins_url('include/core/img/pins/pin_'.$value, __FILE__ ).'.png"></div>';
            $output .='<div id="markers-dropdown" >';
            $output .= '<ul class="pin-list">';
            $x = 1;
            foreach($pins as $pin)
            {
                $selected = ($pin == $value) ? 'class="selected"' : '';
                $output .= '<li '.$selected.' data-pin-url="'.plugins_url('include/core/img/pins/pin_'.$pin, __FILE__ ).'.png" data-pin="'.$pin.'"><img src="'.plugins_url('include/core/img/pins/pin_'.$pin, __FILE__ ).'.png"><label class="pin">'.$pin.'</label></li>';
                $x++;
            }
            $output .='</ul>';
            $output .='</div>';
            $output .= '<script type="text/javascript">
					jQuery(document).ready(function()
					{
                        jQuery("#markers-dropdown li").click(function() 
                        {
                            jQuery(this).attr("class","selected").siblings().removeAttr("class");
                            var icon = jQuery(this).attr("data-pin"),
                                icon_url = jQuery(this).attr("data-pin-url");
                            jQuery("#trace").val(icon);
                            jQuery(".pin-preview img").attr("src", icon_url);
    
                        });
					});
					</script>';

            return $output;
        }
    }
}

new WWWP_VC_GMAPS();