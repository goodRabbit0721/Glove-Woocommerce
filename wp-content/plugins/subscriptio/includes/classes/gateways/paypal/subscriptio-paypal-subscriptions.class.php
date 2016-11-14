<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to subscriptions
 *
 * @class Subscriptio_PayPal_Subscriptions
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_Subscriptions')) {

class Subscriptio_PayPal_Subscriptions
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
        // Process payment
        add_filter('subscriptio_automatic_payment_subscriptio_paypal', array($this, 'process_payment'), 10, 3);

        // Save preapproval key
        add_action('woocommerce_order_status_processing', array($this, 'save_preapproval_key'));
        add_action('woocommerce_order_status_completed', array($this, 'save_preapproval_key'));
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
        // Get customer email
        $customer_email = get_user_meta($subscription->user_id, '_subscriptio_paypal_customer_email', true);

        // Get all keys
        $preapproval_keys = get_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', true);

        // Get the key for this particular subscription
        $preapproval_key = $preapproval_keys[$subscription->id];

        if (empty($preapproval_key)) {
            $order->add_order_note(__('Automatic subscription payment failed: preapproval key not found (PayPal)', 'subscriptio-paypal'));
            return false;
        }

        // Load payment gateway object to access its methods
        $gateway = new Subscriptio_PayPal_Gateway();

        // Create the payment request
        $payment_fields = array(
            'actionType'      => 'PAY',
            'cancelUrl'       => esc_url($order->get_cancel_order_url_raw()),
            'returnUrl'       => esc_url($gateway->get_return_url($order)),
            'currencyCode'    => strtoupper($order->get_order_currency()),
            'receiverList'    => array(
                'receiver'        => array(
                    'email'           => $gateway->paypal_receiver_email,
                    'amount'          => $order->order_total)),
            'requestEnvelope' => array('errorLanguage' => 'en_US'),
            'preapprovalKey' => $preapproval_key,
            'memo'   => esc_html(get_bloginfo('name')) . ' - ' . __('Order #', 'subscriptio-paypal') . $order->get_order_number() . ' (' . __('Subscription #', 'subscriptio-paypal') . $subscription->get_subscription_number() . ')',
        );

        // Send payment request
        $payment_response = $gateway->send_curl_request('pay', $payment_fields);

        // Get the paykey and transaction id(s)
        $pay_key = $payment_response->payKey;

        $transaction_data = $payment_response->paymentInfoList->paymentInfo;
        $transaction_ids = array();

        if (!empty($transaction_data) && is_array($transaction_data)) {
            foreach ($transaction_data as $id => $transaction) {
                $transaction_ids[] = $transaction->transactionId;
            }
        }

        $transaction_ids = join(', ', $transaction_ids);

        // Get results
        $result_message = $payment_response->responseEnvelope->ack;
        $payment_status = $payment_response->paymentExecStatus;

        // Request failed
        if ($result_message == 'Failure' || $result_message == 'FailureWithWarning') {

            // Save error message
            $error_message = Subscriptio_PayPal_Gateway::get_payment_error($payment_response);
            $order->add_order_note(__('Automatic subscription payment (PayPal) failed with message: ', 'subscriptio-paypal') . $error_message);

            // Remove preapproval key, but don't cancel the subscription
            Subscriptio_PayPal_IPN_Handler::remove_preapproval_key($preapproval_key, true);

            return false;
        }

        // Request was successful
        if ($payment_status == 'COMPLETED' && ($result_message == 'Success' || $result_message == 'SuccessWithWarning')) {

            // Add payment method
            update_post_meta($order->id, '_payment_method', 'subscriptio_paypal');

            // Save paykey and transaction id
            update_post_meta($order->id, '_subscriptio_paypal_paykey', $pay_key);
            update_post_meta($order->id, '_subscriptio_paypal_transaction_ids', $transaction_ids);

            // Complete the order
            $order->add_order_note(sprintf(__('PayPal automatic payment completed (paykey: %s, transaction id(s): %s)', 'subscriptio-paypal'), $pay_key, $transaction_ids));
            $order->payment_complete($transaction_ids);

            return true;
        }

        $order->add_order_note(__('Automatic subscription payment failed with unknown message: ', 'subscriptio-paypal') . $result_message);
        return false;
    }

    /**
     * Save preapproval key to subscription(s)
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function save_preapproval_key($order_id)
    {
        // Get subscriptions from order
        $subscriptions = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order_id);

        // And set preapproval keys for those subscriptions
        foreach ($subscriptions as $id => $subscription) {

            // Get all keys
            $preapproval_keys = get_user_meta($subscription->user_id, '_subscriptio_paypal_preapproval_keys', true);

            if (!empty($preapproval_keys) && is_array($preapproval_keys) && !empty($preapproval_keys[$subscription->id])) {

                // Get the key for this particular subscription
                $preapproval_key = $preapproval_keys[$subscription->id];

                // Add key to subscription if it's not yet set
                $post_meta_preapproval_key = get_post_meta($subscription->id, '_subscriptio_preapproval', true);
                if (empty($post_meta_preapproval_key)) {
                    update_post_meta($subscription->id, '_subscriptio_preapproval', $preapproval_key);
                }
            }
        }
    }

}

new Subscriptio_PayPal_Subscriptions();

}
