<?php

/**
 * Plugin Name: Subscriptio
 * Plugin URI: http://www.rightpress.net/subscriptio
 * Description: WooCommerce Subscriptions
 * Version: 2.3
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 * Requires at least: 3.6
 * Tested up to: 4.6
 *
 * Text Domain: subscriptio
 * Domain Path: /languages
 *
 * @package Subscriptio
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('SUBSCRIPTIO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SUBSCRIPTIO_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('SUBSCRIPTIO_VERSION', '2.3');
define('SUBSCRIPTIO_OPTIONS_VERSION', '1');
define('SUBSCRIPTIO_SUPPORT_WP', '3.6');
define('SUBSCRIPTIO_SUPPORT_WC', '2.1');

if (!class_exists('Subscriptio')) {

/**
 * Main plugin class
 *
 * @package Subscriptio
 * @author RightPress
 */
class Subscriptio
{
    /***************************************************************************
     * WARNING WARNING WARNING
     * Never enable debug mode on live system! Only enable debug mode if you
     * will be able to DELETE all subscriptions and give it a fresh start!
     **************************************************************************/
    public static $debug = false;
    /***************************************************************************
     * WARNING WARNING WARNING
     * Never enable debug mode on live system! Only enable debug mode if you
     * will be able to DELETE all subscriptions and give it a fresh start!
     **************************************************************************/

    // Singleton instance
    private static $instance = false;

    // Query variables for frontend navigation
    private static $query_vars = array(
        'subscriptions',
        'view-subscription',
        'subscription-address',
        'pause-subscription',
        'resume-subscription',
        'cancel-subscription',
    );

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Load translation
        load_textdomain('subscriptio', WP_LANG_DIR . '/subscriptio/subscriptio-' . apply_filters('plugin_locale', get_locale(), 'subscriptio') . '.mo');
        load_plugin_textdomain('subscriptio', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        // Execute other code when all plugins are loaded
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 1);
    }

    /**
     * Code executed when all plugins are loaded
     *
     * @access public
     * @return void
     */
    public function on_plugins_loaded()
    {
        // Load helper class
        include_once SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/libraries/rightpress-helper.class.php';

        // Check environment
        if (!self::check_environment()) {
            return;
        }

        // Load includes
        foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/*.inc.php') as $filename)
        {
            include $filename;
        }

        // Load abstract classes
        foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/abstract/*.class.php') as $filename) {
            include $filename;
        }

        // Load classes
        foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/*.class.php') as $filename)
        {
            include $filename;
        }

        // Initialize automatic updates
        require_once(plugin_dir_path(__FILE__) . 'includes/classes/libraries/rightpress-updates.class.php');
        RightPress_Updates_8754068::init(__FILE__, SUBSCRIPTIO_VERSION);

        // Initialize plugin configuration
        $this->settings = subscriptio_plugin_settings();

        // Load/parse plugin settings
        $this->opt = $this->get_options();

        // Load payment gateway classes
        $this->load_payment_gateways();

        // Cache objects in admin list view
        $this->cache = array(
            'subscriptions' => array(),
            'transactions'  => array(),
        );

        // Hook to WordPress 'init' action
        add_action('init', array($this, 'on_init_pre'), 1);
        add_action('init', array($this, 'on_init_post'), 99);

        // Admin-only hooks
        if (is_admin() && !defined('DOING_AJAX')) {

            // Additional Plugins page links
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugins_page_links'));

            // Add settings page menu link
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'plugin_options_setup'));

            // Load backend assets conditionally
            if (self::is_subscriptio_page()) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_assets'));
            }

            // ... and load some assets on all pages
            add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_assets_all'));
        }

        // Frontend-only hooks
        else {

            if (!(defined('DOING_AJAX') && DOING_AJAX)) {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
            }
        }

        // Add payment gateways
        add_filter('woocommerce_payment_gateways', array($this, 'add_payment_gateways'));

        // Customer My Account hooks
        add_filter('woocommerce_my_account_my_orders_query', array($this, 'woocommerce_my_orders_query'));
        add_filter('woocommerce_my_account_my_orders_title', array($this, 'woocommerce_my_orders_title'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('wp_loaded', array($this, 'maybe_flush_rewrite_rules'));
        add_filter('rewrite_rules_array', array($this, 'insert_rewrite_rules'));

        if (!Subscriptio::my_account_supports_tabbed_navigation()) {
            add_action('woocommerce_before_my_account', array($this, 'display_customer_subscription_list'), 1);
        }

        // Other hooks
        add_action('restrict_manage_posts', array($this, 'add_list_filters'));
        add_action('manage_subscription_posts_columns', array($this, 'manage_subscription_list_columns'));
        add_action('manage_sub_transaction_posts_columns', array($this, 'manage_transaction_list_columns'));
        add_action('manage_subscription_posts_custom_column', array($this, 'manage_subscription_list_column_values'), 10, 2);
        add_action('manage_sub_transaction_posts_custom_column', array($this, 'manage_transaction_list_column_values'), 10, 2);
        add_filter('parse_query', array($this, 'handle_list_filter_queries'));
        add_filter('bulk_actions-edit-subscription', array($this, 'manage_subscription_list_bulk_actions'));
        add_filter('bulk_actions-edit-sub_transaction', array($this, 'manage_transaction_list_bulk_actions'));
        add_filter('views_edit-subscription', array($this, 'manage_subscription_list_views'));
        add_filter('views_edit-sub_transaction', array($this, 'manage_transaction_list_views'));
        add_filter('posts_join', array($this, 'expand_list_search_context_join'));
        add_filter('posts_where', array($this, 'expand_list_search_context_where'));
        add_filter('posts_groupby', array($this, 'expand_list_search_context_group_by'));
        add_action('woocommerce_before_checkout_form', array($this, 'enforce_registration'), 99);
        add_action('woocommerce_checkout_process', array($this, 'enforce_createaccount_option'), 99);
        add_filter('wc_checkout_params', array($this, 'enforce_registration_js'), 99);
        add_action('before_delete_post', array($this, 'post_deleted_event'));
        add_action('save_post', array($this, 'save_subscription_meta_box'), 9, 2);
        add_action('init', array($this, 'maybe_save_main_site_url'), 1);
        add_action('admin_notices', array($this, 'url_mismatch_notification'));
        add_action('add_meta_boxes', array($this, 'remove_meta_boxes'), 99, 2);
        add_action('admin_init', array($this, 'admin_redirect'));
        add_action('admin_head', array($this, 'admin_remove_new_post_links'));

        // Add shortcode to display customer's subscription list
        add_shortcode('subscriptio_customer_subscriptions', array($this, 'shortcode_customer_subscriptions'));

        // AJAX handler for subscription date change
        add_action('wp_ajax_change_scheduled_date', array('Subscriptio_Subscription', 'ajax_change_scheduled_date'));

        // Debug class
        if (self::$debug) {
            add_action('init', array($this, 'debug'));
        }

        // Intercept PayPal's response
        add_action('parse_request', array($this, 'paypal_express_return_page'), 10);
    }

    /**
     * Debug function run on WordPress init action - do your testing here
     *
     * @access public
     * @return void
     */
    public function debug()
    {

    }

    /**
     * Add settings link on plugins page
     *
     * @access public
     * @param array $links
     * @return void
     */
    public function plugins_page_links($links)
    {
        $settings_link = '<a href="http://url.rightpress.net/8754068-support" target="_blank">'.__('Support', 'subscriptio').'</a>';
        array_unshift($links, $settings_link);
        $settings_link = '<a href="edit.php?post_type=subscription&page=subscriptio_settings">'.__('Settings', 'subscriptio').'</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * WordPress 'init' @ position 1
     *
     * @access public
     * @return void
     */
    public function on_init_pre()
    {
        // Register new post type to store subscriptions
        $this->add_custom_post_types();

        // Register endpoints
        foreach (self::$query_vars as $var) {
            add_rewrite_endpoint($var, EP_ROOT | EP_PAGES);
        }
    }

    /**
     * WordPress 'init' @ position 99
     *
     * @access public
     * @return void
     */
    public function on_init_post()
    {
        // Add menu item to tabbed navigation (works starting from WooCommerce 2.6)
        if (Subscriptio::my_account_supports_tabbed_navigation()) {
            add_filter('woocommerce_account_menu_items', array($this, 'my_account_menu_items'));
            add_action('woocommerce_account_subscriptions_endpoint', array($this, 'display_customer_subscription_list'));
            add_filter('the_title', array($this, 'subscriptions_endpoint_title'));
        }

        // Replace woocommerce_my_account shortcode with our own
        if (shortcode_exists('woocommerce_my_account')) {
            remove_shortcode('woocommerce_my_account');
            include SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/lazy/subscriptio-my-account.class.php';
            add_shortcode(apply_filters('woocommerce_my_account_shortcode_tag', 'woocommerce_my_account'), array($this, 'intercept_woocommerce_my_account_shortcode'));
        }

        // Display related subscriptions on frontend single order view page
        add_action(
            apply_filters('subscriptio_order_view_hook', 'woocommerce_order_details_after_order_table'),
            array($this, 'display_frontend_order_related_subscriptions'),
            apply_filters('subscriptio_order_view_position', 9)
        );
    }

    /**
     * Add menu item to tabbed navigation
     *
     * @access public
     * @param array $items
     * @return array
     */
    public function my_account_menu_items($items)
    {
        // Check if subscription list needs to be displayed
        if (self::display_frontend_subscriptions_list()) {

            $menu_item = array('subscriptions' => __('Subscriptions', 'subscriptio'));

            // Insert after dashboard or after orders
            if (isset($items['dashboard']) || isset($items['orders'])) {
                $where = isset($items['dashboard']) ? 'dashboard' : 'orders';
                $items = RightPress_Helper::insert_to_array_after_key($items, $where, $menu_item);
            }
            // ... or at the beginning of the list
            else {
                $items = array_merge($menu_item, $items);
            }
        }

        return $items;
    }

    /**
     * Change subscriptions endpoint title
     *
     * @access public
     * @param string $title
     * @return string
     */
    public function subscriptions_endpoint_title($title)
    {
        global $wp_query;

        // Check if we are in My Account
        if (!is_null($wp_query) && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {

            // Subscriptions
            if (isset($wp_query->query_vars['subscriptions'])) {
                $title = __('Subscriptions', 'subscriptio');
            }
            // Shipping address
            else if (isset($wp_query->query_vars['subscription-address'])) {
                $title = __('Shipping Address', 'subscriptio');
            }
            // Single subscription
            else {
                foreach (array('view-subscription', 'pause-subscription', 'resume-subscription', 'cancel-subscription') as $var) {
                    if (isset($wp_query->query_vars[$var])) {
                        $subscription = Subscriptio_Subscription::get_by_id($wp_query->query_vars[$var]);
                        $title = __('Subscription', 'subscriptio') . ' ' . $subscription->get_subscription_number();
                        break;
                    }
                }
            }

            // Remove filter so that no further changes can be made to the page title
            remove_filter('the_title', array($this, 'subscriptions_endpoint_title'));
        }

        return $title;
    }

    /**
     * Exctract some options from plugin settings array
     *
     * @access public
     * @param string $name
     * @param bool $split_by_page
     * @return array
     */
    public function options($name, $split_by_page = false)
    {
        $results = array();

        // Iterate over settings array and extract values
        foreach ($this->settings as $page => $page_value) {
            $page_options = array();

            foreach ($page_value['children'] as $section => $section_value) {
                foreach ($section_value['children'] as $field => $field_value) {
                    if (isset($field_value[$name])) {
                        $page_options['subscriptio_' . $field] = $field_value[$name];
                    }
                }
            }

            $results[preg_replace('/_/', '-', $page)] = $page_options;
        }

        $final_results = array();

        if (!$split_by_page) {
            foreach ($results as $value) {
                $final_results = array_merge($final_results, $value);
            }
        }
        else {
            $final_results = $results;
        }

        return $final_results;
    }

    /**
     * Get options saved to database or default options if no options saved
     *
     * @access public
     * @return array
     */
    public function get_options()
    {
        // Get options from database
        $saved_options = get_option('subscriptio_options', array());

        // Get current version (for major updates in future)
        if (!empty($saved_options)) {
            if (isset($saved_options[SUBSCRIPTIO_OPTIONS_VERSION])) {
                $saved_options = $saved_options[SUBSCRIPTIO_OPTIONS_VERSION];
            }
            else {
                // Migrate options here if needed...
            }
        }

        if (is_array($saved_options)) {
            return array_merge($this->options('default'), $saved_options);
        }
        else {
            return $this->options('default');
        }
    }

    /*
     * Update single option
     *
     * @access public
     * @return bool
     */
    public function update_option($key, $value)
    {
        $this->opt[$key] = $value;
        return update_option('subscriptio_options', $this->opt);
    }

    /**
     * Add custom post types
     *
     * @access public
     * @return void
     */
    public function add_custom_post_types()
    {
        /**
         * SUBSCRIPTION
         */

        // Define labels
        $labels = array(
            'name'               => __('Subscriptions', 'subscriptio'),
            'singular_name'      => __('Subscription', 'subscriptio'),
            'add_new'            => __('Add Subscription', 'subscriptio'),
            'add_new_item'       => __('Add New Subscription', 'subscriptio'),
            'edit_item'          => __('Edit Subscription', 'subscriptio'),
            'new_item'           => __('New Subscription', 'subscriptio'),
            'all_items'          => __('Subscriptions', 'subscriptio'),
            'view_item'          => __('View Subscription', 'subscriptio'),
            'search_items'       => __('Search Subscriptions', 'subscriptio'),
            'not_found'          => __('No Subscriptions Found', 'subscriptio'),
            'not_found_in_trash' => __('No Subscriptions Found In Trash', 'subscriptio'),
            'parent_item_colon'  => '',
            'menu_name'          => __('Subscriptions', 'subscriptio'),
        );

        // Define settings
        $args = array(
            'labels'               => $labels,
            'description'          => __('WooCommerce Subscriptions', 'subscriptio'),
            'public'               => false,
            'show_ui'              => true,
            'menu_position'        => 56,
            'capability_type'      => 'post',
            'capabilities'         => array(
                'create_posts'     => 'do_not_allow',
            ),
            'map_meta_cap'         => true,
            'supports'             => array(''),
            'register_meta_box_cb' => array($this, 'add_subscription_meta_box'),
        );

        // Register new post type
        register_post_type('subscription', $args);

        // Register custom taxonomy (subscription status)
        register_taxonomy('subscription_status', 'subscription', array(
            'label'             => __('Status', 'subscriptio'),
            'labels'            => array(
                'name'          => __('Status', 'subscriptio'),
                'singular_name' => __('Status', 'subscriptio'),
            ),
            'public'            => false,
            'show_admin_column' => true,
            'query_var'         => true,
        ));

        // Register custom terms - subscription status
        foreach (Subscriptio_Subscription::get_statuses() as $status_key => $status) {
            if (!term_exists($status_key, 'subscription_status')) {
                wp_insert_term($status['title'], 'subscription_status', array(
                    'slug' => $status_key,
                ));
            }
        }

        /**
         * SUBSCRIPTION TRANSACTION
         */

        // Define labels
        $labels = array(
            'name'               => __('Transactions', 'subscriptio'),
            'singular_name'      => __('Transaction', 'subscriptio'),
            'add_new'            => __('Add Transaction', 'subscriptio'),
            'add_new_item'       => __('Add New Transaction', 'subscriptio'),
            'edit_item'          => __('Edit Transaction', 'subscriptio'),
            'new_item'           => __('New Transaction', 'subscriptio'),
            'all_items'          => __('Transactions', 'subscriptio'),
            'view_item'          => __('View Transaction', 'subscriptio'),
            'search_items'       => __('Search Transactions', 'subscriptio'),
            'not_found'          => __('No Transactions Found', 'subscriptio'),
            'not_found_in_trash' => __('No Transactions Found In Trash', 'subscriptio'),
            'parent_item_colon'  => '',
            'menu_name'          => __('Transactions', 'subscriptio'),
        );

        // Define settings
        $args = array(
            'labels'               => $labels,
            'description'          => __('WooCommerce Subscriptions', 'subscriptio'),
            'public'               => false,
            'show_ui'              => true,
            'show_in_menu'         => 'edit.php?post_type=subscription',
            'menu_position'        => 59,
            'capability_type'      => 'post',
            'capabilities'         => array(
                'create_posts'     => 'do_not_allow',
            ),
            'map_meta_cap'         => true,
            'supports'             => array('title'),
        );

        // Register new post type
        register_post_type('sub_transaction', $args);

        // Edit this custom post type a bit
        add_filter('post_row_actions', array($this, 'sub_transaction_remove_actions'));

        // Register custom taxonomy (transaction action)
        register_taxonomy('sub_transaction_action', 'sub_transaction', array(
            'label'             => __('Action', 'subscriptio'),
            'labels'            => array(
                'name'          => __('Action', 'subscriptio'),
                'singular_name' => __('Action', 'subscriptio'),
            ),
            'public'            => false,
            'show_admin_column' => true,
            'query_var'         => true,
        ));

        // Register custom taxonomy (transaction result)
        register_taxonomy('sub_transaction_result', 'sub_transaction', array(
            'label'             => __('Result', 'subscriptio'),
            'labels'            => array(
                'name'          => __('Result', 'subscriptio'),
                'singular_name' => __('Result', 'subscriptio'),
            ),
            'public'            => false,
            'show_admin_column' => true,
            'query_var'         => true,
        ));

        // Register custom terms - action
        foreach (Subscriptio_Transaction::get_actions() as $action_key => $action) {
            if (!term_exists($action_key, 'sub_transaction_action')) {
                wp_insert_term($action['title'], 'sub_transaction_action', array(
                    'slug' => $action_key,
                ));
            }
        }

        // Register custom terms - result
        foreach (Subscriptio_Transaction::get_results() as $result_key => $result) {
            if (!term_exists($result_key, 'sub_transaction_result')) {
                wp_insert_term($result['title'], 'sub_transaction_result', array(
                    'slug' => $result_key,
                ));
            }
        }
    }

    /**
     * Add filtering capabilities to custom taxonomies for custom post types
     *
     * @access public
     * @return void
     */
    public function add_list_filters()
    {
        global $typenow;
        global $wp_query;

        // Extract selected filter options
        $selected = array();

        foreach (array('subscription_status', 'sub_transaction_action', 'sub_transaction_result') as $taxonomy) {
            if (!empty($wp_query->query[$taxonomy]) && is_numeric($wp_query->query[$taxonomy])) {
                $selected[$taxonomy] = $wp_query->query[$taxonomy];
            }
            else if (!empty($wp_query->query[$taxonomy])) {
                $term = get_term_by('slug', $wp_query->query[$taxonomy], $taxonomy);
                $selected[$taxonomy] = $term ? $term->term_id : 0;
            }
            else {
                $selected[$taxonomy] = 0;
            }
        }

        if ($typenow == 'subscription') {

            // Statuses
            wp_dropdown_categories(array(
                'show_option_all'   =>  __('All statuses', 'subscriptio'),
                'taxonomy'          =>  'subscription_status',
                'name'              =>  'subscription_status',
                'selected'          =>  $selected['subscription_status'],
                'show_count'        =>  true,
                'hide_empty'        =>  false,
            ));
        }
        else if ($typenow == 'sub_transaction') {

            // Actions
            if (!empty($wp_query->query['sub_transaction_action'])) {
                $term = get_term_by('slug', $wp_query->query['sub_transaction_action'], 'sub_transaction_action');
            }
            wp_dropdown_categories(array(
                'show_option_all'   =>  __('All actions', 'subscriptio'),
                'taxonomy'          =>  'sub_transaction_action',
                'name'              =>  'sub_transaction_action',
                'selected'          =>  $selected['sub_transaction_action'],
                'show_count'        =>  true,
                'hide_empty'        =>  false,
            ));

            // Results
            wp_dropdown_categories(array(
                'show_option_all'   =>  __('All results', 'subscriptio'),
                'taxonomy'          =>  'sub_transaction_result',
                'name'              =>  'sub_transaction_result',
                'selected'          =>  $selected['sub_transaction_result'],
                'show_count'        =>  true,
                'hide_empty'        =>  false,
            ));
        }
    }

    /**
     * Handle list filter queries
     *
     * @access public
     * @param object $query
     * @return void
     */
    public function handle_list_filter_queries($query)
    {
        global $pagenow;

        $qv = &$query->query_vars;

        if ($pagenow == 'edit.php' && isset($qv['post_type'])) {

            $taxonomies = array(
                'subscription' => array('subscription_status'),
                'sub_transaction' => array('sub_transaction_action', 'sub_transaction_result'),
            );

            if (!is_array($qv['post_type']) && isset($taxonomies[$qv['post_type']])) {
                foreach ($taxonomies[$qv['post_type']] as $taxonomy) {
                    if (isset($qv[$taxonomy]) && is_numeric($qv[$taxonomy]) && $qv[$taxonomy] != 0) {
                        $term = get_term_by('id', $qv[$taxonomy], $taxonomy);
                        $qv[$taxonomy] = $term->slug;
                    }
                }
            }
        }
    }

    /**
     * Manage subscription list columns
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function manage_subscription_list_columns($columns)
    {
        $new_columns = array();

        foreach ($columns as $column_key => $column) {
            $allowed_columns = array(
                'cb',
            );

            if (in_array($column_key, $allowed_columns)) {
                $new_columns[$column_key] = $column;
            }
        }

        $new_columns['id'] = __('ID', 'subscriptio');
        $new_columns['status'] = __('Status', 'subscriptio');
        $new_columns['subscriptio_product'] = __('Product', 'subscriptio');
        $new_columns['subscriptio_user'] = __('User', 'subscriptio');
        $new_columns['recurring_amount'] = __('Recurring', 'subscriptio');
        $new_columns['last_order'] = __('Last Order', 'subscriptio');
        $new_columns['started'] = __('Started', 'subscriptio');
        $new_columns['payment_due'] = __('Payment Due', 'subscriptio');
        $new_columns['expires'] = __('Expires', 'subscriptio');

        return $new_columns;
    }

    /**
     * Manage transaction list columns
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function manage_transaction_list_columns($columns)
    {
        $new_columns = array();

        foreach ($columns as $column_key => $column) {
            $allowed_columns = array(
                'cb',
            );

            if (in_array($column_key, $allowed_columns)) {
                $new_columns[$column_key] = $column;
            }
        }

        $new_columns['time'] = __('Timestamp', 'subscriptio');
        $new_columns['action'] = __('Action', 'subscriptio');
        $new_columns['result'] = __('Result', 'subscriptio');
        $new_columns['subscription'] = __('Subscription', 'subscriptio');
        $new_columns['order'] = __('Order', 'subscriptio');
        $new_columns['product'] = __('Product', 'subscriptio');
        $new_columns['subscriptio_note'] = __('Note', 'subscriptio');

        return $new_columns;
    }

    /**
     * Manage subscription list column values
     *
     * @access public
     * @param array $column
     * @param int $post_id
     * @return void
     */
    public function manage_subscription_list_column_values($column, $post_id)
    {
        $subscription = $this->load_from_cache('subscriptions', $post_id);

        switch ($column) {

            case 'id':
                RightPress_Helper::print_link_to_post($subscription->id);
                break;

            case 'status':
                echo '<a class="subscription_status_' . $subscription->status . '" href="edit.php?post_type=subscription&amp;subscription_status=' . $subscription->status . '">' . $subscription->status_title . '</a>';
                break;

            case 'subscriptio_product':
                $products = array();

                foreach (Subscriptio_Subscription::get_subscription_items($subscription->id) as $item) {
                    if (!$item['deleted']) {
                        $products[] = RightPress_Helper::get_link_to_post_html($item['product_id'], $item['name'], '', ($item['quantity'] > 1 ? 'x ' . $item['quantity'] : ''));
                    }
                    else {
                        $products[] = $item['name'];
                    }
                }

                echo implode('<br>', $products);
                break;

            case 'subscriptio_user':
                echo Subscriptio::get_user_full_name_link($subscription->user_id, $subscription->user_full_name);
                break;

            case 'recurring_amount':
                echo $subscription->get_formatted_price($subscription->renewal_order_total);
                break;

            case 'last_order':
                if (RightPress_Helper::post_is_active($subscription->last_order_id)) {
                    RightPress_Helper::print_link_to_post($subscription->last_order_id);
                }
                else {
                    echo '#' . $subscription->last_order_id . ' (' . __('deleted', 'subscriptio') . ')';
                }
                break;

            case 'started':
                if ($subscription->started) {
                    $date_format = apply_filters('subscriptio_date_format', get_option('date_format'), 'subscription_list_started');
                    $date = Subscriptio::get_adjusted_datetime($subscription->started, $date_format);
                    $date_time = Subscriptio::get_adjusted_datetime($subscription->started, null, 'subscription_list_started');
                    echo '<span title="' . $date_time . '">' . $date . '</span>';
                }
                else {
                    echo '—';
                }
                break;

            case 'payment_due':
                if ($subscription->payment_due) {
                    $date_format = apply_filters('subscriptio_date_format', get_option('date_format'), 'subscription_list_payment_due');
                    $date = Subscriptio::get_adjusted_datetime($subscription->payment_due, $date_format);
                    $date_time = Subscriptio::get_adjusted_datetime($subscription->payment_due, null, 'subscription_list_payment_due');
                    echo '<span title="' . $date_time . '">' . $date . '</span>';
                }
                else {
                    echo '—';
                }
                break;

            case 'expires':
                if ($subscription->expires) {
                    $date_format = apply_filters('subscriptio_date_format', get_option('date_format'), 'subscription_list_expires');
                    $date = Subscriptio::get_adjusted_datetime($subscription->expires, $date_format);
                    $date_time = Subscriptio::get_adjusted_datetime($subscription->expires, null, 'subscription_list_expires');
                    echo '<span title="' . $date_time . '">' . $date . '</span>';
                }
                else {
                    echo '—';
                }
                break;

            default:
                break;
        }
    }

    /**
     * Manage transaction list column values
     *
     * @access public
     * @param array $column
     * @param int $post_id
     * @return void
     */
    public function manage_transaction_list_column_values($column, $post_id)
    {
        $transaction = $this->load_from_cache('transactions', $post_id);

        switch ($column) {

            case 'time':
                echo Subscriptio::get_adjusted_datetime($transaction->time, null, 'subscription_edit_started');
                break;

            case 'subscription':
                if ($transaction->subscription_id) {
                    if (RightPress_Helper::post_is_active($transaction->subscription_id)) {
                        RightPress_Helper::print_link_to_post($transaction->subscription_id);
                    }
                    else {
                        echo '#' . $transaction->subscription_id . ' (' . __('deleted', 'subscriptio') . ')';
                    }
                }
                break;

            case 'order':
                if ($transaction->order_id) {
                    if (RightPress_Helper::post_is_active($transaction->order_id)) {
                        RightPress_Helper::print_link_to_post($transaction->order_id);
                    }
                    else {
                        echo '#' . $transaction->order_id . ' (' . __('deleted', 'subscriptio') . ')';
                    }
                }
                break;

            case 'product':
                if ($transaction->product_id) {

                    // Is this a variable product?
                    $title = $transaction->variation_id ? sprintf(__('Variation #%1$s of', 'subscriptio'), $transaction->variation_id) . ' #' . $transaction->product_id : '#' . $transaction->product_id;

                    // Is this product still active?
                    if (self::product_is_active($transaction->product_id)) {
                        RightPress_Helper::print_link_to_post($transaction->product_id, $title);
                    }
                    else {
                        echo $title . ' (' . __('deleted', 'subscriptio') . ')';
                    }
                }
                break;

            case 'action':
                echo '<a href="edit.php?post_type=sub_transaction&amp;sub_transaction_action=' . $transaction->action . '">' . $transaction->action_title . '</a>';
                break;

            case 'result':
                echo '<a class="sub_transaction_result_' . $transaction->result . '" href="edit.php?post_type=sub_transaction&amp;sub_transaction_result=' . $transaction->result . '">' . $transaction->result_title . '</a>';
                break;

            case 'subscriptio_note':
                echo $transaction->note;
                break;

            default:
                break;
        }
    }

    /**
     * Load object from cache
     *
     * @access public
     * @param string $type
     * @param int $id
     * @return object
     */
    public function load_from_cache($type, $id)
    {
        if (!isset($this->cache[$type][$id])) {
            $object = $type == 'subscriptions' ? Subscriptio_Subscription::get_by_id($id) : new Subscriptio_Transaction($id);
            if (!$object) {
                return false;
            }
            $this->cache[$type][$id] = $object;
        }
        return $this->cache[$type][$id];
    }

    /**
     * Remove some post actions for custom post type sub_transaction
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function sub_transaction_remove_actions($actions)
    {
        global $post;

        if ($post->post_type == 'sub_transaction') {
            unset($actions['edit']);
            unset($actions['inline hide-if-no-js']);
            unset($actions['trash']);
        }

        return $actions;
    }

    /**
     * Manage subscription list bulk actions
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function manage_subscription_list_bulk_actions($actions)
    {
        $new_actions = array();

        foreach ($actions as $action_key => $action) {
            if (in_array($action_key, array('trash', 'untrash', 'delete'))) {
                $new_actions[$action_key] = $action;
            }
        }

        return $new_actions;
    }

    /**
     * Manage transaction list bulk actions
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function manage_transaction_list_bulk_actions($actions)
    {
        $new_actions = array();

        foreach ($actions as $action_key => $action) {
            if (in_array($action_key, array('trash', 'untrash', 'delete'))) {
                $new_actions[$action_key] = $action;
            }
        }

        return $new_actions;
    }

    /**
     * Manage subscription list views (All, Trash ...)
     *
     * @access public
     * @param array $views
     * @return array
     */
    public function manage_subscription_list_views($views)
    {
        $new_views = array();

        foreach ($views as $view_key => $view) {
            if (in_array($view_key, array('all', 'trash'))) {
                $new_views[$view_key] = $view;
            }
        }

        return $new_views;
    }

    /**
     * Manage transaction list views (All, Trash ...)
     *
     * @access public
     * @param array $views
     * @return array
     */
    public function manage_transaction_list_views($views)
    {
        $new_views = array();

        foreach ($views as $view_key => $view) {
            if (in_array($view_key, array('all', 'trash'))) {
                $new_views[$view_key] = $view;
            }
        }

        return $new_views;
    }

    /**
     * Expand list search context with more fields
     *
     * @access public
     * @param string $join
     * @return string
     */
    public function expand_list_search_context_join($join)
    {
        global $pagenow;
        global $wpdb;

        $post_types = array('subscription', 'sub_transaction');

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && in_array($_GET['post_type'], $post_types) && isset($_GET['s']) && $_GET['s'] != '') {
            $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' pm ON ' . $wpdb->posts . '.ID = pm.post_id ';
        }

        return $join;
    }

    /**
     * Expand list search context with more fields
     *
     * @access public
     * @param string $where
     * @return string
     */
    public function expand_list_search_context_where($where)
    {
        global $pagenow;
        global $wpdb;

        // Define post types with search contexts, meta field whitelist (searchable meta fields) etc
        $post_types = array(
            'subscription' => array(
                'contexts' => array(
                    'subscription'  => 'ID',
                    'user'          => 'user_id',
                    'name'          => 'user_full_name',
                    'order'         => 'all_order_ids',
                    'product'       => 'product_id',
                    'variation'     => 'variation_id',
                ),
                'meta_whitelist' => array(
                    'product_name',
                    'user_full_name',
                    'all_order_ids',
                    'product_id',
                    'variation_id',
                ),
            ),
            'sub_transaction' => array(
                'contexts' => array(
                    'order'         => 'order_id',
                    'product'       => 'product_id',
                    'variation'     => 'variation_id',
                    'subscription'  => 'subscription_id',
                ),
                'meta_whitelist' => array(
                    'order_id',
                    'product_id',
                    'variation_id',
                    'subscription_id',
                ),
            ),
        );

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && isset($post_types[$_GET['post_type']]) && !empty($_GET['s'])) {

            $search_phrase = trim($_GET['s']);
            $exact_match = false;
            $context = null;

            // Exact match?
            if (preg_match('/^\".+\"$/', $search_phrase) || preg_match('/^\'.+\'$/', $search_phrase)) {
                $exact_match = true;
                $search_phrase = substr($search_phrase, 1, -1);
            }
            else if (preg_match('/^\\\\\".+\\\\\"$/', $search_phrase) || preg_match('/^\\\\\'.+\\\\\'$/', $search_phrase)) {
                $exact_match = true;
                $search_phrase = substr($search_phrase, 2, -2);
            }
            // Or search with context?
            else {
                foreach ($post_types[$_GET['post_type']]['contexts'] as $context_key => $context_value) {
                    if (preg_match('/^' . $context_key . '\:/i', $search_phrase)) {
                        $context = $context_value;
                        $search_phrase = trim(preg_replace('/^' . $context_key . '\:/i', '', $search_phrase));
                        break;
                    }
                }
            }

            // Search by ID?
            if ($context == 'ID') {
                $replacement = $wpdb->prepare(
                    '(' . $wpdb->posts . '.ID LIKE %s)',
                    $search_phrase
                );
            }

            // Search within other context
            else if ($context) {
                $replacement = $wpdb->prepare(
                    '(pm.meta_key LIKE %s) AND (pm.meta_value LIKE %s)',
                    $context,
                    $search_phrase
                );
            }

            // Regular search
            else {
                $whitelist = 'pm.meta_key IN (\'' . join('\', \'', $post_types[$_GET['post_type']]['meta_whitelist']) . '\')';

                // Exact match?
                if ($exact_match) {
                    $replacement = $wpdb->prepare(
                        '(' . $wpdb->posts . '.ID LIKE %s) OR (pm.meta_value LIKE %s)',
                        $search_phrase,
                        $search_phrase
                    );
                    $replacement = '(' . $whitelist . ' AND ' . $replacement . ')';

                }

                // Regular match
                else {
                    $replacement = '(' . $whitelist . ' AND ((' . $wpdb->posts . '.ID LIKE $1) OR (pm.meta_value LIKE $1)))';
                }
            }

            $where = preg_replace('/\(\s*' . $wpdb->posts . '.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/', $replacement, $where);
        }

        return $where;
    }

    /**
     * Expand list search context with more fields - group results by id
     *
     * @access public
     * @param string $groupby
     * @return string
     */
    public function expand_list_search_context_group_by($groupby)
    {
        global $pagenow;
        global $wpdb;

        $post_types = array('subscription', 'sub_transaction');

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && in_array($_GET['post_type'], $post_types) && isset($_GET['s']) && $_GET['s'] != '') {
            $groupby = $wpdb->posts . '.ID';
        }

        return $groupby;
    }

    /**
     * Maybe display URL mismatch notification
     *
     * @access public
     * @return void
     */
    public function url_mismatch_notification()
    {
        if (!current_user_can(apply_filters('subscriptio_capability', 'manage_options', 'notices'))) {
            return;
        }

        $current_url = $this->get_main_site_url();

        if (!empty($_POST['subscriptio_url_mismatch_action'])) {
            if ($_POST['subscriptio_url_mismatch_action'] == 'change') {
                $this->save_main_site_url($this->get_main_site_url());
            }
            else if ($_POST['subscriptio_url_mismatch_action'] == 'ignore') {
                $this->update_option('subscriptio_ignore_url_mismatch', $current_url);
            }
        }
        else if (!self::is_main_site() && (empty($this->opt['subscriptio_ignore_url_mismatch']) || $this->opt['subscriptio_ignore_url_mismatch'] != $current_url)) {

            // Do not display notification on a demo site
            if (!RightPress_Helper::is_demo()) {
                include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/admin/url-mismatch-notification.php';
            }
        }
    }

    /**
     * Maybe save main site URL
     *
     * @access public
     * @return void
     */
    public function maybe_save_main_site_url()
    {
        if (empty($this->opt['subscriptio_main_site_url'])) {
            $this->save_main_site_url($this->get_main_site_url());
        }
    }

    /**
     * Save main site URL so we can disable some actions on development/staging websites
     *
     * @access public
     * @param string $url
     * @return void
     */
    public function save_main_site_url($url)
    {
        $this->update_option('subscriptio_main_site_url', $url);
    }

    /**
     * Get main site URL with placeholder in the middle
     *
     * @access public
     * @return string
     */
    public function get_main_site_url()
    {
        $current_site_url = get_site_url();
        return substr_replace($current_site_url, '%%%SUBSCRIPTIO%%%', strlen($current_site_url) / 2, 0);
    }

    /**
     * Check if this is main site - some actions must be cancelled on development/staging websites
     *
     * @access public
     * @return bool
     */
    public static function is_main_site()
    {
        $is_main_site = false;

        $subscriptio = self::get_instance();
        $current_site_url = get_site_url();

        // Make sure we have saved original URL, otherwise treat as duplicate site
        if (!empty($subscriptio->opt['subscriptio_main_site_url'])) {
            $main_site_url = set_url_scheme(str_replace('%%%SUBSCRIPTIO%%%', '', $subscriptio->opt['subscriptio_main_site_url']));
            $is_main_site = $current_site_url == apply_filters('subscriptio_site_url', $main_site_url) ? true : false;
        }

        return apply_filters('subscriptio_is_main_site', $is_main_site);
    }

    /**
     * Add subscription edit page meta box
     *
     * @access public
     * @param mixed $post
     * @return void
     */
    public function add_subscription_meta_box($post)
    {
        if (in_array($post->post_type, array('subscription'))) {

            // General subscription details block
            add_meta_box(
                'subscriptio_subscription_details',
                __('Subscription Details', 'subscriptio'),
                array($this, 'render_subscription_meta_box_details'),
                'subscription',
                'normal',
                'high'
            );

            // Subscription items
            add_meta_box(
                'subscriptio_subscription_items',
                __('Subscription Items', 'subscriptio'),
                array($this, 'render_subscription_meta_box_items'),
                'subscription',
                'normal',
                'high'
            );

            // Subscription actions
            add_meta_box(
                'subscriptio_subscription_actions',
                __('Subscription Actions', 'subscriptio'),
                array($this, 'render_subscription_meta_box_actions'),
                'subscription',
                'side',
                'default'
            );

            // Subscription transactions
            add_meta_box(
                'subscriptio_subscription_transactions',
                __('Transactions', 'subscriptio'),
                array($this, 'render_subscription_meta_box_transactions'),
                'subscription',
                'side',
                'default'
            );
        }
    }

    /**
     * Save subscription custom fields from edit page
     *
     * @access public
     * @param int $post_id
     * @param object $post
     * @return void
     */
    public function save_subscription_meta_box($post_id, $post)
    {
        // Check if required properties were passed in
        if (empty($post_id) || empty($post)) {
            return;
        }

        // Make sure user has permissions to edit this post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Make sure the correct post ID was passed from form
        if (empty($_POST['post_ID']) || $_POST['post_ID'] != $post_id) {
            return;
        }

        // Make sure it is not a draft save action
        if (defined('DOING_AUTOSAVE') || is_int(wp_is_post_autosave($post)) || is_int(wp_is_post_revision($post))) {
            return;
        }

        // Proceed only if post type is subscription
        if ($post->post_type != 'subscription') {
            return;
        }

        $subscription = $this->load_from_cache('subscriptions', $post_id);

        if (!$subscription) {
            return;
        }

        // Actions
        if (!empty($_POST['subscriptio_subscription_button']) && $_POST['subscriptio_subscription_button'] == 'actions' && !empty($_POST['subscriptio_subscription_actions'])) {
            switch ($_POST['subscriptio_subscription_actions']) {

                // Cancel
                case 'cancel':

                    if ($subscription->can_be_cancelled()) {

                        // Write transaction
                        $transaction = new Subscriptio_Transaction(null, 'manual_cancellation');
                        $transaction->add_subscription_id($subscription->id);
                        $transaction->add_product_id($subscription->product_id);
                        $transaction->add_variation_id($subscription->variation_id);

                        try {
                            // Cancel subscription
                            $subscription->cancel();

                            // Update transaction
                            $transaction->update_result('success');
                            $transaction->update_note(__('Subscription cancelled manually by administrator.', 'subscriptio'), true);
                        }
                        catch (Exception $e) {
                            $transaction->update_result('error');
                            $transaction->update_note($e->getMessage());
                        }

                    }

                    break;

                // Pause
                case 'pause':

                    // Make sure that subscription can be paused
                    if ($subscription->can_be_paused()) {

                        // Write transaction
                        $transaction = new Subscriptio_Transaction(null, 'subscription_pause');
                        $transaction->add_subscription_id($subscription->id);
                        $transaction->add_product_id($subscription->product_id);
                        $transaction->add_variation_id($subscription->variation_id);

                        try {
                            // Pause subscription
                            $subscription->pause();

                            // Update transaction
                            $transaction->update_result('success');
                            $transaction->update_note(__('Subscription paused by administrator.', 'subscriptio'), true);
                        }
                        catch (Exception $e) {
                            $transaction->update_result('error');
                            $transaction->update_note($e->getMessage());
                        }
                    }

                    break;

                // Resume
                case 'resume':

                    // Make sure that subscription can be resumed (was paused)
                    if ($subscription->can_be_resumed()) {

                        // Write transaction
                        $transaction = new Subscriptio_Transaction(null, 'subscription_resume');
                        $transaction->add_subscription_id($subscription->id);
                        $transaction->add_product_id($subscription->product_id);
                        $transaction->add_variation_id($subscription->variation_id);

                        try {
                            // Resume subscription
                            $subscription->resume();

                            // Update transaction
                            $transaction->update_result('success');
                            $transaction->update_note(__('Subscription resumed by administrator.', 'subscriptio'), true);
                        }
                        catch (Exception $e) {
                            $transaction->update_result('error');
                            $transaction->update_note($e->getMessage());
                        }
                    }

                    break;

                default:
                    break;
            }
        }

        // Address update
        else if (!empty($_POST['subscriptio_subscription_button']) && $_POST['subscriptio_subscription_button'] == 'address') {
            $subscription->update_shipping_address($_POST);
        }
    }

    /**
     * Render subscription edit page meta box Subscription Details content
     *
     * @access public
     * @param mixed $post
     * @return void
     */
    public function render_subscription_meta_box_details($post)
    {
        $subscription = $this->load_from_cache('subscriptions', $post->ID);

        if (!$subscription) {
            return;
        }

        // Get subscription statuses
        $subscription_statuses = Subscriptio_Subscription::get_statuses();

        // Load view
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/subscription/subscription-details.php';
    }

    /**
     * Render subscription edit page meta box Subscription Items content
     *
     * @access public
     * @param mixed $post
     * @return void
     */
    public function render_subscription_meta_box_items($post)
    {
        $subscription = $this->load_from_cache('subscriptions', $post->ID);

        if (!$subscription) {
            return;
        }

        // Load view
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/subscription/subscription-items.php';
    }

    /**
     * Render subscription edit page meta box Subscription Actions content
     *
     * @access public
     * @param mixed $post
     * @return void
     */
    public function render_subscription_meta_box_actions($post)
    {
        $subscription = $this->load_from_cache('subscriptions', $post->ID);

        if (!$subscription) {
            return;
        }

        // Load view
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/subscription/subscription-actions.php';
    }

    /**
     * Render subscription edit page meta box Subscription Transactions content
     *
     * @access public
     * @param mixed $post
     * @return void
     */
    public function render_subscription_meta_box_transactions($post)
    {
        $subscription = $this->load_from_cache('subscriptions', $post->ID);

        if (!$subscription) {
            return;
        }

        // Pass a prepared list of transactions to view
        $transactions = array();

        // Get all transaction IDs
        $query = new WP_Query(array(
            'post_type'     => 'sub_transaction',
            'fields'        => 'ids',
            'meta_query'    => array(
                array(
                    'key'       => 'subscription_id',
                    'value'     => $subscription->id,
                    'compare'   => '=',
                ),
            ),
        ));

        // Populate list of transactions
        foreach ($query->posts as $transaction_id) {
            $transactions[] = new Subscriptio_Transaction($transaction_id);
        }

        // Load view
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/subscription/subscription-transactions.php';
    }

    /**
     * Add admin menu items
     *
     * @access public
     * @return void
     */
    public function add_admin_menu()
    {
        // Add submenu links
        add_submenu_page(
            'edit.php?post_type=subscription',
            __('Settings', 'subscriptio'),
            __('Settings', 'subscriptio'),
            apply_filters('subscriptio_capability', 'manage_options', 'settings'),
            'subscriptio_settings',
            array($this, 'set_up_settings_pages')
        );

        // Remove Add Subscription if other plugins have set it back
        global $submenu;

        if (isset($submenu['edit.php?post_type=subscription'])) {
            foreach ($submenu['edit.php?post_type=subscription'] as $item_key => $item) {
                if (in_array('post-new.php?post_type=subscription', $item)) {
                    unset($submenu['edit.php?post_type=subscription'][$item_key]);
                }
            }
        }
    }

    /**
     * Register our settings fields with WordPress
     *
     * @access public
     * @return void
     */
    public function plugin_options_setup()
    {
        // Check if current user can manage Subscriptio options
        if (current_user_can(apply_filters('subscriptio_capability', 'manage_options', 'settings'))) {

            // Iterate over tabs
            foreach ($this->settings as $tab_key => $tab) {

                register_setting(
                    'subscriptio_opt_group_' . $tab_key,
                    'subscriptio_options',
                    array($this, 'options_validate')
                );

                // Iterate over sections
                foreach ($tab['children'] as $section_key => $section) {

                    // Do not show PayPal Adaptive Payments if it's not enabled
                    if ($section_key === 'paypal_gateway' && !Subscriptio::option('paypal_enabled')) {
                        continue;
                    }

                    // Add section
                    add_settings_section(
                        $section_key,
                        $section['title'],
                        array($this, 'render_section_info'),
                        'subscriptio-admin-' . str_replace('_', '-', $tab_key)
                    );

                    // Iterate over fields
                    foreach ($section['children'] as $field_key => $field) {

                        // Add text to display after a field
                        if (in_array($field_key, array('stripe_enabled', 'paypal_enabled', 'paypal_ec_enabled')) && $this->option($field_key)) {
                            $payment_gateway_section = 'subscriptio_' . str_replace('_enabled', '', $field_key);
                            $after = '<a style="padding-left: 20px;" href="admin.php?page=wc-settings&tab=checkout&section=' . $payment_gateway_section . '">'.__('Settings', 'subscriptio').'</a>';
                        }
                        else {
                            $after = isset($field['after']) ? $field['after'] : '';
                        }

                        // Add field
                        add_settings_field(
                            'subscriptio_' . $field_key,
                            $field['title'],
                            array($this, 'render_field_' . $field['type']),
                            'subscriptio-admin-' . str_replace('_', '-', $tab_key),
                            $section_key,
                            array(
                                'name'        => 'subscriptio_' . $field_key,
                                'options'     => $this->opt,
                                'values'      => isset($field['values']) ? $field['values'] : '',
                                'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
                                'after'       => $after,
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Render section info
     *
     * @access public
     * @param array $section
     * @return void
     */
    public function render_section_info($section)
    {
        // Subscription Flow
        if ($section['id'] === 'subscription_flow') {
            echo '<img src="' . SUBSCRIPTIO_PLUGIN_URL . '/assets/img/subscription_flow.png' . '" style="margin: 20px 110px 20px 110px;">';
            echo '<p style="margin: 25px;">' . __('Payment reminders, overdue period and suspensions are optional.<br>Not displayed in illustration are trial periods, expiration dates and subscription pausing and resuming capability.', 'subscriptio') . '</p>';
            echo '<p style="margin: 25px;">' . sprintf(__('You can enable/disable email notifications on the <a href="%s">Emails</a> page.', 'subscriptio'), 'admin.php?page=wc-settings&tab=email') . '</p>';
        }
        // PayPal Adaptive Payments warning
        else if ($section['id'] === 'paypal_gateway' && Subscriptio::option('paypal_enabled')) {
            echo '<p style="margin: 20px 0 0 25px; color: red;">' . __('This payment gateway extension is now deprecated, use PayPal Express Checkout instead.<br>Please note that you will not be allowed to enable this extension again after disabling it.', 'subscriptio') . '</p>';
        }
    }

    /**
     * Render checkbox field
     *
     * @access public
     * @return void
     */
    public function render_field_checkbox($args = array())
    {
        printf(
            '<input type="checkbox" id="%s" name="subscriptio_options[%s]" value="1" %s />%s',
            $args['name'],
            $args['name'],
            checked($args['options'][$args['name']], true, false),
            !empty($args['after']) ? '&nbsp;&nbsp;' . $args['after'] : ''
        );
    }

    /**
     * Render checkbox field
     *
     * @access public
     * @return void
     */
    public function render_field_text($args = array())
    {
        if (in_array($args['name'], array('subscriptio_renewal_order_day_offset', 'subscriptio_reminders_days', 'subscriptio_overdue_length', 'subscriptio_suspensions_length', 'subscriptio_max_pauses', 'subscriptio_max_pause_duration'))) {
            $field_width_class = 'subscriptio_field_width_third';
        }
        else {
            $field_width_class = 'subscriptio_field_width';
        }

        printf(
            '<input type="text" id="%s" name="subscriptio_options[%s]" value="%s" class="' . $field_width_class . '" placeholder="%s" />%s',
            $args['name'],
            $args['name'],
            $args['options'][$args['name']],
            $args['placeholder'],
            !empty($args['after']) ? '&nbsp;&nbsp;' . $args['after'] : ''
        );
    }

    /**
     * Render a dropdown
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function render_field_dropdown($args = array())
    {
        printf(
            '<select id="%s" name="subscriptio_options[%s]" class="subscriptio_field_width">',
            $args['name'],
            $args['name']
        );

        foreach ($args['values'] as $key => $name) {
            printf(
                '<option value="%s" %s>%s</option>',
                $key,
                selected($key, $args['options'][$args['name']], false),
                $name
            );
        }

        echo '</select>';
    }

    /**
     * Validate saved options
     *
     * @access public
     * @param array $input
     * @return void
     */
    public function options_validate($input)
    {
        $output = $this->opt;

        if (empty($_POST['current_tab']) || !isset($this->settings[$_POST['current_tab']])) {
            return $output;
        }

        $errors = array();

        // Iterate over fields and validate new values
        foreach ($this->settings[$_POST['current_tab']]['children'] as $section_key => $section) {
            foreach ($section['children'] as $field_key => $field) {

                $current_field_key = 'subscriptio_' . $field_key;

                switch($field['validation']['rule']) {

                    // Checkbox
                    case 'bool':
                        $input[$current_field_key] = (!isset($input[$current_field_key]) || $input[$current_field_key] == '') ? '0' : $input[$current_field_key];
                        if (in_array($input[$current_field_key], array('0', '1')) || ($input[$current_field_key] == '' && $field['validation']['empty'] == true)) {
                            $output[$current_field_key] = $input[$current_field_key];
                        }
                        else {
                            array_push($errors, array('setting' => $current_field_key, 'code' => 'bool', 'title' => $field['title']));
                        }
                        break;

                    // Number
                    case 'number':
                        if (is_numeric($input[$current_field_key]) || ($input[$current_field_key] == '' && $field['validation']['empty'] == true)) {
                            $output[$current_field_key] = $input[$current_field_key];
                        }
                        else if ($current_field_key == 'subscriptio_reminders_days') {
                            $reminder_days = explode(',', trim($input[$current_field_key], ','));

                            $is_ok = true;

                            foreach ($reminder_days as $reminder_day) {
                                if (!is_numeric($reminder_day)) {
                                    $is_ok = false;
                                    break;
                                }
                            }

                            if ($is_ok) {
                                $output[$current_field_key] = trim($input[$current_field_key], ',');
                            }
                            else {
                                array_push($errors, array('setting' => $current_field_key, 'code' => 'number', 'title' => $field['title']));
                            }
                        }
                        else {
                            array_push($errors, array('setting' => $current_field_key, 'code' => 'number', 'title' => $field['title']));
                        }
                        break;

                    // Option
                    case 'option':
                        if (isset($input[$current_field_key]) && (isset($field['values'][$input[$current_field_key]]) || ($input[$current_field_key] == '' && $field['validation']['empty'] == true))) {
                            $output[$current_field_key] = $input[$current_field_key];
                        }
                        else if (!isset($input[$current_field_key])) {
                            $output[$current_field_key] = '';
                        }
                        else {
                            array_push($errors, array('setting' => $current_field_key, 'code' => 'option', 'title' => $field['title']));
                        }
                        break;

                    // Text input and others - default validation rule
                    default:
                        // Make sure the field is set
                        $input[$current_field_key] = (isset($input[$current_field_key])) ? $input[$current_field_key] : '';

                        if ($input[$current_field_key] == '' && $field['validation']['empty'] === false) {
                            array_push($errors, array('setting' => $current_field_key, 'code' => 'string', 'title' => $field['title']));
                        }
                        else {
                            $output[$current_field_key] = esc_attr(trim($input[$current_field_key]));
                        }
                        break;

                }
            }
        }

        // Display settings updated message
        add_settings_error(
            'subscriptio',
            'subscriptio_' . 'settings_updated',
            __('Your settings have been saved.', 'subscriptio'),
            'updated'
        );

        // Display errors
        foreach ($errors as $error) {
            $reverted = __('Reverted to a previous value.', 'subscriptio');

            $messages = array(
                'number' => __('must be numeric', 'subscriptio') . '. ' . $reverted,
                'bool' => __('must be either 0 or 1', 'subscriptio') . '. ' . $reverted,
                'option' => __('is not allowed', 'subscriptio') . '. ' . $reverted,
                'email' => __('is not a valid email address', 'subscriptio') . '. ' . $reverted,
                'url' => __('is not a valid URL', 'subscriptio') . '. ' . $reverted,
                'string' => __('is not a valid text string', 'subscriptio') . '. ' . $reverted,
            );

            add_settings_error(
                'subscriptio',
                $error['code'],
                __('Value of', 'subscriptio') . ' "' . $error['title'] . '" ' . $messages[$error['code']]
            );
        }

        return $output;
    }

    /**
     * Set up settings pages
     *
     * @access public
     * @return void
     */
    public function set_up_settings_pages()
    {
        // Get current page & tab ids
        $current_tab = $this->get_current_settings_tab();

        // Open form container
        echo '<div class="wrap woocommerce"><form method="post" action="options.php" enctype="multipart/form-data">';

        // Print notices
        settings_errors('subscriptio');

        // Print header
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/settings/header.php';

        // Print settings page content
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/settings/fields.php';

        // Print footer
        include SUBSCRIPTIO_PLUGIN_PATH . 'includes/views/backend/settings/footer.php';

        // Close form container
        echo '</form></div>';
    }

    /**
     * Get current settings tab
     *
     * @access public
     * @return string
     */
    public function get_current_settings_tab()
    {
        // Check if we know tab identifier
        if (isset($_GET['tab']) && isset($this->settings[$_GET['tab']])) {
            $tab = $_GET['tab'];
        }
        else {
            $keys = array_keys($this->settings);
            $tab = array_shift($keys);
        }

        return $tab;
    }

    /**
     * Check if it's Subscriptio page and whether to load scripts for backend
     *
     * @access public
     * @return void
     */
    public static function is_subscriptio_page()
    {
        // Check the query string
        if (!isset($_SERVER['QUERY_STRING'])) {
            return false;
        }

        $query = $_SERVER['QUERY_STRING'];

        if (preg_match('/post_type=(subscription|sub_transaction)/i', $query)) {
            return true;
        }

        // And also check post edit page
        else if (!empty($_REQUEST['post'])) {
            $post = get_post($_REQUEST['post']);

            if (in_array($post->post_type, array('subscription', 'sub_transaction'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load backend assets conditionally
     *
     * @access public
     * @return bool
     */
    public function enqueue_backend_assets()
    {
        // Our own scripts and styles
        wp_register_script('subscriptio-backend-scripts', SUBSCRIPTIO_PLUGIN_URL . '/assets/js/backend.js', array('jquery'), SUBSCRIPTIO_VERSION);
        wp_register_style('subscriptio-backend-styles', SUBSCRIPTIO_PLUGIN_URL . '/assets/css/backend.css', array(), SUBSCRIPTIO_VERSION);

        // Scripts
        wp_enqueue_script('subscriptio-backend-scripts');

        // Styles
        wp_enqueue_style('subscriptio-backend-styles');

        // Datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        // Datepicker styles
        wp_register_style('subscriptio-jquery-ui', SUBSCRIPTIO_PLUGIN_URL . '/assets/jquery-ui/jquery-ui.min.css', array(), SUBSCRIPTIO_VERSION);
        wp_enqueue_style('subscriptio-jquery-ui');
    }

    /**
     * Load backend assets unconditionally
     *
     * @access public
     * @return bool
     */
    public function enqueue_backend_assets_all()
    {
        // Our own scripts and styles
        wp_register_script('subscriptio-backend-scripts-all', SUBSCRIPTIO_PLUGIN_URL . '/assets/js/backend-all.js', array('jquery'), SUBSCRIPTIO_VERSION);
        wp_register_style('subscriptio-backend-styles-all', SUBSCRIPTIO_PLUGIN_URL . '/assets/css/backend-all.css', array(), SUBSCRIPTIO_VERSION);

        // Localize script
        wp_localize_script('subscriptio-backend-scripts-all', 'subscriptio_vars', array(
            'title_subscription_product' => __('Subscription product', 'subscriptio'),
            'current_text'               => __('Today', 'subscriptio'),
            'close_text'                 => __('Close', 'subscriptio'),
            'date_change_alert'          => __('Error! The selected date is incorrect!', 'subscriptio'),
            'wc_version_23'              => (string) RightPress_Helper::wc_version_gte('2.3'),
        ));

        // Font awesome (icons)
        wp_register_style('subscriptio-font-awesome', SUBSCRIPTIO_PLUGIN_URL . '/assets/font-awesome/css/font-awesome.min.css', array(), '4.1');

        // Scripts
        wp_enqueue_script('subscriptio-backend-scripts-all');

        // Styles
        wp_enqueue_style('subscriptio-backend-styles-all');
        wp_enqueue_style('subscriptio-font-awesome');

        // RTL support
        if (is_rtl()) {
            wp_register_style('subscriptio-backend-rtl-styles', SUBSCRIPTIO_PLUGIN_URL . '/assets/css/backend-rtl.css', array(), SUBSCRIPTIO_VERSION);
            wp_enqueue_style('subscriptio-backend-rtl-styles');
        }
    }

    /**
     * Load frontend assets
     *
     * @access public
     * @return bool
     */
    public function enqueue_frontend_assets()
    {
        // Frontent styles
        wp_register_style('subscriptio_frontend', SUBSCRIPTIO_PLUGIN_URL . '/assets/css/frontend.css', array(), SUBSCRIPTIO_VERSION);
        wp_enqueue_style('subscriptio_frontend');

        // Frontent scripts
        wp_register_script('subscriptio_frontend', SUBSCRIPTIO_PLUGIN_URL . '/assets/js/frontend.js', array('jquery'), SUBSCRIPTIO_VERSION);
        wp_localize_script('subscriptio_frontend', 'subscriptio_vars', array(
            'confirm_pause'     => __('Are you sure you want to pause this subscription?', 'subscriptio'),
            'confirm_resume'    => __('Are you sure you want to resume this subscription?', 'subscriptio'),
            'confirm_cancel'    => __('Are you sure you want to cancel this subscription?', 'subscriptio'),
        ));
        wp_enqueue_script('subscriptio_frontend');

        // RTL support
        if (is_rtl()) {
            wp_register_style('subscriptio-frontend-rtl-styles', SUBSCRIPTIO_PLUGIN_URL . '/assets/css/frontend-rtl.css', array(), SUBSCRIPTIO_VERSION);
            wp_enqueue_style('subscriptio-frontend-rtl-styles');
        }
    }

    /**
     * Allow no guest checkout
     *
     * @access public
     * @param object $checkout
     * @return void
     */
    public function enforce_registration($checkout)
    {
        // User already registered?
        if (is_user_logged_in()) {
            return;
        }

        // Only proceed if cart contains subscription
        if (self::cart_contains_subscription()) {

            // Enable registration
            if (!$checkout->enable_signup) {
                $checkout->enable_signup = true;
            }

            // Enforce registration
            if ($checkout->enable_guest_checkout) {
                $checkout->enable_guest_checkout = false;
                $checkout->must_create_account = true;
            }
        }
    }

    /**
     * Enforce create new account option
     *
     * @access public
     * @param array $params
     * @return void
     */
    public function enforce_createaccount_option()
    {
        // User already registered?
        if (is_user_logged_in()) {
            return;
        }

        // Only proceed if cart contains subscription
        if (self::cart_contains_subscription()) {
            $_POST['createaccount'] = 1;
        }
    }

    /**
     * Allow no guest checkout (Javascript part)
     *
     * @access public
     * @param array $properties
     * @return array
     */
    public function enforce_registration_js($properties)
    {
        // User already registered?
        if (is_user_logged_in()) {
            return $properties;
        }

        // No subscription in cart?
        if (!self::cart_contains_subscription()) {
            return $properties;
        }

        $properties['option_guest_checkout'] = 'no';

        return $properties;
    }

    /**
     * Check if cart contains subscription product
     *
     * @access public
     * @return bool
     */
    public static function cart_contains_subscription()
    {
        global $woocommerce;

        if (!empty($woocommerce->cart->cart_contents)) {
            foreach ($woocommerce->cart->cart_contents as $item) {
                if (Subscriptio_Subscription_Product::is_subscription($item['variation_id'] ? $item['variation_id'] : $item['product_id'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Sanitize float (treats as a string)
     *
     * @access public
     * @param string $input
     * @return string
     */
    public static function sanitize_float($input)
    {
        return preg_replace('/[^0-9\.]/', '', $input);
    }

    /**
     * Sanitize integer (treats as a string)
     *
     * @access public
     * @param string $input
     * @return string
     */
    public static function sanitize_integer($input)
    {
        return preg_replace('/[^0-9]/', '', $input);
    }

    /**
     * Get natural number from anything
     *
     * @access public
     * @param string $input
     * @return int
     */
    public static function get_natural_number($input)
    {
        if (!isset($input)) {
            return 1;
        }

        $input = (int) self::sanitize_integer($input);

        return $input < 1 ? 1 : $input;
    }

    /**
     * Get floating point number from anything (but return as string)
     * Returns zero (0) if input variable is not properly defined
     *
     * @access public
     * @param string $input
     * @return string
     */
    public static function get_float_number_as_string($input)
    {
        if (!isset($input)) {
            return '0';
        }

        return (string) (float) self::sanitize_float($input);
    }

    /**
     * Alias for post_is_active
     *
     * @access public
     * @param string $product_id
     * @return bool
     */
    public static function product_is_active($product_id)
    {
        return RightPress_Helper::post_is_active($product_id);
    }

    /**
     * Get timezone-adjusted formatted date/time string
     *
     * @access public
     * @param int $timestamp
     * @param string $format
     * @param string $context
     * @return string
     */
    public static function get_adjusted_datetime($timestamp, $format = null, $context = null)
    {
        // Fix possible issues
        if (empty($timestamp)) {
            return false;
        }

        // No format passed? Get it from WordPress settings and allow developers to override it
        if ($format === null) {
            $date_format = apply_filters('subscriptio_date_format', get_option('date_format'), $context);
            $time_format = apply_filters('subscriptio_time_format', get_option('time_format'), $context);
            $format = $date_format . (apply_filters('subscriptio_display_event_time', true) ? ' ' . $time_format : '');
        }

        // Get adjusted datetime
        return RightPress_Helper::get_adjusted_datetime($timestamp, $format);
    }

    /**
     * Get user full name from database with link to user profile or revert to provided name
     *
     * @access public
     * @param int $user_id
     * @param string $default
     * @return string
     */
    public static function get_user_full_name_link($user_id, $default)
    {
        // User still exists?
        if ($user = get_userdata($user_id)) {
            $first_name = get_the_author_meta('first_name', $user_id);
            $last_name = get_the_author_meta('last_name', $user_id);

            if ($first_name || $last_name) {
                $display_name = join(' ', array($first_name, $last_name));
            }
            else {
                $display_name = $user->display_name;
            }

            return '<a href="user-edit.php?user_id=' . $user_id . '">' . $display_name . '</a>';
        }

        return $default . ' (' . __('deleted', 'subscriptio') . ')';
    }

    /**
     * Get formatted shipping address
     *
     * @access public
     * @param array $input
     * @return string
     */
    public static function get_formatted_shipping_address($input)
    {
        if (!is_array($input) || empty($input)) {
            return '';
        }

        $address = array();

        foreach ($input as $key => $value) {
            $address[str_replace('_shipping_', '', $key)] = $value;
        }

        $address = apply_filters('woocommerce_order_formatted_shipping_address', $address);

        return WC()->countries->get_formatted_address($address);
    }

    /**
     * Get price suffix
     *
     * @access public
     * @return string
     */
    public static function get_wc_price_suffix($price)
    {
        // Create empty product to access get_price_suffix method
        $product = new WC_Product(null);
        return $product->get_price_suffix($price);
    }

    /**
     * Get formatted price
     *
     * @access public
     * @param float $price
     * @param string $currency
     * @param bool $sale_price
     * @param bool $display_price_suffix
     * @return string
     */
    public static function get_formatted_price($price, $currency = '', $sale_price = false, $display_price_suffix = true)
    {
        // Both prices are only needed on shop/product pages, not for cart and checkout
        $show_both_prices = (is_cart() || is_checkout()) ? false : true;

        // Format price
        if ($sale_price && $show_both_prices && !defined('DOING_AJAX')) {
           $formatted_price = '<del>' . wc_price($price, array('currency' => $currency)) . '</del> <ins>' . wc_price($sale_price, array('currency' => $currency)) . '</ins>';
        }
        elseif ($sale_price && (!$show_both_prices || ($show_both_prices && defined('DOING_AJAX')))) {
            $formatted_price = wc_price($sale_price, array('currency' => $currency));
        }
        else {
            $formatted_price = wc_price($price, array('currency' => $currency));
        }

        // Optionally append price suffix
        if ($display_price_suffix) {
            $formatted_price .= self::get_wc_price_suffix($price);
        }

        return $formatted_price;
    }

    /**
     * Handle post deleted event
     *
     * @access public
     * @param int $post_id
     * @return void
     */
    public function post_deleted_event($post_id)
    {
        global $post_type;

        if ($post_type !== 'subscription') {
            return;
        }

        self::post_deleted($post_id);
    }

    /**
     * Post deleted
     *
     * @access public
     * @param int $post_id
     * @param string $note
     * @return void
     */
    public static function post_deleted($post_id, $note = null)
    {
        // Unschedule all events
        Subscriptio_Event_Scheduler::unschedule_all($post_id);

        // Write to transaction log
        $transaction = new Subscriptio_Transaction(null, 'subscription_deleted', $post_id);
        $transaction->update_result('success');

        // Add note
        $note = $note ? $note : __('Subscription manually deleted by administrator.', 'subscriptio');
        $transaction->update_note($note);
    }

    /**
     * Define and return time units
     *
     * @access public
     * @return array
     */
    public static function get_time_units()
    {
        return apply_filters('subscriptio_subscription_time_units', array(
            'day'   => array(
                'seconds'               => 86400,
                'translation_callback'  => array('Subscriptio', 'translate_time_unit'),
            ),
            'week'  => array(
                'seconds'               => 604800,
                'translation_callback'  => array('Subscriptio', 'translate_time_unit'),
            ),
            'month' => array(
                'seconds'               => 2592000,
                'translation_callback'  => array('Subscriptio', 'translate_time_unit'),
            ),
            'year'  => array(
                'seconds'               => 31536000,
                'translation_callback'  => array('Subscriptio', 'translate_time_unit'),
            ),
        ));
    }

    /**
     * Translate time unit (doing this way to allow developers
     * to use their own time units AND to translate them to
     * exoting languages that have complex plurals)
     *
     * @access public
     * @param string $unit
     * @param int $value
     * @return string
     */
    public static function translate_time_unit($unit, $value)
    {
        switch ($unit) {
            case 'day':
                return _n('day', 'days', $value, 'subscriptio');
                break;
            case 'week':
                return _n('week', 'weeks', $value, 'subscriptio');
                break;
            case 'month':
                return _n('month', 'months', $value, 'subscriptio');
                break;
            case 'year':
                return _n('year', 'years', $value, 'subscriptio');
                break;
            default:
                break;
        }
    }

    /**
     * Display customer subscription list under My Account
     *
     * @access public
     * @param string $context
     * @param bool $return_html
     * @return string|void
     */
    public function display_customer_subscription_list($context = 'myaccount', $return_html = false)
    {
        // Check if subscription list needs to be displayed
        if (!self::display_frontend_subscriptions_list() && !$return_html) {
            return;
        }

        if ($return_html) {
            ob_start();

            // Adding this div for standard WooCommerce table formatting
            echo '<div class="woocommerce">';
        }

        Subscriptio::include_template('myaccount/subscription-list', array(
            'subscriptions' => Subscriptio_User::find_subscriptions(),
            'title'         => __('My Subscriptions', 'subscriptio'),
            'display_title' => !Subscriptio::my_account_supports_tabbed_navigation(),
        ));

        if ($return_html) {
            echo '</div>';
            return ob_get_clean();
        }
    }

    /**
     * Check if subscriptions list needs to be displayed in My Account
     *
     * @access public
     * @return bool
     */
    public static function display_frontend_subscriptions_list()
    {
        return (Subscriptio_User::find_subscriptions() || apply_filters('subscriptio_display_empty_subscription_list', false, $context));
    }

    /**
     * Intercept and replace woocommerce_my_account shortcode
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public function intercept_woocommerce_my_account_shortcode($atts)
    {
        return WC_Shortcodes::shortcode_wrapper(array('Subscriptio_My_Account', 'output'), $atts);
    }

    /**
     * Add query vars to WP query vars array
     *
     * @access public
     * @param array $vars
     * @return array
     */
    public function add_query_vars($vars)
    {
        foreach (self::$query_vars as $var) {
            $vars[] = $var;
        }

        return $vars;
    }

    /**
     * Maybe flush rewrite rules if ours are not present
     *
     * @access public
     * @return void
     */
    public function maybe_flush_rewrite_rules()
    {
        $rules = get_option('rewrite_rules');

        foreach (self::$query_vars as $var) {
            if (!isset($rules['(.?.+?)/' . $var . '(/(.*))?/?$'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                break;
            }
        }
    }

    /**
     * Insert our rewrite rules
     *
     * @access public
     * @param array $rules
     * @return array
     */
    public function insert_rewrite_rules($rules)
    {
        foreach (self::$query_vars as $var) {
            $rules['(.?.+?)/' . $var . '(/(.*))?/?$'] = 'index.php?pagename=$matches[1]&' . $var . '=$matches[3]';
        }

        return $rules;
    }

    /**
     * Maybe change WooCommerce My Orders query
     *
     * @access public
     * @param array $params
     * @return array
     */
    public function woocommerce_my_orders_query($params)
    {
        if (defined('SUBSCRIPTIO_PRINTING_RELATED_ORDERS')) {
            $subscription = Subscriptio_Subscription::get_by_id(SUBSCRIPTIO_PRINTING_RELATED_ORDERS);

            if ($subscription) {
                $params['post__in'] = $subscription->all_order_ids;
            }
        }

        return $params;
    }

    /**
     * Maybe change WooCommerce My Orders title
     *
     * @access public
     * @param string $title
     * @return string
     */
    public function woocommerce_my_orders_title($title)
    {
        if (defined('SUBSCRIPTIO_PRINTING_RELATED_ORDERS')) {
            return __('Related Orders', 'subscriptio');
        }

        return $title;
    }

    /**
     * Display related subscriptions on single order view page
     *
     * @access public
     * @param object $order
     * @return void
     */
    public function display_frontend_order_related_subscriptions($order)
    {
        $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);

        // Check if order contains any subscriptions
        if (!empty($subscriptions)) {

            // Allow developers to hide subscriptions list
            if (apply_filters('subscriptio_display_order_related_subscriptions', true)) {
                Subscriptio::include_template('myaccount/subscription-list', array(
                    'subscriptions' => $subscriptions,
                    'title'         => __('Related Subscriptions', 'subscriptio'),
                    'display_title' => true,
                ));
            }

            // Disable order again functionality for renewal orders
            if (Subscriptio_Order_Handler::order_is_renewal($order->id)) {
                remove_action('woocommerce_order_details_after_order_table', 'woocommerce_order_again_button');
            }
        }
    }

    /**
     * Return option
     * Warning: do not use in Subscriptio class constructor!
     *
     * @access public
     * @param string $key
     * @return string|bool
     */
    public static function option($key)
    {
        $subscriptio = Subscriptio::get_instance();
        return isset($subscriptio->opt['subscriptio_' . $key]) ? $subscriptio->opt['subscriptio_' . $key] : false;
    }

    /**
     * Display customer subscriptions via shortcode
     *
     * @access public
     * @param mixed $attributes
     * @return void
     */
    public function shortcode_customer_subscriptions($attributes)
    {
        if (is_home() || is_archive() || is_search() || is_feed()) {
            return '';
        }

        return $this->display_customer_subscription_list('shortcode', true);
    }

    /**
     * Remove third party meta boxes from custom post type
     *
     * @access public
     * @param string $post_type
     * @param object $post
     * @return void
     */
    public function remove_meta_boxes($post_type, $post)
    {
        if (in_array($post_type, array('subscription', 'sub_transaction'))) {
            $meta_boxes_to_leave = apply_filters('subscriptio_third_party_meta_boxes_to_leave', array());

            foreach (self::get_meta_boxes() as $context => $meta_boxes_by_context) {
                foreach ($meta_boxes_by_context as $subcontext => $meta_boxes_by_subcontext) {
                    foreach ($meta_boxes_by_subcontext as $meta_box_id => $meta_box) {
                        if (!in_array($meta_box_id, $meta_boxes_to_leave)) {
                            remove_meta_box($meta_box_id, $post_type, $context);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get list of meta boxes for current screent
     *
     * @access public
     * @return array
     */
    public static function get_meta_boxes()
    {
        global $wp_meta_boxes;

        $screen = get_current_screen();
        $page = $screen->id;

        return $wp_meta_boxes[$page];
    }

    /**
     * Don't allow to create new subscriptions manually (doing this in case some plugin overriden our custom post type settings)
     *
     * @access public
     * @return void
     */
    public function admin_redirect()
    {
        if (is_admin() && is_user_logged_in()) {
            global $typenow;
            global $pagenow;

            if (in_array($typenow, array('subscription', 'sub_transaction')) && in_array($pagenow, array('post-new.php'))) {
                wp_redirect(admin_url('/edit.php?post_type=' . $typenow));
                exit;
            }
        }
    }

    /**
     * Remove New Subscription and New Transaction links
     *
     * @access public
     * @return void
     */
    public function admin_remove_new_post_links()
    {
        global $typenow;

        if (in_array($typenow, array('subscription', 'sub_transaction'))) {
            echo '<style>#favorite-actions{display:none;}.add-new-h2{display:none;}.groups-capabilities-container{display:none!important;}</style>';
        }
    }

    /**
     * Add payment gateways
     *
     * @access public
     * @param array $gateways
     * @return void
     */
    public function add_payment_gateways($gateways)
    {
        // Stripe
        if ($this->opt['subscriptio_stripe_enabled']) {
            $gateways[] = 'Subscriptio_Stripe_Gateway';
            load_textdomain('subscriptio-stripe', WP_LANG_DIR . '/subscriptio/subscriptio-stripe-' . apply_filters('plugin_locale', get_locale(), 'subscriptio') . '.mo');
            load_plugin_textdomain('subscriptio-stripe', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        // PayPal
        if ($this->opt['subscriptio_paypal_enabled']) {
            $gateways[] = 'Subscriptio_PayPal_Gateway';
            load_textdomain('subscriptio-paypal', WP_LANG_DIR . '/subscriptio/subscriptio-paypal-' . apply_filters('plugin_locale', get_locale(), 'subscriptio') . '.mo');
            load_plugin_textdomain('subscriptio-paypal', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        // PayPal EC
        if ($this->opt['subscriptio_paypal_ec_enabled']) {
            $gateways[] = 'Subscriptio_PayPal_EC_Gateway';
            load_textdomain('subscriptio-paypal-ec', WP_LANG_DIR . '/subscriptio/subscriptio-paypal-ec-' . apply_filters('plugin_locale', get_locale(), 'subscriptio') . '.mo');
            load_plugin_textdomain('subscriptio-paypal-ec', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        return $gateways;
    }

    /**
     * Load payment gateway classes
     *
     * @access public
     * @param array $gateways
     * @return void
     */
    public function load_payment_gateways()
    {
        // Stripe
        if ($this->opt['subscriptio_stripe_enabled']) {
            foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/gateways/stripe/*.class.php') as $filename)
            {
                include $filename;
            }
        }
        // PayPal
        if ($this->opt['subscriptio_paypal_enabled']) {
            foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/gateways/paypal/*.class.php') as $filename)
            {
                include $filename;
            }
        }
        // PayPal EC
        if ($this->opt['subscriptio_paypal_ec_enabled']) {
            foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/gateways/paypal-ec/*.class.php') as $filename)
            {
                include $filename;
            }
        }
    }

    /**
     * Intercept PayPal's response and process the payment
     *
     * @access public
     * @return void
     */
    public function paypal_express_return_page()
    {
        if (!empty($_GET['subscriptio_ppec_action']) && ($_GET['subscriptio_ppec_action'] == 'do_payment')) {
            $Subscriptio_PayPal_EC_Gateway = new Subscriptio_PayPal_EC_Gateway();
            $Subscriptio_PayPal_EC_Gateway->do_express_checkout_payment();
        }
    }

    /**
     * Check if multiproduct subscription mode is active
     *
     * @access public
     * @return bool
     */
    public static function multiproduct_mode()
    {
        return Subscriptio::option('multiproduct_subscription') == 1;
    }

    /**
     * Check if environment meets requirements
     *
     * @access public
     * @return bool
     */
    public static function check_environment()
    {
        $is_ok = true;

        // Check WordPress version
        if (!RightPress_Helper::wp_version_gte(SUBSCRIPTIO_SUPPORT_WP)) {
            add_action('admin_notices', array('Subscriptio', 'wp_version_notice'));
            $is_ok = false;
        }

        // Check if WooCommerce is enabled
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array('Subscriptio', 'wc_disabled_notice'));
            $is_ok = false;
        }
        else if (!RightPress_Helper::wc_version_gte(SUBSCRIPTIO_SUPPORT_WC)) {
            add_action('admin_notices', array('Subscriptio', 'wc_version_notice'));
            $is_ok = false;
        }

        return $is_ok;
    }

    /**
     * Display WP version notice
     *
     * @access public
     * @return void
     */
    public static function wp_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Subscriptio</strong> requires WordPress version %s or later. Please update WordPress to use this plugin.', 'subscriptio'), SUBSCRIPTIO_SUPPORT_WP) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'subscriptio'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'subscriptio') . '</a>') . '</p></div>';
    }

    /**
     * Display WC disabled notice
     *
     * @access public
     * @return void
     */
    public static function wc_disabled_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Subscriptio</strong> requires WooCommerce to be activate. You can download WooCommerce %s.', 'subscriptio'), '<a href="http://url.rightpress.net/woocommerce-download-page">' . __('here', 'subscriptio') . '</a>') . ' ' . sprintf(__('If you have any questions, please contact %s.', 'subscriptio'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'subscriptio') . '</a>') . '</p></div>';
    }

    /**
     * Display WC version notice
     *
     * @access public
     * @return void
     */
    public static function wc_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Subscriptio</strong> requires WooCommerce version %s or later. Please update WooCommerce to use this plugin.', 'subscriptio'), SUBSCRIPTIO_SUPPORT_WC) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'subscriptio'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'subscriptio') . '</a>') . '</p></div>';
    }

    /**
     * Get sslverify value (defaults to true)
     *
     * @access public
     * @return bool
     */
    public static function get_sslverify_value()
    {
        return apply_filters('subscriptio_sslverify', true);
    }

    /**
     * Get SSL version value (defaults to 6 (TLV 1.2))
     *
     * @access public
     * @return bool
     */
    public static function get_sslversion_value()
    {
        return apply_filters('subscriptio_sslversion', 6);
    }

    /**
     * Include template
     *
     * @access public
     * @param string $template
     * @param array $args
     * @return string
     */
    public static function include_template($template, $args = array())
    {
        RightPress_Helper::include_template($template, SUBSCRIPTIO_PLUGIN_PATH, 'subscriptio', $args);
    }

    /**
     * Check WooCommerce version
     * Legacy compatibility for template files
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wc_version_gte($version)
    {
        return RightPress_Helper::wc_version_gte($version);
    }

    /**
     * Print frontend link to post
     * Legacy compatibility for template files
     *
     * @access public
     * @param int $id
     * @param string $title
     * @param string $pre
     * @param string $post
     * @return void
     */
    public static function print_frontend_link_to_post($id, $title = '', $pre = '', $post = '')
    {
        RightPress_Helper::print_frontend_link_to_post($id, $title, $pre, $post);
    }

    /**
     * Check if My Account supports tabbed navigation
     *
     * @access public
     * @return bool
     */
    public static function my_account_supports_tabbed_navigation()
    {
        return RightPress_Helper::wc_version_gte('2.6');
    }



}

Subscriptio::get_instance();

}
