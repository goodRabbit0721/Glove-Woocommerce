<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Subscription user class
 *
 * @class Subscriptio_User
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_User')) {

class Subscriptio_User
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
        if ($id) {
            $this->id = $id;
            $this->data = get_userdata($id);
        }
    }

    /**
     * Find user's subscriptions
     *
     * @access public
     * @param bool $active_only
     * @param int $user_id
     * @return array
     */
    public static function find_subscriptions($active_only = false, $user_id = null)
    {
        $subscriptions = array();

        // Check if user id was provided
        if (!$user_id) {

            // If it's not and user is a guest, return empty array
            if (!is_user_logged_in()) {
                return $subscriptions;
            }

            // Otherwise get current user id
            $user_id = get_current_user_id();
        }

        // Search for related subscription post ids
        $subscription_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'subscription',
            'meta_query'        => array(
                array(
                    'key'       => 'user_id',
                    'value'     => $user_id,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Iterate over ids and create objects
        foreach ($subscription_post_ids as $id) {
            if ($subscription = Subscriptio_Subscription::get_by_id($id)) {

                // Check if need to find active only
                if ($active_only) {
                    if (!in_array($subscription->status, array('cancelled', 'expired', 'failed'))) {
                        $subscriptions[$id] = $subscription;
                    }
                }
                else {
                    $subscriptions[$id] = $subscription;
                }
            }
        }

        return $subscriptions;
    }

    /**
     * Check if user has at least one active subscription (not expired, failed or cancelled)
     *
     * @access public
     * @return bool
     */
    public static function has_subscription()
    {
        $subscriptions = self::find_subscriptions(true);
        return !empty($subscriptions);
    }

    /**
     * Check if user has at least one subscription with any status
     *
     * @access public
     * @return bool
     */
    public static function had_subscription()
    {
        $subscriptions = self::find_subscriptions(false);
        return !empty($subscriptions);
    }

    /**
     * Find subscriptions only with given product
     *
     * @access public
     * @param int $product_id
     * @param bool $active_only
     * @return array
     */
    public static function find_subscriptions_with_product($product_id, $active_only = false)
    {
        $subscriptions = self::find_subscriptions($active_only);

        $subscriptions_with_product = array();

        foreach ($subscriptions as $id => $subscription) {

            // Check for single-product subscription
            if ((isset($subscription->product_id) && $subscription->product_id == $product_id) || (isset($subscription->variation_id) && $subscription->variation_id == $product_id)) {
                $subscriptions_with_product[$id] = $subscription;
            }

            // Check for multi-product subscription
            else if (!empty($subscription->products_multiple) && is_array($subscription->products_multiple)) {

                foreach ($subscription->products_multiple as $product) {

                    if ($product['product_id'] == $product_id || (isset($product['variation_id']) && $product['variation_id'] == $product_id)) {
                        $subscriptions_with_product[$id] = $subscription;
                    }
                }
            }
        }

        return $subscriptions_with_product;
    }

    /**
     * Check if user has active subscription (not expired, failed or cancelled) that contains specified product id
     *
     * @access public
     * @param int $product_id
     * @return bool
     */
    public static function has_subscription_product($product_id)
    {
        $subscriptions_with_product = self::find_subscriptions_with_product($product_id, true);
        return !empty($subscriptions_with_product);
    }

    /**
     * Check if user has subscription with any status that contains specified product id
     *
     * @access public
     * @param int $product_id
     * @return bool
     */
    public static function had_subscription_product($product_id)
    {
        $subscriptions_with_product = self::find_subscriptions_with_product($product_id, false);
        return !empty($subscriptions_with_product);
    }

    /**
     * Check if user has access to product downloads
     *
     * @access public
     * @param int $product_id
     * @return bool
     */
    public static function has_access_to_product_downloads($product_id)
    {
        // Stop here if product is not a subscription product
        if (!Subscriptio_Subscription_Product::is_subscription($product_id)) {
            return true;
        }

        // Load user subscriptions
        $subscriptions = self::find_subscriptions_with_product($product_id, true);

        // Iterate over subscriptions and check if at least one of them is fully active
        foreach ($subscriptions as $subscription) {
            if (in_array($subscription->status, array('active', 'trial', 'overdue'))) {
                return true;
            }
        }

        // No active subscription found
        return false;
    }

}

new Subscriptio_User();

}
