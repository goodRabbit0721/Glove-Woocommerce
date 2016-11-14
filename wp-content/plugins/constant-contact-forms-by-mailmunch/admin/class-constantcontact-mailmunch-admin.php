<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.mailmunch.co
 * @since      2.0.0
 *
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Constantcontact_Mailmunch
 * @subpackage Constantcontact_Mailmunch/admin
 * @author     MailMunch <info@mailmunch.co>
 */
class Constantcontact_Mailmunch_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The ID of this plugin's 3rd party integration.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $integration_name    The ID of this plugin's 3rd party integration.
	 */
	private $integration_name;	

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The MailMunch Api object.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $mailmunch_api    The MailMunch Api object.
	 */
	private $mailmunch_api;


	public function __construct( $plugin_name, $integration_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->integration_name = $integration_name;		
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Constantcontact_Mailmunch_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Constantcontact_Mailmunch_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/constantcontact-mailmunch-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Constantcontact_Mailmunch_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Constantcontact_Mailmunch_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/constantcontact-mailmunch-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function sign_up() {
		$this->initiate_api();
		$email = $_POST['email'];
		$password = $_POST['password'];
		echo json_encode($this->mailmunch_api->signUpUser($email, $password, $_POST['site_name'], $_POST['site_url']));
		exit;
	}

	public function sign_in() {
		$this->initiate_api();
		$email = $_POST['email'];
		$password = $_POST['password'];
		echo json_encode($this->mailmunch_api->signInUser($email, $password));
		exit;
	}

	public function delete_widget() {
		$this->initiate_api();
		echo json_encode($this->mailmunch_api->deleteWidget($_POST['widget_id']));
		exit;
	}

	/**
	 * Register menu for the admin area
	 *
	 * @since    2.0.0
	 */
	public function menu() {
		add_options_page( $this->integration_name, $this->integration_name, 'manage_options', CONSTANTCONTACT_MAILMUNCH_SLUG, array($this, 'get_dashboard_html'));
		add_menu_page( $this->integration_name, $this->integration_name, 'manage_options', CONSTANTCONTACT_MAILMUNCH_SLUG, array($this, 'get_dashboard_html'), plugins_url( 'img/icon.png', __FILE__ ), 104.786);

		add_submenu_page( CONSTANTCONTACT_MAILMUNCH_SLUG, $this->integration_name, 'Forms', 'manage_options', CONSTANTCONTACT_MAILMUNCH_SLUG, array($this, 'get_dashboard_html') );
		add_submenu_page( CONSTANTCONTACT_MAILMUNCH_SLUG, $this->integration_name. ' Settings', 'Settings', 'manage_options', CONSTANTCONTACT_MAILMUNCH_SLUG. '-settings', array($this, 'settings_page') );
	}

	/**
	 * Activation redirect for admin area
	 *
	 * @since    2.0.4
	 */
	public function activation_redirect() {
		if (get_option(CONSTANTCONTACT_MAILMUNCH_PREFIX. '_activation_redirect', 'true') == 'true') {
			update_option(CONSTANTCONTACT_MAILMUNCH_PREFIX. '_activation_redirect', 'false');
			wp_redirect(esc_url(admin_url('admin.php?page='. CONSTANTCONTACT_MAILMUNCH_SLUG)));
			exit();
		}
	}

	/**
	 * Check and store installation/activation date
	 *
	 * @since    2.1.4
	 */
	public function check_installation_date() {
		$activation_date = get_option( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_activation_date' );
		if (!$activation_date) {
			add_option( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_activation_date', strtotime( "now" ) );
		}
	}

	/**
	 * Review notice after two weeks of usage
	 *
	 * @since    2.1.4
	 */
	public function review_us_notice() {
		$show_notice = true;
		$past_date = strtotime( '-14 days' );
		$activation_date = get_option( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_activation_date' );

		$notice_dismissed = get_option( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_dismiss_review_notice' );
		if ($notice_dismissed == 'true') {
			$show_notice = false;
		} elseif (!in_array(get_current_screen()->base , array( 'dashboard' , 'post' , 'edit' )) && strpos(get_current_screen()->base , CONSTANTCONTACT_MAILMUNCH_SLUG) == false) {
			$show_notice = false;
		} elseif (!current_user_can( 'install_plugins' )) {
			$show_notice = false;
		} elseif ( !$activation_date || $past_date < $activation_date ) {
			$show_notice = false;
		}

		if ($show_notice) {
			$review_url = 'https://wordpress.org/support/plugin/'. CONSTANTCONTACT_MAILMUNCH_PLUGIN_DIRECTORY. '/reviews/#new-post';
			$dismiss_url = esc_url_raw( add_query_arg( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_dismiss_review_notice', '1', admin_url() ) );

			$review_message = '<div class="mailmunch-review-logo"><img src="'.plugins_url( 'admin/img/constantcontact_logo.png', dirname(__FILE__) ) .'" /></div>';
			$review_message .= sprintf( __( "You have been using <strong>%s</strong> for a few weeks now. We hope you are enjoying the features. Please consider leaving us a nice review. Reviews help people find our plugin and lets you provide us with useful feedback which helps us improve." , CONSTANTCONTACT_MAILMUNCH_SLUG ), $this->plugin_name );
			$review_message .= "<div class='mailmunch-buttons'>";
			$review_message .= sprintf( "<a href='%s' target='_blank' class='button-secondary'><span class='dashicons dashicons-star-filled'></span>" . __( "Leave a Review" , CONSTANTCONTACT_MAILMUNCH_SLUG ) . "</a>", $review_url );
			$review_message .= sprintf( "<a href='%s' target='_blank' class='button-secondary'><span class='dashicons dashicons-no-alt'></span>" . __( "Dismiss" , CONSTANTCONTACT_MAILMUNCH_SLUG ) . "</a>", $dismiss_url );
			$review_message .= "</div>";
?>
			<div class="mailmunch-review-notice">
				<?php echo $review_message; ?>
			</div>
<?php
		}
	}

	/**
	 * Dismiss review notice
	 *
	 * @since    2.1.4
	 */
	public function dismiss_review_notice() {
		if ( isset( $_GET[CONSTANTCONTACT_MAILMUNCH_PREFIX. '_dismiss_review_notice'] ) ) {
			add_option( CONSTANTCONTACT_MAILMUNCH_PREFIX. '_dismiss_review_notice', 'true' );
		}
	}

	/**
	 * Adds settings link for plugin
	 *
	 * @since    2.0.0
	 */
	public function settings_link($links) {
	  $settings_link = '<a href="admin.php?page='.CONSTANTCONTACT_MAILMUNCH_SLUG.'">Settings</a>';
	  array_unshift($links, $settings_link);
	  return $links;
	}

	/**
	 * Get current step
	 *
	 * @since    2.0.0
	 */
	public function getStep() {
		if (isset($_GET['step'])) {
			$step = $_GET['step'];
			if ($step == 'skip_onboarding') {
				$this->mailmunch_api->setSkipOnBoarding();
				$step = '';
			}
		}
		elseif ($this->mailmunch_api->skipOnBoarding()) { $step = ''; }
		else {
			$step = 'connect';
			$ccAccessToken = get_option($this->mailmunch_api->getPrefix(). 'constantcontact_access_token');
			$ccListId = get_option($this->mailmunch_api->getPrefix(). 'constantcontact_list_id');
			if (!empty($ccAccessToken)) $step = 'integrate';
			if (!empty($ccListId)) $step = '';
		}
		return $step;
	}

	public function initiate_api() {
		if (empty($this->mailmunch_api)) {
			$this->mailmunch_api = new ConstantContact_Mailmunch_Api();
		}
		return $this->mailmunch_api;
	}

	/**
	 * Settings Page
	 *
	 * @since    2.0.0
	 */
	public function settings_page() {
		$this->initiate_api();
		if ($_POST) {
			$this->mailmunch_api->setSetting('auto_embed', $_POST['auto_embed']);
		}
		require_once(plugin_dir_path(__FILE__) . 'partials/constantcontact-mailmunch-settings.php');
	}

	/**
	 * Get Dashboard HTML
	 *
	 * @since    2.0.0
	 */
	public function get_dashboard_html() {

		$this->initiate_api();

		switch ($this->getStep()) {
			case 'sign_out':
				$this->mailmunch_api->signOutUser();
				require_once(plugin_dir_path(__FILE__) . 'partials/constantcontact-mailmunch-connect.php');
			break;

			case 'connect':
				require_once(plugin_dir_path(__FILE__) . 'partials/constantcontact-mailmunch-connect.php');
			break;

			case 'integrate':
				$var = $this->mailmunch_api->getPrefix(). 'constantcontact_access_token';
				if (isset($_POST['access_token'])) {
					update_option($var, $_POST['access_token']);
				}

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/constantcontact_api.php';
				$constantcontactApi = new ConstantcontactApi(get_option($var));
				$lists = $constantcontactApi->getLists();
				require_once(plugin_dir_path( __FILE__ ) . 'partials/constantcontact-mailmunch-integrate.php');
			break;

			default:
				$var = $this->mailmunch_api->getPrefix(). 'constantcontact_access_token';
				if (isset($_POST['list_id'])) {
					update_option($this->mailmunch_api->getPrefix(). 'constantcontact_list_id', $_POST['list_id']);
					$accessToken = get_option($this->mailmunch_api->getPrefix(). 'constantcontact_access_token');

					$this->mailmunch_api->createIntegration($accessToken, $_POST['list_id']);
				}
				require_once(plugin_dir_path( __FILE__ ) . 'partials/constantcontact-mailmunch-admin-display.php');
		}

		require_once(plugin_dir_path( __FILE__ ) . 'partials/constantcontact-mailmunch-modals.php');
	}

}
