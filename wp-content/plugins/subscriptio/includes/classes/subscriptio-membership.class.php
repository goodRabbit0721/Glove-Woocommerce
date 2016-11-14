<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Integration with membership plugins
 *
 * @class Subscriptio_Membership
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Membership')) {

class Subscriptio_Membership
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Define membership support
        add_filter('woocommerce_membership_subscription_support', array($this, 'define_support'));

        // Add properties to subscription object
        add_filter('subscriptio_subscription_properties_to_populate', array($this, 'properties_to_populate'));
        add_filter('subscriptio_subscription_fields_to_update', array($this, 'fields_to_update'));
        add_filter('subscriptio_subscription_fields_to_add', array($this, 'fields_to_add'));
        add_action('subscriptio_subscription_save_meta', array($this, 'save_meta'), 10, 8);
        add_action('subscriptio_multisubscription_save_meta', array($this, 'save_meta_multi'), 10, 8);

        // Check if provided product is a subscription
        add_filter('woocommerce_membership_product_is_subscription', array($this, 'membership_is_subscription'), 10, 2);

        // Grab membership IDs for this subscription
        add_action('woocommerce_membership_subscription_membership_ids', array($this, 'save_membership_ids'), 10, 2);

        // Activate membership
        add_action('subscriptio_status_changed_to_trial', array($this, 'activate'));
        add_action('subscriptio_status_changed_to_active', array($this, 'activate'));

        // Deactivate membership
        add_action('subscriptio_status_changed_to_pending', array($this, 'deactivate'));
        add_action('subscriptio_status_changed_to_paused', array($this, 'deactivate'));
        add_action('subscriptio_status_changed_to_suspended', array($this, 'deactivate'));
        add_action('subscriptio_status_changed_to_cancelled', array($this, 'deactivate'));
        add_action('subscriptio_status_changed_to_expired', array($this, 'deactivate'));
        add_action('subscriptio_status_changed_to_failed', array($this, 'deactivate'));
    }

    /**
     * Define support for membership plugins
     *
     * @access public
     * @param bool $supports
     * @return bool
     */
    public function define_support($supports)
    {
        return true;
    }

    /**
     * Additional properties for subscription
     *
     * @access public
     * @param array $properties
     * @return array
     */
    public function properties_to_populate($properties)
    {
        return array_merge($properties, array(
            'order_item_id',
            'membership_ids',
        ));
    }

    /**
     * Additional properties for subscription
     *
     * @access public
     * @param array $properties
     * @return array
     */
    public function fields_to_update($fields)
    {
        return array_merge($fields, array(
            'order_item_id',
        ));
    }

    /**
     * Additional properties for subscription
     *
     * @access public
     * @param array $properties
     * @return array
     */
    public function fields_to_add($fields)
    {
        return array_merge($fields, array(
            'membership_ids',
        ));
    }

    /**
     * Save membership-related fields to subscription meta
     *
     * @access public
     * @param object $subscription
     * @param object $order
     * @param array $order_meta
     * @param int $order_item_id
     * @param array $order_item
     * @param object $product
     * @param array $product_meta
     * @param array $renewal
     * @return void
     */
    public function save_meta($subscription, $order, $order_meta, $order_item_id, $order_item, $product, $product_meta, $renewal)
    {
        $subscription->update_subscription_details(array('order_item_id' => $order_item_id));
    }

    /**
     * Save membership-related fields to multi-subscription meta
     *
     * @access public
     * @param object $subscription
     * @param object $order
     * @param array $order_meta
     * @param array $all_subs
     * @param array $renewal
     * @return void
     */
    public function save_meta_multi($subscription, $order, $order_meta, $all_subs, $renewal)
    {
        $item_ids = array();

        foreach ($all_subs as $item) {
            $item_ids[] = $item['order_item_key'];
        }

        $subscription->update_subscription_details(array('order_item_id' => join(',', $item_ids)));
    }

    /**
     * Check if membership is also a subscription
     *
     * @access public
     * @param bool $is_subscription
     * @param int $product_id
     * @param bool
     */
    public function membership_is_subscription($is_subscription, $product_id)
    {
        return Subscriptio_Subscription_Product::is_subscription($product_id);
    }

    /**
     * Save membership IDs
     *
     * @access public
     * @param array $ids
     * @return void
     */
    public function save_membership_ids($order_item_id, $ids)
    {
        // Load subscription by Order Item ID
        $query = new WP_Query(array(
            'post_type'         => 'subscription',
            'post_status'       => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'       => 'order_item_id',
                    'value'     => $order_item_id,
                    'compare'   => 'LIKE',
                ),
            ),
        ));

        // Iterate over found subscriptions
        foreach ($query->posts as $subscription_id) {
            $subscription = Subscriptio_Subscription::get_by_id($subscription_id);

            // Subscription looks ok?
            if (is_object($subscription) && isset($subscription->status)) {
                if (is_array($ids) && !empty($ids)) {
                    foreach ($ids as $id) {
                        $subscription->update_subscription_details(array('membership_ids' => $id));
                    }
                }
            }
        }
    }

    /**
     * Activate membership
     *
     * @access public
     * @param object $subscription
     * @return void
     */
    public function activate($subscription)
    {
        if (isset($subscription->membership_ids)) {
            do_action('subscriptio_membership_activate', $subscription->user_id, (array) $subscription->membership_ids);
        }
    }

    /**
     * Deactivate membership
     *
     * @access public
     * @param object $subscription
     * @return void
     */
    public function deactivate($subscription)
    {
        if (isset($subscription->membership_ids)) {
            do_action('subscriptio_membership_deactivate', $subscription->user_id, (array) $subscription->membership_ids);
        }
    }

}

new Subscriptio_Membership();

}
