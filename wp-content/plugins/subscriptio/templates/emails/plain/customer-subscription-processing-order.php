<?php

/**
 * Customer Subscription Processing Order email template (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo $email_heading . "\n\n";

echo sprintf(__('Your subscription renewal order is now being processed. Your order details are shown below for your reference:', 'subscriptio'), get_option('blogname')) . "\n\n";

echo "****************************************************\n\n";

do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text);

echo __('Order:', 'subscriptio') . ' ' . $order->get_order_number() . "\n";

do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text);

Subscriptio::include_template('emails/plain/email-order-items', array('order' => $order, 'plain_text' => true));

echo "\n****************************************************\n\n";

do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text);

echo __('Customer details', 'subscriptio') . "\n";

Subscriptio::include_template('emails/plain/email-customer-details', array('order' => $order, 'plain_text' => true));

echo "\n****************************************************\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
