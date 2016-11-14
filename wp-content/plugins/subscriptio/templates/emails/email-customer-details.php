<?php

/**
 * Customer email customer details
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php if ($order->billing_email): ?>
	<p><strong><?php _e('Email:', 'subscriptio'); ?></strong> <?php echo $order->billing_email; ?></p>
<?php endif; ?>
<?php if ($order->billing_phone): ?>
	<p><strong><?php _e('Tel:', 'subscriptio'); ?></strong> <?php echo $order->billing_phone; ?></p>
<?php endif; ?>

<?php wc_get_template('emails/email-addresses.php', array('order' => $order)); ?>
