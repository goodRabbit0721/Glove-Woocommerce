<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to automatic payments
 *
 * @class Subscriptio_Automatic_Payments
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Automatic_Payments')) {

class Subscriptio_Automatic_Payments
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Check if order is renewal order
        add_filter('woocommerce_stripe_is_renewal_order', array($this, 'is_renewal_order'), 10, 2);

        // Attempt to process automatic payment
        add_filter('subscriptio_automatic_payment', array($this, 'process_payment'), 10, 3);
    }

    /**
     * Check if order is renewal order
     *
     * @access public
     * @param bool $is_renewal
     * @param object $order
     * @return bool
     */
    public function is_renewal_order($is_renewal, $order)
    {
        return Subscriptio_Order_Handler::order_is_renewal($order->id);
    }

    /**
     * Process automatic payment
     *
     * @access public
     * @param bool $payment_processed
     * @param object $order
     * @param object $subscription
     * @return bool
     */
    public function process_payment($payment_processed, $order, $subscription)
    {
        global $woocommerce;

        $selected_gateway = null;

        // Load payment methods
        if ($woocommerce->payment_gateways) {
            foreach ($woocommerce->payment_gateways->payment_gateways() as $gateway) {
                if ($gateway->id == $subscription->payment_method) {
                    if ($gateway->supports('subscriptio')) {
                        $selected_gateway = $gateway->id;
                    }
                    break;
                }
            }
        }

        // Gateway is active and supports Subscriptio
        if ($selected_gateway) {
            return apply_filters('subscriptio_automatic_payment_' . $selected_gateway, false, $order, $subscription);
        }

        return false;
    }

}

new Subscriptio_Automatic_Payments();

}
