<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Payment Reminder email
 *
 * @class Subscriptio_Email_Customer_Subscription_Payment_Reminder
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Payment_Reminder')) {

class Subscriptio_Email_Customer_Subscription_Payment_Reminder extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_payment_reminder';
        $this->customer_email = true;
        $this->title          = __('Subscription payment reminder', 'subscriptio');
        $this->description    = __('Subscription payment reminder emails are sent to customers at predefined intervals when they have outstanding subscription payments.', 'subscriptio');

        $this->heading        = __('Subscription payment reminder', 'subscriptio');
        $this->subject        = __('Payment for subscription renewal order {order_number} has not been received', 'subscriptio');

        // Call parent constructor
        parent::__construct();
    }

    /**
     * Trigger a notification
     *
     * @access public
     * @param object $subscription
     * @param array $args
     * @param bool $send_to_admin
     * @return void
     */
    public function trigger($subscription, $args = array(), $send_to_admin = false)
    {
        if (!$subscription || !isset($subscription->last_order_id)) {
            return;
        }

        $order = RightPress_Helper::wc_get_order($subscription->last_order_id);

        if (!$order) {
            return;
        }

        $this->object = $order;

        if ($send_to_admin) {
            $this->recipient = get_option('admin_email');
        }
        else {
            $this->recipient = $this->object->billing_email;
        }

        // Replace macros
        $this->find[] = '{order_number}';
        $this->replace[] = $this->object->get_order_number();

        // Check if this email type is enabled, recipient is set and we are not on a development website
        if (!$this->is_enabled() || !$this->get_recipient() || !Subscriptio::is_main_site()) {
            return;
        }

        // Get next action and next action date
        $next_action_datetime = Subscriptio_Event_Scheduler::get_scheduled_event_datetime('payment', $subscription->id);
        $next_action_is_overdue = false;

        if ($subscription->calculate_overdue_time()) {
            $next_action = __('marked overdue', 'subscriptio');
            $next_action_is_overdue = true;
            $subsequent_action = $subscription->calculate_suspension_time() > 0 ? __('suspension', 'subscriptio') : __('cancellation', 'subscriptio');
        }
        else if ($subscription->calculate_suspension_time() > 0) {
            $next_action = __('suspended', 'subscriptio');
        }
        else {
            $next_action = __('cancelled', 'subscriptio');
        }

        $this->template_variables = array(
            'subscription'              => $subscription,
            'order'                     => $this->object,
            'email_heading'             => $this->get_heading(),
            'sent_to_admin'             => false,
            'next_action'               => $next_action,
            'next_action_datetime'      => $next_action_datetime,
            'next_action_is_overdue'    => $next_action_is_overdue,
            'subsequent_action'         => isset($subsequent_action) ? $subsequent_action : '',
        );

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

}
}
