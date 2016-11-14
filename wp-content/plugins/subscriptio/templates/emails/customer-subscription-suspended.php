<?php

/**
 * Customer Subscription Cancelled email template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php printf(__('Your subscription on %s has been suspended.', 'subscriptio'), get_option('blogname')); ?></p>

<p><?php printf(__('If you do not pay for the order by <strong>%s</strong>, your %s will be <strong>%s</strong>.', 'subscriptio'), $next_action_datetime, __('subscription', 'subscriptio'), __('cancelled', 'subscriptio')); ?></p>

<p><?php printf(__('To avoid this please use the following link to pay: %s', 'subscriptio'), '<a href="' . esc_url($order->get_checkout_payment_url()) . '">' . __('pay now', 'subscriptio') . '</a>'); ?></p>

<p><?php _e('Details of the suspended subscription are shown below for your reference:', 'subscriptio'); ?></p>

<?php do_action('subscriptio_email_before_subscription_table', $subscription, $sent_to_admin, $plain_text); ?>

<h2><?php echo __('Subscription:', 'subscriptio') . ' ' . $subscription->get_subscription_number(); ?></h2>
<?php Subscriptio::include_template('emails/email-subscription-items', array('subscription' => $subscription, 'plain_text' => false)); ?>

<?php do_action('subscriptio_email_after_subscription_table', $subscription, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_footer'); ?>
