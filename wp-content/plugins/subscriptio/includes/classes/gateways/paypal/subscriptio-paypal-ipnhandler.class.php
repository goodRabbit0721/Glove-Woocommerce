<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to handling IPN responses
 *
 * @class Subscriptio_PayPal_IPN_Handler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_IPN_Handler')) {

class Subscriptio_PayPal_IPN_Handler
{

    /**
     * Constructor class
     *
     * @access public
     * @param bool $sandbox
     * @return void
     */
    public function __construct()
    {
        // Load payment gateway object to access its methods
        $this->gateway = new Subscriptio_PayPal_Gateway();

        // Also load few properties
        $this->sandbox = $this->gateway->sandbox == 'yes' ? true : false;
        $this->preapproval_cancel = $this->gateway->preapproval_cancel == 'cancel' ? true : false;

        // Add hook if gateway is enabled
        if ($this->gateway->enabled == 'yes') {
            add_action('woocommerce_api_subscriptio_paypal_gateway', array($this, 'check_and_process_response'));
        }
    }

    /**
     * Check and process IPN response from PayPal
     *
     * @access public
     * @params array $args
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

            $log = array();
            $posted = $this->parse_paypal_ipn_message($raw_post);

            // Check if the request is about PAY operation
            if (!empty($posted['transaction_type']) && strtolower($posted['transaction_type']) == 'adaptive payment pay') {

                // Get the pay key and find the order with such key as temporal
                if (!empty($posted['pay_key'])) {

                    $pay_key = $posted['pay_key'];

                    // Get the order
                    if ($order = $this->get_order_for_ipn('temp_pay', $pay_key)) {

                        $posted['status'] = strtolower($posted['status']);

                        // Change the status for sandbox operation
                        if (isset($posted['test_ipn']) && $posted['test_ipn'] == 1 && $posted['status'] == 'pending') {
                            $posted['status'] = 'completed';
                        }

                        // Actions for different payment statuses
                        if ($posted['status'] == 'completed') {

                            // Check if order was already completed
                            if ($order->has_status('completed')) {
                                exit;
                            }

                            // Save user meta data
                            $this->save_paypal_user_meta($order, $posted);

                            // Delete temporal pay key field, save normal
                            delete_post_meta($order->id, '_subscriptio_paypal_temp_paykey');
                            update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);

                            // Get the transaction id(s)
                            $transaction_ids = array();

                            if (!empty($posted['transaction']) && is_array($posted['transaction'])) {
                                foreach ($posted['transaction'] as $id => $transaction) {
                                    $transaction_ids[] = $transaction['id'];
                                }
                            }

                            $transaction_ids = join(', ', $transaction_ids);

                            // Now make changes to the order
                            $order->add_order_note(sprintf(__('PayPal payment completed via IPN (paykey: %s, transaction id(s): %s)', 'subscriptio-paypal'), $pay_key, $transaction_ids));
                            $order->payment_complete($transaction_ids);
                        }

                        else if ($posted['status'] == 'pending') {
                            $order->update_status('on-hold', sprintf(__( 'Payment pending: %s', 'subscriptio-paypal'), $posted['pending_reason']));
                        }

                        else if ($posted['status'] == 'refunded') {
                            $order->update_status('refunded', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }

                        else if ($posted['status'] == 'reversed') {
                            $order->update_status('on-hold', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }

                        else if (in_array($posted['status'], array('failed', 'denied', 'expired', 'voided'))) {
                            $order->update_status('failed', sprintf(__('Payment %s via IPN.', 'subscriptio-paypal'), wc_clean($posted['status'])));
                        }
                        // Unknown status
                        else {
                            $log[] = 'Unknown status: ' . $posted['status'];
                        }
                    }

                    // No orders found
                    else {
                        $log[] = 'No orders found with temp_pay: ' . $pay_key;
                    }
                }

                // There was no pay_key
                else {
                    $log[] = 'Empty pay_key';
                }
            }

            // And/or if the request is about PREAPPROVAL operation
            if (!empty($posted['transaction_type']) && strtolower($posted['transaction_type']) == 'adaptive payment preapproval') {

                // Get the pay key and find the order with such key as temporal
                if (!empty($posted['preapproval_key'])) {
                    $preapproval_key = $posted['preapproval_key'];

                    // Get the order
                    if ($order = $this->get_order_for_ipn('temp_preapproval', $preapproval_key)) {

                        // Check if it was approved
                        if (!empty($posted['approved']) && $posted['approved'] === 'true') {

                            // Save user meta data
                            $this->save_paypal_user_meta($order, $posted);

                            // Get temp payment fields
                            $payment_fields = maybe_unserialize(get_post_meta($order->id, '_subscriptio_paypal_temp_payfields', true));

                            // Execute the first payment from fields saved on checkout
                            $this->execute_first_subscription_payment($order, $payment_fields);
                        }
                        else {
                            $log[] = 'Preapproval request failed: ' . $posted['approved'];
                            $order->update_status('failed', __('Preapproval request and payment failed (IPN).', 'subscriptio-paypal'));
                        }
                    }

                    // No orders found
                    else {
                        $log[] = 'No orders found with temp_preapproval: ' . $preapproval_key;
                    }

                    // Check if it got cancelled and remove preapproval key (no typo here - PayPal returns 'CANCELED')
                    if (!empty($posted['status']) && strtolower($posted['status']) == 'canceled') {
                        $this->remove_preapproval_key($preapproval_key, false);
                    }

                }

                // There was no preapproval_key
                else {
                    $log[] = 'Empty preapproval_key';
                }
            }

            // Check if there's anything to write in log
            if (!empty($log)) {
                foreach ($log as $log_entry) {
                    Subscriptio_PayPal_Gateway::log_write('ipn_process_response', $log_entry, maybe_serialize($posted));
                }
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
        $post_url = $this->sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, Subscriptio::get_sslverify_value());
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $raw_post_validate);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check if we received the VERIFIED
        if ($http_code >= 200 && $http_code < 300 && strstr($response, 'VERIFIED')) {
            return true;
        }

        // If received an error
        if ($response === false) {
            return false;
        }
        else if (strstr($response, 'INVALID')) {

            // Write to log
            Subscriptio_PayPal_Gateway::log_write('ipn_validate', 'INVALID', maybe_serialize($response));

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

    /**
     * Get order with specific temporal key to use with IPN response
     *
     * @access private
     * @param string $key
     * @param string $value
     * @return object|array
     */
    private function get_order_for_ipn($key, $value)
    {
        // Set map for easier use of keys
        $meta_keys_map = array(
            'temp_pay'          => '_subscriptio_paypal_temp_paykey',
            'temp_preapproval'  => '_subscriptio_paypal_temp_preapproval_key',
        );

        // Search for related subscription post ids
        $order_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'shop_order',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => $meta_keys_map[$key],
                    'value'     => $value,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Check how many found - there should be only one
        if (count($order_post_ids) == 1) {
            $order = RightPress_Helper::wc_get_order($order_post_ids[0]);
            return $order;
        }

        // If there are more - gather them all, but return only first one
        else if (count($order_post_ids) > 1) {

            $orders = array();
            foreach ($order_post_ids as $order_id) {

                $order = RightPress_Helper::wc_get_order($order_id);

                if ($order) {
                    $orders[] = $order;
                }
            }

            return $orders[0];
        }

        // Return false if nothing found
        return false;
    }

    /**
     * Save data from response to user meta
     *
     * @access private
     * @param array $order
     * @param array $posted
     * @return void
     */
    private function save_paypal_user_meta($order, $posted)
    {
        // Get user id
        $user_id = $order->get_user_id();

        // Save the preapproval key
        if (!empty($posted['preapproval_key'])) {

            // Get current preapproval keys
            $current_keys = get_user_meta($user_id, '_subscriptio_paypal_preapproval_keys', true);

            // Get subscriptions from order
            $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);

            // And set preapproval keys for those subscriptions
            foreach ($subscriptions as $id => $subscription) {

                if (empty($current_keys[$id])) {
                    $current_keys[$id] = $posted['preapproval_key'];
                }
            }

            update_user_meta($user_id, '_subscriptio_paypal_preapproval_keys', $current_keys);
        }

        // Save customer email
        if (!empty($posted['sender_email'])) {
            update_user_meta($user_id, '_subscriptio_paypal_customer_email', wc_clean($posted['sender_email']));
        }
    }

    /**
     * Remove preapproval key if preapproval was canceled
     *
     * @access private
     * @param string $preapproval_key
     * @return void
     */
    public function remove_preapproval_key($preapproval_key, $failed_payment = false)
    {
        // Search for related subscriptions
        $subscription_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'subscription',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => '_subscriptio_preapproval',
                    'value'     => $preapproval_key,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Iterate over found ids
        foreach ($subscription_post_ids as $id) {

            if ($subscription = Subscriptio_Subscription::get_by_id($id)) {

                // Get all keys of this user and iterate through them
                $current_keys = get_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', true);

                foreach ($current_keys as $subscription_id => $key) {

                    // If the keys match, delete them
                    if ($preapproval_key == $key) {
                        unset($current_keys[$subscription_id]);
                        delete_post_meta($subscription_id, '_subscriptio_preapproval');

                        // Also maybe add note to the latest order
                        if (isset($subscription->last_order_id) && $failed_payment === false) {
                            $order = RightPress_Helper::wc_get_order($subscription->last_order_id);
                            $order->add_order_note(__('Preapproval cancelled by user (IPN).', 'subscriptio-paypal'));
                        }

                        // Maybe cancel the subscription
                        $this->maybe_cancel_subscription($subscription, $failed_payment);
                    }
                }

                // Save the updated keys
                update_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', $current_keys);
            }
        }
    }

    /**
     * Maybe cancel the whole subscription
     *
     * @access private
     * @param obj $subscription
     * @return void
     */
    private function maybe_cancel_subscription($subscription, $failed_payment = false)
    {
        // Cancel the subscription if such option set, but not because of failed auto-payment
        if ($this->preapproval_cancel && $subscription->can_be_cancelled() && $failed_payment === false) {

            // Start transaction
            $transaction = new Subscriptio_Transaction(null, 'automatic_cancellation', $subscription->id);

            // Make sure that subscription is not already cancelled
            if ($subscription->status == 'cancelled') {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
                return;
            }

            // Cancel subscription
            try {
                $subscription->cancel();
                $transaction->update_result('success');
                $transaction->update_note(__('User cancelled preapproval agreement - subscription cancelled as well.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }
    }

    /**
     * Process payment set along with preapproval request
     *
     * @access private
     * @param obj $order
     * @param array $payment_fields
     * @return void
     */
    private function execute_first_subscription_payment($order, $payment_fields)
    {
        // Send request through the gateway
        $payment_response = $this->gateway->send_curl_request('pay', $payment_fields);
        $pay_key = $payment_response->payKey;

        // Get results
        $result_message = $payment_response->responseEnvelope->ack;
        $payment_status = $payment_response->paymentExecStatus;

        // Request failed
        if ($payment_status == 'ERROR' || $result_message == 'Failure' || $result_message == 'FailureWithWarning') {
            $payment_error = Subscriptio_PayPal_Gateway::get_payment_error($payment_response);
            $order->add_order_note(__('Payment failed (PayPal). Error message: ', 'subscriptio-paypal') . $payment_error);
        }

        // Request was successful
        if ($payment_status == 'COMPLETED' && ($result_message == 'Success' || $result_message == 'SuccessWithWarning')) {

            // Add payment method
            update_post_meta($order->id, '_payment_method', 'subscriptio_paypal');

            // Save paykey in normal field
            update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);

            // Remove the temp fields
            delete_post_meta($order->id, '_subscriptio_paypal_temp_payfields');
            delete_post_meta($order->id, '_subscriptio_paypal_temp_preapproval_key');

            // Get the transaction id(s)
            $transaction_data = $payment_response->paymentInfoList->paymentInfo;
            $transaction_ids = array();

            if (!empty($transaction_data) && is_array($transaction_data)) {
                foreach ($transaction_data as $id => $transaction) {
                    $transaction_ids[] = $transaction->transactionId;
                }
            }

            $transaction_ids = join(', ', $transaction_ids);

            // Complete the order
            $order->add_order_note(sprintf(__('PayPal payment completed (paykey: %s, transaction id(s): %s)', 'subscriptio-paypal'), $pay_key, $transaction_ids));
            $order->payment_complete($transaction_ids);
        }
    }

}

new Subscriptio_PayPal_IPN_Handler();

}
