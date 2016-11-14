<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to subscriptions
 *
 * @class Subscriptio_PayPal_EC_Subscriptions
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_EC_Subscriptions')) {

class Subscriptio_PayPal_EC_Subscriptions
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Process payment
        add_filter('subscriptio_automatic_payment_subscriptio_paypal_ec', array($this, 'process_payment'), 10, 3);

        // Save billing agreement id
        add_action('woocommerce_order_status_processing', array($this, 'save_billing_agreement_id'));
        add_action('woocommerce_order_status_completed', array($this, 'save_billing_agreement_id'));
    }


    /**
     * Get billing agreement ID
     *
     * @access public
     * @param obj $order
     * @param obj $subscription
     * @return bool|string
     */
    public static function get_billing_agreement_id($order = null, $subscription = null)
    {
        // Get billing agreement ID from anywhere (redundancy is fine)
        $billing_agreement_id = '';

        if (!is_null($subscription)) {
            $billing_agreement_user = get_user_meta($subscription->user_id, '_subscriptio_paypal_ec_billing_agreement', true);
            $billing_agreement_subscription = get_post_meta($subscription->id, '_subscriptio_paypal_ec_billing_agreement', true);

            $billing_agreement_id = !empty($billing_agreement_subscription) ? $billing_agreement_subscription : $billing_agreement_user;
        }

        else if (!is_null($order)) {
            $billing_agreement_order = get_post_meta($order->id, '_subscriptio_paypal_ec_billing_agreement', true);
            $billing_agreement_order_user = get_user_meta($order->get_user_id(), '_subscriptio_paypal_ec_billing_agreement', true);

            $billing_agreement_id = !empty($billing_agreement_order) ? $billing_agreement_order : $billing_agreement_order_user;
        }

        return !empty($billing_agreement_id) ? $billing_agreement_id : false;
    }


    /**
     * Save billing agreement id to subscription(s)
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function save_billing_agreement_id($order_id)
    {
        // Get subscriptions from order
        $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order_id);

        // And set preapproval keys for those subscriptions
        foreach ($subscriptions as $subscription) {

            $billing_agreement_id = get_user_meta($subscription->user_id, '_subscriptio_paypal_ec_billing_agreement', true);
            $post_meta_billing_agreement_id = get_post_meta($subscription->id, '_subscriptio_paypal_ec_billing_agreement', true);

            if (empty($post_meta_billing_agreement_id)) {
                update_post_meta($subscription->id, '_subscriptio_paypal_ec_billing_agreement', $billing_agreement_id);
            }
        }
    }


    /**
     * Process the result of subscription payment
     *
     * @access public
     * @param string $billing_agreement_id
     * @param obj $order
     * @return bool|string
     */
    public static function do_reference_transaction($billing_agreement_id, $order)
    {
        // Load payment gateway object to access its methods
        $gateway = new Subscriptio_PayPal_EC_Gateway();

        $request = array(
            'METHOD'        => 'DoReferenceTransaction',
            'REFERENCEID'   => $billing_agreement_id,
            'AMT'           => $order->order_total,
            'PAYMENTACTION' => 'Sale',
            'CURRENCYCODE'  => strtoupper($order->get_order_currency()),
            'NOTIFYURL'     => WC()->api_request_url('Subscriptio_PayPal_EC_Gateway'),
            'CUSTOM'        => maybe_serialize(array('order_id' => $order->id)),
        );

        // Prepare the NVP string
        $nvp_request = $gateway->get_credentials() . Subscriptio_PayPal_EC_NVP::create_nvp_from_array($request);

	$nvp_response = $gateway->send_curl_request($nvp_request);

        return Subscriptio_PayPal_EC_NVP::convert_nvp_to_array($nvp_response);
    }


    /**
     * Process the result of subscription payment
     *
     * @access public
     * @param obj $order
     * @param array $response
     * @return bool|string
     */
    public static function process_result($order, $response, $auto = true)
    {
        // Load payment gateway object to write the log
        $gateway = new Subscriptio_PayPal_EC_Gateway();

        // Check for cURL error
        if (isset($response['error'])) {
            $error_message = __('Error connecting to PayPal (Subscription payment): ', 'subscriptio-paypal-ec') . $response['error'];
            $order->add_order_note($error_message);
            $gateway->log_add($error_message);
            return false;
        }

        // Get results
        $transaction_id = isset($response['TRANSACTIONID']) ? $response['TRANSACTIONID'] : '';
        $result_message = isset($response['ACK']) ? $response['ACK'] : '';
        $payment_status = isset($response['PAYMENTSTATUS']) ? $response['PAYMENTSTATUS'] : '';

        // Request was successful
        if (in_array($result_message, array('Success', 'SuccessWithWarning'))) {

            // Create notes
            $payment_type = $auto ? __('Automatic', 'subscriptio-paypal-ec') : __('Manual', 'subscriptio-paypal-ec');
            $order_note = sprintf(__('%s PayPal Express Checkout subscription payment operation completed (payment status: %s, transaction id: %s)', 'subscriptio-paypal-ec'), $payment_type, $payment_status, $transaction_id);
            $log_entry = sprintf(__('%s subscription payment operation completed (payment status: %s, transaction id: %s)', 'subscriptio-paypal-ec'), $payment_type, $payment_status, $transaction_id);

            // Payment completed
            if (in_array($payment_status, array('Completed', 'Processed'))) {

                // Complete the order
                $order->payment_complete($transaction_id);
            }

            // Pending and other statuses
            else {
                if ($payment_status == 'Pending' && isset($response['PENDINGREASON'])) {
                    $pending_reason_key = $response['PENDINGREASON'];
                    $pending_reason = Subscriptio_PayPal_EC_Gateway::get_pending_reason($pending_reason_key);
                    $log_entry .= sprintf(__(' Pending reason: %s (%s)', 'subscriptio-paypal-ec'), $pending_reason_key, $pending_reason);
                }
            }

            // Write notes
            $order->add_order_note($order_note);
            $gateway->log_add($log_entry);

            // Save transaction id
            update_post_meta($order->id, '_subscriptio_paypal_ec_transaction_id', $transaction_id);

            // Add payment method
            update_post_meta($order->id, '_payment_method', $gateway->id);
            update_post_meta($order->id, '_payment_method_title', $gateway->title);

            // Save transaction id
            update_post_meta($order->id, '_subscriptio_paypal_ec_transaction_id', $transaction_id);

            return true;
        }

        else {
            $paypal_error = array(
                'full_response' => $response,
                'error_code'    => isset($response['L_ERRORCODE0']) ? $response['L_ERRORCODE0'] : '',
                'severity_code' => isset($response['L_SEVERITYCODE0']) ? $response['L_SEVERITYCODE0'] : '',
                'short_msg'     => isset($response['L_SHORTMESSAGE0']) ? $response['L_SHORTMESSAGE0'] : '',
                'long_msg'      => isset($response['L_LONGMESSAGE0']) ? $response['L_LONGMESSAGE0'] : '',
            );

            // Log error
            $gateway->log_add(__('Subscription payment request failed with error: ', 'subscriptio-paypal-ec') . $paypal_error['long_msg']);

            // Get the correct message
            $error_message = !empty($paypal_error['long_msg']) ? $paypal_error['long_msg'] : $paypal_error['short_msg'];
            $error_message = __(($auto ? 'Automatic ' : '') . 'PayPal Express Checkout subscription payment failed with message: ', 'subscriptio-paypal-ec') . $error_message;

            $order->add_order_note($error_message);
            $gateway->log_add($error_message);

            return false;
        }
    }

    /**
     * Process automatic subscription payment
     *
     * @access public
     * @param bool $payment_successful
     * @param array $order
     * @param array $subscription
     * @return bool
     */
    public function process_payment($payment_successful, $order, $subscription)
    {
        // Get billing agreement ID
        $billing_agreement_id = self::get_billing_agreement_id($order, $subscription);

        if ($billing_agreement_id === false) {

            $error_message = __('Automatic subscription payment failed: billing agreement ID not found (PayPal)', 'subscriptio-paypal-ec');
            $order->add_order_note($error_message);

            // Add to log
            $gateway = new Subscriptio_PayPal_EC_Gateway();
            $gateway->log_add($error_message);

            return false;
        }

        $response = self::do_reference_transaction($billing_agreement_id, $order);

        return self::process_result($order, $response, true);
    }


    /**
     * Process renewal order payment
     *
     * @access public
     * @params obj $order
     * @return bool
     */
    public static function process_renewal_order_payment($order)
    {
        // Get billing agreement ID
        $billing_agreement_id = self::get_billing_agreement_id($order);

        if ($billing_agreement_id === false) {
            return false;
        }

        $response = self::do_reference_transaction($billing_agreement_id, $order);

        return self::process_result($order, $response, false);
    }


}

new Subscriptio_PayPal_EC_Subscriptions();

}
