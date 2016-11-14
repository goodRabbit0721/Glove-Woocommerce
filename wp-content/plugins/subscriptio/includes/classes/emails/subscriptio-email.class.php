<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Extends WC_Email class to override some methods
 *
 * @class Subscriptio_Email
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Email')) {

class Subscriptio_Email extends WC_Email
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Template file name
        $this->template = str_replace('_', '-', $this->id);

        // Send a copy to admin?
        $this->send_to_admin = $this->get_option('send_to_admin') === 'yes';

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
        if (!$subscription) {
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

        $this->template_variables = array(
            'subscription' => $this->object,
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
        return apply_filters('subscriptio_email_subject_' . $this->id, $this->format_string($this->subject), $this->object);
    }

    /**
     * Get heading
     *
     * @access public
     * @return string
     */
    public function get_heading()
    {
        return apply_filters('subscriptio_email_heading_' . $this->id, $this->format_string($this->heading), $this->object);
    }

    /**
     * Get recipient
     *
     * @access public
     * @return string
     */
    public function get_recipient()
    {
        return apply_filters('subscriptio_email_recipient_' . $this->id, $this->recipient, $this->object);
    }

    /**
     * Get HTML email content
     *
     * @access public
     * @return string
     */
    public function get_content_html()
    {
        ob_start();
        Subscriptio::include_template('emails/' . $this->template, array_merge($this->template_variables, array('plain_text' => false)));
        return ob_get_clean();
    }

    /**
     * Get plain text email content
     *
     * @access public
     * @return string
     */
    public function get_content_plain()
    {
        ob_start();
        Subscriptio::include_template('emails/plain/' . $this->template, array_merge($this->template_variables, array('plain_text' => true)));
        return ob_get_clean();
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
                'title'         => __('Email heading', 'subscriptio'),
                'type'          => 'text',
                'description'   => sprintf(__('Defaults to <code>%s</code>', 'subscriptio'), $this->heading),
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
