<?php
/*
 * Plugin Admin Class : SB_Infinite_Scroll_Admin
 */

class SB_Infinite_Scroll_Admin {
	
	function __construct($parent) {
		$this->parent = $parent;
		add_action('admin_menu', array($this, 'sb_add_menu_page'));												//Add settings Page
		add_action('admin_enqueue_scripts', array($this, 'sb_admin_enqueue_scripts'));							//Including Required Scripts for Backend
		add_action('wp_ajax_save_infinite_scroll_settings', array($this, 'save_infinite_scroll_settings'));		//Fire ajax for add/update settings
		add_action('wp_ajax_import_export_settings', array($this, 'import_export_settings'));			//Import Export Settings Form
		add_action('wp_ajax_import_settings', array($this, 'import_settings'));							//Import Settings
	}
	
	//Including Required Scripts for Backend
	function sb_admin_enqueue_scripts($hook) {
		//Prevent adding scripts if page is not plugin settings page
		$add_scripts_pages = array('settings_page_'.$this->parent->plugin_slug);
		if( !in_array($hook ,$add_scripts_pages) ) {
			return;
		}
		wp_enqueue_style('sb-admin-style', $this->parent->plugin_dir_url.'assets/css/sb-admin.css', array(), $this->parent->plugin_version);
		wp_enqueue_style('sb-animate-style', $this->parent->plugin_dir_url.'assets/css/animate.css', array(), $this->parent->plugin_version);
		wp_enqueue_style('wp-jquery-ui-dialog');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_media();
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('sb-admin', $this->parent->plugin_dir_url.'assets/js/sb-admin.js', array(), $this->parent->plugin_version, true);
		wp_localize_script('sb-admin', 'SB', array('AJAX' => admin_url('admin-ajax.php')));
	}
	
	//Add settings Page
	function sb_add_menu_page() {
		add_submenu_page('options-general.php', $this->parent->menu_text, $this->parent->menu_text, 'manage_options', $this->parent->plugin_slug, array($this, 'sb_admin_settings_page'));
	}
	
	//Including Admin Settings Page File
	function sb_admin_settings_page() {
		require $this->parent->plugin_dir_path.'/admin/sb-admin-panel-form.php';
	}
	
	//Including Single Setting Box File
	function sb_admin_setting_box() {
		require $this->parent->plugin_dir_path.'/admin/sb-admin-panel-setting-box.php';
	}
	
	//Including Single Setting Box File
	function save_infinite_scroll_settings() {
		global $wpdb;
		$return = array();
		extract($_POST);
		
		if(!isset($settings['status']))
			$settings['status'] = 0;
		if(!isset($settings['scrolltop']))
			$settings['scrolltop'] = 0;
		
		update_option('sbwis_settings', $settings);
		
		echo json_encode($return);
		die;
	}
	
	//Get Settings by id
	function get_infinite_scroll_setting() {
		$settings = get_option('sbwis_settings');
		return $settings;
	}
	
	//Get Pagination Type
	function get_pagination_type() {
		return array(
			'infinite_scroll'		=>		'Infinite Scroll',
			'load_more_button'		=>		'Load More Button',
			'ajax_pagination'		=>		'Ajax Pagination'
		);
	}
	
	//Escape DB fields when display
	function sb_display_field($string) {
		return htmlspecialchars(stripslashes($string));
	}
	
	//CSV Import Export Settings
	function import_export_settings() {
		require $this->parent->plugin_dir_path.'/admin/import-export.php';
		die;
	}
	
	function import_settings() {
		$response = '';
		$settings = $_POST['settings'];
		if(trim($settings) != '') {
			$settings = base64_decode($settings);
			$settings = @unserialize($settings);
			if(is_array($settings)) {
				update_option('sbwis_settings', $settings);
				$response = "Settings Imported.";
			} else {
				$response = "Invalid settings format.";
			}
		} else {
			$response = "Please your paste settings before Import.";
		}
		echo $response;
		die;
	}
}



