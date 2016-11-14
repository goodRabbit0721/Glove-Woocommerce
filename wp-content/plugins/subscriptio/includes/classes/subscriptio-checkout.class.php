<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to checkout procedure
 *
 * @class Subscriptio_Checkout
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Checkout')) {

class Subscriptio_Checkout
{
    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->cart_prices_changed = false;

        // Change prices and totals in cart
        add_action('woocommerce_cart_loaded_from_session', array($this, 'change_prices'), 99);

        // Change cart item subtotal
        add_filter('woocommerce_cart_item_subtotal', array($this, 'change_cart_item_price_html'), 99, 3);

        // Some filters/actions need to be hooked on init
        add_action('init', array($this, 'on_init'));
    }

    /**
     * Hook filters/actions that need to be hooked later
     *
     * @access public
     * @return void
     */
    public function on_init()
    {
        // Change cart item prices
        add_filter('woocommerce_cart_item_price', array($this, 'change_cart_item_price_html'), 99, 3);
    }

    /**
     * Actually change subscription prices in cart (add signup fee, apportion if needed etc).
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function change_prices($cart)
    {
        if ($this->cart_prices_changed || empty($cart->cart_contents)) {
            return;
        }

        // Iterate over all cart items and check if price needs to be updated for subscriptions
        foreach ($cart->cart_contents as $cart_item_key => $cart_item) {
            $id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];

            // Check if given item is subscription
            if (Subscriptio_Subscription_Product::is_subscription($id)) {

                // Get new price (or false if price does not need to be changed)
                $new_price = Subscriptio_Subscription_Product::get_new_price($id, $cart_item['data']->price);

                // Needs to be changed?
                if ($new_price !== false) {
                    global $woocommerce;

                    if (isset($woocommerce->cart->cart_contents[$cart_item_key])) {
                        $woocommerce->cart->cart_contents[$cart_item_key]['data']->price = $new_price;
                    }
                }
            }
        }

        $this->cart_prices_changed = true;
    }

    /**
     * Change frontent cart item price with Subscription price (cosmetic change)
     *
     * @access public
     * @param float $price_html
     * @param array $cart_item
     * @param string $cart_item_key
     * @return string
     */
    public function change_cart_item_price_html($price_html, $cart_item, $cart_item_key)
    {
        $id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];

        // Check if given item is subscription
        if (Subscriptio_Subscription_Product::is_subscription($id)) {

            global $woocommerce;

            // Is subtotal?
            $is_subtotal = current_filter() == 'woocommerce_cart_item_subtotal' ? true : false;

            // Set quantity for 1 item if it's "price" column
            if (!$is_subtotal) {
                $price = $cart_item['data']->price;
                $quantity = 1;
            }
            else {
                $quantity = $cart_item['quantity'];
            }

            // Get current item price in cart depending on tax display mode
            if ($woocommerce->cart->tax_display_cart == 'excl') {
                $price = $cart_item['line_subtotal'];
            }
            else {
                $price = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
            }

            // Format checkout price html and return
            return Subscriptio_Subscription_Product::get_formatted_subscription_price($id, true, $is_subtotal, $quantity, $price);
        }

        return $price_html;
    }

}

new Subscriptio_checkout();

}
