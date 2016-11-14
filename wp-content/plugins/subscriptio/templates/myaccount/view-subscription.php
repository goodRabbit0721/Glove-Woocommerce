<?php

/**
 * Customer Subscription View
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php wc_print_notices(); ?>

<?php if (Subscriptio::my_account_supports_tabbed_navigation()): ?>
    <?php do_action('woocommerce_account_navigation'); ?>
    <div class="woocommerce-MyAccount-content">
<?php endif; ?>

<?php do_action('subscriptio_before_subscription', $subscription); ?>

<?php if ($subscription->status == 'pending'): ?>
    <p class="subscriptio_subscription_info"><?php printf(__('Subscription <mark class="subscriptio_subscription_info_number">%s</mark> is pending first payment.', 'subscriptio'), $subscription->get_subscription_number()); ?></p>
<?php elseif (!$subscription->is_inactive()): ?>
    <p class="subscriptio_subscription_info"><?php printf(__('Subscription <mark class="subscriptio_subscription_info_number">%s</mark> was started on <mark class="subscriptio_subscription_info_start">%s</mark> and is currently <mark class="subscriptio_subscription_info_status">%s</mark>%s.', 'subscriptio'), $subscription->get_subscription_number(), Subscriptio::get_adjusted_datetime($subscription->started, null, 'subscription_frontend_started'), $subscription->get_formatted_status(), $subscription->get_status_details()); ?></p>
<?php elseif ($subscription->status == 'cancelled'): ?>
    <p class="subscriptio_subscription_info"><?php printf(__('Subscription <mark class="subscriptio_subscription_info_number">%s</mark> has been <mark class="subscriptio_subscription_info_status">%s</mark>.', 'subscriptio'), $subscription->get_subscription_number(), $subscription->get_formatted_status()); ?></p>
<?php else: ?>
    <p class="subscriptio_subscription_info"><?php printf(__('Subscription <mark class="subscriptio_subscription_info_number">%s</mark> has <mark class="subscriptio_subscription_info_status">%s</mark>.', 'subscriptio'), $subscription->get_subscription_number(), $subscription->get_formatted_status()); ?></p>
<?php endif; ?>

<div class="subscriptio_frontend_details">
    <div class="subscriptio_frontend_details_general">
        <h2><?php _e('Subscription Details', 'subscriptio'); ?></h2>

            <dl>
                <?php if ($subscription->overdue_since): ?>
                    <dt><?php _e('Overdue Since:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($subscription->overdue_since, null, 'subscription_frontend_overdue_since'); ?></dd>
                <?php endif; ?>

                <?php if ($subscription->paused_since): ?>
                    <dt><?php _e('Paused Since:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($subscription->paused_since, null, 'subscription_frontend_paused_since'); ?></dd>
                <?php endif; ?>

                <?php if ($subscription->suspended_since): ?>
                    <dt><?php _e('Suspended Since:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($subscription->suspended_since, null, 'subscription_frontend_suspended_since'); ?></dd>
                <?php endif; ?>

                <?php if ($subscription->cancelled_since): ?>
                    <dt><?php _e('Cancelled Since:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($subscription->cancelled_since, null, 'subscription_frontend_cancelled_since'); ?></dd>
                <?php endif; ?>

                <?php if ($subscription->expired_since): ?>
                    <dt><?php _e('Expired Since:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($subscription->expired_since, null, 'subscription_frontend_expired_since'); ?></dd>
                <?php endif; ?>

                <dt><?php _e('Recurring Amount:', 'subscriptio'); ?></dt><dd><?php echo $subscription->get_formatted_recurring_amount(); ?></dd>

                <?php if ($subscription->payment_method_title): ?>
                    <dt><?php _e('Payment Method:', 'subscriptio'); ?></dt><dd><?php echo $subscription->payment_method_title; ?></dd>
                <?php endif; ?>

                <?php if ($scheduled_payment = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('payment', $subscription->id)): ?>
                    <dt><?php _e('Payment Due:', 'subscriptio'); ?></dt><dd><?php echo Subscriptio::get_adjusted_datetime($scheduled_payment, null, 'subscription_frontend_payment_due'); ?></dd>
                <?php endif; ?>

                <?php $actions = $subscription->get_frontend_actions(false); ?>
                <?php if (!empty($actions)): ?>
                    <dt><?php _e('Actions:', 'subscriptio'); ?></dt><dd>
                    <?php foreach ($actions as $action_key => $action): ?>
                        <a href="<?php echo $action['url']; ?>" id="subscriptio_button_<?php echo sanitize_html_class($action_key); ?>" class="button subscriptio_button subscriptio_button_<?php echo sanitize_html_class($action_key); ?>"><?php echo $action['title']; ?></a>
                    <?php endforeach; ?>
                    </dd>
                <?php endif; ?>
            </dl>

    </div>
    <?php if ($subscription->needs_shipping()): ?>
        <div class="subscriptio_frontend_details_shipping">
            <h2><?php _e('Shipping Details', 'subscriptio'); ?></h2>

            <dl>
                <dt><?php _e('Shipping Method:', 'subscriptio'); ?></dt><dd><?php echo $subscription->shipping['name']; ?></dd>
                <dt><?php _e('Shipping Address:', 'subscriptio'); ?></dt><dd>
                    <address><p>
                        <?php echo wp_kses(Subscriptio::get_formatted_shipping_address($subscription->shipping_address), array('br' => array())); ?>
                    </p></address>
                </dd>
            </dl>

        </div>
    <?php endif; ?>
    <div style="clear: both;"></div>
</div>

<h2><?php _e('Subscription Items', 'subscriptio'); ?></h2>

<table class="shop_table subscriptio_frontend_items_list">
    <thead>
        <tr>
            <th class="subscriptio_frontend_items_list_item"><?php _e('Item', 'subscriptio'); ?></th>
            <th class="subscriptio_frontend_items_list_quantity"><?php _e('Qty', 'subscriptio'); ?></th>
            <th class="subscriptio_frontend_items_list_total"><?php _e('Total', 'subscriptio'); ?></th>
            <th class="subscriptio_frontend_items_list_tax"><?php _e('Tax', 'subscriptio'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($subscription->get_items() as $item): ?>
            <tr>
                <td class="subscriptio_frontend_items_list_item">
                    <?php if (!$item['deleted']): ?>
                        <?php RightPress_Helper::print_frontend_link_to_post($item['product_id'], $item['name'], '', ($item['quantity'] > 1 ? 'x ' . $item['quantity'] : '')); ?>
                    <?php else: ?>
                        <?php echo $item['name']; ?>
                    <?php endif; ?>
                    <?php $subscription->show_variable_item_meta($item); ?>
                </td>
                <td class="subscriptio_frontend_items_list_quantity"><?php echo $item['quantity']; ?></td>
                <td class="subscriptio_frontend_items_list_total"><?php echo $subscription->get_formatted_price($item['total']); ?></td>
                <td class="subscriptio_frontend_items_list_tax"><?php echo $subscription->get_formatted_price($item['tax']); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ($subscription->needs_shipping()): ?>
            <tr>
                <td class="subscriptio_frontend_items_list_item"><?php echo $subscription->shipping['name']; ?></td>
                <td class="subscriptio_frontend_items_list_quantity"><?php echo '1'; ?></td>
                <td class="subscriptio_frontend_items_list_total"><?php echo $subscription->get_formatted_price($subscription->renewal_order_shipping); ?></td>
                <td class="subscriptio_frontend_items_list_tax"><?php echo $subscription->get_formatted_price($subscription->renewal_order_shipping_tax); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php define('SUBSCRIPTIO_PRINTING_RELATED_ORDERS', $subscription->id); ?>
<?php wc_get_template('myaccount/my-orders.php', array('order_count' => -1)); ?>

<?php do_action('subscriptio_after_subscription', $subscription); ?>

<?php if (Subscriptio::my_account_supports_tabbed_navigation()): ?>
    </div>
<?php endif; ?>
