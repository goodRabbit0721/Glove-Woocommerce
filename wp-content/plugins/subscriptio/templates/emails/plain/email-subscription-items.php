<?php

/**
 * Customer email subscription items (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo "\n\n" . sprintf(__('Product: %s', 'subscriptio'), ($subscription->variation_id ? sprintf(__('Variation #%1$s of', 'subscriptio'), $subscription->variation_id) . ' ' : '') . $subscription->product_name);

echo "\n" . sprintf(__('Quantity: %s', 'subscriptio'), $subscription->quantity);

echo "\n" . sprintf(__('Cost: %s', 'subscriptio'), $subscription->get_formatted_recurring_amount());

if ($subscription->needs_shipping()) {

    echo "\n\n" . sprintf(__('Shipping: %s', 'subscriptio'), $subscription->shipping['name']);

    echo "\n" . sprintf(__('Quantity: %s', 'subscriptio'), $subscription->quantity);

    echo "\n" . sprintf(__('Cost: %s', 'subscriptio'), $subscription->get_formatted_price(($subscription->renewal_order_shipping + $subscription->renewal_order_shipping_tax)));

}
