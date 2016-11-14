<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Resumed email
 *
 * @class Subscriptio_Email_Customer_Subscription_Resumed
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Resumed')) {

class Subscriptio_Email_Customer_Subscription_Resumed extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_resumed';
        $this->customer_email = true;
        $this->title          = __('Subscription resumed', 'subscriptio');
        $this->description    = __('Subscription resumed emails are sent to customers when subscriptions are resumed (unpaused) by administrator or customers (if they are allowed to).', 'subscriptio');

        $this->heading        = __('Your subscription has been resumed', 'subscriptio');
        $this->subject        = __('Your {site_title} subscription has been resumed', 'subscriptio');

        // Call parent constructor
        parent::__construct();
    }

}
}
