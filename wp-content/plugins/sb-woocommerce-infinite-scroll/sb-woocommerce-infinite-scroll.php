<?php
/*
	Plugin Name: WooCommerce Infinite Scroll and Ajax Pagination
	Description: Load your WooCommerce products on page scroll. No need to click next button.
	Plugin URI: http://www.sbthemes.com/plugins/scroll-animation-wordpress-plugin/
	Version: 1.0
	Author: SB Themes
	Author URI: http://codecanyon.net/user/sbthemes/portfolio?ref=sbthemes
*/

function sbwis_init() {
	ob_start();
}
add_action('init', 'sbwis_init');
require_once('admin/sb-admin-panel.php');
class SB_WooCommerce_Infinite_Scroll {
	
	public $plugin_version 	= '1.0';
	public $db_version 		= '1.0';
	public $plugin_name 	= 'WooCommerce Infinite Scroll and Ajax Pagination';
	public $menu_text 		= 'WooCommerce Infinite Scroll';
	public $plugin_slug 	= 'sb-woocommerce-infinite-scroll';

	public $plugin_dir_url;
	public $plugin_dir_path;

	public $sb_admin;
	public $animation_styles;
	
	//Initialize plugin
	function __construct() {
		
		$this->plugin_dir_url 	= plugin_dir_url(__FILE__);
		$this->plugin_dir_path 	= plugin_dir_path(__FILE__);
		
		$this->sb_admin   = new SB_Infinite_Scroll_Admin($this);
		
		add_filter('plugin_action_links', array($this, 'sb_plugin_action_links'), 10, 2);			//Add settings link in plugins page
		register_activation_hook(__FILE__, array($this, 'sb_active_plugin'));						//Plugin Activation Hook
		add_action('wp_enqueue_scripts', array($this, 'sb_enqueue_scripts'));						//Including Required Scripts for Frontend
		add_action('wp_footer', array($this, 'print_script_in_footer'));							//Add script in footer
		add_filter('woocommerce_placeholder_img_src', array($this, 'sb_woo_placeholder_img_src'));	//WooCommerce placeholder
		
		$settings = $this->sb_admin->get_infinite_scroll_setting();
		
		if($settings['lazyload'] == 1) {
			$is_smartphone = $this->is_smartphone();
			if($is_smartphone && $settings['lazyload_mobile'] == 0) {
				add_filter('post_thumbnail_html', array($this, 'sb_lazy_load_image'), PHP_INT_MAX);
			}
			if(!$is_smartphone) {
				add_filter('post_thumbnail_html', array($this, 'sb_lazy_load_image'), PHP_INT_MAX);
			}
		}
		
		if(!empty($settings['products_per_page'])) {
			add_filter( 'loop_shop_per_page', create_function( '$cols', 'return '.$settings['products_per_page'].';' ), PHP_INT_MAX );
		}
	}
	
	//Plugin Activation Hook
	function sb_active_plugin() {
		global $wpdb;
		//Importing default settings
		
		$current_settings = get_option('sbwis_settings');
		if(!$current_settings) {
			$default_settings = "YToyNTp7czo2OiJzdGF0dXMiO3M6MToiMSI7czoxNToicGFnaW5hdGlvbl90eXBlIjtzOjE1OiJpbmZpbml0ZV9zY3JvbGwiO3M6MjY6Im1vYmlsZV9wYWdpbmF0aW9uX3NldHRpbmdzIjtzOjE6IjEiO3M6MjI6Im1vYmlsZV9wYWdpbmF0aW9uX3R5cGUiO3M6MTY6ImxvYWRfbW9yZV9idXR0b24iO3M6MTE6ImJyZWFrX3BvaW50IjtzOjM6Ijc2NyI7czo5OiJhbmltYXRpb24iO3M6NjoiZmFkZUluIjtzOjE3OiJwcm9kdWN0c19wZXJfcGFnZSI7czowOiIiO3M6MjE6Indvb19wbGFjZWhvbGRlcl9pbWFnZSI7czowOiIiO3M6MTU6ImxvYWRpbmdfbWVzc2FnZSI7czoxMDoiTG9hZGluZy4uLiI7czoyMToibG9hZGluZ193cmFwcGVyX2NsYXNzIjtzOjA6IiI7czoxNjoiZmluaXNoZWRfbWVzc2FnZSI7czoyOToiTm8gbW9yZSBwcm9kdWN0cyBhdmFpbGFibGUuLi4iO3M6MTM6ImxvYWRpbmdfaW1hZ2UiO3M6MTAzOiJodHRwOi8vbG9jYWxob3N0L3dvcmRwcmVzcy93cC1jb250ZW50L3BsdWdpbnMvc2Itd29vY29tbWVyY2UtaW5maW5pdGUtc2Nyb2xsL2Fzc2V0cy9pbWcvYWpheC1sb2FkZXIuZ2lmIjtzOjIxOiJsb2FkX21vcmVfYnV0dG9uX3RleHQiO3M6MTg6IkxvYWQgTW9yZSBQcm9kdWN0cyI7czoyMjoibG9hZF9tb3JlX2J1dHRvbl9jbGFzcyI7czowOiIiO3M6MTY6ImNvbnRlbnRfc2VsZWN0b3IiO3M6MTE6InVsLnByb2R1Y3RzIjtzOjEzOiJpdGVtX3NlbGVjdG9yIjtzOjEwOiJsaS5wcm9kdWN0IjtzOjE5OiJuYXZpZ2F0aW9uX3NlbGVjdG9yIjtzOjIzOiIud29vY29tbWVyY2UtcGFnaW5hdGlvbiI7czoxMzoibmV4dF9zZWxlY3RvciI7czozMDoiLndvb2NvbW1lcmNlLXBhZ2luYXRpb24gYS5uZXh0IjtzOjE1OiJsYXp5bG9hZF9tb2JpbGUiO3M6MToiMSI7czoyMjoibGF6eWxvYWRfbG9hZGluZ19pbWFnZSI7czoxMDA6Imh0dHA6Ly9sb2NhbGhvc3Qvd29yZHByZXNzL3dwLWNvbnRlbnQvcGx1Z2lucy9zYi13b29jb21tZXJjZS1pbmZpbml0ZS1zY3JvbGwvYXNzZXRzL2ltZy9sYXp5bG9hZC5naWYiO3M6MTM6ImJ1ZmZlcl9waXhlbHMiO3M6MjoiNTAiO3M6OToic2Nyb2xsdG9wIjtzOjE6IjEiO3M6ODoic2Nyb2xsdG8iO3M6MTA6Imh0bWwsIGJvZHkiO3M6Nzoib25zdGFydCI7czowOiIiO3M6ODoib25maW5pc2giO3M6MDoiIjt9";
			$settings = base64_decode($default_settings);
			$settings = @unserialize($settings);
			$settings['loading_image'] = $this->plugin_dir_url.'/assets/img/ajax-loader.gif';
			$settings['lazyload_loading_image'] = $this->plugin_dir_url.'/assets/img/lazyload.gif';
			update_option('sbwis_settings', $settings);
		}
		//Adding DB Version to database
		update_option('sbwis_db_version', $this->db_version);
		
	}
	
	//Add settings link in plugins page
	function sb_plugin_action_links($links, $file) {
		if ($file == plugin_basename( __FILE__ )) {
			$sbsa_links = '<a href="'.get_admin_url().'options-general.php?page='.$this->plugin_slug.'">'.__('Settings').'</a>';
			// Make the 'Settings' link appear first
			array_unshift( $links, $sbsa_links );
		}
		return $links;
	}
	
	//Including Required Scripts on Frontend
	function sb_enqueue_scripts() {
		wp_enqueue_style('sb-style', $this->plugin_dir_url.'assets/css/sbsa.css', array(), $this->plugin_version);
		wp_enqueue_style('sb-animate-style', $this->plugin_dir_url.'assets/css/animate.css', array(), $this->plugin_version);
		wp_enqueue_script('jquery');
		
	}
	
	//Get Animation Style Array
	function get_animation_style() {
		require_once $this->plugin_dir_path.'/includes/sb-animation-styles.php';
		return $this->animation_styles;
	}
	
	//Print script in footer
	function print_script_in_footer() {
		require_once $this->plugin_dir_path.'/includes/sbis.js.php';
	}
	
	//WooCommerce placeholder image
	function sb_woo_placeholder_img_src($src) {
		$settings = $this->sb_admin->get_infinite_scroll_setting();
		$woo_placeholder_image = trim($settings['woo_placeholder_image']);
		if(!empty($woo_placeholder_image)) {
			return $settings['woo_placeholder_image'];
		}
		return $src;
	}
	
	//Lazy Load Image
	function sb_lazy_load_image($content) {
		$content = preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', array($this, 'sb_replace_callback'), $content);
		return $content;
	}
	
	//Lazy Load replace src
	function sb_replace_callback($matches){
	  	$dummy_image = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
		if (substr_count($matches[0], 'sb-lazy-src=') > 0) {
			return $matches[0];
	  	}
		$data = '<img' . $matches[1] . 'src="' . $dummy_image . '" sb-lazy-src="' . $matches[2] . '"' . $matches[3].' >';
		return preg_replace('/class\s*=\s*"/i', 'class="sb-lazy-img ', $data);
	}
	
	//Check if device is smartphone
	function is_smartphone(){
    	$useragents = array(
        	'Android.*Mobile','Windows.*Phone','dream','CUPCAKE','blackberry','incognito','webmate','symbian','smartphone','alcatel','amoi','android','avantgo','benq','cell','cricket','docomo','elaine','htc','iemobile','iphone','ipaq','ipod','j2me','java','midp','mini','mmp','mobi','motorola','nec-','nokia','palm','panasonic','philips','phone','playbook','sagem','sharp','sie-','silk','sony','t-mobile','telus','up\.browser','up\.link','vodafone','wap','webos','wireless','xda','xoom','zte'
    	);
	  
    	$search_pattern = '/' . implode('|', $useragents) . '/i';
	  
	  	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

	  	return preg_match($search_pattern, $useragent);
	}
}
$sb_infinite_scroll = new SB_WooCommerce_Infinite_Scroll();


