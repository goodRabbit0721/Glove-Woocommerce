<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Load complicated shortcodes
// **********************************************************************// 
etheme_load_shortcode('banner');
etheme_load_shortcode('title');
etheme_load_shortcode('images-carousel');
etheme_load_shortcode('carousel');
etheme_load_shortcode('icon-box');
etheme_load_shortcode('tabs');
etheme_load_shortcode('team-member');
etheme_load_shortcode('testimonials');
etheme_load_shortcode('twitter');
etheme_load_shortcode('instagram');
etheme_load_shortcode('blog');
etheme_load_shortcode('follow');
etheme_load_shortcode('countdown');

etheme_load_shortcode('categories');
etheme_load_shortcode('products');
etheme_load_shortcode('brands');
etheme_load_shortcode('looks');
etheme_load_shortcode('the-look');
etheme_load_shortcode('custom-tabs');


// Add shortcodes to MCE
if( ! function_exists('etheme_add_mce_button') ) {
    add_action('admin_head', 'etheme_add_mce_button');
    function etheme_add_mce_button() {
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }
        if ( 'true' == get_user_option( 'rich_editing' ) ) {
            add_filter( 'mce_external_plugins', 'etheme_add_tinymce_plugin');
            add_filter( 'mce_buttons', 'etheme_register_mce_button');
        }
    }

}

if( ! function_exists('etheme_add_tinymce_plugin') ) {
    // Declare script for new button
    function etheme_add_tinymce_plugin($plugin_array ) {
        $plugin_array['et_mce_button'] = ETHEME_CODE_JS . 'mce.js';
        return $plugin_array;
    }
}

if( ! function_exists('etheme_register_mce_button') ) {
    // Register new button in the editor
    function etheme_register_mce_button($buttons ) {
        array_push( $buttons, 'et_mce_button' );
        return $buttons;
    }
}

// **********************************************************************// 
// ! Shortcodes
// **********************************************************************// 

function etheme_quick_view_shortcodes($atts, $content=null){
    extract(shortcode_atts(array( 
        'id' => '',
        'class' => ''
    ), $atts));
    
    
    return '<div class="show-quickly-btn '.$class.'" data-prodid="'.$id.'">'. do_shortcode($content) .'</div>';

}


// **********************************************************************// 
// ! Buttons
// **********************************************************************// 

function etheme_btn_shortcode($atts){
    $a = shortcode_atts( array(
       'title' => 'Button',
       'url' => '#',
       'icon' => '',
       'size' => '',
       'style' => '',
       'el_class' => '',
       'type' => ''
   ), $atts );
    $icon = $class = '';
    if($a['icon'] != '') {
        $icon = '<i class="fa fa-'.$a['icon'].'"></i>';
    }
    if($a['style'] != '') {
	    $class .= ' '.$a['style'];
    }
    if($a['type'] != '') {
	    $class .= ' '.$a['type'];
    }
    if($a['size'] != '') {
	    $class .= ' '.$a['size'];
    }
    if($a['size'] != '') {
	    $class .= ' '.$a['size'];
    }
    if($a['el_class'] != '') {
	    $class .= ' '.$a['el_class'];
    }
    return '<a class="btn'. $class .'" href="' . $a['url'] . '"><span>'. $icon . $a['title'] . '</span></a>';
}

// **********************************************************************// 
// ! Animated counter
// **********************************************************************// 

function etheme_counter_shortcode($atts, $content = null) {
    $a = shortcode_atts( array(
        'init_value' => 1,
        'final_value' => 100,
        'class' => ''
    ), $atts);

    return '<span id="animatedCounter" class="animated-counter '.$a['class'].'" data-value='.$a['final_value'].'>'.$a['init_value'].'</span>';
}


// **********************************************************************// 
// ! Dropcap
// **********************************************************************// 

function etheme_dropcap_shortcode($atts,$content=null) {
    $a = shortcode_atts( array(
       'style' => '',
       'color' => '',
    ), $atts );

    $style = '';
    if( ! empty( $a['color'] ) ) {
        $style = 'style="color:' . $a['color'] . ';"';
    }
   
    return '<span class="dropcap ' . $a['style'] . '" ' . $style . '>' . $content . '</span>';
}

// **********************************************************************// 
// ! Mark
// **********************************************************************// 

function etheme_mark_shortcode($atts,$content=null) {
    $a = shortcode_atts( array(
       'style' => '',
       'color' => '',
    ), $atts );

    $style = '';

    if( ! empty( $a['color'] ) ) {
        $style = 'style="background-color:' . $a['color'] . ';"';
    }

    if( ! empty( $a['color'] ) && $a['style'] == 'paragraph' ) {
        $style = 'style="color:' . $a['color'] . ';"';
    }
   
    return '<span class="mark-text ' . $a['style'] . '" ' . $style . '>' . $content . '</span>';
}


// **********************************************************************// 
// ! Blockquote
// **********************************************************************// 

function etheme_blockquote_shortcode($atts, $content = null) {
    $a = shortcode_atts( array(
        'align' => 'left',
        'class' => ''
    ), $atts);
    switch($a['align']) {

        case 'right':
            $align = 'fl-r';
        break;
        case 'center':
            $align = 'fl-none';
        break;
        default:
            $align = 'fl-l';        
    }
    $content = wpautop(trim($content));
    return '<blockquote class="' . $align .' '. $a['class'] . '">' . $content . '</blockquote>';
}


// **********************************************************************// 
// ! Checklist
// **********************************************************************// 

function etheme_checklist_shortcode($atts, $content = null) {
    $a = shortcode_atts( array(
        'style' => 'arrow'
    ), $atts);
    switch($a['style']) {
        case 'arrow':
            $class = 'arrow';
        break;
        case 'circle':
            $class = 'circle';
        break;
        case 'star':
            $class = 'star';
        break;
        case 'square':
            $class = 'square';
        break;
        case 'dash':
            $class = 'dash';
        break;
        default:
            $class = 'arrow';
    }
    return '<div class="list list-' . $class . '">' . do_shortcode($content) . '</div	>';
}

// **********************************************************************// 
// ! QR Code
// **********************************************************************// 

function etheme_qrcode_shortcode($atts, $content = null) {
$a = shortcode_atts(array(
        'size' => '128',
        'self_link' => 0,
        'title' => 'QR Code',
        'lightbox' => 0,
        'class' => ''
    ), $atts);

    return etheme_qr_code($content,$a['title'],$a['size'],$a['class'],$a['self_link'],$a['lightbox']);
}


// **********************************************************************// 
// ! Project links
// **********************************************************************// 

function etheme_project_links($atts, $content = null) {
    $next_post = get_next_post();
    $prev_post = get_previous_post();
    ?>
        <div class="posts-navigation">
            <?php if(!empty($prev_post)) : ?>
                <div class="posts-nav-btn prev-post">
                    <div>
                        <a href="<?php echo get_permalink($prev_post->ID); ?>" class="button btn-previous"><?php esc_html_e('Previous', 'xstore'); ?></a>
                        <div class="post-info">
                            <span class="post-title"><?php echo get_the_title($prev_post->ID); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!empty($next_post)) : ?>
                <div class="posts-nav-btn next-post">
                    <div>
                        <a href="<?php echo get_permalink($next_post->ID); ?>" class="button btn-next"><?php esc_html_e('Next', 'xstore'); ?></a>
                        <div class="post-info">
                        	<span class="post-title"><?php echo get_the_title($next_post->ID); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php
}

// **********************************************************************// 
// ! Tooltip
// **********************************************************************// 

function etheme_tooltip_shortcode($atts,$content=null){
    $a = shortcode_atts( array(
       'position' => 'top',
       'text' => '',
       'class' => ''
   ), $atts );
   
    return '<div class="et-tooltip '.$a['class'].'" rel="tooltip" data-placement="'.$a['position'].'" data-original-title="'.$a['text'].'"><div><div>'.$content.'</div></div></div>';
}

// **********************************************************************// 
// ! Share This Product
// **********************************************************************// 

function etheme_share_shortcode($atts, $content = null) {
	extract(shortcode_atts(array(
		'title'  => '',
		'text' => '',
		'tooltip' => 1,
        'twitter' => etheme_get_option( 'share_twitter' ),
        'facebook' =>  etheme_get_option( 'share_facebook' ),
        'vk' =>  etheme_get_option( 'share_vk' ),
        'pinterest' =>  etheme_get_option( 'share_pinterest' ),
        'google' =>  etheme_get_option( 'share_google' ),
        'mail' =>  etheme_get_option( 'share_mail' ),
		'class' => ''
	), $atts));
	global $post;
	if(!isset($post->ID)) return;
    $html = '';
	$permalink = get_permalink($post->ID);
	$tooltip_class = '';
	if($tooltip) {
		$tooltip_class = 'title-toolip';
	}
	$image =  wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'small' );
    $image = $image[0];
	$post_title = rawurlencode(get_the_title($post->ID)); 
	if($title) $html .= '<span class="share-title">'.$title.'</span>';
    $html .= '
        <ul class="menu-social-icons '.$class.'">
    ';
    if($twitter == 1) {
        $html .= '
                <li>
                    <a href="https://twitter.com/share?url='.$permalink.'&text='.$post_title.'" class="'.$tooltip_class.'" title="'.__('Twitter', 'xstore').'" target="_blank">
                        <i class="fa fa-twitter"></i>
                    </a>
                </li>
        ';
    }

    if($facebook == 1) {
        $html .= '
                <li>
                    <a href="http://www.facebook.com/sharer.php?u='.$permalink.'&amp;images='.$image.'" class="'.$tooltip_class.'" title="'.__('Facebook', 'xstore').'" target="_blank">
                        <i class="fa fa-facebook"></i>
                    </a>
                </li>
        ';
    }

    if($vk == 1) {
        $html .= '
                <li>
                    <a href="http://vk.com/share.php?url='.$permalink.'&image='.$image.'" class="'.$tooltip_class.'" title="'.__('VK', 'xstore').'" target="_blank">
                        <i class="fa fa-vk"></i>
                    </a>
                </li>
        ';
    }

    if($pinterest == 1) {
        $html .= '
                <li>
                    <a href="http://pinterest.com/pin/create/button/?url='.$permalink.'&amp;media='.$image.'&amp;description='.$post_title.'" class="'.$tooltip_class.'" title="'.__('Pinterest', 'xstore').'" target="_blank">
                        <i class="fa fa-pinterest"></i>
                    </a>
                </li>
        ';
    }

    if($google == 1) {
        $html .= '
                <li>
                    <a href="http://plus.google.com/share?url='.$permalink.'&title='.$text.'" class="'.$tooltip_class.'" title="'.__('Google +', 'xstore').'" target="_blank">
                        <i class="fa fa-google-plus"></i>
                    </a>
                </li>
        ';
    }

    if($mail == 1) {
        $html .= '
                <li>
                    <a href="mailto:enteryour@addresshere.com?subject='.$post_title.'&amp;body=Check%20this%20out:%20'.$permalink.'" class="'.$tooltip_class.'" title="'.__('Mail to friend', 'xstore').'" target="_blank">
                        <i class="fa fa-envelope"></i>
                    </a>
                </li>
        ';
    }
    
    $html .= '
        </ul>
    ';
	return $html;
}

// **********************************************************************// 
// ! Static Block Shortcode
// **********************************************************************// 

function etheme_block_shortcode($atts) {
    $a = shortcode_atts(array(
        'class' => '',
        'id' => ''
    ),$atts);

    return etheme_get_block($a['id']);
}
?>
