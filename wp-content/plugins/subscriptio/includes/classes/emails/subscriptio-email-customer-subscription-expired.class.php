<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Expired email
 *
 * @class Subscriptio_Email_Customer_Subscription_Expired
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Expired')) {

class Subscriptio_Email_Customer_Subscription_Expired extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_expired';
        $this->customer_email = true;
        $this->title          = __('Subscription expired', 'subscriptio');
        $this->description    = __('Subscription expired emails are sent to customers when subscriptions expire (if they are configured to expire).', 'subscriptio');

        $this->heading        = __('Your subscription expired', 'subscriptio');
        $this->subject        = __('Your {site_title} subscription expired', 'subscriptio');

        // Call parent constructor
        parent::__construct();
    }

}
}
