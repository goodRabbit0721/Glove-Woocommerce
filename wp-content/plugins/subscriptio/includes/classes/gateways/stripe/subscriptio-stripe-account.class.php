<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to WooCommerce My Account area
 *
 * @class Subscriptio_Stripe_Account
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Stripe_Account')) {

class Subscriptio_Stripe_Account
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct($id = null)
    {
        // Display credit card list
        add_action('woocommerce_after_my_account', array($this, 'display_card_list'));

        // Handle actions
        add_action('template_redirect', array($this, 'delete_card'));
        add_action('template_redirect', array($this, 'card_make_default'));
    }

    /**
     * Display credit card list on My Account page
     *
     * @access public
     * @return void
     */
    public function display_card_list()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();

        // Get customer's cards
        $cards = get_user_meta($user_id, '_subscriptio_stripe_customer_cards', true);

        if (!empty($cards)) {

            $cards = maybe_unserialize($cards);

            // Get customer's default card
            $default_card = get_user_meta($user_id, '_subscriptio_stripe_customer_default_card', true);
            $default_card = !empty($default_card) ? $default_card : 'none';

            Subscriptio::include_template('gateways/stripe/credit-card-list', array(
                'cards'     => $cards,
                'default'   => $default_card,
            ));
        }
    }

    /**
     * Delete card
     *
     * @access public
     * @return void
     */
    public function delete_card()
    {
        global $woocommerce;

        // Not our request?
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['subscriptio_stripe_delete_card'])) {
            return;
        }

        // User not logged in?
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();

        // Load cards
        $cards = get_user_meta($user_id, '_subscriptio_stripe_customer_cards', true);

        if (empty($cards)) {
            return;
        }

        $cards = maybe_unserialize($cards);

        // No such card?
        if (!isset($cards[$_GET['subscriptio_stripe_delete_card']])) {
            return;
        }

        // Load customer id
        $customer_id = get_user_meta($user_id, '_subscriptio_stripe_customer_id', true);

        if (empty($customer_id)) {
            return;
        }

        // Load payment gateway object to access its methods
        $gateway = new Subscriptio_Stripe_Gateway();

        // Send request to delete this card
        $response = $gateway->send_request('cards', 'delete', array(
            'id'            => $customer_id,
            'secondary_id'  => $_GET['subscriptio_stripe_delete_card'],
        ));

        // Delete card from user's card list
        unset($cards[$_GET['subscriptio_stripe_delete_card']]);

        // Last card deleted?
        if (empty($cards)) {
            delete_user_meta($user_id, '_subscriptio_stripe_customer_cards');
            delete_user_meta($user_id, '_subscriptio_stripe_customer_default_card');
        }
        else {
            update_user_meta($user_id, '_subscriptio_stripe_customer_cards', $cards);

            // Default card deleted?
            $default_card = get_user_meta($user_id, '_subscriptio_stripe_customer_default_card', true);

            if (!empty($default_card) && $default_card == $_GET['subscriptio_stripe_delete_card']) {
                $new_default = array_keys($cards);
                $new_default = array_shift($new_default);
                update_user_meta($user_id, '_subscriptio_stripe_customer_default_card', $new_default);
            }
        }

        // Show success message
        RightPress_Helper::wc_add_notice(__('Credit card deleted successfully.', 'subscriptio-stripe'));
        wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
        exit;
    }

    /**
     * Make card a default card
     *
     * @access public
     * @return void
     */
    public function card_make_default()
    {
        global $woocommerce;

        // Not our request?
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['subscriptio_stripe_card_make_default'])) {
            return;
        }

        // User not logged in?
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();

        // Load cards
        $cards = get_user_meta($user_id, '_subscriptio_stripe_customer_cards', true);

        if (empty($cards)) {
            return;
        }

        $cards = maybe_unserialize($cards);

        // No such card?
        if (!isset($cards[$_GET['subscriptio_stripe_card_make_default']])) {
            return;
        }

        // Everything looks fine, change default card
        update_user_meta($user_id, '_subscriptio_stripe_customer_default_card', $_GET['subscriptio_stripe_card_make_default']);

        // Show success message
        RightPress_Helper::wc_add_notice(__('Default card changed successfully.', 'subscriptio-stripe'));
        wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
        exit;
    }

}

new Subscriptio_Stripe_Account();

}
