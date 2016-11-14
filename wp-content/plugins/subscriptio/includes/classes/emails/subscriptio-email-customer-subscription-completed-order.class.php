<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customer Subscription Completed Order email
 *
 * @class Subscriptio_Email_Customer_Subscription_Completed_Order
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email_Customer_Subscription_Completed_Order')) {

class Subscriptio_Email_Customer_Subscription_Completed_Order extends Subscriptio_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->id             = 'customer_subscription_completed_order';
        $this->customer_email = true;
        $this->title          = __('Subscription completed order', 'subscriptio');
        $this->description    = __('Subscription completed order emails are sent to the customer when subscription renewal order is marked complete.', 'subscriptio');

        $this->heading        = __('Your subscription renewal order is complete', 'subscriptio');
        $this->subject        = __('Your {site_title} subscription renewal order from {order_date} is complete', 'subscriptio');

        $this->heading_downloadable = $this->get_option('heading_downloadable', __('Your subscription renewal order is complete - download your files', 'subscriptio'));
        $this->subject_downloadable = $this->get_option('subject_downloadable', __('Your {site_title} subscription renewal order is complete - download your files', 'subscriptio'));

        // Call parent constructor
        parent::__construct();
    }

    /**
     * Trigger a notification
     *
     * @access public
     * @param object $order
     * @param array $args
     * @param bool $send_to_admin
     * @return void
     */
    public function trigger($order, $args = array(), $send_to_admin = false)
    {
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
        $this->find[] = '{order_date}';
        $this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->order_date));

        // Check if this email type is enabled, recipient is set and we are not on a development website
        if (!$this->is_enabled() || !$this->get_recipient() || !Subscriptio::is_main_site()) {
            return;
        }

        // Get subscription
        $subscription = Subscriptio_Order_Handler::get_subscriptions_from_order_id($order->id);
        $subscription = reset($subscription);

        if (!$subscription) {
            return;
        }

        $this->template_variables = array(
            'subscription'  => $subscription,
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
        );

        $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    /**
     * Get subject
     *
     * @access public
     * @return string
     */
    public function get_subject()
    {
        if (!empty($this->object) && $this->object->has_downloadable_item()) {
            return apply_filters('subscriptio_email_subject_' . $this->id, $this->format_string($this->subject_downloadable), $this->object);
        }
        else {
            return apply_filters('subscriptio_email_subject_' . $this->id, $this->format_string($this->subject), $this->object);
        }
    }

    /**
     * Get heading
     *
     * @access public
     * @return string
     */
    public function get_heading()
    {
        if (!empty($this->object) && $this->object->has_downloadable_item()) {
            return apply_filters('subscriptio_email_heading_' . $this->id, $this->format_string($this->heading_downloadable), $this->object);
        }
        else {
            return apply_filters('subscriptio_email_heading_' . $this->id, $this->format_string($this->heading), $this->object);
        }
    }

    /**
     * Initialise settings form fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Enable/Disable', 'subscriptio'),
                'type'      => 'checkbox',
                'label'     => __('Enable this email notification', 'subscriptio'),
                'default'   => 'yes',
            ),
            'send_to_admin' => array(
                'title'     => __('Send to admin?', 'subscriptio'),
                'type'      => 'checkbox',
                'label'     => __('Send copy of this email to admin', 'subscriptio'),
                'default'   => 'no',
            ),
            'subject' => array(
                'title'         => __('Subject', 'subscriptio'),
                'type'          => 'text',
                'description'   => sprintf(__('Defaults to <code>%s</code>', 'subscriptio'), $this->subject),
                'placeholder'   => '',
                'default'       => '',
            ),
            'heading' => array(
                'title'         => __('Email Heading', 'subscriptio'),
                'type'          => 'text',
                'description'   => sprintf(__('Defaults to <code>%s</code>', 'subscriptio'), $this->heading),
                'placeholder'   => '',
                'default'       => '',
            ),
            'subject_downloadable' => array(
                'title'         => __('Subject (downloadable)', 'subscriptio'),
                'type'          => 'text',
                'description'   => sprintf(__('Defaults to <code>%s</code>', 'subscriptio'), $this->subject_downloadable),
                'placeholder'   => '',
                'default'       => '',
            ),
            'heading_downloadable' => array(
                'title'         => __('Email Heading (downloadable)', 'subscriptio'),
                'type'          => 'text',
                'description'   => sprintf(__('Defaults to <code>%s</code>', 'subscriptio'), $this->heading_downloadable),
                'placeholder'   => '',
                'default'       => '',
            ),
            'email_type' => array(
                'title'         => __('Email type', 'subscriptio'),
                'type'          => 'select',
                'description'   => __('Choose which format of email to send.', 'subscriptio'),
                'default'       => 'html',
                'class'         => 'email_type',
                'options'       => array(
                    'plain'         => __('Plain text', 'subscriptio'),
                    'html'          => __('HTML', 'subscriptio'),
                    'multipart'     => __('Multipart', 'subscriptio'),
                ),
            ),
        );
    }

}
}
