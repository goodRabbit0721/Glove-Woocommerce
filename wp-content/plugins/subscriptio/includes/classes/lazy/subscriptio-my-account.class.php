<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Override WooCommerce My Account shortcode output method
 *
 * @class Subscriptio_My_Account
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_My_Account')) {

class Subscriptio_My_Account extends WC_Shortcode_My_Account
{
    /**
     * Override WC_Shortcode_My_Account method output()
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($atts) {

        global $woocommerce, $wp;

        if (is_null(WC()->cart)) {
            return;
        }

        // User logged in?
        if (is_user_logged_in()) {

            // View Subscription
            if (!empty($wp->query_vars['view-subscription'])) {
                self::view_subscription(absint($wp->query_vars['view-subscription']));
            }
            else if (!empty($wp->query_vars['subscription-address'])) {
                self::subscription_address(absint($wp->query_vars['subscription-address']));
            }
            else if (!empty($wp->query_vars['pause-subscription'])) {
                self::pause_subscription(absint($wp->query_vars['pause-subscription']));
            }
            else if (!empty($wp->query_vars['resume-subscription'])) {
                self::resume_subscription(absint($wp->query_vars['resume-subscription']));
            }
            else if (!empty($wp->query_vars['cancel-subscription'])) {
                self::cancel_subscription(absint($wp->query_vars['cancel-subscription']));
            }
            else {
                parent::output($atts);
            }
        }
        else {
            parent::output($atts);
        }
    }

    /**
     * Check if current frontend user owns subscription and return it
     *
     * @access public
     * @param int $subscription_id
     * @return object|bool
     */
    public static function get_subscription($subscription_id)
    {
        $user_id = get_current_user_id();
        $subscription = Subscriptio_Subscription::get_by_id($subscription_id);

        if (!$user_id || !$subscription || $subscription->user_id != $user_id) {
            echo '<div class="woocommerce-error">' . __('Invalid subscription.', 'subscriptio') . ' <a href="' . get_permalink(wc_get_page_id('myaccount')).'" class="wc-forward">'. __('My Account', 'subscriptio') .'</a>' . '</div>';
            return false;
        }

        return $subscription;
    }

    /**
     * Redirect to subscription
     *
     * @access public
     * @return void
     */
    public static function redirect_to_subscription($subscription)
    {
        echo '<meta http-equiv="refresh" content="0; url=' . $subscription->get_frontend_link('view-subscription') . '">';
    }

    /**
     * Render View Subscription page
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function view_subscription($subscription_id)
    {
        if ($subscription = self::get_subscription($subscription_id)) {
            Subscriptio::include_template('myaccount/view-subscription', array('subscription' => $subscription));
        }
    }

    /**
     * Edit subscription shipping address
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function subscription_address($subscription_id)
    {
        if ($subscription = self::get_subscription($subscription_id)) {
            if (!$subscription->needs_shipping() || !apply_filters('subscriptio_allow_shipping_address_edit', true)) {
                self::redirect_to_subscription($subscription);
                return;
            }

            // Form submitted?
            if (isset($_POST['action']) && $_POST['action'] == 'subscriptio_edit_address') {

                // Validate address WooCommerce-style
                $address = WC()->countries->get_address_fields(esc_attr($_POST['shipping_country' ]), 'shipping_');

                foreach ($address as $key => $field) {

                    // Make sure we have field type before proceeding
                    $field['type'] = isset($field['type']) ? $field['type'] : 'text';

                    // Sanitize values
                    if ($field['type'] == 'checkbox') {
                        $_POST[$key] = isset($_POST[$key]) ? 1 : 0;
                    }
                    else {
                        $_POST[$key] = isset($_POST[$key]) ? wc_clean($_POST[$key]) : '';
                    }

                    // Required field empty?
                    if (!empty($field['required']) && empty($_POST[$key])) {
                        RightPress_Helper::wc_add_notice($field['label'] . ' ' . __('is a required field.', 'subscriptio'), 'error');
                    }

                    // Validate field according to rules
                    if (!empty($field['validate']) && is_array($field['validate'])) {
                        foreach ($field['validate'] as $rule) {
                            if ($rule == 'postcode') {
                                $_POST[$key] = strtoupper(str_replace(' ', '', $_POST[$key]));

                                if (WC_Validation::is_postcode($_POST[$key], $_POST['shipping_country'])) {
                                    $_POST[$key] = wc_format_postcode($_POST[$key], $_POST['shipping_country']);
                                } else {
                                    RightPress_Helper::wc_add_notice(__('Please enter a valid postcode/ZIP.', 'subscriptio'), 'error');
                                }
                            }
                            else if ($rule == 'phone') {
                                $_POST[$key] = wc_format_phone_number($_POST[$key]);

                                if (!WC_Validation::is_phone($_POST[$key])) {
                                    RightPress_Helper::wc_add_notice('<strong>' . $field['label'] . '</strong> ' . __('is not a valid phone number.', 'subscriptio'), 'error');
                                }
                            }
                            else if ($rule == 'email') {
                                $_POST[$key] = strtolower($_POST[$key]);

                                if (!is_email($_POST[$key])) {
                                    RightPress_Helper::wc_add_notice('<strong>' . $field['label'] . '</strong> ' . __('is not a valid email address.', 'woocommerce'), 'error');
                                }
                            }
                        }
                    }
                }

                // No errors in form?
                if (wc_notice_count('error') == 0) {

                    // Try to save address
                    if ($subscription->update_shipping_address($_POST, true, true)) {
                        RightPress_Helper::wc_add_notice(__('Shipping address has been updated.', 'subscriptio'));
                    }

                    // Something went really wrong...
                    else {
                        RightPress_Helper::wc_add_notice(__('Something went wrong...', 'subscriptio'), 'error');
                    }

                    // Redirect to subscription page
                    self::redirect_to_subscription($subscription);
                }
                else {
                    self::display_address_form($subscription);
                }
            }

            // Display form
            else {
                self::display_address_form($subscription);
            }
        }
    }

    /**
     * Prepare and display address form
     *
     * @access public
     * @param object $subscription
     * @return void
     */
    public static function display_address_form($subscription)
    {
        // Load address fields from WooCommerce settings
        $address = WC()->countries->get_address_fields($subscription->shipping_address['_shipping_country'], 'shipping_');

        // Enqueue WooCommerce address-related scripts
        wp_enqueue_script('wc-country-select');
        wp_enqueue_script('wc-address-i18n');

        // Load values from current address (or from POST data in case there were errors when saving new address)
        foreach ($address as $key => $field) {
            if (isset($_POST['action']) && $_POST['action'] == 'subscriptio_edit_address') {
                $address[$key]['value'] = isset($_POST[$key]) ? $_POST[$key] : '';
            }
            else {
                $address[$key]['value'] = isset($subscription->shipping_address['_' . $key]) ? $subscription->shipping_address['_' . $key] : '';
            }
        }

        // Display form
        Subscriptio::include_template('myaccount/form-edit-address', array('subscription' => $subscription, 'address' => $address));
    }

    /**
     * Pause subscription
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function pause_subscription($subscription_id)
    {
        if ($subscription = self::get_subscription($subscription_id)) {

            // Check if subscription can be paused
            if (!$subscription->can_be_paused() || !$subscription->allow_customer_subscription_pausing()) {
                self::redirect_to_subscription($subscription);
                return;
            }

            // Check for pause limit and add the correct notice
            if ($subscription->check_pause_limit() === false) {
                RightPress_Helper::wc_add_notice(__('You are no longer allowed to pause this subscription', 'subscriptio'));
            }
            // Attempt to pause this subscription
            else if ($subscription->pause_by_customer()) {
                RightPress_Helper::wc_add_notice(__('Subscription has been paused', 'subscriptio'));
            }
            // Something went wrong
            else {
                RightPress_Helper::wc_add_notice(__('Something went wrong...', 'subscriptio'), 'error');
            }

            Subscriptio::include_template('myaccount/view-subscription', array('subscription' => $subscription));
        }
    }

    /**
     * Resume subscription
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function resume_subscription($subscription_id)
    {
        if ($subscription = self::get_subscription($subscription_id)) {

            // Check if subscription can be resumed
            if (!$subscription->can_be_resumed() || !$subscription->allow_customer_subscription_pausing() || !apply_filters('subscriptio_allow_subscription_resuming', true)) {
                self::redirect_to_subscription($subscription);
                return;
            }

            // Resume and check if resumed successfully
            if ($subscription->resume_by_customer()) {
                RightPress_Helper::wc_add_notice(__('Subscription has been resumed.', 'subscriptio'));
            }
            else {
                RightPress_Helper::wc_add_notice(__('Something went wrong...', 'subscriptio'), 'error');
            }

            Subscriptio::include_template('myaccount/view-subscription', array('subscription' => $subscription));
        }
    }

    /**
     * Cancel subscription
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function cancel_subscription($subscription_id)
    {
        if ($subscription = self::get_subscription($subscription_id)) {

            // Check if subscription can be cancelled
            if (!$subscription->can_be_cancelled() || !$subscription->allow_customer_subscription_cancelling()) {
                self::redirect_to_subscription($subscription);
                return;
            }

            // Cancel and check if cancelled successfully
            if ($subscription->cancel_by_customer()) {
                RightPress_Helper::wc_add_notice(__('Subscription has been cancelled.', 'subscriptio'));
            }
            else {
                RightPress_Helper::wc_add_notice(__('Something went wrong...', 'subscriptio'), 'error');
            }

            Subscriptio::include_template('myaccount/view-subscription', array('subscription' => $subscription));
        }
    }

}
}
