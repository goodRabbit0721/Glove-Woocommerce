<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main transaction class
 *
 * @class Subscriptio_Transaction
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Transaction')) {

class Subscriptio_Transaction
{
    // Define transaction properties
    public $id;
    public $action;
    public $action_title;
    public $result;
    public $result_title;
    public $subscription_id;
    public $order_id;
    public $product_id;
    public $variation_id;
    public $time;
    public $iso_date;
    public $note;

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @param string $action
     * @param int $subscription_id
     * @param int $order_id
     * @param int $product_id
     * @param int $variation_id
     * @return void
     */
    public function __construct($id = null, $action = null, $subscription_id = null, $order_id = null, $product_id = null, $variation_id = null)
    {

        // Create new or load existing?
        if ($id === null) {

            // Predefined transaction action?
            $actions = self::get_actions();

            if (!isset($actions[$action])) {
                throw new Exception('Using undefined Subscriptio transaction action.');
            }

            $this->action = $action;
            $this->result = 'processing';

            $this->id = wp_insert_post(array(
                'post_title'        => '',
                'post_name'         => '',
                'post_status'       => 'publish',
                'post_type'         => 'sub_transaction',
                'ping_status'       => 'closed',
                'comment_status'    => 'closed',
            ));

            wp_set_object_terms($this->id, $this->result, 'sub_transaction_result');
            wp_set_object_terms($this->id, $this->action, 'sub_transaction_action');

            // Add subscription id as post meta
            if ($subscription_id) {
                $this->subscription_id = (int) $subscription_id;
                add_post_meta($this->id, 'subscription_id', $this->subscription_id);
            }

            // Add order id as post meta
            if ($order_id) {
                $this->order_id = (int) $order_id;
                add_post_meta($this->id, 'order_id', $this->order_id);
            }

            // Add product id as post meta
            if ($product_id) {
                $this->product_id = (int) $product_id;
                add_post_meta($this->id, 'product_id', $this->product_id);
            }

            // Add variation id as post meta
            if ($variation_id) {
                $this->variation_id = (int) $variation_id;
                add_post_meta($this->id, 'variation_id', $this->variation_id);
            }

            // Add time
            $this->time = time();
            add_post_meta($this->id, 'time', $this->time);

            // Add ISO date for search
            $this->iso_date = date('Y-m-d H:i:s');
            add_post_meta($this->id, 'iso_date', $this->iso_date);
        }
        else {
            $this->id = $id;
            $this->populate();
        }
    }

    /**
     * Define and return transaction actions
     *
     * @access public
     * @return array
     */
    public static function get_actions()
    {
        return array(
            'new_order' => array(
                'title' => __('New Order', 'subscriptio'),
            ),
            'payment_received' => array(
                'title' => __('Payment Received', 'subscriptio'),
            ),
            'renewal_order' => array(
                'title' => __('Renewal Order', 'subscriptio'),
            ),
            'suspension' => array(
                'title' => __('Suspension', 'subscriptio'),
            ),
            'automatic_cancellation' => array(
                'title' => __('Automatic Cancellation', 'subscriptio'),
            ),
            'manual_cancellation' => array(
                'title' => __('Manual Cancellation', 'subscriptio'),
            ),
            'order_cancellation' => array(
                'title' => __('Order Cancellation', 'subscriptio'),
            ),
            'order_refund' => array(
                'title' => __('Order Refund', 'subscriptio'),
            ),
            'expiration' => array(
                'title' => __('Expiration', 'subscriptio'),
            ),
            'payment_due' => array(
                'title' => __('Payment Due', 'subscriptio'),
            ),
            'payment_reminder' => array(
                'title' => __('Payment Reminder', 'subscriptio'),
            ),
            'subscription_deleted' => array(
                'title' => __('Subscription Deleted', 'subscriptio'),
            ),
            'subscription_pause' => array(
                'title' => __('Pausing Subscription', 'subscriptio'),
            ),
            'subscription_resume' => array(
                'title' => __('Resuming Subscription', 'subscriptio'),
            ),
            'address_changed' => array(
                'title' => __('Address Changed', 'subscriptio'),
            ),
            'date_change' => array(
                'title' => __('Scheduled Date Change', 'subscriptio'),
            ),
        );
    }

    /**
     * Define and return transaction results
     *
     * @access public
     * @return array
     */
    public static function get_results()
    {
        return array(
            'processing'    => array(
                'title' => __('processing', 'subscriptio'),
            ),
            'success'       => array(
                'title' => __('success', 'subscriptio'),
            ),
            'failed'        => array(
                'title' => __('failed', 'subscriptio'),
            ),
            'error'         => array(
                'title' => __('error', 'subscriptio'),
            ),
        );
    }

    /**
     * Load existing subscription transaction
     *
     * @access public
     * @return void
     */
    public function populate()
    {
        if (!$this->id) {
            return false;
        }

        // Get action
        $actions = self::get_actions();
        $post_terms = wp_get_post_terms($this->id, 'sub_transaction_action');
        $this->action = RightPress_Helper::clean_term_slug($post_terms[0]->slug);
        $this->action_title = $actions[$this->action]['title'];

        // Get status
        $results = self::get_results();
        $post_terms = wp_get_post_terms($this->id, 'sub_transaction_result');
        $this->result = RightPress_Helper::clean_term_slug($post_terms[0]->slug);
        $this->result_title = $results[$this->result]['title'];

        // Get other fields
        $post_meta = RightPress_Helper::unwrap_post_meta(get_post_meta($this->id));

        // Load other properties from meta
        foreach (array('subscription_id', 'order_id', 'product_id', 'variation_id', 'time', 'iso_date', 'note') as $property) {
            $this->$property = isset($post_meta[$property]) ? maybe_unserialize($post_meta[$property]) : null;
        }
    }

    /**
     * Change transaction result
     *
     * @access public
     * @param string $result
     * @return void
     */
    public function update_result($result)
    {
        $results = self::get_results();

        if (!isset($results[$result])) {
            return;
        }

        $this->result = $result;

        wp_set_object_terms($this->id, $this->result, 'sub_transaction_result');
    }

    /**
     * Update transaction note
     *
     * @access public
     * @param string $note
     * @param bool $append
     * @return void
     */
    public function update_note($note, $append = false)
    {
        $old_note = $append ? get_post_meta($this->id, 'note', true) : '';
        $this->note = $old_note . ($old_note ? PHP_EOL : '') . (string) $note;
        update_post_meta($this->id, 'note', $this->note);
    }

    /**
     * Add subscription ID
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public function add_subscription_id($subscription_id)
    {
        $this->subscription_id = (int) $subscription_id;
        add_post_meta($this->id, 'subscription_id', $this->subscription_id);
    }

    /**
     * Add order ID
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function add_order_id($order_id)
    {
        $this->order_id = (int) $order_id;
        add_post_meta($this->id, 'order_id', $this->order_id);
    }

    /**
     * Add product ID
     *
     * @access public
     * @param int $product_id
     * @return void
     */
    public function add_product_id($product_id)
    {
        $this->product_id = (int) $product_id;
        add_post_meta($this->id, 'product_id', $this->product_id);
    }

    /**
     * Add variation ID
     *
     * @access public
     * @param int $variation_id
     * @return void
     */
    public function add_variation_id($variation_id)
    {
        $this->variation_id = $variation_id === null ? null : (int) $variation_id;
        add_post_meta($this->id, 'variation_id', $this->variation_id);
    }

}
}
