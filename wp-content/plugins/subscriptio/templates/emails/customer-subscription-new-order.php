<?php

/**
 * Customer Subscription New Order email template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php _e('A new subscription renewal order has been generated and is pending payment.', 'subscriptio'); ?></p>

<p><?php _e('To pay for this order please use the following link:', 'subscriptio'); ?></p>
<p style="padding:10px 0;"><a style="background-color:#557da1;padding:10px 15px;color:#fff;text-decoration:none;font-weight:bold;" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"><?php _e('pay now', 'subscriptio'); ?></a></p>

<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text); ?>

<h2><?php echo __('Order:', 'subscriptio') . ' ' . $order->get_order_number(); ?></h2>
<?php Subscriptio::include_template('emails/email-order-items', array('order' => $order, 'plain_text' => false)); ?>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text); ?>

<h2><?php _e('Customer details', 'subscriptio'); ?></h2>
<?php Subscriptio::include_template('emails/email-customer-details', array('order' => $order, 'plain_text' => false)); ?>

<?php do_action('woocommerce_email_footer'); ?>
