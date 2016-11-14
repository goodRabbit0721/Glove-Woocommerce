<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Cancelled email
 *
 * @class Subscriptio_Email_Customer_Subscription_Cancelled
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Cancelled')) {

class Subscriptio_Email_Customer_Subscription_Cancelled extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_cancelled';
        $this->customer_email = true;
        $this->title          = __('Subscription cancelled', 'subscriptio');
        $this->description    = __('Subscription cancelled emails are sent to customers when subscriptions are cancelled - either manually (by customer or shop manager) or automatically (due to non-payment).', 'subscriptio');

        $this->heading        = __('Your subscription has been cancelled', 'subscriptio');
        $this->subject        = __('Your {site_title} subscription has been cancelled', 'subscriptio');

        // Call parent constructor
        parent::__construct();
    }

}
}
