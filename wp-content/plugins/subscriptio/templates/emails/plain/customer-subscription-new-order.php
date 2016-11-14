<?php

/**
 * Customer Subscription New Order email template (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo $email_heading . "\n\n";

echo sprintf(__('A new subscription renewal order has been generated and is pending payment. To pay for this order please use the following link: %s', 'subscriptio'), esc_url($order->get_checkout_payment_url())) . "\n\n";

echo "****************************************************\n\n";

do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text);

echo sprintf(__('Order ID: %s', 'subscriptio'), $order->get_order_number()) . "\n";
echo sprintf(__('Subscription ID: %s', 'subscriptio'), $subscription->get_subscription_number()) . "\n";

do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text);

Subscriptio::include_template('emails/plain/email-order-items', array('order' => $order, 'plain_text' => true));

echo "\n****************************************************\n\n";

do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text);

echo __('Customer details', 'subscriptio') . "\n";

Subscriptio::include_template('emails/plain/email-customer-details', array('order' => $order, 'plain_text' => true));

echo "\n****************************************************\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
