<?php
/*
Plugin Name: WP Instagram Widget
Plugin URI: https://github.com/cftp/wp-instagram-widget
Description: A WordPress widget for showing your latest Instagram photos
Version: 1.4.1
Author: Scott Evans (Code For The People)
Author URI: http://codeforthepeople.com
Text Domain: wpiw
Domain Path: /assets/languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This comment is added for compatibility with the null framework https://github.com/scottsweb/null
Widget Name: Instagram Widget

Copyright © 2013 Code for the People ltd

                _____________
               /      ____   \
         _____/       \   \   \
        /\    \        \___\   \
       /  \    \                \
      /   /    /          _______\
     /   /    /          \       /
    /   /    /            \     /
    \   \    \ _____    ___\   /
     \   \    /\    \  /       \
      \   \  /  \____\/    _____\
       \   \/        /    /    / \
        \           /____/    /___\
         \                        /
          \______________________/


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class ETheme_Instagram_Widget extends WP_Widget {

	function __construct() {
		global $wpiwdomain;
		$this->wpiwdomain = $wpiwdomain;
		$widget_ops = array('classname' => 'null-instagram-feed', 'description' => esc_html__('Displays your latest Instagram photos', 'xstore') );
		parent::__construct('null-instagram-feed', esc_html__('Instagram', 'xstore'), $widget_ops);
	}

	function widget($args, $instance) {

		extract($args, EXTR_SKIP);

		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$username = empty($instance['username']) ? '' : $instance['username'];
		$limit = empty($instance['number']) ? 9 : $instance['number'];
		$columns = empty($instance['columns']) ? 3 : (int) $instance['columns'];
		$size = empty($instance['size']) ? 'thumbnail' : $instance['size'];
		$target = empty($instance['target']) ? '_self' : $instance['target'];
		$link = empty($instance['link']) ? '' : $instance['link'];
		$slider = empty($instance['slider']) ? false : true;
		$spacing = empty($instance['spacing']) ? false : true;

		echo $before_widget;
		if(!empty($title)) { echo $before_title . $title . $after_title; };

		do_action( 'wpiw_before_widget', $instance );

		if ($username != '') {

			$media_array = $this->scrape_instagram($username, $limit);

			if ( is_wp_error($media_array) ) {

				echo $media_array->get_error_message();

			} else {

				// filter for images only?
				if ( $images_only = apply_filters( 'wpiw_images_only', FALSE ) )
					$media_array = array_filter( $media_array, array( $this, 'images_only' ) );

				// filters for custom classes
				$liclass = esc_attr( apply_filters( 'wpiw_item_class', '' ) );
				$aclass = esc_attr( apply_filters( 'wpiw_a_class', '' ) );
				$imgclass = esc_attr( apply_filters( 'wpiw_img_class', '' ) );


				?><ul class="instagram-pics instagram-size-<?php echo esc_attr( $instance['size'] ); ?> instagram-columns-<?php echo esc_attr( $columns ); ?> <?php if($spacing) echo 'instagram-no-space'; ?> <?php if($slider) echo 'instagram-slider'; ?>"><?php
				foreach ( $media_array as $item ) {
					// copy the else line into a new file (parts/wp-instagram-widget.php) within your theme and customise accordingly
					
					$image_src = $item['medium'];

					if( $size == 'thumbnail' ) {
						$image_src = $item['thumbnail'];
					}

					if( $size == 'large' ) {
						$image_src = $item['large'];
					}

					if ( locate_template( 'parts/wp-instagram-widget.php' ) != '' ) {
						include( locate_template( 'parts/wp-instagram-widget.php' ) );
					} else {
						echo '<li class="'. $liclass .'"><a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"  class="'. $aclass .'">
							<img src="'. esc_url( $image_src ) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'" width="1080" height="1080" class="'. $imgclass .'"/>
							<div class="insta-info">
								<span class="insta-likes">' . $item['likes']. '</span>
								<span class="insta-comments">' . $item['comments']. '</span>
							</div>
						</a></li>';
					}
				}
				?></ul><?php

				if($slider) {
					$large_items = 6;
					switch ($instance['size']) {
						case 'thumbnail':
							$large_items = 8;
						break;
						case 'medium':
							$large_items = 6;
						break;
						case 'large':
							$large_items = 4;
						break;
					}
			        $items = '[[0, 2], [479,2], [619,3], [768,' . ($large_items - 2) . '],  [1200, ' . ($large_items - 1) . '], [1600, ' . $large_items . ']]';
					?>
			            <script type="text/javascript">
			            	(function() {
				            	var instaOptions = {
				                    items:4, 
				                    lazyLoad : false,
				                    navigation: true,
				                    navigationText:false,
				                    rewindNav: false,
				                    itemsCustom: <?php echo $items; ?>
				                };
				                
				                jQuery(".instagram-slider").owlCarousel(instaOptions);

				                var instaOwl = jQuery(".instagram-slider").data('owlCarousel');

								jQuery( window ).bind( 'vc_js', function() {
									instaOwl.reinit(instaOptions);
								} );
			            	})();
			            </script>
					<?php
				}
			}
		}

		if ($link != '') {
			?><p class="clear"><a href="//instagram.com/<?php echo trim($username); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"><?php echo $link; ?></a></p><?php
		}

		do_action( 'wpiw_after_widget', $instance );

		echo $after_widget;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => esc_html__('Instagram', 'xstore'), 'username' => '', 'link' => esc_html__('Follow Us', 'xstore'), 'number' => 9, 'size' => 'thumbnail', 'target' => '_self') );
		$title = esc_attr($instance['title']);
		$username = esc_attr($instance['username']);
		$number = absint($instance['number']);
		$size = esc_attr($instance['size']);
		$columns = (int) $instance['columns'];
		$target = esc_attr($instance['target']);
		$link = esc_attr($instance['link']);
		$slider = esc_attr($instance['slider']);
		$spacing = esc_attr($instance['spacing']);

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title', 'xstore'); ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('username'); ?>"><?php esc_html_e('Username or hashtag', 'xstore'); ?>: <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of photos', 'xstore'); ?>: <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" /></label></p>
		<!--p><label for="<?php echo $this->get_field_id('size'); ?>"><?php esc_html_e('Photo size', 'xstore'); ?>:</label>
			<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" class="widefat">
				<option value="thumbnail" <?php selected('thumbnail', $size) ?>><?php esc_html_e('Thumbnail', 'xstore'); ?></option>
				<option value="medium" <?php selected('medium', $size) ?>><?php esc_html_e('Medium', 'xstore'); ?></option>
				<option value="large" <?php selected('large', $size) ?>><?php esc_html_e('Large', 'xstore'); ?></option>
			</select>
		</p-->
		<p><label for="<?php echo $this->get_field_id('target'); ?>"><?php esc_html_e('Open links in', 'xstore'); ?>:</label>
			<select id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" class="widefat">
				<option value="_self" <?php selected('_self', $target) ?>><?php esc_html_e('Current window (_self)', 'xstore'); ?></option>
				<option value="_blank" <?php selected('_blank', $target) ?>><?php esc_html_e('New window (_blank)', 'xstore'); ?></option>
			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('columns'); ?>"><?php esc_html_e('Columns', 'xstore'); ?>:</label>
			<select id="<?php echo $this->get_field_id('columns'); ?>" name="<?php echo $this->get_field_name('columns'); ?>" class="widefat">
				<option value="2" <?php selected(2, $columns) ?>>2</option>
				<option value="3" <?php selected(3, $columns) ?>>3</option>
				<option value="4" <?php selected(4, $columns) ?>>4</option>
			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php esc_html_e('Link text', 'xstore'); ?>: <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" /></label></p>

		<p>
			<input type="checkbox" <?php checked( true, $spacing, true); ?> id="<?php echo $this->get_field_id('spacing'); ?>" name="<?php echo $this->get_field_name('spacing'); ?>">
			<label for="<?php echo $this->get_field_id('spacing'); ?>"><?php esc_html_e('Without spacing', 'xstore'); ?></label>
		</p>
		<?php

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = trim(strip_tags($new_instance['username']));
		$instance['number'] = !absint($new_instance['number']) ? 9 : $new_instance['number'];
		$instance['columns'] = !absint($new_instance['columns']) ? 3 : $new_instance['columns'];
		$instance['size'] = (($new_instance['size'] == 'thumbnail' || $new_instance['size'] == 'medium' || $new_instance['size'] == 'large' || $new_instance['size'] == 'small') ? $new_instance['size'] : 'thumbnail');
		$instance['target'] = (($new_instance['target'] == '_self' || $new_instance['target'] == '_blank') ? $new_instance['target'] : '_self');
		$instance['link'] = strip_tags($new_instance['link']);
		$instance['slider'] = ($new_instance['slider'] != '') ? true : false;
		$instance['spacing'] = ($new_instance['spacing'] != '') ? true : false;
		return $instance;
	}

	// based on https://gist.github.com/cosmocatalano/4544576
	function scrape_instagram( $username, $slice = 9 ) {
		$username = strtolower( $username );
		$is_hash = ( substr( $username, 0, 1) == '#' );
		if (  false === ( $instagram = get_transient( 'instagram-media-new-'.sanitize_title_with_dashes( $username ) ) ) ) {
			$request_param = ( $is_hash ) ? 'explore/tags/' . substr( $username, 1) : trim( $username );
			$remote = wp_remote_get( 'http://instagram.com/'. $request_param );
			if ( is_wp_error( $remote ) )
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'xstore' ) );
			if ( 200 != wp_remote_retrieve_response_code( $remote ) )
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'xstore' ) );
			$shards = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], TRUE );

			if ( !$insta_array )
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'xstore' ) );
			// old style
			if ( isset( $insta_array['entry_data']['UserProfile'][0]['userMedia'] ) ) {
				$images = $insta_array['entry_data']['UserProfile'][0]['userMedia'];
				$type = 'old';
			// new style
			} else if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
				$type = 'new';
			} elseif( $is_hash && isset( $insta_array['entry_data']['TagPage'][0]['tag']['media']['nodes'] )) {
				$images = $insta_array['entry_data']['TagPage'][0]['tag']['media']['nodes'];
				$type = 'new';
			}
			else {
				return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'xstore' ) );
			}
			if ( !is_array( $images ) )
				return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'xstore' ) );
			$instagram = array();
			switch ( $type ) {
				case 'old':
					foreach ( $images as $image ) {
						if ( $image['user']['username'] == $username ) {
							$image['link']						  = preg_replace( "/^http:/i", "", $image['link'] );
							$image['images']['thumbnail']		   = preg_replace( "/^http:/i", "", $image['images']['thumbnail'] );
							$image['images']['standard_resolution'] = preg_replace( "/^http:/i", "", $image['images']['standard_resolution'] );
							$image['images']['low_resolution']	  = preg_replace( "/^http:/i", "", $image['images']['low_resolution'] );
							$instagram[] = array(
								'description'   => $image['caption']['text'],
								'link'		  	=> $image['link'],
								'time'		  	=> $image['created_time'],
								'comments'	  	=> $image['comments']['count'],
								'likes'		 	=> $image['likes']['count'],
								'thumbnail'	 	=> $image['images']['thumbnail'],
								'large'		 	=> $image['images']['standard_resolution'],
								'small'		 	=> $image['images']['low_resolution'],
								'type'		  	=> $image['type']
							);
						}
					}
				break;
				default:
					foreach ( $images as $image ) {
						$image['thumbnail_src'] = preg_replace( "/^https:/i", "", $image['thumbnail_src'] );
						$image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
						$image['medium'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
						$image['large'] = $image['thumbnail_src'];
						$image['display_src'] = preg_replace( "/^https:/i", "", $image['display_src'] );
						if ( $image['is_video'] == true ) {
							$type = 'video';
						} else {
							$type = 'image';
						}
						$caption = esc_html__( 'Instagram Image', 'xstore' );
						if ( ! empty( $image['caption'] ) ) {
							$caption = $image['caption'];
						}
						$instagram[] = array(
							'description'   => $caption,
							'link'		  	=> '//instagram.com/p/' . $image['code'],
							'time'		  	=> $image['date'],
							'comments'	  	=> $image['comments']['count'],
							'likes'		 	=> $image['likes']['count'],
							'thumbnail'	 	=> $image['thumbnail'],
							'medium'		=> $image['medium'],
							'large'			=> $image['large'],
							'original'		=> $image['display_src'],
							'type'		  	=> $type
						);
					}
				break;

			}

			// do not set an empty transient - should help catch private or empty accounts
			if ( ! empty( $instagram ) ) {
				$instagram = etheme_encoding( serialize( $instagram ) );
				set_transient( 'instagram-media-new-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
			}
		}
		if ( ! empty( $instagram ) ) {
			$instagram = unserialize( etheme_decoding( $instagram ) );
			return array_slice( $instagram, 0, $slice );
		} else {
			return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'xstore' ) );
		}
	}

	function images_only($media_item) {

		if ($media_item['type'] == 'image')
			return true;

		return false;
	}
}
