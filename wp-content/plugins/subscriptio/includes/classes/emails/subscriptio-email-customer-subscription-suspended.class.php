<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Suspended email
 *
 * @class Subscriptio_Email_Customer_Subscription_Suspended
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Suspended')) {

class Subscriptio_Email_Customer_Subscription_Suspended extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_suspended';
        $this->customer_email = true;
        $this->title          = __('Subscription suspended', 'subscriptio');
        $this->description    = __('Subscription cancelled emails are sent to customers when subscriptions are suspended due to non-payment (if suspensions are enabled).', 'subscriptio');

        $this->heading        = __('Your subscription has been suspended', 'subscriptio');
        $this->subject        = __('Your {site_title} subscription has been suspended', 'subscriptio');

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

        $this->object = $subscription;

        if ($send_to_admin) {
            $this->recipient = get_option('admin_email');
        }
        else {
            $this->recipient = get_user_meta($subscription->user_id, 'billing_email', true);
        }

        // Check if this email type is enabled, recipient is set and we are not on a development website
        if (!$this->is_enabled() || !$this->get_recipient() || !Subscriptio::is_main_site()) {
            return;
        }

        // Get next action date and time
        $next_action_datetime = Subscriptio_Event_Scheduler::get_scheduled_event_datetime('cancellation', $subscription->id);

        $this->template_variables = array(
            'subscription'          => $this->object,
            'order'                 => $order,
            'email_heading'         => $this->get_heading(),
            'sent_to_admin'         => false,
            'next_action_datetime'  => $next_action_datetime,
        );

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

}
}
