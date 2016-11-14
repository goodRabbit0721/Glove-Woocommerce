<?php 

if(!class_exists('ETheme_Import')) {

	class ETheme_Import {

		private $_import_url = '';

		private $_widgets_counter = 0;

		private $_folder = '';

		private $_remote_folder = '';

		private $_version = '';

		private $_content = 'full';

		private $_all_widgets = array();

		public $versions = array();

		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
			if( ! defined( 'ETHEME_THEME_SLUG' ) ) return;

			$this->_import_url 	= 'http://8theme.com/import/' . ETHEME_THEME_SLUG . '_versions/';

			$this->_all_widgets 	= require apply_filters('etheme_file_url', ETHEME_THEME . 'widgets-import.php');
			$this->versions 		= require apply_filters('etheme_file_url', ETHEME_THEME . 'versions.php');

			add_action('wp_ajax_etheme_import_ajax', array($this, 'import_data'));
		}

		public function import_data() {
			//delete_option('demo_data_installed');die();
			//sleep(3); echo 'test complete'; die();
			$versions_imported = get_option('versions_imported');

			if( empty( $versions_imported ) ) $versions_imported = array();

			$xml_result = '';

			if(!empty($_POST['version'])) {
				$this->_version = $_POST['version'];
			}

			if( ! empty( $this->versions[ $this->_version ] ) ) {

				do_action('et_before_data_import');

				$to_import = $this->versions[ $this->_version ]['to_import'];

				$this->_folder = ETHEME_THEME_DIR . 'assets/dummy/' . $this->_version . '/';

				$this->_remote_folder = ETHEME_BASE_URI . 'theme/assets/dummy/' . $this->_version . '/';

				if( ! empty( $to_import['content'] ) && ! in_array( $this->_version, $versions_imported ) ) {
					$xml_result = $this->import_xml_file();
				}

				if( ! empty( $to_import['slider'] ) ) {
					for( $i = 0; $i < $to_import['slider']; $i++ ) {
						$slider_result = $this->import_slider( $i );
					}
				}

				if( ! empty( $to_import['menu'] ) && ! in_array( $this->_version, $versions_imported ) ) {
					$this->update_menus();
				}

				if( ! empty( $to_import['widgets'] ) ) {
					$this->update_widgets();
				}

				if( ! empty( $to_import['home_page'] ) ) {
					$this->update_home_page();
				}

				if( ! empty( $to_import['options'] ) ) {
					$this->update_options();
				}
			}

			do_action('et_after_data_import');

			echo '<p><strong>Successfully imported!</strong></p>';

			if($xml_result) {
				echo $xml_result;
			} else {
				//echo '<p>XML not imported.</p>';
			}

			if(isset($slider_result['success']) && $slider_result['success'] != '') {
				echo '<p>Revolutions slider has been successfully imported!</p>';
			}

			$versions_imported[] = $this->_version;

			update_option('versions_imported', $versions_imported);

			die();
		}

		public function import_slider_from_url() {

			$folder = $this->_import_url . $this->_version;
			$sliderZip = $folder . '/slider.zip';
			$slider_data = wp_remote_get($sliderZip);

			if( ! is_wp_error($slider_data) ) {

				$tmpZip = ETHEME_BASE.'/framework/tmp/tempSliderZip.zip';
				file_put_contents($tmpZip, $slider_data['body']);
				return $this->import_slider();
			}
		}

		public function import_slider( $i = 0 ) {

			$zip_file = ( $i > 0 ) ? $this->_folder . 'slider' . $i . '.zip' : $this->_folder . 'slider.zip' ;

			if(!class_exists('RevSlider')) return;

			$revapi = new RevSlider();

			ob_start();

			$slider_result = $revapi->importSliderFromPost(true, true, $zip_file);

			ob_end_clean();

			return $slider_result;

		}

		public function import_xml_from_url() {
			$folder = $this->_import_url . $this->_version;

			$version_xml = $folder.'/content-' . $this->_content . '.xml';

			$version_data = wp_remote_get($version_xml);

			if( ! is_wp_error($version_data)) {

				$tmpxml = ETHEME_BASE.'/framework/tmp/version_data.xml';

				file_put_contents($tmpxml, $version_data['body']);

				return $this->import_xml_file();

			}

			return false;
		}

		public function import_xml_file() {

			$result = false;

			// Load Importer API
			require_once ABSPATH . 'wp-admin/includes/import.php';

			$importerError = false;

			//check if wp_importer, the base importer class is available, otherwise include it
			if ( !class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) )
					require_once($class_wp_importer);
				else
					$importerError = true;
			}


			if($importerError !== false) {
				echo ("The Auto importing script could not be loaded. Please use the wordpress importer and import the XML file that is located in your themes folder manually.");
				return;
			}


			if(class_exists('WP_Importer')) {

				try {

					ob_start();

					add_filter( 'intermediate_image_sizes', array( $this, 'sizes_array') );

					$file_url = $this->_folder . 'dummy.xml';

					$importer = new WP_Import();

					$importer->fetch_attachments = true;

					$importer->import($file_url);

					$result = ob_get_clean();

				} catch (Exception $e) {
					$result = false;
					echo ("Error while importing");
				}

			}

			return $result;

		}

		public function sizes_array( $sizes ) {
			return array();
		}

		/**
		 *
		 */
		public function update_menus(){

			global $wpdb;

			$menuname = 'Main menu';
			$bpmenulocation = 'main-menu';
			$mobilemenulocation = 'mobile-menu';

			$tablename = $wpdb->prefix.'terms';
			$menu_ids = $wpdb->get_results(
				"
			    SELECT term_id
			    FROM ".$tablename."
			    WHERE name= '".$menuname."'
			    "
			);

			// results in array
			foreach($menu_ids as $menu):
				$menu_id = $menu->term_id;
			endforeach;

			$shop_page = get_page_by_title('Shop');

			$itemData =  array(
				'menu-item-object-id'	=> $shop_page->ID,
				'menu-item-parent-id'	=> 0,
				'menu-item-position'  	=> 2,
				'menu-item-object' 		=> 'page',
				'menu-item-type'      	=> 'post_type',
				'menu-item-status'    	=> 'publish'
			);

			wp_update_nav_menu_item($menu_id, 0, $itemData);

			if( !has_nav_menu( $bpmenulocation ) ){
				$locations = get_theme_mod('nav_menu_locations');
				$locations[$bpmenulocation] = $menu_id;
				$locations[$mobilemenulocation] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}

		}

		private function update_widgets() {

			$widgets = $this->_all_widgets[ $this->_version ];

			// We don't want to undo user changes, so we look for changes first.
			$this->_active_widgets = get_option( 'sidebars_widgets' );

			$this->_widgets_counter = 1;

			if( ! empty( $widgets['custom-sidebars'] ) ) {
				foreach ($widgets['custom-sidebars'] as $customsidebar) {
					etheme_add_sidebar( $customsidebar );
				}
			}

			foreach ($widgets['sidebar-widgets'] as $area => $params) {
				if ( ! empty ( $this->_active_widgets[$area] ) && $params['flush'] ) {
					$this->_flush_widget_area($area);
				} else if(! empty ( $this->_active_widgets[$area] ) && ! $params['flush'] ) {
					continue;
				}
				foreach ($params['widgets'] as $widget => $args) {
					$this->_add_widget($area, $widget, $args);
				}
			}

			// Now save the $active_widgets array.
			update_option( 'sidebars_widgets', $this->_active_widgets );

		}

		private function _add_widget( $sidebar, $widget, $options = array() ) {
			$this->_active_widgets[ $sidebar ][] = $widget . '-' . $this->_widgets_counter;
			$widget_content = get_option( 'widget_' . $widget );
			$widget_content[ $this->_widgets_counter ] = $options;
			update_option(  'widget_' . $widget, $widget_content );
			$this->_widgets_counter++;
		}

		private function _flush_widget_area( $area ) {
			unset($this->_active_widgets[ $area ]);
		}

		public function update_home_page() {
			$blog_id = get_page_by_title('Blog');
			$home_page = get_page_by_title('Home ' . $this->_version);
			$pageid = $home_page->ID;
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $pageid );
			update_option( 'page_for_posts', $blog_id->ID );
		}

		public function update_options() {
			global $et_options;

			if(!class_exists('ReduxFrameworkInstances')) return;

			$options_file = $this->_remote_folder . 'options.json';

			$new_options = wp_remote_get($options_file);

			$default_options = require apply_filters('etheme_file_url', ETHEME_THEME . 'default-options.php');

			if( ! is_wp_error( $new_options )) {

				$new_options = json_decode($new_options['body'], true);

				$new_options = wp_parse_args( $new_options, $default_options );

				$new_options = wp_parse_args( $new_options, $et_options );

				$redux = ReduxFrameworkInstances::get_instance( 'et_options' );

				if ( isset ( $redux->validation_ran ) ) {
					unset ( $redux->validation_ran );
				}

				$redux->set_options( $redux->_validate_options( $new_options ) );
			}
		}
	}

	new ETheme_Import();
}