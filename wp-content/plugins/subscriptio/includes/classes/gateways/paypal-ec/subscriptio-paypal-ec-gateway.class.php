<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Subscriptio PayPal Express Checkout payment gateway class
 *
 * @class Subscriptio_PayPal_EC_Gateway
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_EC_Gateway') && class_exists('WC_Payment_Gateway')) {

class Subscriptio_PayPal_EC_Gateway extends WC_Payment_Gateway
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct($id = null)
    {
        // Gateway configuration
        $this->id                    = 'subscriptio_paypal_ec';
        $this->has_fields            = false;
        $this->supports              = array('products', 'refunds', 'subscriptio');
        $this->method_title          = __('PayPal Express Checkout by Subscriptio', 'subscriptio-paypal-ec');
        $this->method_description    = sprintf(wp_kses(__('Reference Transactions must be enabled on your PayPal account, learn more <a href="%s">here</a>.<br>PayPal requires all communications to be secured with TLS 1.2, make sure that your server <a href="%s">supports it</a>.', 'subscriptio-paypal-ec'), array('a' => array('href' => array()), 'br' => array())), 'http://url.rightpress.net/paypal-express-checkout-integration-help', 'http://url.rightpress.net/payment-gateways-tls-version-help');
        $this->order_button_text = __( 'Go to PayPal', 'subscriptio-paypal-ec');

        // Load settings fields
        $this->init_form_fields();
        $this->init_settings();

        // Define properties
        $this->enabled               = apply_filters('subscriptio_paypal_ec_enabled', $this->get_option('enabled'));
        $this->sandbox               = apply_filters('subscriptio_paypal_ec_sandbox', $this->get_option('sandbox'));
        $this->title                 = $this->get_option('title');
        $this->description           = $this->get_option('description');

        // API Credentials
        $this->api_username          = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_username') : $this->get_option('api_username');
        $this->api_password          = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_password') : $this->get_option('api_password');
        $this->api_signature         = $this->sandbox == 'yes' ? $this->get_option('sandbox_api_signature') : $this->get_option('api_signature');
        $this->endpoint_url          = $this->sandbox == 'yes' ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';
        $this->api_version           = '64';

        // Log
        $this->enable_log            = $this->get_option('enable_log') == 'yes' ? true : false;

        // Branding
        $this->enable_branding       = $this->get_option('enable_branding') == 'yes' ? true : false;

        // Address Override
        $this->address_override      = $this->get_option('address_override') == 'yes' ? true : false;

        // Save gateway settings
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        // Get admin notices and disable payment gateway if any
        $this->notices = $this->get_notices();

        // Disable payments and show admin notices if something is wrong with settings
        if ($this->enabled == 'yes' && !empty($this->notices)) {

            // Show admin notices
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                add_action('admin_notices', array($this, 'show_admin_notices'));
            }

            // Disable payments
            if (count($this->notices) > 1 || !isset($this->notices['ssl'])) {
                $this->enabled = 'no';
            }
        }

        // Disable payments if PayPal is unavailable
        if (!$this->is_paypal_available_for_use()) {
            $this->enabled = 'no';
        }

        // Add logger if not set
        if ($this->enable_log && !isset($this->logger)) {
            $this->logger = new WC_Logger();
        }

        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Save form data from checkout page
        add_action('woocommerce_after_checkout_validation', array($this, 'checkout_form_save'));

        // Process IPN message
        add_action('woocommerce_api_subscriptio_paypal_ec_gateway', array($this, 'check_and_process_response'));
    }


    /**
     * Get notices for admin
     *
     * @access public
     * @param bool $return_error
     * @return array|bool
     */
    public function get_notices()
    {
        $notices = array();

        // Check WooCommerce version
        if (!RightPress_Helper::wc_version_gte('2.1')) {
            $notices['version'] = __('Subscriptio PayPal payment gateway requires WooCommerce 2.1 or later.', 'subscriptio-paypal-ec');
        }

        // Check secret keys
        if ($this->sandbox == 'no' && empty($this->api_username) || empty($this->api_password) || empty($this->api_signature)) {
            $notices['secret'] = __('Subscriptio PayPal payment gateway requires all API credentials to be set.', 'subscriptio-paypal-ec');
        }

        return $notices;
    }


    /**
     * Show admin notices
     *
     * @access public
     * @return void
     */
    public function show_admin_notices()
    {
        foreach ($this->notices as $notice) {
            echo '<div class="error"><p>' . __($notice, 'subscriptio-paypal-ec') . '</p></div>';
        }
    }


    /**
     * Check if this gateway is available for use
     *
     * @access public
     * @return bool
     */
    public function is_available()
    {
        return $this->enabled == 'yes' ? true : false;
    }


    /**
     * Check if PayPal is enabled and available in the user's country
     *
     * @access public
     * @return bool
     */
    public function is_paypal_available_for_use()
    {
        // Using the woocommerce filter
        $supported_currencies = apply_filters('woocommerce_paypal_supported_currencies', array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB'));

        return in_array(get_woocommerce_currency(), $supported_currencies);
    }

    /**
     * Enqueue backend scripts
     *
     * @access public
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_register_script('subscriptio_paypal_ec_backend', SUBSCRIPTIO_PLUGIN_URL . '/assets/gateways/paypal-ec/js/backend.js', array('jquery'), SUBSCRIPTIO_VERSION);
        wp_enqueue_script('subscriptio_paypal_ec_backend');
    }

    /**
     * Get PayPal credentials for request
     *
     * @access public
     * @return string
     */
    public function get_credentials()
    {
        $credentials_string = 'USER=' . $this->api_username . '&PWD=' . $this->api_password . '&VERSION=' . $this->api_version . '&SIGNATURE=' . $this->api_signature;
        return $credentials_string;
    }


    /**
     * Get return page URL - the page where payment call will be made
     *
     * @access public
     * @return string
     */
    public static function get_return_page()
    {
        // Try to get checkout/cart page id
        $checkout_page_id = wc_get_page_id('checkout');
        $cart_page_id = wc_get_page_id('cart');

        $return_page_url_id = !empty($checkout_page_id) ? $checkout_page_id : $cart_page_id;

        // Get url and return it
        return get_permalink($return_page_url_id);
    }


    /**
     * Add branding parameters to customize the Express Checkout page
     *
     * @access public
     * @param array $request
     * @return array
     */
    public function add_branding_fields($request)
    {
        return array_merge($request, array(
            'BRANDNAME'    => $this->get_option('branding_brandname'),
            'PAGESTYLE'    => $this->get_option('branding_pagestyle'),
            'HDRIMG'       => $this->get_option('branding_hdrimg'),
            'LOGOIMG'      => $this->get_option('branding_logoimg'),
            'PAYFLOWCOLOR' => $this->get_option('branding_payflowcolor'),
        ));
    }


    /**
     * Get the email to pre-fill on PayPal login page
     *
     * @access public
     * @param array $checkout_form
     * @return array
     */
    public static function get_user_email($checkout_form = null)
    {
        $user_email = '';

        // Try to get email from posted form
        if (isset($checkout_form['billing_email'])) {
            $user_email = $checkout_form['billing_email'];
        }

        // Or try to get it from WP user
        else if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_email = $current_user->user_email;
        }

        return $user_email;
    }


    /**
     * Send cURL request to PayPal
     *
     * @access public
     * @param string $action
     * @param array $params
     * @return object|string
     */
    public function send_curl_request($request = '')
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, Subscriptio::get_sslverify_value());
        curl_setopt($curl, CURLOPT_SSLVERSION, Subscriptio::get_sslversion_value());
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_URL, $this->endpoint_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check for errors
        if ($response === false || $http_code != 200) {
            $curl_error = curl_error($curl);
            $response = array('error' => $curl_error);
        }

        // Convert to array
        $request_array = Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($request);
        $response_array = Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($response);

        // Maybe add to log
        $this->log_add(__('REQUEST: ', 'subscriptio-paypal-ec'));
        $this->log_add($request_array);
        $this->log_add(__('RESPONSE: ', 'subscriptio-paypal-ec'));
        $this->log_add($response_array);

        curl_close($curl);
        return $response;
    }


    /**
     * Add checkout form data to session
     *
     * @access public
     * @return void
     */
    public function checkout_form_save($posted) {
        $this->session_set_var('checkout_form', serialize($posted));
    }


    /**
     * Process the payment
     *
     * @access public
     * @param obj $order_id
     * @return array
     */
    public function process_payment($order_id)
    {
        global $woocommerce;

        // Log the beginning
        $this->log_add(sprintf(__('Processing order started (order id: %s)', 'subscriptio-paypal-ec'), $order_id));

        // Get order object
        $order = RightPress_Helper::wc_get_order($order_id);
        $cart = $woocommerce->cart->get_cart();

        // Check order
        if (!$order) {
            $this->log_add(__('Cancelling - order not found', 'subscriptio-paypal-ec'));
            RightPress_Helper::wc_add_notice(__('Order not found. We have not charged you for this order. Please try again.', 'subscriptio-paypal-ec'), 'error');
            return;
        }

        // Check if the order is renewal and try to auto-process it
        if (Subscriptio_Order_Handler::order_is_renewal($order_id)) {

            $this->log_add(__('Renewal order detected - trying to auto-process', 'subscriptio-paypal-ec'));

            // Complete the payment if order was processed successfully
            if (Subscriptio_PayPal_EC_Subscriptions::process_renewal_order_payment($order) === true) {

                $this->log_add(sprintf(__('Manual payment for renewal order successfully auto-completed (order id: %s)', 'subscriptio-paypal-ec'), $order_id));

                return array(
                    'result'   => 'success',
                    'redirect' => esc_url($this->get_return_url($order)),
                );
            }
        }

        // Check cart
        if (empty($cart)) {
            $this->log_add(__('Cancelling - the cart is empty', 'subscriptio-paypal-ec'));
            RightPress_Helper::wc_add_notice(__('The cart is empty - we have not charged you for this order.', 'subscriptio-paypal-ec'), 'error');
            return;
        }

        // Get review order page url and add order_id to it
        $return_page_url = self::get_return_page();

        // Set the return and cancel urls
        $return_url = add_query_arg(array(
                'order_id'                => $order_id,
                'subscriptio_ppec_action' => 'do_payment',
            ),
            $return_page_url
        );

        $cancel_url = $order->get_cancel_order_url_raw();

        // Prepare the express checkout page
        $express_checkout_response = $this->set_express_checkout($order, $return_url, $cancel_url);

        // Get token
        if (!empty($express_checkout_response['TOKEN'])) {

            // Save token
            $token = $express_checkout_response['TOKEN'];
            $this->session_set_var('TOKEN', $token);

            // Set redirect url
            $redirect_url = $this->sandbox == 'yes' ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&' . '&token=' . $token : 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&' . '&token=' . $token;

            $this->log_add(__('Express Checkout is set, redirecting to PayPal', 'subscriptio-paypal-ec'));

            // Redirect user
            return array(
                'result'    => 'success',
                'redirect'  => $redirect_url,
            );
        }

        // No token - should be error
        else {

            // Check for cURL error
            if (isset($express_checkout_response['error'])) {

                // Create error message
                $error_message = __('Error connecting to PayPal (Set Express Checkout): ', 'subscriptio-paypal-ec') . $express_checkout_response['error'];

                // Log error
                $this->log_add($error_message);

                // Add error
                RightPress_Helper::wc_add_notice($error_message, 'error');
                return;
            }

            else {

                $paypal_error = array(
                    'full_response' => $express_checkout_response,
                    'error_code'    => isset($express_checkout_response['L_ERRORCODE0']) ? $express_checkout_response['L_ERRORCODE0'] : '',
                    'severity_code' => isset($express_checkout_response['L_SEVERITYCODE0']) ? $express_checkout_response['L_SEVERITYCODE0'] : '',
                    'short_msg'     => isset($express_checkout_response['L_SHORTMESSAGE0']) ? $express_checkout_response['L_SHORTMESSAGE0'] : '',
                    'long_msg'      => isset($express_checkout_response['L_LONGMESSAGE0']) ? $express_checkout_response['L_LONGMESSAGE0'] : '',
                );

                // Get the correct message
                $error_message = __('PayPal Set Express Checkout Request failed with message: ', 'subscriptio-paypal-ec');
                $error_message .= !empty($paypal_error['long_msg']) ? $paypal_error['long_msg'] : (!empty($paypal_error['short_msg']) ? $paypal_error['short_msg'] : __('Unknown error (check log for more details)', 'subscriptio-paypal-ec'));

                // Log error
                $this->log_add($error_message);

                // Add error
                RightPress_Helper::wc_add_notice($error_message, 'error');
                return;
            }
        }
    }


    /**
     * Initialize form fields
     *
     * @access public
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __('Enable/Disable', 'subscriptio-paypal-ec'),
                'type'    => 'checkbox',
                'label'   => __('Enable PayPal Express Checkout', 'subscriptio-paypal-ec'),
                'default' => 'no',
            ),
            'sandbox' => array(
                'title'   => __('Sandbox Mode', 'subscriptio-paypal-ec'),
                'type'    => 'checkbox',
                'label'   => __('Enable PayPal Sandbox mode', 'subscriptio-paypal-ec'),
                'default' => 'no',
            ),
            'enable_log' => array(
                'title'   => __('Enable Log', 'subscriptio-paypal-ec'),
                'type'    => 'checkbox',
                'label'   => __('Enable logging of PayPal operations', 'subscriptio-paypal-ec'),
                'default' => 'yes',
            ),
            'title' => array(
                'title'       => __('Title', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('The title which the user sees during checkout.', 'subscriptio-paypal-ec'),
                'default'     => __('PayPal', 'subscriptio-paypal-ec'),
            ),
            'description' => array(
                'title'       => __('Description', 'subscriptio-paypal-ec'),
                'type'        => 'textarea',
                'description' => __('The description which the user sees during checkout.', 'subscriptio-paypal-ec'),
                'default'     => __('Pay Securely via PayPal', 'subscriptio-paypal-ec'),
            ),
            'address_override' => array(
                'title'   => __('Address Override', 'subscriptio-paypal-ec'),
                'type'    => 'checkbox',
                'label'   => __('Send address data entered on checkout page to PayPal.', 'subscriptio-paypal-ec'),
                'default' => 'no',
            ),
            'api_credentials' => array(
                    'title'       => __( 'API Credentials', 'subscriptio-paypal-ec' ),
                    'type'        => 'title',
                    'description' => sprintf(wp_kses(__('Refer to <a href="%s">this knowledge base article</a> for some guidance on how to acquire your API credentials.', 'subscriptio-paypal-ec'), array('a' => array('href' => array()))), 'http://url.rightpress.net/paypal-express-checkout-integration-help'),
            ),
            'sandbox_api_username' => array(
                'title'       => __('Sandbox API Username', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('Sandbox API Username from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'sandbox_api_password' => array(
                'title'       => __('Sandbox API Password', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('Sandbox API Password from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'sandbox_api_signature' => array(
                'title'       => __('Sandbox API Signature', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('Sandbox API Signature from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'api_username' => array(
                'title'       => __('API Username', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('API Username from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'api_password' => array(
                'title'       => __('API Password', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('API Password from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'api_signature' => array(
                'title'       => __('API Signature', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'description' => __('API Signature from your PayPal Account.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'branding' => array(
                'title'       => __( 'Branding', 'subscriptio-paypal-ec' ),
                'type'        => 'title',
            ),
            'enable_branding' => array(
                'title'   => __('Enable Branding Styles', 'subscriptio-paypal-ec'),
                'type'    => 'checkbox',
                'label'   => __('Enable Branding Style changes for PayPal pages.', 'subscriptio-paypal-ec'),
                'default' => 'yes',
            ),
            'branding_brandname' => array(
                'title'       => __('Brand Name', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'class'       => 'subscriptio_paypal_ec_branding',
                'description' => __('Label that overrides the business name.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'branding_pagestyle' => array(
                'title'       => __('Page Style', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'class'       => 'subscriptio_paypal_ec_branding',
                'description' => __('Name of the Custom Payment Page Style for payment pages - it is the same name as the Page Style Name you chose to add or edit the page style in your PayPal Account profile. ', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'branding_hdrimg' => array(
                'title'       => __('Top Image URL', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'class'       => 'subscriptio_paypal_ec_branding',
                'description' => __('URL for the image you want to appear at the top left of the payment page. The image has a maximum size of 750 pixels wide by 90 pixels high. PayPal requires that you provide an image that is stored on a secure (https) server. If you do not specify an image, the business name displays.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'branding_logoimg' => array(
                'title'       => __('Logo Image URL', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'class'       => 'subscriptio_paypal_ec_branding',
                'description' => __('URL to your logo image. Use a valid graphics format, such as .gif, .jpg, or .png. Limit the image to 190 pixels wide by 60 pixels high.', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
            'branding_payflowcolor' => array(
                'title'       => __('Payment Page Background', 'subscriptio-paypal-ec'),
                'type'        => 'text',
                'class'       => 'subscriptio_paypal_ec_branding',
                'description' => __('Sets the background color for the payment page. By default, the color is white (6-character HTML hexadecimal ASCII color code)', 'subscriptio-paypal-ec'),
                'default'     => '',
            ),
        );
    }


    /**
     * Get gateway icon
     *
     * @access public
     * @return string
     */
    public function get_icon() {

        $icon_label = esc_attr__('PayPal Express Checkout', 'subscriptio-paypal-ec');
        $icon_html = '<img src="' . SUBSCRIPTIO_PLUGIN_URL . '/assets/img/paypal_express_checkout_icon.gif" title="' . $icon_label . '" alt="' . $icon_label . '"/>';

        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }


    /**
     * Checkbox field on Checkout page
     *
     * @access public
     * @return void
     */
    public function payment_fields()
    {
        echo $this->description;
    }


    /**
     * Set express checkout page and get a token
     *
     * @access public
     * @param obj $order
     * @param obj $return_url
     * @param obj $cancel_url
     * @return array
     */
    public function set_express_checkout($order, $return_url, $cancel_url)
    {
        // Get the posted checkout form from session
        $checkout_form = maybe_unserialize($this->session_get_var('checkout_form'));

        // Set the general fields of request
        $set_ec_request = array(
            'METHOD'       => 'SetExpressCheckout',
            'MAXAMT'       => '',
            'RETURNURL'    => $return_url,
            'CANCELURL'    => $cancel_url,
            'NOSHIPPING'   => '1',
            'SURVEYENABLE' => '0',
            'LOCALECODE'   => get_locale(),
            'EMAIL'        => self::get_user_email($checkout_form),
            'TOTALTYPE'    => 'Total',
        );

        // Maybe add branding
        if ($this->enable_branding) {
            $set_ec_request = $this->add_branding_fields($set_ec_request);
        }

        // Maybe override the address
        if ($this->address_override) {
            $set_ec_request['ADDROVERRIDE'] = 1;
        }

        // Prepare the NVP string
        $set_ec_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_array($set_ec_request);

        // Create payment_request fields
        $payment_request_object = new Subscriptio_PayPal_EC_Payment_Request($order, $checkout_form, $this->address_override);
        $payment_request = $payment_request_object->get_payment_array();

        // Prepare the NVP string
        $payment_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_payment_request_array($payment_request);

        // Maybe add billing agreement
        if (Subscriptio_Order_Handler::contains_subscription($order->id)) {

            $billing_agreement_request = array(
                // TBD: apply_filters('subscriptio_paypal_ec_billing_type', 'MerchantInitiatedBilling'); possible values: MerchantInitiatedBilling or MerchantInitiatedBillingSingleAgreement
                'L_BILLINGTYPE0'            => 'MerchantInitiatedBilling',
                'L_PAYMENTTYPE0'            => 'Any',
                'L_BILLINGAGREEMENTCUSTOM0' => '',
            );

            $billing_agreement_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_array($billing_agreement_request);
        }

        // Prepare and send request
        $nvp_request = $this->get_credentials() . $set_ec_request_nvp . (isset($billing_agreement_request_nvp) ? $billing_agreement_request_nvp : '') . $payment_request_nvp;
	$nvp_response = $this->send_curl_request($nvp_request);

        // Return response in array
        return Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($nvp_response);
    }


    /**
     * Do express checkout payment
     *
     * @access public
     * @return void
     */
    public function do_express_checkout_payment()
    {
        global $woocommerce;

        $this->log_add(__('Starting Do Express Checkout process', 'subscriptio-paypal-ec'));

        // Get cart url
        $cart_url = get_permalink(wc_get_page_id('cart'));

        // Save token, PayerID, order_id
        $token = $this->session_get_var('TOKEN');
        $payer_id = isset($_GET['PayerID']) ? $_GET['PayerID'] : '';
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

        // Get the order
        $order = RightPress_Helper::wc_get_order($order_id);

        // Check everything
        if (empty($token) || (empty($payer_id) && $order->order_total != 0) || empty($order_id)) {

            // Add error
            RightPress_Helper::wc_add_notice(__('Unfortunately, your session for this payment has expired. Please try again.', 'subscriptio-paypal-ec'), 'error');
            $this->log_add(__('Cancelling - session expired', 'subscriptio-paypal-ec'));

            // Redirect to cart page
            wp_redirect($cart_url);
            exit();
        }

        // Handle order with free trial
        if ($order->order_total == 0) {
            $this->handle_trial($order, $token);
        }

        // Prevent double payments
        else if (self::is_order_paid($token)) {

            // Add error
            RightPress_Helper::wc_add_notice(__('Operation cancelled - current token was already used to make a payment.', 'subscriptio-paypal-ec'), 'error');
            $this->log_add(__('Cancelling - order already paid', 'subscriptio-paypal-ec'));

            // Redirect to cart page
            wp_redirect($cart_url);
            exit();
        }

        // Start making the request
        $do_ec_request = array(
            'METHOD'  => 'DoExpressCheckoutPayment',
            'TOKEN'   => urlencode($token),
            'PAYERID' => urlencode($payer_id),
            'AMT'           => $order->order_total,
            'PAYMENTACTION' => 'Sale',
            'CURRENCYCODE'  => strtoupper($order->get_order_currency()),
        );

        // Prepare the NVP string
        $do_ec_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_array($do_ec_request);

        // Create payment_request fields
        $payment_request_object = new Subscriptio_PayPal_EC_Payment_Request($order);
        $payment_request = $payment_request_object->get_payment_array();

        // Prepare the NVP string
        $payment_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_payment_request_array($payment_request);

        // Save payment method
        update_post_meta($order->id, '_payment_method', $this->id);
        update_post_meta($order->id, '_payment_method_title', $this->title);

        // Send request
        $nvp_request = $this->get_credentials() . $do_ec_request_nvp . $payment_request_nvp;
	$nvp_response = $this->send_curl_request($nvp_request);
        $nvp_response_array = Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($nvp_response);

        // Check for cURL error
        if (isset($nvp_response_array['error'])) {
            $error_message = __('Error connecting to PayPal (Do Express Checkout): ', 'subscriptio-paypal-ec') . $nvp_response_array['error'];

            // Log error
            $this->log_add($error_message);

            // Add error
            $order->add_order_note($error_message);
            RightPress_Helper::wc_add_notice($error_message, 'error');

            // Redirect to cart page
            wp_redirect($cart_url);
            exit();
        }

        // Get results
        $transaction_id = isset($nvp_response_array['PAYMENTINFO_0_TRANSACTIONID']) ? $nvp_response_array['PAYMENTINFO_0_TRANSACTIONID'] : '';
        $result_message = isset($nvp_response_array['ACK']) ? $nvp_response_array['ACK'] : '';
        $payment_status = isset($nvp_response_array['PAYMENTINFO_0_PAYMENTSTATUS']) ? $nvp_response_array['PAYMENTINFO_0_PAYMENTSTATUS'] : '';

        // Request was successful
        if (in_array($result_message, array('Success', 'SuccessWithWarning'))) {

            // Create notes
            $order_note = sprintf(__('PayPal Express Checkout payment operation completed (payment status: %s, transaction id: %s)', 'subscriptio-paypal-ec'), $payment_status, $transaction_id);
            $log_entry = sprintf(__('Payment operation completed (payment status: %s, transaction id: %s)', 'subscriptio-paypal-ec'), $payment_status, $transaction_id);

            // Payment completed
            if (in_array($payment_status, array('Completed', 'Processed'))) {

                // Complete the order
                $order->payment_complete($transaction_id);
            }

            // Pending and other statuses
            else {
                if ($payment_status == 'Pending' && isset($nvp_response_array['PAYMENTINFO_0_PENDINGREASON'])) {
                    $pending_reason_key = $nvp_response_array['PAYMENTINFO_0_PENDINGREASON'];
                    $pending_reason = self::get_pending_reason($pending_reason_key);
                    $log_entry .= sprintf(__(' Pending reason: %s (%s)', 'subscriptio-paypal-ec'), $pending_reason_key, $pending_reason);
                }
            }

            // Write notes
            $order->add_order_note($order_note);
            $this->log_add($log_entry);

            // Save transaction id
            update_post_meta($order->id, '_subscriptio_paypal_ec_transaction_id', $transaction_id);

            // Add billing agreement id to user/post meta
            if (isset($nvp_response_array['BILLINGAGREEMENTID'])) {
                $user_id = get_current_user_id();
                update_post_meta($order_id, '_subscriptio_paypal_ec_billing_agreement', $nvp_response_array['BILLINGAGREEMENTID']);
                update_user_meta($user_id, '_subscriptio_paypal_ec_billing_agreement', $nvp_response_array['BILLINGAGREEMENTID']);
            }

            // Save token
            update_post_meta($order_id, '_subscriptio_paypal_ec_token', $token);

            // Clean up
            $this->session_erase_var('TOKEN');
            $this->session_erase_var('checkout_form');

            // Empty cart
            $woocommerce->cart->empty_cart();

            wp_redirect($this->get_return_url($order));
            exit();
        }

        else {

            $paypal_error = array(
                'full_response' => $nvp_response_array,
                'error_code'    => isset($nvp_response_array['PAYMENTREQUEST_0_ERRORCODE']) ? $nvp_response_array['PAYMENTREQUEST_0_ERRORCODE'] : '',
                'severity_code' => isset($nvp_response_array['PAYMENTREQUEST_0_SEVERITYCODE']) ? $nvp_response_array['PAYMENTREQUEST_0_SEVERITYCODE'] : '',
                'short_msg'     => isset($nvp_response_array['PAYMENTREQUEST_0_SHORTMESSAGE']) ? $nvp_response_array['PAYMENTREQUEST_0_SHORTMESSAGE'] : '',
                'long_msg'      => isset($nvp_response_array['PAYMENTREQUEST_0_LONGMESSAGE']) ? $nvp_response_array['PAYMENTREQUEST_0_LONGMESSAGE'] : '',
            );

            // Get the correct message
            $error_message = __('PayPal Express Checkout payment failed with message: ', 'subscriptio-paypal-ec');
            $error_message .= !empty($paypal_error['long_msg']) ? $paypal_error['long_msg'] : (!empty($paypal_error['short_msg']) ? $paypal_error['short_msg'] : __('Unknown error (check log for more details)', 'subscriptio-paypal-ec'));

            // Log error
            $this->log_add($error_message);

            // Add error
            $order->add_order_note($error_message);
            RightPress_Helper::wc_add_notice($error_message, 'error');

            // Redirect to cart page
            $cart_url = get_permalink(wc_get_page_id('cart'));

            wp_redirect($cart_url);
            exit();
        }
    }


    /**
     * Handle order with free trial
     *
     * @access public
     * @param object $order
     * @return array
     */
    public function handle_trial($order, $token)
    {
        global $woocommerce;

        $this->log_add(__('Starting process of handling trial subscription from order #', 'subscriptio-paypal-ec') . $order->id);

        // Save payment method
        update_post_meta($order->id, '_payment_method', $this->id);
        update_post_meta($order->id, '_payment_method_title', $this->title);

        // Start making the request
        $ba_request = array(
            'METHOD' => 'CreateBillingAgreement',
            'TOKEN'  => urlencode($token),
        );

        // Prepare the NVP string
        $ba_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_array($ba_request);

        // Send request
        $nvp_request = $this->get_credentials() . $ba_request_nvp;
	$nvp_response = $this->send_curl_request($nvp_request);
        $nvp_response_array = Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($nvp_response);

        // Add billing agreement id to user/post meta
        if (isset($nvp_response_array['BILLINGAGREEMENTID'])) {

            $user_id = get_current_user_id();

            update_post_meta($order->id, '_subscriptio_paypal_ec_billing_agreement', $nvp_response_array['BILLINGAGREEMENTID']);
            update_user_meta($user_id, '_subscriptio_paypal_ec_billing_agreement', $nvp_response_array['BILLINGAGREEMENTID']);

            $order->add_order_note(__('PayPal details saved for future use.', 'subscriptio-stripe'));
            $this->log_add(__('Billing agreement id saved', 'subscriptio-paypal-ec'));
        }
        else {

            $error_message = __('Error: no billing agreement id found', 'subscriptio-paypal-ec');

            $order->add_order_note($error_message);
            $this->log_add($error_message);
        }

        // Complete the order
        $order->update_status('processing');
        $order->reduce_order_stock();

        // Empty cart
        $woocommerce->cart->empty_cart();

        // Redirect user
        wp_redirect($this->get_return_url($order));
        exit();
    }


    /**
     * Check if order was already paid using this payment method
     *
     * @access public
     * @return bool
     */
    public static function is_order_paid($token)
    {
        // Check token
        if (empty($token)) {
            return false;
        }

        // Search for related orders
        $order_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'shop_order',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => '_subscriptio_paypal_ec_token',
                    'value'     => $token,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        if (!empty($order_ids)) {
            return true;
        }
    }


    /**
     * Process refund manually issued from order page
     *
     * @access public
     * @param int $order_id
     * @param float $amount
     * @param string $reason
     * @return bool
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        $this->log_add(__('Processing refund for order #', 'subscriptio-paypal-ec') . $order_id);

        // Load order
        $order = RightPress_Helper::wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Get transaction id
        $transaction_id = get_post_meta($order_id, '_subscriptio_paypal_ec_transaction_id', true);

        if (empty($transaction_id)) {
            return;
        }

        // Get order total
        $refund_type = $amount < $order->get_total() ? 'Partial' : 'Full';
        $reason = esc_html($reason);
        $reason = substr($reason, 0, 255);

        // Start making the request
        $refund_request = array(
            'METHOD'        => 'RefundTransaction',
            'TRANSACTIONID' => $transaction_id,
            'REFUNDTYPE'    => $refund_type,
            'AMT'           => $refund_type == 'Full' ? '' : $amount,
            'CURRENCYCODE'  => strtoupper($order->get_order_currency()),
            'NOTE'          => $reason,
        );

        // Prepare the NVP string
        $refund_request_nvp = Subscriptio_PayPal_EC_NVP::create_nvp_from_array($refund_request);

        // Send request
        $nvp_request = $this->get_credentials() . $refund_request_nvp;
	$nvp_response = $this->send_curl_request($nvp_request);
        $nvp_response_array = Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($nvp_response);

        // Get results
        $result_message = $nvp_response_array['ACK'];
        $refund_transaction_id = $nvp_response_array['REFUNDTRANSACTIONID']; // TBD - maybe add somewhere

        // Request was successful
        if ($result_message == 'Success' || $result_message == 'SuccessWithWarning') {
            $order->add_order_note(sprintf(__('%s of PayPal charge %s refunded.', 'subscriptio-paypal-ec'), Subscriptio::get_formatted_price($amount), $transaction_id));
            $this->log_add(__('Refund successfull', 'subscriptio-paypal-ec'));
            return true;
        }

        // Request failed
        else {
            $order->add_order_note(__('PayPal refund failed.', 'subscriptio-paypal-ec'));
            $this->log_add(__('Refund failed', 'subscriptio-paypal-ec'));
            return false;
        }
    }


    /**
     * Get WC currency
     *
     * @access public
     * @param string $currency_code
     * @return void
     */
    public static function get_currency()
    {
        // TBD - $order->get_order_currency() is more correct, but needs order object
        return strtoupper(get_woocommerce_currency());
    }


    /**
     * Check if currency supports decimals
     * more details: https://developer.paypal.com/docs/classic/api/currency_codes/
     *
     * @access public
     * @param string $currency_code
     * @return void
     */
    public static function currency_supports_decimals($currency_code = '')
    {
        if (empty($currency_code)) {
            $currency_code = self::get_currency();
        }

        return !in_array($currency_code, array('HUF', 'JPY', 'TWD'));
    }


    /**
     * Round the amount
     *
     * @access public
     * @param double $amount
     * @param int $precision
     * @return void
     */
    public static function round($amount, $precision = 2)
    {
        // Maybe change precision for non-decimal currencies
        $precision = self::currency_supports_decimals() ? $precision : 0;

        // Return rounded amount
        return round($amount, $precision);
    }


    /**
     * Format the amount
     *
     * @access public
     * @param double $amount
     * @param int $decimals
     * @return void
     */
    public static function number_format($amount, $decimals = 2)
    {
        // Maybe change decimals number for non-decimal currencies
        $decimals = self::currency_supports_decimals() ? $decimals : 0;

        // Return formatted amount
        return number_format($amount, $decimals, '.', '');

    }


    /**
     * Add log entry
     *
     * @access public
     * @return void
     */
    public function log_add($entry)
    {
        if (isset($this->logger)) {

            if (!is_array($entry)) {

                // Save string
                $this->logger->add('subscriptio_paypal_ec', $entry);
            }

            else {

                // Hide credentials from request array
                foreach (array('USER', 'PWD', 'SIGNATURE') as $key) {
                    if (isset($entry[$key])) {
                        $entry[$key] = '***';
                    }
                }

                // Save array
                $this->logger->add('subscriptio_paypal_ec', print_r($entry, true));
            }
        }
    }


    /**
     * Erase log
     *
     * @access public
     * @return void
     */
    private function log_erase()
    {
        if (isset($this->logger)) {
            $this->logger->clear('subscriptio_paypal_ec');
        }
    }


    /**
     * Set session variable
     *
     * @access public
     * @param string $key
     * @param string $value
     * @return void
     */
    private function session_set_var($key, $value) {
        global $woocommerce;
        $woocommerce->session->$key = $value;
    }


    /**
     * Get session variable
     *
     * @access public
     * @param string $key
     * @return void
     */
    private function session_get_var($key) {
        global $woocommerce;
        return $woocommerce->session->$key;
    }


    /**
     * Erase session variable
     *
     * @access public
     * @param string $key
     * @return void
     */
    private function session_erase_var($key) {
        global $woocommerce;
        $woocommerce->session->$key = '';
    }


    /**
     * Get readable pending reason
     *
     * @access public
     * @param string $key
     * @return void
     */
    public static function get_pending_reason($key)
    {
        // Add readable reasons from PayPal documentation
        $pending_reasons = array(
            'none'              => __('No pending reason.', 'subscriptio-paypal-ec'),
            'address'           => __('The payment is pending because your buyer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.', 'subscriptio-paypal-ec'),
            'authorization'     => __('The payment is pending because it has been authorized but not settled. You must capture the funds first.', 'subscriptio-paypal-ec'),
            'echeck'            => __('The payment is pending because it was made by an eCheck that has not yet cleared.', 'subscriptio-paypal-ec'),
            'intl'              => __('The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.', 'subscriptio-paypal-ec'),
            'multi-currency'    => __('You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.', 'subscriptio-paypal-ec'),
            'order'             => __('The payment is pending because it is part of an order that has been authorized but not settled.', 'subscriptio-paypal-ec'),
            'payment-review'    => __('The payment is pending while it is being reviewed by PayPal for risk.', 'subscriptio-paypal-ec'),
            'regulatory-review' => __('The payment is pending while we make sure it meets regulatory requirements. You will be contacted again in 24-72 hours with the outcome of the review.', 'subscriptio-paypal-ec'),
            'unilateral'        => __('The payment is pending because it was made to an email address that is not yet registered or confirmed.', 'subscriptio-paypal-ec'),
            'verify'            => __('The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.', 'subscriptio-paypal-ec'),
            'other'             => __('The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.', 'subscriptio-paypal-ec'),
        );

        // Also make sure no-hyphens versions supported
        $pending_reasons['multicurrency'] = $pending_reasons['multi-currency'];
        $pending_reasons['paymentreview'] = $pending_reasons['payment-review'];
        $pending_reasons['regulatoryreview'] = $pending_reasons['regulatory-review'];

        return (isset($pending_reasons[$key]) ? $pending_reasons[$key] : $pending_reasons['none']);
    }


    /**
     * Get order with specific temporal key to use with IPN response
     *
     * @access public
     * @param string $txn_id
     * @return object|bool
     */
    public function get_order_from_txnid($txn_id)
    {
        $order_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'shop_order',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => '_subscriptio_paypal_ec_transaction_id',
                    'value'     => $txn_id,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        if (!empty($order_post_ids)) {
            $order = RightPress_Helper::wc_get_order($order_post_ids[0]);
            return $order;
        }

        return false;
    }


    /**
     * Check and process IPN response from PayPal
     *
     * @access public
     * @return string
     */
    public function check_and_process_response()
    {
        $raw_post = file_get_contents("php://input");

        if (!empty($raw_post)) {

            // Validate the request
            if ($this->validate_ipn($raw_post) !== true) {
                wp_die('PayPal IPN Request Failure', 'PayPal IPN', array('response' => 200));
            }

            $posted = $this->parse_paypal_ipn_message($raw_post);
            $this->log_add(__('Decoded IPN message: ', 'subscriptio-paypal-ec'));
            $this->log_add($posted);

            $payment_status = $posted['payment_status'];
            $txn_id = $posted['txn_id'];
            $custom = maybe_unserialize($posted['custom']);
            $order_id = $custom['order_id'];

            $order = RightPress_Helper::wc_get_order($order_id);

            if (!$order) {
                $order = $this->get_order_from_txnid($txn_id);
            }

            // Update only unpaid orders
            if ($payment_status == 'Completed' && Subscriptio_Order_Handler::order_is_paid($order) === false) {

                // Complete the order
                $order->add_order_note(sprintf(__('PayPal Express Checkout payment completed via IPN (transaction id: %s)', 'subscriptio-paypal-ec'), $txn_id));
                $this->log_add(__('Payment completed via IPN', 'subscriptio-paypal-ec'));
                $order->payment_complete($txn_id);
            }

            exit;
        }
    }


    /**
     * Validate IPN response with PayPal
     *
     * @access private
     * @return bool
     */
    private function validate_ipn($raw_post)
    {
        // Combine received input values with validate command
        $raw_post_validate = 'cmd=_notify-validate&' . $raw_post;

        // Compare and maybe change encoding
        if (function_exists('mb_detect_encoding')) {
            $raw_post_enc = mb_detect_encoding($raw_post);
            $raw_post_validate_enc = mb_detect_encoding($raw_post_validate);

            if ($raw_post_enc !== $raw_post_validate_enc && $raw_post_validate_encoded = iconv($raw_post_validate_enc, $raw_post_enc, $raw_post_validate)) {
                $raw_post_validate = $raw_post_validate_encoded;
            }
        }

        // Set url
        $post_url = $this->sandbox == 'yes' ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, Subscriptio::get_sslverify_value());
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $raw_post_validate);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->log_add(__('Incoming IPN message: ', 'subscriptio-paypal-ec'));
        $this->log_add($raw_post);
        $this->log_add(__('IPN validation result: ', 'subscriptio-paypal-ec'));
        $this->log_add($response);

        // Check if we received the VERIFIED
        if ($http_code >= 200 && $http_code < 300 && strstr($response, 'VERIFIED')) {
            return true;
        }
        // If received an error
        else if ($response === false || strstr($response, 'INVALID')) {
            return false;
        }

        return null;
    }


    /**
     * Parse IPN message and return the correctly structured array
     * (based on a method created by donut2d from PayPal community)
     *
     * @access private
     * @param string $raw_post
     * @return array
     */
    private function parse_paypal_ipn_message($raw_post) {

        $post = array();
        $pairs = explode('&', $raw_post);

        foreach ($pairs as $pair) {

            $pair = explode('=', $pair);
            $key = urldecode($pair[0]);
            $value = urldecode($pair[1]);
            $key_parts = array();

            // Look for a key as simple as 'return_url' or as complex as 'somekey[x].property'
            preg_match('/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts);

            switch (count($key_parts)) {

                // Converting key[x].property to $post[key][x][property]
                case 4:

                    if (!isset($post[$key_parts[1]])) {
                        $post[$key_parts[1]] = array($key_parts[2] => array($key_parts[3] => $value));
                    }

                    else if (!isset($post[$key_parts[1]][$key_parts[2]])) {
                        $post[$key_parts[1]][$key_parts[2]] = array($key_parts[3] => $value);
                    }

                    else {
                        $post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
                    }
                    break;

                // Converting key[x] to $post[key][x]
                case 3:

                    if (!isset($post[$key_parts[1]])) {
                        $post[$key_parts[1]] = array();
                    }

                    $post[$key_parts[1]][$key_parts[2]] = $value;
                    break;

                // No special format
                default:
                    $post[$key] = $value;
                    break;
            }
        }

        return $post;
    }



}
}
