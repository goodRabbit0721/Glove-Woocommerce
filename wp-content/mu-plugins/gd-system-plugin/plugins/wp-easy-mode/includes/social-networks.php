<?php

$username = sanitize_title( _x( 'username', 'Must be lowercase and use URL-safe characters', 'wp-easy-mode' ) );
$channel  = sanitize_title( _x( 'channel', 'Must be lowercase and use URL-safe characters', 'wp-easy-mode' ) );
$company  = sanitize_title( _x( 'company', 'Must be lowercase and use URL-safe characters', 'wp-easy-mode' ) );
$board    = sanitize_title( _x( 'board', 'Must be lowercase and use URL-safe characters', 'wp-easy-mode' ) );

$social_networks = [
	'facebook' => [
		'icon'   => 'facebook-official',
		'label'  => __( 'Facebook', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'facebook', "https://www.facebook.com/{$username}" ),
		'select' => $username,
	],
	'twitter' => [
		'label'  => __( 'Twitter', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'twitter', "https://twitter.com/{$username}" ),
		'select' => $username,
	],
	'googleplus' => [
		'icon'  => 'google-plus',
		'label'  => __( 'Google+', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'googleplus', "https://google.com/+{$username}" ),
		'select' => $username,
	],
	'linkedin' => [
		'icon'  => 'linkedin-square',
		'label'  => __( 'LinkedIn', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'linkedin', "https://www.linkedin.com/in/{$username}" ),
		'select' => $username,
	],
	'pinterest' => [
		'label'  => __( 'Pinterest', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'pinterest', "https://www.pinterest.com/{$username}" ),
		'select' => $username,
	],
	'youtube' => [
		'label'  => __( 'YouTube', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'youtube', "https://www.youtube.com/user/{$username}" ),
		'select' => $username,
	],
	'vimeo' => [
		'label'  => __( 'Vimeo', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'vimeo', "https://vimeo.com/{$username}" ),
		'select' => $username,
	],
	'flickr' => [
		'label'  => __( 'Flickr', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'flickr', "https://www.flickr.com/photos/{$username}" ),
		'select' => $username,
	],
	'foursquare' => [
		'label'  => __( 'Foursquare', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'foursquare', "https://foursquare.com/{$username}" ),
		'select' => $username,
	],
	'github' => [
		'label'  => __( 'GitHub', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'github', "https://github.com/{$username}" ),
		'select' => $username,
	],
	'slack' => [
		'label'  => __( 'Slack', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'slack', "https://{$channel}.slack.com/" ),
		'select' => $channel,
	],
	'skype' => [
		'label'  => __( 'Skype', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'skype', "skype:{$username}?chat" ),
		'select' => $username,
	],
	'soundcloud' => [
		'label'  => __( 'SoundCloud', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'soundcloud', "https://soundcloud.com/{$username}" ),
		'select' => $username,
	],
	'tripadvisor' => [
		'label' => __( 'TripAdvisor', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'tripadvisor', 'https://www.tripadvisor.com/' ),
	],
	'wordpress' => [
		'label'  => __( 'WordPress', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'wordpress', "https://profiles.wordpress.org/{$username}" ),
		'select' => $username,
	],
	'yelp' => [
		'label'  => __( 'Yelp', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'yelp', "http://www.yelp.com/biz/{$company}" ),
		'select' => $company,
	],
	'amazon' => [
		'label' => __( 'Amazon', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'amazon', 'https://www.amazon.com/' ),
	],
	'instagram' => [
		'label'  => __( 'Instagram', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'instagram', "https://www.instagram.com/{$username}" ),
		'select' => $username,
	],
	'vine' => [
		'label'  => __( 'Vine', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'vine', "https://vine.co/{$username}" ),
		'select' => $username,
	],
	'reddit' => [
		'label'  => __( 'reddit', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'reddit', "https://www.reddit.com/user/{$username}" ),
		'select' => $username,
	],
	'xing' => [
		'label' => __( 'XING', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'xing', 'https://www.xing.com/' ),
	],
	'tumblr' => [
		'label'  => __( 'Tumblr', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'tumblr', "https://{$username}.tumblr.com/" ),
		'select' => $username,
	],
	'whatsapp' => [
		'label' => __( 'WhatsApp', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'whatsapp', 'https://www.whatsapp.com/' ),
	],
	'wechat' => [
		'label' => __( 'WeChat', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'wechat', 'http://www.wechat.com/' ),
	],
	'medium' => [
		'label'  => __( 'Medium', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'medium', "https://medium.com/@{$username}" ),
		'select' => $username,
	],
	'dribbble' => [
		'label'  => __( 'Dribbble', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'dribbble', "https://dribbble.com/{$username}" ),
		'select' => $username,
	],
	'twitch' => [
		'label'  => __( 'Twitch', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'twitch', "https://www.twitch.tv/{$username}" ),
		'select' => $username,
	],
	'vk' => [
		'label' => __( 'VK', 'wp-easy-mode' ),
		'url'   => wpem_get_social_profile_url( 'vk', 'https://vk.com/' ),
	],
	'trello' => [
		'label'  => __( 'Trello', 'wp-easy-mode' ),
		'url'    => wpem_get_social_profile_url( 'trello', "https://trello.com/b/{$board}" ),
		'select' => $board,
	],
];
