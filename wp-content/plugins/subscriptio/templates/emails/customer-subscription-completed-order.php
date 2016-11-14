<?php

/**
 * Customer Subscription Completed Order email template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php printf(__('Hi there. Your recent subscription renewal order on %s has been completed. Your order details are shown below for your reference:', 'subscriptio'), get_option('blogname')); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text); ?>

<h2><?php echo __('Order:', 'subscriptio') . ' ' . $order->get_order_number(); ?></h2>
<?php Subscriptio::include_template('emails/email-order-items', array('order' => $order, 'plain_text' => false)); ?>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text); ?>

<?php do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text); ?>

<h2><?php _e('Customer details', 'subscriptio'); ?></h2>
<?php Subscriptio::include_template('emails/email-customer-details', array('order' => $order, 'plain_text' => false)); ?>

<?php do_action('woocommerce_email_footer'); ?>
