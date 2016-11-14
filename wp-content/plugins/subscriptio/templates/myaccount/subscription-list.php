<?php

/**
 * Customer Subscription List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php if ($display_title): ?>
    <h2><?php echo $title; ?></h2>
<?php endif; ?>

<?php if (!empty($subscriptions)): ?>

    <?php do_action('subscriptio_before_subscription_list'); ?>

    <table class="shop_table shop_table_responsive subscriptio_subscription_list my_account_orders">

        <thead>
            <tr>
                <th class="subscriptio_list_id"><?php _e('ID', 'subscriptio'); ?></th>
                <th class="subscriptio_list_status"><?php _e('Status', 'subscriptio'); ?></th>
                <th class="subscriptio_list_product"><?php _e('Products', 'subscriptio'); ?></th>
                <th class="subscriptio_list_recurring"><?php _e('Recurring', 'subscriptio'); ?></th>
                <th class="subscriptio_list_actions">&nbsp;</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($subscriptions as $subscription): ?>

            <tr class="subscriptio_subscription_list_subscription">
                <td data-title="<?php _e('ID', 'subscriptio'); ?>" class="subscriptio_list_id"><?php echo '<a href="' . $subscription->get_frontend_link('view-subscription') . '">' . $subscription->get_subscription_number() . '</a>'; ?></td>
                <td data-title="<?php _e('Status', 'subscriptio'); ?>" class="subscriptio_list_status"><?php echo $subscription->get_formatted_status(true); ?></td>
                <td data-title="<?php _e('Products', 'subscriptio'); ?>" class="subscriptio_list_product">
                    <?php foreach (Subscriptio_Subscription::get_subscription_items($subscription->id) as $item): ?>
                        <?php if (!$item['deleted']): ?>
                            <?php RightPress_Helper::print_frontend_link_to_post($item['product_id'], $item['name'], '', ($item['quantity'] > 1 ? 'x ' . $item['quantity'] : '')); ?>
                        <?php else: ?>
                            <?php echo $item['name']; ?>
                        <?php endif; ?>
                        <?php echo '<br>'; ?>
                    <?php endforeach; ?>
                </td>
                <td data-title="<?php _e('Recurring', 'subscriptio'); ?>" class="subscriptio_list_recurring"><?php echo $subscription->get_formatted_recurring_amount(); ?></td>
                <td class="order-actions subscriptio_list_actions">
                    <?php foreach ($subscription->get_frontend_actions() as $action_key => $action): ?>
                        <a href="<?php echo $action['url']; ?>" class="button subscriptio_button_<?php echo sanitize_html_class($action_key); ?>"><?php echo $action['title']; ?></a>
                    <?php endforeach; ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <?php do_action('subscriptio_after_subscription_list'); ?>

<?php else: ?>

    <p><?php _e('You have no subscriptions.', 'subscriptio'); ?></p>

<?php endif; ?>
