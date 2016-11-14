<?php

/**
 * Customer email customer details (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if ($order->billing_email) {
    echo __('Email:', 'subscriptio');
    echo $order->billing_email . "\n";
}
if ($order->billing_phone) {
    echo __('Tel:', 'subscriptio');
    echo $order->billing_phone . "\n";
}

wc_get_template('emails/plain/email-addresses.php', array('order' => $order));
