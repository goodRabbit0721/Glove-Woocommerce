<?php

/**
 * Customer email subscription items
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'subscriptio'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'subscriptio'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Total Recurring', 'subscriptio'); ?></th>
        </tr>
    </thead>
    <tbody>

        <?php foreach($subscription->get_items() as $item): ?>
            <tr>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $item['name']; ?></td>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['quantity']; ?></td>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $subscription->get_formatted_price($item['total'] + $item['tax']); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ($subscription->needs_shipping()): ?>
            <tr>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php echo $subscription->shipping['name']; ?></td>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo '1'; ?></td>
                <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $subscription->get_formatted_price(($subscription->renewal_order_shipping + $subscription->renewal_order_shipping_tax)); ?></td>
            </tr>
        <?php endif; ?>

    </tbody>
</table>
