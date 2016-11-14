<?php

/**
 * Customer email order items (plain text)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo "\n" . $order->email_order_items_table (!RightPress_Helper::wc_version_gte('2.5') ? $order->email_order_items_table(false, true, '', '', '', true) : $order->email_order_items_table(array('show_sku' => true, 'plain_text' => true)));

echo "----------\n\n";

if ($totals = $order->get_order_item_totals()) {
    foreach ($totals as $total) {
        echo $total['label'] . "\t " . $total['value'] . "\n";
    }
}
