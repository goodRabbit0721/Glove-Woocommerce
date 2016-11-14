<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main subscription class
 *
 * @class Subscriptio_Subscription
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Subscription')) {

class Subscriptio_Subscription
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
            $this->populate();
        }
    }

    /**
     * Return subscription or false on error
     *
     * @access public
     * @param int $subscription_id
     * @return object|bool
     */
    public static function get_by_id($subscription_id)
    {
        if (is_numeric($subscription_id)) {
            $post = get_post($subscription_id);

            if ($post && $post->post_type == 'subscription') {
                return new Subscriptio_Subscription($subscription_id);
            }
        }

        return false;
    }

    /**
     * Define and return all subscription statuses
     *
     * @access public
     * @return array
     */
    public static function get_statuses()
    {
        return array(
            'pending'   => array(     // Usually pending payment
                'title' => __('pending', 'subscriptio'),
            ),
            'active'    => array(     // Active, new payment may be pending or even overdue
                'title' => __('active', 'subscriptio'),
            ),
            'paused' => array(        // Inactive, paused by administrator or customer
                'title' => __('paused', 'subscriptio'),
            ),
            'suspended' => array(     // Inactive, payment overdue
                'title' => __('suspended', 'subscriptio'),
            ),
            'cancelled' => array(     // Order cancelled, failed, subscription cancelled independently of order or subscription cancelled due to no payment
                'title' => __('cancelled', 'subscriptio'),
            ),
            'failed'    => array(     // Technical problems (like payment gateway issues etc), admin action needed
                'title' => __('failed', 'subscriptio'),
            ),
            'trial'     => array(     // Subscription is in trial, i.e. user has not paid for it yet, just trying out
                'title' => __('trial', 'subscriptio'),
            ),
            'overdue'   => array(     // Payment is overdue but subscription is still active before it is suspended or cancelled
                'title' => __('overdue', 'subscriptio'),
            ),
            'expired'   => array(     // Subscription expired (maximum length reached)
                'title' => __('expired', 'subscriptio'),
            ),
        );
    }

    /**
     * Load existing subscription details
     *
     * @access public
     * @return void
     */
    public function populate()
    {
        if (!$this->id) {
            return false;
        }

        // Get status
        $statuses = self::get_statuses();
        $post_terms = wp_get_post_terms($this->id, 'subscription_status');

        $this->status = !empty($post_terms) ? RightPress_Helper::clean_term_slug($post_terms[0]->slug) : '';
        $this->status_title = !empty($post_terms) ? $statuses[$this->status]['title'] : '';

        // Get other fields
        $post_meta = RightPress_Helper::unwrap_post_meta(get_post_meta($this->id));

        if (empty($post_meta)) {
            return false;
        }

        // Subscription object properties to populate from database
        $properties_to_populate = apply_filters('subscriptio_subscription_properties_to_populate', array(

            // Time related properties
            'started',              // Timestamp when subscription was first activated (or went into trial mode)
            'started_readable',     // Same as 'started' but in human readable format (ISO date/time)
            'payment_due',          // Next payment due timestamp (start of next billing period)
            'payment_due_readable', // Same as 'payment_due' but in human readable format (ISO date/time)
            'expires',              // Subscription expiration timestamp
            'expires_readable',     // Same as 'expires' but in human readable format (ISO date/time)
            'overdue_since',        // Timestamp since which subscription is marked as overdue
            'pre_paused_status',    // Status of the subscription just before it was paused
            'pre_paused_events',    // Scheduled events of the subscription just before it was paused
            'paused_since',         // Timestamp since which subscription is paused (value reset to null on resume)
            'resumes',              // Subscription auto-resume timestamp
            'resumes_readable',     // Same as 'resumes' but in human readable format (ISO date/time)
            'suspended_since',      // Timestamp since which subscription is suspended (value reset to null on unsuspension)
            'cancelled_since',      // Timestamp since which subscription is cancelled
            'expired_since',        // Timestamp since which subscription is expired

            // Subscription settings
            'price_time_unit',          // Product price time unit, e.g. week, month etc.
            'price_time_value',         // How many price_time_units product price includes?
            'free_trial_time_unit',     // Free trial time unit, e.g. week, month etc.
            'free_trial_time_value',    // How many free_trial_time_units free trial includes?
            'max_length_time_unit',     // Subscription expiration time unit, e.g. week, month etc.
            'max_length_time_value',    // How many max_length_time_units does the subscription expiration time include?
            'signup_fee',               // Signup fee charged on first order

            // Other properties
            'last_order_id',        // ID of last order related to this subscription
            'all_order_ids',        // Array of IDs of all orders that were related to this subscription over time
            'user_id',              // Associated user ID
            'user_full_name',       // Associated user full name
            'product_id',           // Associated product ID
            'product_name',         // Associated product name
            'variation_id',         // Associated variation ID
            'quantity',             // Quantity of product or variation ordered
            'products_multiple',    // The above info, but for multiple products in one subscription

            // Properties needed for renewal orders
            'payment_method',
            'payment_method_title',
            'shipping_address',
            'shipping',
            'taxes',
            'renewal_line_subtotal',
            'renewal_line_subtotal_tax',
            'renewal_line_total',
            'renewal_line_tax',
            'renewal_order_shipping',
            'renewal_order_shipping_tax',
            'renewal_cart_discount',
            'renewal_order_discount',
            'renewal_order_tax',
            'renewal_order_total',
            'renewal_order_subtotal',
            'renewal_order_currency',
            'renewal_prices_include_tax',
            'renewal_customer_ip_address',
            'renewal_customer_user_agent',
            'renewal_tax_class',
            'renewal_customer_note',
            'renewal_all_order_meta',
            'renewal_all_items_meta',
        ), $this->id);

        foreach ($properties_to_populate as $property) {
            $this->$property = isset($post_meta[$property]) ? maybe_unserialize($post_meta[$property]) : null;

            if ($property == 'all_order_ids') {

                // Fix "unwrapped" single array item
                if (!is_array($this->all_order_ids)) {
                    $this->all_order_ids = array($this->all_order_ids);
                }

                // Reverse order of elements (we want the most recent to appear first)
                $this->all_order_ids = array_reverse($this->all_order_ids);
            }
            else if ($property == 'shipping' && !empty($this->shipping) && isset($this->shipping[0])) {
                $this->shipping = $this->shipping[0];
            }
            else if ($property == 'pre_paused_events' && !is_array($this->pre_paused_events)) {
                $this->pre_paused_events = array();
            }
            else if ($property == 'renewal_order_subtotal' && is_null($this->$property)) {
                $this->$property = $this->renewal_order_total;
            }
        }

        return true;
    }

    /**
     * Create subscription from new order item
     *
     * @access public
     * @param object $order
     * @param array $order_meta
     * @param int $order_item_id
     * @param array $order_item
     * @param object $product
     * @param array $product_meta
     * @param array $renewal
     * @return void
     */
    public function create_from_order_item($order, $order_meta, $order_item_id, $order_item, $product, $product_meta, $renewal)
    {
        // Create post
        $this->id = wp_insert_post(array(
            'post_title'        => '',
            'post_name'         => '',
            'post_status'       => 'publish',
            'post_type'         => 'subscription',
            'ping_status'       => 'closed',
            'comment_status'    => 'closed',
        ));

        // Post created?
        if ($this->id == 0) {
            throw new Exception(__('Error saving subscription object.', 'subscriptio'));
        }

        // Update subscription details
        $this->update_subscription_details(array(

            // Subscription status
            'status' => 'pending',

            // Subscription settings
            'price_time_unit'       => !empty($product_meta['_subscriptio_price_time_unit']) ? $product_meta['_subscriptio_price_time_unit'] : null,
            'price_time_value'      => !empty($product_meta['_subscriptio_price_time_value']) ? $product_meta['_subscriptio_price_time_value'] : null,
            'free_trial_time_unit'  => !empty($product_meta['_subscriptio_free_trial_time_unit']) ? $product_meta['_subscriptio_free_trial_time_unit'] : null,
            'free_trial_time_value' => !empty($product_meta['_subscriptio_free_trial_time_value']) ? $product_meta['_subscriptio_free_trial_time_value'] : null,
            'max_length_time_unit'  => !empty($product_meta['_subscriptio_max_length_time_unit']) ? $product_meta['_subscriptio_max_length_time_unit'] : null,
            'max_length_time_value' => !empty($product_meta['_subscriptio_max_length_time_value']) ? $product_meta['_subscriptio_max_length_time_value'] : null,
            'signup_fee'            => !empty($product_meta['_subscriptio_signup_fee']) ? $product_meta['_subscriptio_signup_fee'] : null,

            // Other properties
            'user_id'           => $order_meta['_customer_user'],
            'last_order_id'     => $order->id,
            'all_order_ids'     => $order->id,
            'product_id'        => $order_item['product_id'],
            'product_name'      => $order_item['name'],
            'variation_id'      => !empty($order_item['variation_id']) ? $order_item['variation_id'] : null,
            'quantity'          => $order_item['qty'],
            'user_full_name'    => join(' ', array($order_meta['_billing_first_name'], $order_meta['_billing_last_name'])),

            // Properties needed for renewal orders
            'shipping_address'              => !empty($renewal['shipping_address']) ? $renewal['shipping_address'] : '',
            'shipping'                      => $renewal['shipping'],
            'taxes'                         => $renewal['taxes'],
            'renewal_line_subtotal'         => $renewal['renewal_line_subtotal'],
            'renewal_line_subtotal_tax'     => $renewal['renewal_line_subtotal_tax'],
            'renewal_line_total'            => $renewal['renewal_line_total'],
            'renewal_line_tax'              => $renewal['renewal_line_tax'],
            'renewal_order_shipping'        => $renewal['renewal_order_shipping'],
            'renewal_order_shipping_tax'    => $renewal['renewal_order_shipping_tax'],
            'renewal_cart_discount'         => $renewal['renewal_cart_discount'],
            'renewal_order_discount'        => $renewal['renewal_order_discount'],
            'renewal_order_tax'             => $renewal['renewal_order_tax'],
            'renewal_order_subtotal'        => $renewal['renewal_order_subtotal'],
            'renewal_order_total'           => $renewal['renewal_order_total'],
            'renewal_order_currency'        => !empty($order_meta['_order_currency']) ? $order_meta['_order_currency'] : '',
            'renewal_prices_include_tax'    => !empty($order_meta['_prices_include_tax']) ? $order_meta['_prices_include_tax'] : '',
            'renewal_customer_ip_address'   => !empty($order_meta['_customer_ip_address']) ? $order_meta['_customer_ip_address'] : '',
            'renewal_customer_user_agent'   => !empty($order_meta['_customer_user_agent']) ? $order_meta['_customer_user_agent'] : '',
            'renewal_tax_class'             => !empty($order_item['tax_class']) ? $order_item['tax_class'] : '',
            'renewal_customer_note'         => !empty($order->customer_note) ? $order->customer_note : '',

            // Save all order meta in case there's custom data added
            'renewal_all_order_meta'        => $order_meta,
            'renewal_all_items_meta'        => array(
                $order_item['product_id'] => array(
                    'item_meta'       => $order_item['item_meta'],
                    'item_meta_array' => isset($order_item['item_meta_array']) ? $order_item['item_meta_array'] : '',
                )
            ),
        ));

        // Allow other plugins to add subscription meta
        do_action('subscriptio_subscription_save_meta', $this, $order, $order_meta, $order_item_id, $order_item, $product, $product_meta, $renewal);

        return $this->id;
    }

    /**
     * Create subscription from all subscription products in order
     *
     * @access public
     * @param object $order
     * @param array $order_meta
     * @param array $all_subs
     * @param array $renewal
     * @return void
     */
    public function create_from_all_order_items($order, $order_meta, $all_subs, $renewal)
    {
        // Create post
        $this->id = wp_insert_post(array(
            'post_title'        => '',
            'post_name'         => '',
            'post_status'       => 'publish',
            'post_type'         => 'subscription',
            'ping_status'       => 'closed',
            'comment_status'    => 'closed',
        ));

        // Post created?
        if ($this->id == 0) {
            throw new Exception(__('Error saving subscription object.', 'subscriptio'));
        }

        // Update subscription details
        $this->update_subscription_details(array(

            // Subscription status
            'status' => 'pending',

            // User and order details
            'user_full_name'    => join(' ', array($order_meta['_billing_first_name'], $order_meta['_billing_last_name'])),
            'user_id'           => $order_meta['_customer_user'],
            'last_order_id'     => $order->id,
            'all_order_ids'     => $order->id,

            // Properties needed for renewal orders
            'shipping_address'              => !empty($renewal['shipping_address']) ? $renewal['shipping_address'] : '',
            'shipping'                      => $renewal['shipping'],
            'taxes'                         => $renewal['taxes'],
            'renewal_line_subtotal'         => $renewal['renewal_line_subtotal'],
            'renewal_line_subtotal_tax'     => $renewal['renewal_line_subtotal_tax'],
            'renewal_line_total'            => $renewal['renewal_line_total'],
            'renewal_line_tax'              => $renewal['renewal_line_tax'],
            'renewal_order_shipping'        => $renewal['renewal_order_shipping'],
            'renewal_order_shipping_tax'    => $renewal['renewal_order_shipping_tax'],
            'renewal_cart_discount'         => $renewal['renewal_cart_discount'],
            'renewal_order_discount'        => $renewal['renewal_order_discount'],
            'renewal_order_tax'             => $renewal['renewal_order_tax'],
            'renewal_order_subtotal'        => $renewal['renewal_order_subtotal'],
            'renewal_order_total'           => $renewal['renewal_order_total'],
            'renewal_order_currency'        => !empty($order_meta['_order_currency']) ? $order_meta['_order_currency'] : '',
            'renewal_prices_include_tax'    => !empty($order_meta['_prices_include_tax']) ? $order_meta['_prices_include_tax'] : '',
            'renewal_customer_ip_address'   => !empty($order_meta['_customer_ip_address']) ? $order_meta['_customer_ip_address'] : '',
            'renewal_customer_user_agent'   => !empty($order_meta['_customer_user_agent']) ? $order_meta['_customer_user_agent'] : '',
            'renewal_customer_note'         => !empty($order->customer_note) ? $order->customer_note : '',

            // Save all order meta in case there's custom data added
            'renewal_all_order_meta'        => $order_meta,
        ));

        // Also prepare to update time units and values
        $time_fields = array(
            'price_time_unit'        => '_subscriptio_price_time_unit',
            'price_time_value'       => '_subscriptio_price_time_value',
            'free_trial_time_unit'   => '_subscriptio_free_trial_time_unit',
            'free_trial_time_value'  => '_subscriptio_free_trial_time_value',
            'max_length_time_unit'   => '_subscriptio_max_length_time_unit',
            'max_length_time_value'  => '_subscriptio_max_length_time_value',
        );

        // Set current product meta
        // Only first one - since the fields should match for all to be compatible
        $product_meta = $all_subs[0]['product_post_meta'];

        // Set array for update values
        $time_fields_update = array();

        foreach ($time_fields as $key => $time_field) {
            $time_fields_update[$key] = !empty($product_meta[$time_field]) ? $product_meta[$time_field] : null;
        }

        // Now update the fields
        $this->update_subscription_details($time_fields_update);

        // Combine signup fees
        $total_signup_fee = 0;

        foreach ($all_subs as $product_details) {
            $total_signup_fee += (isset($product_details['product_post_meta']['_subscriptio_signup_fee']) ? $product_details['product_post_meta']['_subscriptio_signup_fee'] : 0);
        }

        // Now update the signup fee
        $this->update_subscription_details(array(
            'signup_fee' => ($total_signup_fee != 0) ? $total_signup_fee : null
        ));

        // Multiple product details
        foreach ($all_subs as $product_details) {

            // Save product details
            $products_multiple[] = array(
                'product_id'   => $product_details['product_id'],
                'product_name' => $product_details['order_item']['name'],
                'variation_id' => $product_details['variation_id'],
                'quantity'     => $product_details['order_item']['qty'],
                'total'        => $product_details['order_item']['line_total'],
                'tax'          => $product_details['order_item']['line_tax'],
            );

            // Save product items tax class and other meta
            $items_tax_class[$product_details['product_id']] = !empty($product_details['order_item']['tax_class']) ? $product_details['order_item']['tax_class'] : '';
            $items_meta[$product_details['product_id']] = array(
                'item_meta' => $product_details['order_item']['item_meta'],
                'item_meta_array' => isset($product_details['order_item']['item_meta_array']) ? $product_details['order_item']['item_meta_array'] : '',
            );
        }

        $this->update_subscription_details(array(
            'products_multiple'      => $products_multiple,
            'renewal_tax_class'      => $items_tax_class,
            'renewal_all_items_meta' => $items_meta,
        ));

        // Allow other plugins to add subscription meta
        do_action('subscriptio_multisubscription_save_meta', $this, $order, $order_meta, $all_subs, $renewal);

        return $this->id;
    }


    /**
     * Check subscription products for compatibility
     *
     * @access public
     * @param array $all_subs
     * @return bool
     */
    public function check_compatibility($all_subs)
    {
        // Check time fields, which should match to create the correct subscription
        $fields_to_check = array(
            '_subscriptio_price_time_unit',
            '_subscriptio_price_time_value',
            '_subscriptio_free_trial_time_unit',
            '_subscriptio_free_trial_time_value',
            '_subscriptio_max_length_time_unit',
            '_subscriptio_max_length_time_value',
        );

        foreach ($all_subs as $subs_product) {

            // Set current product meta
            $product_meta = $subs_product['product_post_meta'];

            if (isset($product_meta_prev)) {
                foreach ($fields_to_check as $field) {

                    $product_meta[$field] = isset($product_meta[$field]) ? $product_meta[$field] : '';
                    $product_meta_prev[$field] = isset($product_meta_prev[$field]) ? $product_meta_prev[$field] : '';

                    if ($product_meta[$field] != $product_meta_prev[$field]) {
                        return false;
                    }
                }
            }

            // Set previous product meta to compare (for next)
            $product_meta_prev = $product_meta;
        }

        return true;
    }

    /**
     * Update subscription details (or create those that may change over time)
     *
     * @access public
     * @param array $params
     * @return void
     */
    public function update_subscription_details($params)
    {
        // Taxonomies to update
        $taxonomies_to_update = array(
            'status'   => 'subscription_status',
        );

        foreach ($taxonomies_to_update as $taxonomy_key => $taxonomy) {
            if (!empty($params[$taxonomy_key])) {

                // Set object parameter
                $this->$taxonomy_key = $params[$taxonomy_key];

                // Status? Update readable title as well
                if ($taxonomy_key == 'status') {
                    $statuses = self::get_statuses();
                    $this->status_title = $statuses[$this->status]['title'];
                }

                // Save post terms
                if (!wp_set_post_terms($this->id, $params[$taxonomy_key], $taxonomy)) {
                    throw new Exception(__('Error updating subscription field', 'subscriptio') . ' ' . $taxonomy . '.');
                }
            }
        }

        // Update unique fields
        $fields_to_update = apply_filters('subscriptio_subscription_fields_to_update', array(
            'user_id',
            'last_order_id',
            'product_id',
            'product_name',
            'variation_id',
            'quantity',
            'products_multiple',
            'user_full_name',
            'shipping_address',
            'shipping',
            'taxes',
            'started',
            'started_readable',
            'payment_due',
            'payment_due_readable',
            'expires',
            'expires_readable',
            'overdue_since',
            'pre_paused_status',
            'pre_paused_events',
            'paused_since',
            'resumes',
            'resumes_readable',
            'suspended_since',
            'cancelled_since',
            'expired_since',
            'price_time_unit',
            'price_time_value',
            'free_trial_time_unit',
            'free_trial_time_value',
            'max_length_time_unit',
            'max_length_time_value',
            'signup_fee',
            'payment_method',
            'payment_method_title',
            'renewal_line_subtotal',
            'renewal_line_subtotal_tax',
            'renewal_line_total',
            'renewal_line_tax',
            'renewal_order_shipping',
            'renewal_order_shipping_tax',
            'renewal_cart_discount',
            'renewal_order_discount',
            'renewal_order_tax',
            'renewal_order_subtotal',
            'renewal_order_total',
            'renewal_order_currency',
            'renewal_prices_include_tax',
            'renewal_customer_ip_address',
            'renewal_customer_user_agent',
            'renewal_tax_class',
            'renewal_customer_note',
            'renewal_all_order_meta',
            'renewal_all_items_meta',
        ), $this->id);

        foreach ($fields_to_update as $field) {
            if (!empty($params[$field])) {

                // Set object parameter
                $this->$field = $params[$field];

                // Save to post meta
                update_post_meta($this->id, $field, $params[$field]);
            }
        }

        // Add to set
        $fields_to_add = apply_filters('subscriptio_subscription_fields_to_add', array(
            'all_order_ids',
        ), $this->id);

        foreach ($fields_to_add as $field) {
            if (!empty($params[$field])) {

                // Set object parameter
                $this->$field = isset($this->$field) && is_array($this->$field) && !empty($this->$field) ? $this->$field : array();
                array_push($this->$field, $params[$field]);

                // Save to post meta
                if (!add_post_meta($this->id, $field, $params[$field])) {
                    throw new Exception(__('Error adding to subscription field', 'subscriptio') . ' ' . $field . '.');
                }
            }
        }

    }

    /**
     * Clear subscription details (set object property to null and delete WP post meta
     *
     * @access public
     * @param array $params
     * @return bool
     */
    public function clear_subscription_details($params)
    {
        foreach ($params as $param) {
            $this->$param = null;
            delete_post_meta($this->id, $param);
        }
    }

    /**
     * Register new payment for this subscription
     *
     * @access public
     * @param int $order_id
     * @param object $transaction
     * @return void
     */
    public function pay_by_order($order_id, $transaction)
    {
        $update_subscription_details = array();

        try {

            // Failed or cancelled subscription?
            if (in_array($this->status, array('cancelled', 'failed'))) {
                throw new Exception(__('Trying to modify cancelled or failed subscription.', 'subscriptio'));
            }

            // Note old status
            $old_status = $this->status;

            // Activating for the first time?
            if ($old_status == 'pending') {

                // Set started time
                $update_subscription_details['started'] = time();
                $update_subscription_details['started_readable'] = RightPress_Helper::get_iso_datetime($update_subscription_details['started']);

                // Free trial?
                if ($this->can_be_in_trial() && $this->is_trial()) {
                    $this->status = 'trial';
                    $this->add_trial_user_meta();
                }
                else {
                    $this->status = 'active';
                }

                // Subscription expires after specific period of time?
                if (!empty($this->max_length_time_unit) && !empty($this->max_length_time_value)) {

                    // Calculate subscription expiration date/time
                    $update_subscription_details['expires'] = $this->calculate_expiration_time();

                    // Do we have correct expiration settings?
                    if ($update_subscription_details['expires'] === false) {
                        throw new Exception(__('Failed calculating value for field', 'subscriptio') . ' expires.');
                    }

                    // Set readable expiration date
                    $update_subscription_details['expires_readable'] = RightPress_Helper::get_iso_datetime($update_subscription_details['expires']);

                    // Schedule expiration event
                    if (!Subscriptio_Event_Scheduler::schedule_expiration($this->id, $update_subscription_details['expires'])) {
                        throw new Exception(__('Failed scheduling expiration event.', 'subscriptio'));
                    }
                }
            }
            else {
                $this->status = 'active';
            }

            // Before status change hooks
            $this->before_status_change($old_status, $this->status);

            $update_subscription_details['status'] = $this->status;

            // Calculate next payment due date/time
            $update_subscription_details['payment_due'] = $this->calculate_next_payment_time();

            // Next payment due date calculated successfully?
            if ($update_subscription_details['payment_due'] === false) {
                throw new Exception(__('Failed calculating value for field', 'subscriptio') . ' payment_due.');
            }

            // Set readable next payment due date
            $update_subscription_details['payment_due_readable'] = RightPress_Helper::get_iso_datetime($update_subscription_details['payment_due']);

            // Schedule next payment event
            if (!Subscriptio_Event_Scheduler::schedule_payment($this->id, $update_subscription_details['payment_due'])) {
                throw new Exception(__('Failed scheduling next payment event.', 'subscriptio'));
            }

            // Schedule renewal order event
            if (!Subscriptio_Event_Scheduler::schedule_order($this->id, self::calculate_renewal_order_time($update_subscription_details['payment_due']))) {
                throw new Exception(__('Failed scheduling renewal order event.', 'subscriptio'));
            }

            // Mark that this subscription was paid by this specific order (so we don't apply the same payment multiple times)
            if (!add_post_meta($this->id, 'paid_by_orders', $order_id)) {
                throw new Exception(__('Error adding to subscription field', 'subscriptio') . ' paid_by_orders.');
            }

            // Get payment method
            if ($payment_method = get_post_meta($order_id, '_payment_method', true)) {
                $update_subscription_details['payment_method'] = $payment_method;
            }

            // Get payment method title
            if ($payment_method_title = get_post_meta($order_id, '_payment_method_title', true)) {
                $update_subscription_details['payment_method_title'] = $payment_method_title;
            }

            // Update subscription details in database
            $this->update_subscription_details($update_subscription_details);

            // Update transaction
            $transaction->update_result('success');

            if ($this->status == 'trial') {
                $transaction->update_note(__('Trial started, next payment due date set.', 'subscriptio'), true);
            }
            else {
                $transaction->update_note(__('Payment applied, next payment due date set.', 'subscriptio'), true);

                if ($old_status != $this->status) {
                    $transaction->update_note(sprintf(__('Subscription status changed from %s to %s.', 'subscriptio'), $old_status, $this->status), true);
                }
            }

            // Fire actions
            do_action('subscriptio_payment_applied', $this->id, $order_id);
            $this->on_status_change($old_status, $this->status);

        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note(__('Error', 'subscriptio') . ': ' . $e->getMessage(), true);
        }
    }

    /**
     * Check if payment on specific order has already been applied to this subscription
     *
     * @access public
     * @param int $order_id
     * @return bool
     */
    public function paid_by_order($order_id)
    {
        $paid_by_orders = RightPress_Helper::unwrap_post_meta(get_post_meta($this->id, 'paid_by_orders'));
        $paid_by_orders = is_array($paid_by_orders) ? $paid_by_orders : array($paid_by_orders);

        if (empty($paid_by_orders) || !in_array((string) $order_id, $paid_by_orders)) {
            return false;
        }

        return true;
    }

    /**
     * Pause subscription
     *
     * @access public
     * @return void
     */
    public function pause()
    {
        // Only subscriptions with specific statuses can be paused
        if (!in_array($this->status, array('trial', 'active', 'overdue', 'suspended'))) {
            return;
        }

        // Handle pause limit if set
        if (($current_pause_amount = $this->check_pause_limit()) !== false) {
            update_user_meta($this->user_id, '_subscriptio_pause_limit', array($this->id => ++$current_pause_amount));
        }
        else {
            return;
        }

        // Schedule auto-resuming
        if (Subscriptio::option('max_pause_duration') > 0) {

            // Calculate subscription resuming date/time
            $resumes = $this->calculate_resuming_time();

            // Do we have correct resuming settings?
            if ($resumes === false) {
                return;
            }

            // Set readable resuming date
            $resumes_readable = RightPress_Helper::get_iso_datetime($resumes);

            // Update the details
            $this->update_subscription_details(array(
                'resumes'          => $resumes,
                'resumes_readable' => $resumes_readable,
            ));

            // Schedule resume event
            if (!Subscriptio_Event_Scheduler::schedule_resume($this->id, $resumes)) {
                return;
            }
        }

        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, 'paused');

        // Change subscription status and set timestamp
        $this->update_subscription_details(array(
            'status'            => 'paused',
            'pre_paused_status' => $old_status,
            'pre_paused_events' => Subscriptio_Event_Scheduler::get_scheduled_events_timestamps($this->id),
            'paused_since'      => time(),
        ));

        // Unschedule almost all events (they were just saved to subscription property pre_paused_events)
        Subscriptio_Event_Scheduler::unschedule_multiple(array(
            'payment', 'order', 'reminder', 'suspension', 'cancellation', 'expiration',
        ), $this->id);

        // Fire actions hooks
        if ($old_status != 'paused') {
            $this->on_status_change($old_status, 'paused');
        }

        // Send notifications
        Subscriptio_Mailer::send('paused', $this);
    }

    /**
     * Resume paused subscription
     *
     * @access public
     * @return void
     */
    public function resume()
    {
        // Check if subscription is really paused
        if ($this->status != 'paused') {
            return;
        }

        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, $this->pre_paused_status);

        // Revert status
        $properties_to_update = array(
            'status' => $this->pre_paused_status,
        );

        // Offset all scheduled events by the amount of time this subscription was paused
        $time_offset = max(array(0, (time() - $this->paused_since)));

        // Offset payment due date
        if (!empty($this->payment_due)) {
            $properties_to_update['payment_due'] = $this->payment_due + $time_offset;
            $properties_to_update['payment_due_readable'] = RightPress_Helper::get_iso_datetime($properties_to_update['payment_due']);
        }

        // Offset expiration date
        if (!empty($this->expires)) {
            $properties_to_update['expires'] = $this->expires + $time_offset;
            $properties_to_update['expires_readable'] = RightPress_Helper::get_iso_datetime($properties_to_update['expires']);
        }

        // Offset overdue since date
        if (!empty($this->overdue_since)) {
            $properties_to_update['overdue_since'] = $this->overdue_since + $time_offset;
        }

        // Offset suspended since date
        if (!empty($this->suspended_since)) {
            $properties_to_update['suspended_since'] = $this->suspended_since + $time_offset;
        }

        // Save subscription properties
        $this->update_subscription_details($properties_to_update);

        // Schedule saved events
        foreach ($this->pre_paused_events as $event) {
            $new_event_time = $event['timestamp'] + $time_offset;
            // Note: Do not change 'hook' to 'event' in the line below (backwards compatibility)
            Subscriptio_Event_Scheduler::schedule($event['hook'], $this->id, $new_event_time);
        }

        // Fire actions hooks
        if ($old_status != $this->pre_paused_status) {
            $this->on_status_change($old_status, $this->pre_paused_status);
        }

        // Clear properties related to paused subscriptions
        $this->clear_subscription_details(array(
            'pre_paused_status',
            'paused_since',
            'resumes',
            'resumes_readable',
        ));

        // Unschedule auto-resuming event
        Subscriptio_Event_Scheduler::unschedule_resume($this->id);

        // Send notifications
        Subscriptio_Mailer::send('resumed', $this);
    }

    /**
     * Overdue subscription
     *
     * @access public
     * @return void
     */
    public function overdue()
    {
        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, 'overdue');

        // Change subscription status and set timestamp
        $this->update_subscription_details(array(
            'status'            => 'overdue',
            'overdue_since'     => time(),
        ));

        // Fire actions hooks
        $this->on_status_change($old_status, 'overdue');

    }

    /**
     * Suspend subscription
     *
     * @access public
     * @return void
     */
    public function suspend()
    {
        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, 'suspended');

        // Change subscription status and set timestamp
        $this->update_subscription_details(array(
            'status'            => 'suspended',
            'suspended_since'   => time(),
        ));

        // Fire actions hooks
        $this->on_status_change($old_status, 'suspended');

        // Send notifications
        Subscriptio_Mailer::send('suspended', $this);
    }

    /**
     * Cancel subscription
     *
     * @access public
     * @return void
     */
    public function cancel()
    {
        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, 'cancelled');

        // Cancel all unpaid renewal orders
        foreach ($this->all_order_ids as $order_id) {
            if (Subscriptio_Order_Handler::order_is_renewal($order_id) && !$this->paid_by_order($order_id)) {
                $order = RightPress_Helper::wc_get_order($order_id);
                $order->update_status('cancelled', __('Unpaid subscription renewal order cancelled.', 'subscriptio'));
            }
        }

        // Change subscription status
        $this->update_subscription_details(array(
            'status'            => 'cancelled',
            'cancelled_since'   => time(),
        ));

        // Clear payment due and expires dates
        $this->clear_subscription_details(array(
            'payment_due',
            'payment_due_readable',
            'expires',
            'expires_readable',
            'overdue_since',
            'pre_paused_status',
            'pre_paused_events',
            'paused_since',
            'resumes',
            'resumes_readable',
            'suspended_since',
        ));

        // Clear any other scheduled events
        Subscriptio_Event_Scheduler::unschedule_all($this->id);

        // Fire actions hooks
        if ($old_status != 'cancelled') {
            $this->on_status_change($old_status, 'cancelled');
        }

        // Send notifications
        Subscriptio_Mailer::send('cancelled', $this);
    }

    /**
     * Expire subscription
     *
     * @access public
     * @return void
     */
    public function expire()
    {
        $old_status = $this->status;

        // Before status change hooks
        $this->before_status_change($old_status, 'expired');

        // Change subscription status
        $this->update_subscription_details(array(
            'status'            => 'expired',
            'expired_since'     => time(),
        ));

        // Clear payment due and expires dates
        $this->clear_subscription_details(array(
            'payment_due',
            'payment_due_readable',
            'expires',
            'expires_readable',
            'overdue_since',
            'pre_paused_status',
            'pre_paused_events',
            'paused_since',
            'resumes',
            'resumes_readable',
            'suspended_since',
        ));

        // Clear any other scheduled events
        Subscriptio_Event_Scheduler::unschedule_all($this->id);

        // Fire actions hooks
        $this->on_status_change($old_status, 'expired');

        // Send notifications
        Subscriptio_Mailer::send('expired', $this);
    }

    /**
     * Calculate next renewal order time
     *
     * @access public
     * @param int $payment_due
     * @return int
     */
    public function calculate_renewal_order_time($payment_due)
    {
        // Calculate offset in seconds
        $offset_in_seconds = self::get_period_length_in('second', 'day', Subscriptio::option('renewal_order_day_offset'));

        $fifteen_minutes = Subscriptio::$debug ? 15 : 900;
        $one_minute = Subscriptio::$debug ? 1 : 60;

        // Calculate renewal time
        $renewal_order_time = $payment_due - $offset_in_seconds;

        // Make sure it's at least 15 minutes in the future
        $renewal_order_time = ($renewal_order_time >= (time() + $fifteen_minutes)) ? $renewal_order_time : (time() + $fifteen_minutes);

        // Make sure it does not fall before next payment time
        $renewal_order_time = (($renewal_order_time + $one_minute) <= $payment_due) ? $renewal_order_time : ($payment_due - $one_minute);

        return $renewal_order_time;
    }

    /**
     * Calculate next payment time
     *
     * @access public
     * @return int
     */
    public function calculate_next_payment_time()
    {
        // Get subscription period length in seconds
        $time_units = $this->status == 'trial' ? $this->free_trial_time_unit : $this->price_time_unit;
        $time_value = $this->status == 'trial' ? $this->free_trial_time_value : $this->price_time_value;

        $period_length_in_seconds = self::get_period_length_in('second', $time_units, $time_value);

        // Something wrong with settings? Don't create a mess then..
        if (!$period_length_in_seconds) {
            return false;
        }

        // Has this subscription been suspended? User does not have to pay for this amount of time
        $suspension_length = is_numeric($this->suspended_since) ? max(array(0, time() - $this->suspended_since)) : 0;

        // Calculate next payment time
        $next_payment_time = (is_numeric($this->payment_due) ? $this->payment_due : time()) + $period_length_in_seconds + $suspension_length;

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $twenty_minutes = Subscriptio::$debug ? 20 : 1200;

        // Make sure it's at least 20 minutes in the future
        $next_payment_time = ($next_payment_time >= (time() + $twenty_minutes)) ? $next_payment_time : (time() + $twenty_minutes);

        return $next_payment_time;
    }

    /**
     * Calculate expiration time
     *
     * @access public
     * @return mixed
     */
    public function calculate_expiration_time()
    {
        // Get expiration period length in seconds
        $period_length_in_seconds = self::get_period_length_in('second', $this->max_length_time_unit, $this->max_length_time_value);

        // Something wrong with settings? Don't create a mess then..
        if (!$period_length_in_seconds) {
            return false;
        }

        // Calculate expiration time
        $expiration_time = time() + $period_length_in_seconds;

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $thirty_minutes = Subscriptio::$debug ? 30 : 1800;

        // Make sure it's at least 30 minutes in the future
        $expiration_time = ($expiration_time >= (time() + $thirty_minutes)) ? $expiration_time : (time() + $thirty_minutes);

        return $expiration_time;
    }

    /**
     * Calculate overdue time
     *
     * @access public
     * @return mixed
     */
    public function calculate_overdue_time()
    {
        // Check if overdue time is enabled
        if (!Subscriptio::option('overdue_enabled') || !Subscriptio::option('overdue_length')) {
            return false;
        }

        // Get overdue period length in seconds
        $period_length_in_seconds = self::get_period_length_in('second', 'day', Subscriptio::option('overdue_length'));

        // Something wrong with settings? Don't create a mess then..
        if (!$period_length_in_seconds) {
            return false;
        }

        // Calculate expiration time
        $overdue_time = time() + $period_length_in_seconds;

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $thirty_minutes = Subscriptio::$debug ? 30 : 1800;

        // Make sure it's at least 30 minutes in the future
        $overdue_time = ($overdue_time >= (time() + $thirty_minutes)) ? $overdue_time : (time() + $thirty_minutes);

        return $overdue_time;
    }

    /**
     * Calculate resuming time
     *
     * @access public
     * @return mixed
     */
    public function calculate_resuming_time()
    {
        // Check if pause max time is enabled
        if (Subscriptio::option('max_pause_duration') <= 0) {
            return false;
        }

        // Get pause period length in seconds
        $period_length_in_seconds = self::get_period_length_in('second', 'day', Subscriptio::option('max_pause_duration'));

        // Something wrong with settings? Don't create a mess then..
        if (!$period_length_in_seconds) {
            return false;
        }

        // Calculate resuming time
        $resuming_time = time() + $period_length_in_seconds;

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $thirty_minutes = Subscriptio::$debug ? 30 : 1800;

        // Make sure it's at least 30 minutes in the future
        $resuming_time = ($resuming_time >= (time() + $thirty_minutes)) ? $resuming_time : (time() + $thirty_minutes);

        return $resuming_time;
    }

    /**
     * Calculate suspension time
     *
     * @access public
     * @param int $time
     * @return mixed
     */
    public function calculate_suspension_time($time = null)
    {
        $time = $time ? $time : time();

        // Check if suspensions are enabled
        if (!Subscriptio::option('suspensions_enabled') || !Subscriptio::option('suspensions_length')) {
            return false;
        }

        // Get suspension period length in seconds
        $period_length_in_seconds = self::get_period_length_in('second', 'day', Subscriptio::option('suspensions_length'));

        // Something wrong with settings? Don't create a mess then..
        if (!$period_length_in_seconds) {
            return false;
        }

        // Calculate expiration time
        $suspension_time = time() + $period_length_in_seconds;

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $thirty_minutes = Subscriptio::$debug ? 30 : 1800;

        // Make sure it's at least 30 minutes in the future
        $suspension_time = ($suspension_time >= (time() + $thirty_minutes)) ? $suspension_time : (time() + $thirty_minutes);

        return $suspension_time;
    }

    /**
     * Return subscription length
     * $units_to and $units_from should be passed in a singular form (e.g. day)
     *
     * @access public
     * @param string $units_to
     * @param string $units_from
     * @param int $value
     * @return mixed
     */
    public static function get_period_length_in($units_to, $units_from, $value)
    {
        // Get time units
        $time_units = Subscriptio::get_time_units();

        // Check if given units are supported
        if (!isset($time_units[$units_from])) {
            return false;
        }

        // Extend with more units to convert to
        $time_units = array_merge(array(
            'second'    => array(
                'seconds'   => 1,
            ),
            'minute'    => array(
                'seconds'   => 60,
            ),
            'hour'      => array(
                'seconds'   => 3600,
            ),
        ), Subscriptio::get_time_units());

        // Check if units to convert to are supported
        if (!isset($time_units[$units_to])) {
            return false;
        }

        // Check if $value is a number
        if (!is_numeric($value) || $value < 0) {
            return false;
        }

        // Calculate value in seconds
        $value_in_seconds = $value * $time_units[$units_from]['seconds'];

        // Use scale of 1:10080 (1 minute = 1 week) when debugging
        $value_in_seconds = Subscriptio::$debug ? $value_in_seconds / 10080 : $value_in_seconds;

        // Calculate value in required units
        return round($value_in_seconds / $time_units[$units_to]['seconds']);
    }

    /**
     * Change subscription's scheduled date
     *
     * @access public
     * @return void
     */
    public static function ajax_change_scheduled_date()
    {
        // Check if current user can edit subscription settings
        if (!current_user_can('edit_users')) {
            return;
        }

        // Get variables
        $user_id         = $_POST['user_id'];
        $subscription_id = $_POST['subscription_id'];
        $date_type       = $_POST['date_type'];
        $date            = $_POST['date'];

        // Pass the data for checks and process the result
        if ($new_timestamp = self::scheduled_date_check_and_change($date, $date_type, $subscription_id)) {
            echo json_encode(array(
                'newdate' => Subscriptio::get_adjusted_datetime($new_timestamp),
            ));
            exit;
        }
        else {
            echo json_encode(array(
                'newdate' => 'error',
            ));
            exit;
        }
    }

    /**
     * Get date change fields
     *
     * @access public
     * @param int $timestamp
     * @param string $type
     * @return string
     */
    public function get_date_change_fields($timestamp, $type)
    {
        if (empty($type)) {
            return;
        }

        $default_date = $timestamp ? Subscriptio::get_adjusted_datetime($timestamp, 'Y-m-d') : '';

        return sprintf('
            <input type="text" name="subscription_date" style="display:none; position:relative; top:-25px;">
            <input type="hidden" name="subscription_default_date" value="%s">
            <input type="hidden" name="subscription_date_type" value="%s">
            <input type="hidden" name="subscription_user_id" value="%s">
            <input type="hidden" name="subscription_id" value="%s">', $default_date, $type, $this->user_id, $this->id);
    }

    /**
     * Check and change subscription's scheduled date
     *
     * @access public
     * @param string $new_date
     * @param string $date_type
     * @param int $subscription_id
     * @return bool|int
     */
    public static function scheduled_date_check_and_change($new_date, $date_type, $subscription_id)
    {
        // Get all current events timestamps
        $scheduled_events = array(
            'renewal_order' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('order', $subscription_id),
            'payment' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('payment', $subscription_id),
            'reminder' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('reminder', $subscription_id),
            'suspension' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('suspension', $subscription_id),
            'cancellation' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('cancellation', $subscription_id),
            'expiration' => Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('expiration', $subscription_id),
        );

        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'date_change', $subscription_id);

        // Check if dates are set at all
        if (empty($scheduled_events[$date_type]) || empty($new_date)) {
            return false;
        }

        // Check if date is set in future
        if (strtotime($new_date) <= time()) {
            $transaction->update_result('error');
            $transaction->update_note(__('Scheduled date should be in future', 'subscriptio'), true);
            return false;
        }

        // Changing renewal order date
        if ($date_type == 'renewal_order') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['renewal_order'], $new_date);

            // Check if order date is set before payment due
            if ($new_timestamp >= $scheduled_events['payment']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Renewal order date should be before payment due.', 'subscriptio'), true);
                return false;
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_order($subscription_id, $scheduled_events['renewal_order']);
            Subscriptio_Event_Scheduler::schedule_order($subscription_id, $new_timestamp);
            $transaction->update_result('success');
            $transaction->update_note(__('Renewal order date changed.', 'subscriptio'), true);
            return $new_timestamp;
        }

        // Changing payment due date
        if ($date_type == 'payment') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['payment'], $new_date);

            // Check if it's not set before renewal order
            if ($new_timestamp <= $scheduled_events['renewal_order']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Payment Due date should be after renewal order.', 'subscriptio'), true);
                return false;
            }

            // Check if it's not set before next reminder
            else if ($new_timestamp <= $scheduled_events['reminder']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Payment Due date should be after scheduled reminder.', 'subscriptio'), true);
                return false;
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_payment($subscription_id, $scheduled_events['payment']);
            Subscriptio_Event_Scheduler::schedule_payment($subscription_id, $new_timestamp);

            // Get subscription object
            $subscription = Subscriptio_Subscription::get_by_id($subscription_id);

            // Set next payment due date in properties
            $subscription->update_subscription_details(array(
                'payment_due'          => $new_timestamp,
                'payment_due_readable' => RightPress_Helper::get_iso_datetime($new_timestamp),
            ));

            // Update transaction
            $transaction->update_result('success');
            $transaction->update_note(__('Payment Due date changed.', 'subscriptio'), true);

            return $new_timestamp;
        }

        // Changing next payment reminder date
        if ($date_type == 'reminder') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['reminder'], $new_date);

            // Check for which event is the reminder
            if (!empty($scheduled_events['payment'])) {
                $reminder_scheduled_event = $scheduled_events['payment'];
            }

            else if (!empty($scheduled_events['suspension'])) {
                $reminder_scheduled_event = $scheduled_events['suspension'];
            }

            else if (!empty($scheduled_events['cancellation'])) {
                $reminder_scheduled_event = $scheduled_events['cancellation'];
            }

            // Check if it's set before the next event
            if ($new_timestamp >= $reminder_scheduled_event) {
                $transaction->update_result('error');
                $transaction->update_note(__('Payment Reminder date should be set before the next event.', 'subscriptio'), true);
                return false;
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_reminder($subscription_id, $scheduled_events['reminder']);
            Subscriptio_Event_Scheduler::schedule_reminder($subscription_id, $new_timestamp);
            $transaction->update_result('success');
            $transaction->update_note(__('Payment Reminder date changed.', 'subscriptio'), true);
            return $new_timestamp;
        }

        // Changing suspension date
        if ($date_type == 'suspension') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['suspension'], $new_date);

            // Check if it's set after payment due date
            if ($new_timestamp <= $scheduled_events['payment']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Suspension date should be after Payment Due date.', 'subscriptio'), true);
                return false;
            }

            // Check if it's not set before next reminder
            else if ($new_timestamp <= $scheduled_events['reminder']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Suspension date should be after scheduled reminder.', 'subscriptio'), true);
                return false;
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_suspension($subscription_id, $scheduled_events['suspension']);
            Subscriptio_Event_Scheduler::schedule_suspension($subscription_id, $new_timestamp);
            $transaction->update_result('success');
            $transaction->update_note(__('Suspension date changed.', 'subscriptio'), true);
            return $new_timestamp;
        }

        // Changing suspension date
        if ($date_type == 'cancellation') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['cancellation'], $new_date);

            // Check if it's not set before next reminder
            if ($new_timestamp <= $scheduled_events['reminder']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Cancellation date should be after scheduled reminder.', 'subscriptio'), true);
                return false;
            }

            // Check if it's not set before suspension
            else if ($new_timestamp <= $scheduled_events['suspension']) {
                $transaction->update_result('error');
                $transaction->update_note(__('Cancellation date should be after suspension date.', 'subscriptio'), true);
                return false;
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_cancellation($subscription_id, $scheduled_events['cancellation']);
            Subscriptio_Event_Scheduler::schedule_cancellation($subscription_id, $new_timestamp);
            $transaction->update_result('success');
            $transaction->update_note(__('Cancellation date changed.', 'subscriptio'), true);
            return $new_timestamp;
        }

        // Changing suspension date
        if ($date_type == 'expiration') {

            // Create timestamp
            $new_timestamp = self::get_new_adjusted_timestamp($scheduled_events['expiration'], $new_date);

            // Check if it's set after all other events
            foreach ($scheduled_events as $event_name => $event) {
                if ($new_timestamp < $event && $event_name != 'expiration') {
                    $transaction->update_result('error');
                    $transaction->update_note(__('Expiration date should be after other scheduled events.', 'subscriptio'), true);
                    return false;
                }
            }

            // Re-schedule if checks were passed
            Subscriptio_Event_Scheduler::unschedule_expiration($subscription_id, $scheduled_events['expiration']);
            Subscriptio_Event_Scheduler::schedule_expiration($subscription_id, $new_timestamp);
            $transaction->update_result('success');
            $transaction->update_note(__('Expiration date changed.', 'subscriptio'), true);
            return $new_timestamp;
        }

        // Return false by default
        return false;
    }

    /**
     * Get new adjusted timestamp using the current timestamp
     * New date must be in format Y-m-d
     *
     * @access public
     * @param int $current_timestamp
     * @param string $new_date
     * @return int
     */
    public static function get_new_adjusted_timestamp($current_timestamp, $new_date)
    {
        // Get datetime object with correct time zone
        if ($current_timestamp) {
            $dt = RightPress_Helper::get_datetime_object($current_timestamp);
        }
        else {
            $dt = RightPress_Helper::get_datetime_object();
            $dt->setTime(23, 59, 59);
        }

        // Split date
        $new_date = explode('-', $new_date);

        // Do not proceed if date does not look valid
        if (count($new_date) !== 3) {
            exit;
        }

        // Set year, month and day
        $dt->setDate($new_date[0], $new_date[1], $new_date[2]);

        // Get timestamp and return
        return $dt->format('U');
    }

    /**
     * Check if subscription is in trial mode or if new subscription is applicable for trial
     *
     * @access public
     * @return bool
     */
    public function is_trial()
    {
        // Has a status indicating this is a trial?
        if ($this->status == 'trial') {
            return true;
        }

        // Trial applicable to newly placed subscription?
        if (isset($this->free_trial_time_unit) && isset($this->free_trial_time_value) && self::get_period_length_in('second', $this->free_trial_time_unit, $this->free_trial_time_value)) {
            return true;
        }

        return false;
    }

    /**
     * Get reminder timestamps
     *
     * @access public
     * @param string $type
     * @param int $base_timestamp
     * @return array
     */
    public function get_reminders($type, $base_timestamp)
    {
        $reminders = array();

        if (!Subscriptio::option('reminders_enabled') || !Subscriptio::option('reminders_days')) {
            return $reminders;
        }

        $days = explode(',', Subscriptio::option('reminders_days'));

        // Iterate over days array and calculate timestamps for events
        foreach ($days as $day) {

            // Calculate offset in seconds
            $offset_in_seconds = $day * 86400;

            // Use scale of 1:10080 (1 minute = 1 week) when debugging
            $offset_in_seconds = Subscriptio::$debug ? $offset_in_seconds / 10080 : $offset_in_seconds;

            // Calculate current reminder event timestamp
            $timestamp = $base_timestamp - $offset_in_seconds;

            // Only proceed if this moment in time has not yet passed
            if (time() < $timestamp) {
                $reminders[] = $timestamp;
            }
        }

        return $reminders;
    }

    /**
     * Load ONLY valid subscription (subscription exists, not in trash, user exists etc)
     *
     * @access public
     * @param $subscription_id
     * @param $transaction
     * @return mixed
     */
    public static function get_valid_subscription($subscription_id, $transaction = null)
    {
        // Check if subscription ID was passed in
        if (!is_numeric($subscription_id) || $subscription_id < 1) {

            if ($transaction) {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription ID unknown.', 'subscriptio'), true);
            }

            return false;
        }

        // Update transaction with subscription ID
        if ($transaction) {
            $transaction->add_subscription_id($subscription_id);
        }

        // Get subscription by its ID
        $subscription = self::get_by_id($subscription_id);

        // Check if subscription exists
        if (!isset($subscription->id)) {

            if ($transaction) {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription no longer exists.', 'subscriptio'), true);
            }

            return false;
        }

        // Check if subscription is not cancelled or expired
        if (in_array($subscription->status, array('cancelled', 'expired'))) {

            if ($transaction) {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription is cancelled or expired.', 'subscriptio'), true);
            }

            return false;
        }

        // Check if user exists
        if (!isset($subscription->user_id) || !($user = get_userdata($subscription->user_id))) {

            if ($transaction) {
                $transaction->update_result('error');
                $transaction->update_note(__('User no longer exists.', 'subscriptio'), true);
            }

            return false;
        }

        // Check if subscription shouldn't be expired (better be safe than sorry...)
        if (!defined('SUBSCRIPTIO_DOING_EXPIRATION') && $subscription->expires && time() >= $subscription->expires) {

            // Expire the subscription now
            Subscriptio_Event_Scheduler::scheduled_expiration($subscription->id);

            if ($transaction) {
                $transaction->update_result('error');
                $transaction->update_note(__('Subscription was already expired. Scheduled tasks failing?', 'subscriptio'), true);
            }

            return false;
        }

        // Update transaction with product and variation IDs
        if ($transaction) {
            $transaction->add_product_id($subscription->product_id);
            $transaction->add_variation_id($subscription->variation_id);
        }

        // All tests passed.. Return subscription object
        return $subscription;
    }

    /**
     * Logic to execute before status change
     *
     * @access public
     * @param string $old_status
     * @param string $new_status
     * @return void
     */
    public function before_status_change($old_status, $new_status)
    {
        if ($new_status != $old_status) {
            do_action('subscriptio_status_changing_from_' . $old_status . '_to_' . $new_status, $this);
            do_action('subscriptio_status_changing_to_' . $new_status, $this, $old_status);
            do_action('subscriptio_status_changing', $this, $old_status, $new_status);
        }
    }

    /**
     * Logic to execute on any status change
     *
     * @access public
     * @param string $old_status
     * @param string $new_status
     * @return void
     */
    public function on_status_change($old_status, $new_status)
    {
        if ($new_status != $old_status) {
            do_action('subscriptio_status_changed_from_' . $old_status . '_to_' . $new_status, $this);
            do_action('subscriptio_status_changed_to_' . $new_status, $this, $old_status);
            do_action('subscriptio_status_changed', $this, $old_status, $new_status);
        }
    }

    /**
     * Get formatted subscription number
     *
     * @access public
     * @return string
     */
    public function get_subscription_number()
    {
        return apply_filters('subscriptio_formatted_subscription_number', _x('#', 'hash before subscription number', 'subscriptio') . $this->id, $this);
    }

    /**
     * Get subscription items
     *
     * @access public
     * @return array
     */
    public function get_items()
    {
        $items = array();

        // Prepare all items for multi-product subscription
        if (!empty($this->products_multiple)) {

            foreach ($this->products_multiple as $product) {

                // Set quantity and meta
                $quantity = !empty($product['quantity']) ? $product['quantity'] : 1;
                $meta = isset($this->renewal_all_items_meta[$product['product_id']]) ? $this->renewal_all_items_meta[$product['product_id']] : '';

                // Prepare and save item in correct format
                $items[] = $this->prepare_item($product['product_id'], $product['product_name'], $quantity, $product['total'], $product['tax'], $product['variation_id'], $meta);
            }
        }

        // Prepare one item for single-product subscription
        else if (!empty($this->product_id)) {

            // Set quantity and meta
            $quantity = !empty($this->quantity) ? $this->quantity : 1;
            $meta = isset($this->renewal_all_items_meta[$this->product_id]) ? $this->renewal_all_items_meta[$this->product_id] : '';

            // Prepare and save item in correct format
            $items[] = $this->prepare_item($this->product_id, $this->product_name, $quantity, $this->renewal_line_total, $this->renewal_line_tax, $this->variation_id, $meta);
        }

        return $items;
    }

    /**
     * Prepare subscription item
     *
     * @access public
     * @param int $product_id
     * @param string $product_name
     * @param int $quantity
     * @param int $total
     * @param int $tax
     * @param mixed $variation_id
     * @param mixed $meta
     * @return array
     */
    public function prepare_item($product_id, $product_name, $quantity, $total, $tax, $variation_id = '', $meta = '')
    {
        $item = array(
            'product_id'    => $product_id,
            'quantity'      => $quantity,
            'total'         => $total,
            'tax'           => $tax,
            'meta'          => $meta,
            'deleted'       => false,
        );

        // Is this a variable product?
        $item['name'] = (!empty($variation_id) && is_admin()) ? sprintf(__('Variation #%1$s of', 'subscriptio'), $variation_id) . ' ' : '';

        // Is this product still active?
        if (Subscriptio::product_is_active($product_id)) {

            // Get current product name
            $item['name'] .= get_the_title($product_id);

            // Is variation still active?
            if (!empty($variation_id) && !Subscriptio::product_is_active($variation_id)) {
                $item['name'] .= ' (' . __('variation deleted', 'subscriptio') . ')';
            }
        }
        else {
            $item['name'] .= $product_name;
            $item['name'] .= ' (' . __('deleted', 'subscriptio') . ')' . ($quantity > 1 ? ' x ' . $quantity : '');
            $item['deleted'] = true;
        }

        return $item;
    }

    /**
     * Show variable subscription item meta
     *
     * @access public
     * @param array $item
     * @return array
     */
    public function show_variable_item_meta($item)
    {
        if (!is_array($item) || empty($item)) {
            return false;
        }

        // Change format for WC 2.4+
        if (RightPress_Helper::wc_version_gte('2.4')) {

            if (!isset($item['meta']['item_meta']) && !isset($item['meta']['item_meta_array'])) {
                return false;
            }

            $item_meta = array_merge($item, array('item_meta' => $item['meta']['item_meta'], 'item_meta_array' => $item['meta']['item_meta_array']));
            unset($item_meta['meta']);
        }

        else {

            if (isset($item['meta']['item_meta'])) {
                $item_meta = $item['meta']['item_meta'];
            }

            else {
                $item_meta = $item['meta'];
            }
        }

        $item_meta_object = new WC_Order_Item_Meta($item_meta);
        $item_meta_object->display();
    }

    /**
     * Get subscription items statically
     *
     * @access public
     * @param int $subscription_id
     * @return array
     */
    public static function get_subscription_items($subscription_id)
    {
        $subscription = self::get_by_id($subscription_id);

        if ($subscription && !empty($subscription->id)) {
            return $subscription->get_items();
        }

        return array();
    }

    /**
     * Check if subscription is free
     *
     * @access public
     * @param int $subscription_id
     * @return array
     */
    public static function is_free($subscription_id, $first_time = false)
    {
        // Get all subscription items
        $subscription_items = self::get_subscription_items($subscription_id);

        // Iterate over subscription items and check if at least one of them is not free
        foreach ($subscription_items as $item) {
            if (!empty($item['total'])) {
                return false;
            }
        }

        // All subscription items were free
        return true;
    }

    /**
     * Get formatted price
     *
     * @access public
     * @param float $price
     * @param bool $display_price_suffix
     * @return string
     */
    public function get_formatted_price($price, $display_price_suffix = false)
    {
        return Subscriptio::get_formatted_price($price, $this->renewal_order_currency, false, $display_price_suffix);
    }

    /**
     * Get formatted recurring amount
     *
     * @access public
     * @return string
     */
    public function get_formatted_recurring_amount()
    {
        return Subscriptio_Subscription_Product::format_recurring_amount($this->renewal_order_total, $this->price_time_unit, $this->price_time_value, $this->renewal_order_currency, false, false);
    }

    /**
     * Get formatted status for frontend display
     *
     * @access public
     * @param bool $capital
     * @return string
     */
    public function get_formatted_status($capital = false)
    {
        $title = $capital ? ucfirst($this->status_title) : $this->status_title;
        return apply_filters('subscriptio_formatted_status', $title);
    }

    /**
     * Get actions for frontend subscription list
     *
     * @access public
     * @param bool $list_view
     * @return array
     */
    public function get_frontend_actions($list_view = true)
    {
        $actions = array();

        // View subscription
        if ($list_view) {
            $actions['view'] = array(
                'title' => __('View', 'subscriptio'),
                'url'   => $this->get_frontend_link('view-subscription'),
            );
        }

        // Subscription inactive? No other actions allowed then...
        if ($this->is_inactive()) {
            return apply_filters('subscriptio_subscription_actions', $actions, $this);
        }

        // Edit shipping address
        if (!$list_view && $this->needs_shipping()) {
            if (apply_filters('subscriptio_allow_shipping_address_edit', true)) {
                $actions['edit_address'] = array(
                    'title' => __('Edit Address', 'subscriptio'),
                    'url'   => $this->get_frontend_link('subscription-address'),
                );
            }
        }

        // Pause subscription
        if (!$list_view && $this->can_be_paused() && $this->allow_customer_subscription_pausing()) {
            $actions['pause_subscription'] = array(
                'title' => __('Pause Subscription', 'subscriptio'),
                'url'   => $this->get_frontend_link('pause-subscription'),
            );
        }

        // Resume subscription
        if (!$list_view && $this->can_be_resumed() && $this->allow_customer_subscription_pausing()) {
            // Allow individual control for resume action
            if (apply_filters('subscriptio_allow_subscription_resuming', true)) {
                $actions['resume_subscription'] = array(
                    'title' => __('Resume Subscription', 'subscriptio'),
                    'url'   => $this->get_frontend_link('resume-subscription'),
                );
            }
        }

        // Cancel subscription
        if (!$list_view && $this->can_be_cancelled() && $this->allow_customer_subscription_cancelling()) {
            $actions['cancel_subscription'] = array(
                'title' => __('Cancel Subscription', 'subscriptio'),
                'url'   => $this->get_frontend_link('cancel-subscription'),
            );
        }

        return apply_filters('subscriptio_subscription_actions', $actions, $this);
    }

    /**
     * Get frontend link
     *
     * @access public
     * @param string $view
     * @return string
     */
    public function get_frontend_link($view)
    {
        return wc_get_endpoint_url($view, $this->id, get_permalink(wc_get_page_id('myaccount')));
    }

    /**
     * Check if subscription is inactive
     *
     * @access public
     * @return bool
     */
    public function is_inactive()
    {
        if (in_array($this->status, array('expired', 'cancelled', 'failed'))) {
            return true;
        }

        false;
    }

    /**
     * Check if customers are allowed to pause/resume subscriptions
     *
     * @access public
     * @return bool
     */
    public function allow_customer_subscription_pausing()
    {
        if (Subscriptio::option('customer_pausing_allowed') && apply_filters('subscriptio_allow_subscription_pausing', true)) {
            return true;
        }

        return false;
    }

    /**
     * Check pause limit for customer's subscription
     *
     * @access public
     * @return bool|int
     */
    public function check_pause_limit()
    {
        // Get pause limit
        $user_current_pause_amount = get_user_meta($this->user_id, '_subscriptio_pause_limit', true);

        // Convert to correct format
        if (!empty($user_current_pause_amount)) {
            $user_current_pause_amount = RightPress_Helper::unwrap_post_meta($user_current_pause_amount);
            if (!isset($user_current_pause_amount[$this->id])) {
                return 0;
            }
        }
        else {
            return 0;
        }

        // Don't allow pause if max amount of pauses reached
        if (Subscriptio::option('customer_pausing_allowed') && Subscriptio::option('max_pauses') > 0 && ($user_current_pause_amount[$this->id] >= Subscriptio::option('max_pauses'))) {
            return false;
        }

        return (int) $user_current_pause_amount[$this->id];
    }

    /**
     * Check if customers are allowed to cancel subscriptions
     *
     * @access public
     * @return bool
     */
    public function allow_customer_subscription_cancelling()
    {
        if (Subscriptio::option('customer_cancelling_allowed') && apply_filters('subscriptio_allow_subscription_cancelling', true)) {
            return true;
        }

        return false;
    }

    /**
     * Cancel subscription by customer
     *
     * @access public
     * @return void
     */
    public function cancel_by_customer()
    {
        // Write transaction
        $transaction = new Subscriptio_Transaction(null, 'manual_cancellation');
        $transaction->add_subscription_id($this->id);
        $transaction->add_product_id($this->product_id);
        $transaction->add_variation_id($this->variation_id);

        try {
            // Cancel subscription
            $this->cancel();

            // Update transaction
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription cancelled manually by customer.', 'subscriptio'), true);

            return true;
        }
        catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage());
            return false;
        }
    }

    /**
     * Pause subscription by customer
     *
     * @access public
     * @return void
     */
    public function pause_by_customer()
    {
        // Write transaction
        $transaction = new Subscriptio_Transaction(null, 'subscription_pause');
        $transaction->add_subscription_id($this->id);
        $transaction->add_product_id($this->product_id);
        $transaction->add_variation_id($this->variation_id);

        try {
            // Pause subscription
            $this->pause();

            // Update transaction
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription paused by customer.', 'subscriptio'), true);

            return true;
        }
        catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage());
            return false;
        }
    }

    /**
     * Resume subscription by customer
     *
     * @access public
     * @return void
     */
    public function resume_by_customer()
    {
        // Write transaction
        $transaction = new Subscriptio_Transaction(null, 'subscription_resume');
        $transaction->add_subscription_id($this->id);
        $transaction->add_product_id($this->product_id);
        $transaction->add_variation_id($this->variation_id);

        try {
            // Resume subscription
            $this->resume();

            // Update transaction
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription resumed by customer.', 'subscriptio'), true);

            return true;
        }
        catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage());
            return false;
        }
    }

    /**
     * Check if subscription can be paused
     *
     * @access public
     * @return bool
     */
    public function can_be_paused()
    {
        return in_array($this->status, array('active', 'suspended', 'trial', 'overdue')) && $this->check_pause_limit() !== false ? true : false;
    }

    /**
     * Check if subscription can be resumed
     *
     * @access public
     * @return bool
     */
    public function can_be_resumed()
    {
        return in_array($this->status, array('paused')) ? true : false;
    }

    /**
     * Check if subscription can be cancelled
     *
     * @access public
     * @return bool
     */
    public function can_be_cancelled()
    {
        return in_array($this->status, array('pending', 'active', 'paused', 'suspended', 'trial', 'overdue')) ? true : false;
    }

    /**
     * Check if subscription can be in trial state for this customer
     *
     * @access public
     * @return bool
     */
    public function can_be_in_trial()
    {
        // Check settings
        $limit_trials = Subscriptio::option('limit_trials');

        if ($limit_trials == 0 || empty($limit_trials)) {
            return true;
        }

        else if (in_array($limit_trials, array(1, 2))) {

            // One-product subscription
            if (isset($this->product_id)) {

                if (Subscriptio_Subscription_Product::allow_trial($this->product_id)) {
                    return true;
                }
            }

            // Multi-product subscription
            else if (!empty($this->products_multiple)) {

                foreach ($this->products_multiple as $product) {

                    // Check if at least one product is not allowed
                    if (!Subscriptio_Subscription_Product::allow_trial($product['product_id'])) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Check if subscription needs shipping
     *
     * @access public
     * @return bool
     */
    public function needs_shipping()
    {
        return (is_array($this->shipping) && !empty($this->shipping['name'])) ? true : false;
    }

    /**
     * Update shipping address
     *
     * @access public
     * @param array $address
     * @param bool $is_frontend
     * @param bool $is_customer
     * @return bool
     */
    public function update_shipping_address($address, $is_frontend = false, $is_customer = false)
    {
        $fields = array(
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
            'shipping_country',
        );

        $shipping_address = array();

        foreach ($fields as $field) {
            if ($is_frontend) {
                $shipping_address['_' . $field] = isset($address[$field]) ? $address[$field] : '';
            }
            else {
                $shipping_address['_' . $field] = isset($address['_' . $field]) ? $address['_' . $field] : '';
            }
        }

        // Check if address has changed
        if (array_diff($this->shipping_address, $shipping_address) || array_diff($shipping_address, $this->shipping_address)) {

            // Write transaction
            $transaction = new Subscriptio_Transaction(null, 'address_changed');
            $transaction->add_subscription_id($this->id);
            $transaction->add_product_id($this->product_id);
            $transaction->add_variation_id($this->variation_id);

            try {

                // Save fields
                $this->update_subscription_details(array('shipping_address' => $shipping_address));

                // Update transaction
                $transaction->update_result('success');

                if ($is_customer) {
                    $transaction->update_note(__('Shipping address changed by customer.', 'subscriptio'), true);
                }
                else {
                    $transaction->update_note(__('Shipping address changed by administrator.', 'subscriptio'), true);
                }

                return true;
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Get admin shipping address edit fields
     *
     * @access public
     * @return array
     */
    public static function get_admin_shipping_fields()
    {
        return array(
            '_shipping_first_name'  => array(
                'type'      => 'text',
                'title'     => __('First Name', 'subscriptio'),
            ),
            '_shipping_last_name'   => array(
                'type'      => 'text',
                'title'     => __('Last Name', 'subscriptio'),
            ),
            '_shipping_company'     => array(
                'type'      => 'text',
                'title'     => __('Company', 'subscriptio'),
            ),
            '_shipping_address_1'   => array(
                'type'      => 'text',
                'title'     => __('Address 1', 'subscriptio'),
            ),
            '_shipping_address_2'   => array(
                'type'      => 'text',
                'title'     => __('Address 2', 'subscriptio'),
            ),
            '_shipping_city'        => array(
                'type'      => 'text',
                'title'     => __('City', 'subscriptio'),
            ),
            '_shipping_postcode'    => array(
                'type'      => 'text',
                'title'     => __('Postcode', 'subscriptio'),
            ),
            '_shipping_country'     => array(
                'type'      => 'select',
                'title'     => __('Country', 'subscriptio'),
                'values'    => array('' => __('Select a country&hellip;', 'subscriptio')) + WC()->countries->get_shipping_countries(),
            ),
            '_shipping_state'       => array(
                'type'      => 'text',
                'title'     => __('State/County', 'subscriptio'),
            ),
        );
    }

    /**
     * Get status details
     *
     * @access public
     * @return string
     */
    public function get_status_details()
    {
        if ($this->status === 'paused' && Subscriptio::option('max_pause_duration') > 0 && !empty($this->resumes)) {
            return ' ' . __('until', 'subscriptio') . ' ' . Subscriptio::get_adjusted_datetime($this->resumes, null, 'subscription_frontend_resumes_readable');
        }

        return '';
    }

    /**
     * Maybe record subscription product's id - to limit trials
     *
     * @access public
     * @return void
     */
    public function add_trial_user_meta()
    {
        // Check settings if it needs to be recorded
        if (!empty($this->user_id) && in_array(Subscriptio::option('limit_trials'), array(1, 2))) {

            // One-product subscription
            if (isset($this->product_id)) {
                add_user_meta($this->user_id, '_subscriptio_trial_product_ids', $this->product_id, false);
            }

            // Multi-product subscription
            else if (!empty($this->products_multiple)) {

                // Add all products
                foreach ($this->products_multiple as $product) {
                    add_user_meta($this->user_id, '_subscriptio_trial_product_ids', $product['product_id'], false);
                }
            }
        }
    }

    /**
     * Removes subscription post completely
     *
     * Warning! Use only when really needed and add a transaction log entry to explain what happened
     *
     * @access public
     * @return bool
     */
    public function delete()
    {
        return wp_delete_post($this->id, true);
    }


}
}
