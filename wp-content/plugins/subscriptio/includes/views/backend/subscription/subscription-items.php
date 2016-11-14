<?php

/**
 * View for Subscription Edit page Subscription Items block
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<table class="subscriptio_subscription_items_list">
    <thead>
        <tr>
            <th class="subscriptio_subscription_items_list_item"><?php _e('Item', 'subscriptio'); ?></th>
            <th class="subscriptio_subscription_items_list_quantity"><?php _e('Qty', 'subscriptio'); ?></th>
            <th class="subscriptio_subscription_items_list_total"><?php _e('Total', 'subscriptio'); ?></th>
            <th class="subscriptio_subscription_items_list_tax"><?php _e('Tax', 'subscriptio'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($subscription->get_items() as $item): ?>
            <tr>
                <td class="subscriptio_subscription_items_list_item">
                    <?php if (!$item['deleted']): ?>
                        <?php RightPress_Helper::print_link_to_post($item['product_id'], $item['name'], '', ($item['quantity'] > 1 ? 'x ' . $item['quantity'] : '')); ?>
                    <?php else: ?>
                        <?php echo $item['name']; ?>
                    <?php endif; ?>
                </td>
                <td class="subscriptio_subscription_items_list_quantity"><?php echo $item['quantity']; ?></td>
                <td class="subscriptio_subscription_items_list_total"><?php echo $subscription->get_formatted_price($item['total']); ?></td>
                <td class="subscriptio_subscription_items_list_tax"><?php echo $subscription->get_formatted_price($item['tax']); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ($subscription->needs_shipping()): ?>
            <tr>
                <td class="subscriptio_subscription_items_list_item"><?php echo $subscription->shipping['name']; ?></td>
                <td class="subscriptio_subscription_items_list_quantity"><?php echo '1'; ?></td>
                <td class="subscriptio_subscription_items_list_total"><?php echo $subscription->get_formatted_price($subscription->renewal_order_shipping); ?></td>
                <td class="subscriptio_subscription_items_list_tax"><?php echo $subscription->get_formatted_price($subscription->renewal_order_shipping_tax); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
