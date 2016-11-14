<?php

/**
 * View for Subscription Edit page main subscription details block
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<style type="text/css">
    #post-body-content,
    #submitdiv {
        display: none;
    }
</style>

<div id="subscription_data">
    <h2>
        <?php echo __('Subscription', 'subscriptio') . ' ' . $subscription->get_subscription_number(); ?>
        <span class="subscription_edit_page_status subscription_status_<?php echo $subscription_statuses[$subscription->status]['title']; ?>"><?php echo $subscription_statuses[$subscription->status]['title']; ?></span>
    </h2>

    <p class="subscription_subheading">
        <?php echo $subscription->get_formatted_recurring_amount(); ?>
    </p>

    <div class="subscription_data_container">
        <div class="subscription_data_content">
            <h4><?php _e('General Details', 'subscriptio'); ?></h4>

            <p>
                <strong><?php _e('Started:', 'subscriptio'); ?></strong>
                <?php if ($subscription->started): ?>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->started, null, 'subscription_edit_started'); ?>
                <?php else: ?>
                    <span class="subscriptio_nothing_to_display">
                        <?php _e('Not started yet.', 'subscriptio'); ?>
                    </span>
                <?php endif; ?>
            </p>

            <?php if ($subscription->overdue_since): ?>
                <p>
                    <strong><?php _e('Overdue Since:', 'subscriptio'); ?></strong>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->overdue_since, null, 'subscription_edit_overdue_since'); ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->paused_since): ?>
                <p>
                    <strong><?php _e('Paused Since:', 'subscriptio'); ?></strong>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->paused_since, null, 'subscription_edit_paused_since'); ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->suspended_since): ?>
                <p>
                    <strong><?php _e('Suspended Since:', 'subscriptio'); ?></strong>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->suspended_since, null, 'subscription_edit_suspended_since'); ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->cancelled_since): ?>
                <p>
                    <strong><?php _e('Cancelled Since:', 'subscriptio'); ?></strong>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->cancelled_since, null, 'subscription_edit_cancelled_since'); ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->expired_since): ?>
                <p>
                    <strong><?php _e('Expired Since:', 'subscriptio'); ?></strong>
                    <?php echo Subscriptio::get_adjusted_datetime($subscription->expired_since, null, 'subscription_edit_expired_since'); ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->payment_method_title): ?>
                <p>
                    <strong><?php _e('Payment Method:', 'subscriptio'); ?></strong>
                    <?php echo $subscription->payment_method_title; ?>
                </p>
            <?php endif; ?>

            <?php if ($subscription->needs_shipping()): ?>
                <p>
                    <strong><?php _e('Shipping Method:', 'subscriptio'); ?></strong>
                    <?php echo $subscription->shipping['name']; ?>
                </p>
            <?php endif; ?>

            <p>
                <strong><?php _e('Related Orders:', 'subscriptio'); ?></strong>
                <?php foreach($subscription->all_order_ids as $order_id): ?>
                    <?php if (RightPress_Helper::post_is_active($order_id)): ?>
                        <?php RightPress_Helper::print_link_to_post($order_id); ?>
                    <?php else: ?>
                        <?php echo '#' . $order_id . ' (' . __('deleted', 'subscriptio') . ')'; ?>
                    <?php endif; ?>
                    <?php echo ($order_id != end($subscription->all_order_ids) ? ', ' : ''); ?>
                <?php endforeach; ?>
            </p>

        </div>
        <div class="subscription_data_content">
            <h4><?php _e('Customer Details', 'subscriptio'); ?></h4>

            <p>
                <strong><?php _e('Name:', 'subscriptio'); ?></strong>
                <?php echo Subscriptio::get_user_full_name_link($subscription->user_id, $subscription->user_full_name); ?>
            </p>

            <?php if ($user_email = get_user_meta($subscription->user_id, 'billing_email', true)): ?>
                <p>
                    <strong><?php _e('Email:', 'subscriptio'); ?></strong>
                    <a href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a>
                </p>
            <?php endif; ?>

            <?php if ($user_phone = get_user_meta($subscription->user_id, 'billing_phone', true)): ?>
                <p>
                    <strong><?php _e('Phone:', 'subscriptio'); ?></strong>
                    <?php echo $user_phone; ?>
                </p>
            <?php endif; ?>

            <?php if (is_array($subscription->shipping_address) && !empty($subscription->shipping_address)): ?>
                <p class="subscriptio_admin_address_paragaph">
                    <strong><?php _e('Shipping Address:', 'subscriptio'); ?>
                        <?php if (!$subscription->is_inactive()): ?>
                            &nbsp;&nbsp;<a id="subscriptio_admin_edit_address" href="#"><?php _e('[edit]', 'subscriptio'); ?></a>
                        <?php endif; ?>
                    </strong>
                    <span class="subscriptio_admin_address"><?php echo wp_kses(Subscriptio::get_formatted_shipping_address($subscription->shipping_address), array('br' => array())); ?></span>
                </p>
                <?php if (!$subscription->is_inactive()): ?>
                    <div class="subscriptio_admin_address_fields">
                        <?php foreach (Subscriptio_Subscription::get_admin_shipping_fields() as $key => $field): ?>
                            <?php if ($field['type'] == 'select'): ?>
                                <?php woocommerce_wp_select(array('id' => $key, 'value' => $subscription->shipping_address[$key], 'label' => $field['title'], 'options' => $field['values'])); ?>
                            <?php else: ?>
                                <?php woocommerce_wp_text_input(array('id' => $key, 'value' => $subscription->shipping_address[$key], 'label' => $field['title'])); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="subscriptio_admin_address_save">
                            <button type="submit" class="button subscriptio_save_address" name="subscriptio_subscription_button" value="address" title="<?php _e('Save Address', 'subscriptio'); ?>"><?php _e('Save Address', 'subscriptio'); ?></button>
                            <button class="button" id="subscriptio_cancel_address_edit" title="<?php _e('Cancel', 'subscriptio'); ?>"><?php _e('Cancel', 'subscriptio'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
        <div class="subscription_data_content">
            <h4><?php _e('Scheduled Events', 'subscriptio'); ?></h4>

            <?php $scheduled_events = false; ?>

            <?php if ($scheduled_order = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('order', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Renewal Order:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change renewal order date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_order);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_order, 'renewal_order'); ?>
                </p>
            <?php endif; ?>

            <?php if ($scheduled_reminder = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('reminder', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Payment Reminder:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change reminder date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_reminder);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_reminder, 'reminder'); ?>
                </p>
            <?php endif; ?>

            <?php if ($scheduled_payment = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('payment', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Payment Due:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change payment date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_payment);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_payment, 'payment'); ?>
                </p>
            <?php endif; ?>

            <?php if ($scheduled_suspension = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('suspension', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Suspension:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change suspension date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_suspension);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_suspension, 'suspension'); ?>
                </p>
            <?php endif; ?>

            <?php if ($scheduled_cancellation = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('cancellation', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Cancellation:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change cancellation date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_cancellation);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_cancellation, 'cancellation'); ?>
                </p>
            <?php endif; ?>

            <?php if ($scheduled_expiration = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('expiration', $subscription->id)): ?>
                <p>
                    <strong><?php _e('Expiration:', 'subscriptio'); ?></strong>
                    <a class="subscriptio_date_change_link" href="" title="<?php _e('Change expiration date', 'subscriptio'); ?>">
                        <?php echo Subscriptio::get_adjusted_datetime($scheduled_expiration);
                        $scheduled_events = true; ?>
                    </a>
                    <?php echo $subscription->get_date_change_fields($scheduled_expiration, 'expiration'); ?>
                </p>
            <?php endif; ?>

            <?php if (!$scheduled_events): ?>
                <p class="subscriptio_nothing_to_display">
                    <?php _e('No events scheduled.', 'subscriptio'); ?>
                </p>
            <?php endif; ?>

        </div>
    </div>

</div>
