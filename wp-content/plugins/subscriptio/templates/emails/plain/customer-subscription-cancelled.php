<?php

/**
 * Customer Subscription Cancelled email template (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo $email_heading . "\n\n";

echo sprintf(__('Your subscription on %s has been cancelled.', 'subscriptio'), get_option('blogname')) . "\n\n";

echo __('Details of the cancelled subscription are shown below for your reference:', 'subscriptio') . "\n\n";

echo "****************************************************\n";

do_action('subscriptio_email_before_subscription_table', $subscription, $sent_to_admin, $plain_text);

echo __('Subscription:', 'subscriptio') . ' ' . $subscription->get_subscription_number() . "\n";

Subscriptio::include_template('emails/plain/email-subscription-items', array('subscription' => $subscription, 'plain_text' => true));

echo "\n****************************************************\n\n";

do_action('subscriptio_email_after_subscription_table', $subscription, $sent_to_admin, $plain_text);

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
