<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! team_member
// **********************************************************************// 

function etheme_team_member_shortcode($atts, $content = null) {
    $a = shortcode_atts(array(
        'class' => '',
        'type' => 1,
        'name' => '',
        'email' => '',
        'twitter' => '',
        'facebook' => '',
        'skype' => '',
        'linkedin' => '',
        'instagram' => '',
        'position' => '',
        'content' => '',
        'img' => '',
        'img_size' => '270x170'
    ), $atts);

	$img = intval( $a['img'] );

	$image = etheme_get_image($img, $a['img_size']);

    if ($a['content'] != '') {
        $content = $a['content'];
    }

    
    $html = '';
    $span = 12;
    $html .= '<div class="team-member member-type-'.$a['type'].' '.$a['class'].'">';

        if($a['type'] == 2) {
            $html .= '<div class="row">';
        }
	    if( ! empty( $image ) ){

            if($a['type'] == 2) {
                $html .= '<div class="col-md-6">';
                $span = 6;
            }
            $html .= '<div class="member-image">';
                $html .= $image;
	            if ($a['linkedin'] != '' || $a['twitter'] != '' || $a['facebook'] != '' || $a['skype'] != '' || $a['instagram'] != '') {
	                $html .= '<div class="member-content"><ul class="menu-social-icons">';
	                    $html .= '';
	                        if ($a['linkedin'] != '') {
	                            $html .= '<li><a href="'.$a['linkedin'].'"><i class="fa fa-linkedin"></i></a></li>';
	                        }
	                        if ($a['twitter'] != '') {
	                            $html .= '<li><a href="'.$a['twitter'].'"><i class="fa fa-twitter"></i></a></li>';
	                        }
	                        if ($a['facebook'] != '') {
	                            $html .= '<li><a href="'.$a['facebook'].'"><i class="fa fa-facebook"></i></a></li>';
	                        }
	                        if ($a['skype'] != '') {
	                            $html .= '<li><a href="'.$a['skype'].'"><i class="fa fa-skype"></i></a></li>';
	                        }
	                        if ($a['instagram'] != '') {
	                            $html .= '<li><a href="'.$a['instagram'].'"><i class="fa fa-instagram"></i></a></li>';
	                        }
	                $html .= '</ul></div>';
	            }
            $html .= '</div>';
            $html .= '<div class="clear"></div>';
            if($a['type'] == 2) {
                $html .= '</div>';
            }		      
	    }

    
        if($a['type'] == 2) {
            $html .= '<div class="col-md-'.$span.'">';
        }
        $html .= '<div class="member-details">';
            if($a['position'] != ''){
                $html .= '<h4>'.$a['name'].'</h4>';
            }

		    if($a['name'] != ''){
			    $html .= '<h5 class="member-position">'.$a['position'].'</h5>';
		    }

            if($a['email'] != ''){
                $html .= '<p class="member-email"><span>'.__('Email:', 'xstore').'</span> <a href="'.$a['email'].'">'.$a['email'].'</a></p>';
            }
		    $html .= do_shortcode($content);
    	$html .= '</div>';

        if($a['type'] == 2) {
                $html .= '</div>';
            $html .= '</div>';
        }
    $html .= '</div>';
    
    
    return $html;
}

// **********************************************************************// 
// ! Register New Element: team_member
// **********************************************************************//
add_action( 'init', 'etheme_register_vc_team_member');
if(!function_exists('etheme_register_vc_team_member')) {
	function etheme_register_vc_team_member() {
		if(!function_exists('vc_map')) return;
	    $team_member_params = array(
	      'name' => '[8theme] Team member',
	      'base' => 'team_member',
		  'icon' => ETHEME_CODE_IMAGES . 'vc/el-team.png',
	      'category' => 'Eight Theme',
	      'params' => array(
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Member name", 'xstore'),
	          "param_name" => "name"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Member email", 'xstore'),
	          "param_name" => "email"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Position", 'xstore'),
	          "param_name" => "position"
	        ),
	        array(
	          'type' => 'attach_image',
	          "heading" => esc_html__("Avatar", 'xstore'),
	          "param_name" => "img"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Image size", 'xstore' ),
	          "param_name" => "img_size",
	          "description" => esc_html__("Enter image size. Example in pixels: 200x100 (Width x Height).", 'xstore' )
	        ),
	        array(
	          "type" => "textarea_html",
	          "holder" => "div",
	          "heading" => esc_html__("Member information", 'xstore' ),
	          "param_name" => "content",
	          "value" => esc_html__("Member description", 'xstore' )
	        ),
	        array(
	          "type" => "dropdown",
	          "heading" => esc_html__("Display Type", 'xstore' ),
	          "param_name" => "type",
	          "value" => array( 
	              "", 
	              esc_html__("Vertical", 'xstore') => 1,
	              esc_html__("Horizontal", 'xstore') => 2
	            )
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Twitter link", 'xstore'),
	          "param_name" => "twitter"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Facebook link", 'xstore'),
	          "param_name" => "facebook"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Linkedin", 'xstore'),
	          "param_name" => "linkedin"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Skype name", 'xstore'),
	          "param_name" => "skype"
	        ),
	        array(
	          'type' => 'textfield',
	          "heading" => esc_html__("Instagram", 'xstore'),
	          "param_name" => "instagram"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Extra Class", 'xstore'),
	          "param_name" => "class",
	          "description" => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'xstore')
	        )
	      )
	
	    );  
	    vc_map($team_member_params);
	}
}
