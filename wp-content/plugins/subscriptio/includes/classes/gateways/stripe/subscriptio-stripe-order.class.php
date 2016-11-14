<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to WooCommerce Orders
 *
 * @class Subscriptio_Stripe_Order
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Stripe_Order')) {

class Subscriptio_Stripe_Order
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
        // Capture authorized payments
        add_action('woocommerce_order_status_processing', array($this, 'capture'));
        add_action('woocommerce_order_status_completed', array($this, 'capture'));
    }

    /**
     * Capture previously authorized payment
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function capture($order_id)
    {
        $order = RightPress_Helper::wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Authorized and not yet charged?
        if ($order->payment_method == 'subscriptio_stripe' && get_post_meta($order_id, '_subscriptio_stripe_charge_captured', true) === 'no') {

            // Get charge id
            $charge_id = get_post_meta($order_id, '_subscriptio_stripe_charge_id', true);

            if (empty($charge_id)) {
                return;
            }

            // Load payment gateway object to access its methods
            $gateway = new Subscriptio_Stripe_Gateway();

            // Send request to capture payment
            $response = $gateway->send_request('charges', 'capture', array(
                'id'        => $charge_id,
                'amount'    => $order->order_total * apply_filters('subscriptio_stripe_decimals_in_currency', 100, $order),
            ));

            // Request failed?
            if (!is_object($response)) {
                $order->add_order_note(__('Failed capturing previously authorized Stripe payment.', 'subscriptio-stripe') . ' ' . $response);
                return;
            }

            // Received error from Stripe?
            if (!empty($response->error)) {
                $order->add_order_note(__('Failed capturing previously authorized Stripe payment.', 'subscriptio-stripe') . ' ' . $response->error->message);
                return;
            }

            // Everything seems to be ok, let's mark order as paid
            $order->add_order_note(sprintf(__('Stripe charge %s captured.', 'subscriptio-stripe'), $response->id));
            update_post_meta($order->id, '_subscriptio_stripe_charge_captured', 'yes');
        }
    }

}

new Subscriptio_Stripe_Order();

}
