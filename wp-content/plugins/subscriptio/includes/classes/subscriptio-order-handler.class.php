<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WC Order related methods (mostly hooks like "do something when new order is placed")
 *
 * @class Subscriptio_Order_Handler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Order_Handler')) {

class Subscriptio_Order_Handler
{

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Temporary store renewal order properties
        $this->renewal_orders = array(
            'by_cart_item_key'  => array(),
            'by_line_item_id'   => array(),
        );

        // Move renewal order properties from by_cart_item_key to by_line_item_id
        add_action('woocommerce_add_order_item_meta', array($this, 'move_renewal_order_properties'), 10, 3);

        // Calculate renewal taxes etc
        add_action('woocommerce_new_order', array($this, 'new_order'));
        add_action('woocommerce_resume_order', array($this, 'new_order'));

        // Create new subscription if order contains subscription products
        add_action('woocommerce_checkout_update_order_meta', array($this, 'new_order_placed'), 99, 2);

        // Activate subscription or renew existing subscription
        add_action('woocommerce_payment_complete', array($this, 'payment_received'));
        add_action('woocommerce_order_status_processing', array($this, 'payment_received_status'));
        add_action('woocommerce_order_status_completed', array($this, 'payment_received_status'));

        // Cancel subscription if order is cancelled and subscription is still pending
        add_action('woocommerce_order_status_cancelled', array($this, 'order_cancelled'));

        // Cancel subscription if initial order gets refunded and there's another still unpaid
        add_filter('woocommerce_order_status_refunded', array($this, 'order_refunded_maybe_cancel_subscription'));

        // Prevent WooCommerce from cancelling unpaid renewal orders prematurely
        add_filter('woocommerce_cancel_unpaid_order', array($this, 'cancel_unpaid_order'), 10, 2);

        // Handling product downloads
        add_filter('woocommerce_get_item_downloads', array($this, 'filter_download_links'), 10, 3);
        add_filter('woocommerce_customer_get_downloadable_products', array($this, 'filter_downloadable_products'));
        add_action('woocommerce_download_product', array($this, 'maybe_prevent_file_download'), 10, 6);
    }

    /**
     * Prepare recurring amounts, taxes etc for subscription items
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function new_order($order_id)
    {
        global $woocommerce;

        // Check if need to create one subscription for all products
        $multiproduct_subscription = (Subscriptio::multiproduct_mode() && self::multiproduct_cart_check()) ? true : false;

        // Maybe create new cart and renewal order here...
        if ($multiproduct_subscription) {

            $renewal_cart = new WC_Cart();

            $renewal_order = array(
                'taxes'     => array(),
                'shipping'  => array(),
            );
        }

        // Set item counter for correct taxes adding for multiproduct subscription
        $itemcount = 0;

        // Iterate over real cart and work with subscription products (if any)
        foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) {

            $itemcount++;
            $id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];

            if (Subscriptio_Subscription_Product::is_subscription($id)) {

                // Get the cart item key of first subscription product in cart
                if (!isset($cart_item_key_first)) {
                    $cart_item_key_first = $cart_item_key;
                }

                $product = RightPress_Helper::wc_version_gte('2.2') ? wc_get_product($id) : get_product($id);

                // ... or create them here
                if (!$multiproduct_subscription) {

                    // Store all required renewal order fields here
                    $renewal_order = array(
                        'taxes'     => array(),
                        'shipping'  => array(),
                    );

                    // Create fake cart to mimic renewal order
                    $renewal_cart = new WC_Cart();
                }

                // Add product to cart
                $renewal_cart_item_key = $renewal_cart->add_to_cart(
                    $cart_item['product_id'],
                    $cart_item['quantity'],
                    (isset($cart_item['variation_id']) ? $cart_item['variation_id'] : ''),
                    (isset($cart_item['variation']) ? $cart_item['variation'] : '')
                );

                // Get fake cart item key
                $renewal_cart_item_keys = array_keys($renewal_cart->cart_contents);

                // Set renewal price(s) and everything else
                foreach($renewal_cart_item_keys as $renewal_cart_item_key) {

                    // Add shipping
                    if ($product->needs_shipping() && $renewal_cart->needs_shipping()) {

                        // Get instance of checkout object to retrieve shipping options
                        $wc_checkout = WC_Checkout::instance();

                        // Iterate over shipping packages
                        foreach ($woocommerce->shipping->get_packages() as $package_key => $package) {

                            // Check if this rate was selected
                            if (isset($package['rates'][$wc_checkout->shipping_methods[$package_key]])) {

                                // Check if it contains current subscription
                                if (isset($package['contents'][$cart_item_key]) || isset($package['contents'][$renewal_cart_item_key])) {

                                    // Save shipping details for further calculation
                                    $shipping_details = array(
                                        'shipping_method'   => $wc_checkout->shipping_methods[$package_key],
                                        'destination'       => $package['destination'],
                                    );

                                    // Save shipping address
                                    $renewal_order['shipping_address'] = array(
                                        // First three lines may need to be changed to make this compatible with shipping extensions that allow multiple shipping addresses
                                        '_shipping_first_name'  => $wc_checkout->posted['ship_to_different_address'] ? $wc_checkout->posted['shipping_first_name'] : $wc_checkout->posted['billing_first_name'],
                                        '_shipping_last_name'   => $wc_checkout->posted['ship_to_different_address'] ? $wc_checkout->posted['shipping_last_name'] : $wc_checkout->posted['billing_last_name'],
                                        '_shipping_company'     => $wc_checkout->posted['ship_to_different_address'] ? $wc_checkout->posted['shipping_company'] : $wc_checkout->posted['billing_company'],
                                        '_shipping_address_1'   => $shipping_details['destination']['address'],
                                        '_shipping_address_2'   => $shipping_details['destination']['address_2'],
                                        '_shipping_city'        => $shipping_details['destination']['city'],
                                        '_shipping_state'       => $shipping_details['destination']['state'],
                                        '_shipping_postcode'    => $shipping_details['destination']['postcode'],
                                        '_shipping_country'     => $shipping_details['destination']['country'],
                                    );

                                    break;
                                }
                            }
                        }

                        // Got the shipping method and address for the package that contains current subscription?
                        if (!isset($shipping_details)) {
                            continue;
                        }

                        // Get packages based on renewal order details
                        $packages = apply_filters('woocommerce_cart_shipping_packages', array(
                            0 => array(
                                'contents'          => $renewal_cart->get_cart(),
                                'contents_cost'     => isset($renewal_cart->cart_contents[$renewal_cart_item_key]['line_total']) ? $renewal_cart->cart_contents[$renewal_cart_item_key]['line_total'] : 0,
                                'applied_coupons'   => $renewal_cart->applied_coupons,
                                'destination'       => $shipping_details['destination'],
                            ),
                        ));

                        // Now we need to calculate shipping costs but this requires overwriting session variables
                        // In order not to affect real cart, we will overwrite them but then set them back to original values
                        $original_session = array(
                            'chosen_shipping_methods'   => $woocommerce->session->get('chosen_shipping_methods'),
                            'shipping_method_counts'    => $woocommerce->session->get('shipping_method_counts'),
                        );

                        // Set fake renewal values
                        $woocommerce->session->set('chosen_shipping_methods', array($shipping_details['shipping_method']));
                        $woocommerce->session->set('shipping_method_counts', array(1));

                        // Override chosen shipping method in case there's a mismatch in shipping_method_counts (more than one available)
                        add_filter('woocommerce_shipping_chosen_method', array($this, 'set_shipping_chosen_method'));
                        $this->temp_shipping_chosen_method = $shipping_details['shipping_method'];

                        // Calculate shipping for fake renewal order now
                        $woocommerce->shipping->calculate_shipping($packages);

                        // Remove filter
                        remove_filter('woocommerce_shipping_chosen_method', array($this, 'set_shipping_chosen_method'));
                        $this->temp_shipping_chosen_method = null;
                    }

                    // Recalculate totals
                    $renewal_cart->calculate_totals();

                    // Get renewal_order_shipping
                    $renewal_order['renewal_order_shipping'] = wc_format_decimal($renewal_cart->shipping_total);

                    // Get renewal_order_shipping_tax
                    $renewal_order['renewal_order_shipping_tax'] = wc_format_decimal($renewal_cart->shipping_tax_total);

                    // Get renewal_cart_discount
                    $renewal_order['renewal_cart_discount'] = wc_format_decimal($renewal_cart->get_cart_discount_total());

                    // Get renewal_order_discount
                    $renewal_order['renewal_order_discount'] = wc_format_decimal(RightPress_Helper::wc_version_gte('2.2') ? $renewal_cart->get_total_discount() : $renewal_cart->get_order_discount_total());

                    // Get renewal_order_tax
                    $renewal_order['renewal_order_tax'] = wc_format_decimal($renewal_cart->tax_total);

                    // Get renewal_order_subtotal
                    $renewal_order['renewal_order_subtotal'] = wc_format_decimal($renewal_cart->subtotal, get_option('woocommerce_price_num_decimals'));

                    // Get renewal_order_total
                    $renewal_order['renewal_order_total'] = wc_format_decimal($renewal_cart->total, get_option('woocommerce_price_num_decimals'));


                    // Differently add lines totals
                    $lines_total = array('line_subtotal', 'line_subtotal_tax', 'line_total', 'line_tax');

                    foreach ($lines_total as $line_key) {

                        $new_value = wc_format_decimal($renewal_cart->cart_contents[$renewal_cart_item_key][$line_key]);
                        $new_key = 'renewal_' . $line_key;

                        if ($multiproduct_subscription) {
                            $renewal_order[$new_key][$id] = $new_value;
                        }
                        else {
                            $renewal_order[$new_key] = $new_value;
                        }
                    }

                }

                // Count the subscriptions
                $all_cart_subscriptions = array();
                foreach ($woocommerce->cart->cart_contents as $cart_item) {

                    $id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];
                    if (Subscriptio_Subscription_Product::is_subscription($id)) {
                        $all_cart_subscriptions[] = $id;
                    }
                }

                // Add taxes always for regular subscription and only on last subscription item for multiproduct
                if (!$multiproduct_subscription || $multiproduct_subscription && $itemcount == count($all_cart_subscriptions)) {
                    foreach ($renewal_cart->get_tax_totals() as $rate_key => $rate) {
                        $renewal_order['taxes'][] = array(
                            'name'                  => $rate_key,
                            'rate_id'               => $rate->tax_rate_id,
                            'label'                 => $rate->label,
                            'compound'              => absint($rate->is_compound ? 1 : 0),
                            'tax_amount'            => wc_format_decimal(isset($renewal_cart->taxes[$rate->tax_rate_id]) ? $renewal_cart->taxes[$rate->tax_rate_id] : 0),
                            'shipping_tax_amount'   => wc_format_decimal(isset($renewal_cart->shipping_taxes[$rate->tax_rate_id]) ? $renewal_cart->shipping_taxes[$rate->tax_rate_id] : 0),
                        );
                    }
                }


                // Get shipping details
                if ($product->needs_shipping()) {

                    if (isset($woocommerce->shipping->packages[0]['rates'][$shipping_details['shipping_method']])) {

                        $method = $woocommerce->shipping->packages[0]['rates'][$shipping_details['shipping_method']];

                        $renewal_order['shipping'] = array(
                            'name'      => $method->label,
                            'method_id' => $method->id,
                            'cost'      => wc_format_decimal($method->cost),
                        );
                    }

                    // Set session variables to original values and recalculate shipping for original order which is being processed now
                    $woocommerce->session->set('chosen_shipping_methods', $original_session['chosen_shipping_methods']);
                    $woocommerce->session->set('shipping_method_counts', $original_session['shipping_method_counts']);
                    $woocommerce->shipping->calculate_shipping($packages);
                }

                // Save to object property so it can be accessed from another method
                if ($multiproduct_subscription) {
                    $this->renewal_orders['by_cart_item_key'][$cart_item_key_first] = $renewal_order;
                }
                else {
                    $this->renewal_orders['by_cart_item_key'][$cart_item_key] = $renewal_order;
                }
            }
        }
    }

    /**
     * Overwrite chosen shipping method if needed when working with fake renewal orders
     *
     * @access public
     * @return string
     */
    public function set_shipping_chosen_method($method)
    {
        return isset($this->temp_shipping_chosen_method) ? $this->temp_shipping_chosen_method : $method;
    }

    /**
     * Move renewal order properties so we can pick them by item id
     *
     * @access public
     * @param int $item_id
     * @param array $cart_item
     * @param string $cart_item_key
     * @return void
     */
    public function move_renewal_order_properties($item_id, $cart_item, $cart_item_key)
    {
        if (isset($this->renewal_orders['by_cart_item_key'][$cart_item_key])) {
            $this->renewal_orders['by_line_item_id'][$item_id] = $this->renewal_orders['by_cart_item_key'][$cart_item_key];
        }
    }

    /**
     * Get all subscriptions from cart and return if they are compatible
     *
     * @access public
     * @return array
     */
    public static function multiproduct_cart_check()
    {
        global $woocommerce;

        foreach ($woocommerce->cart->cart_contents as $cart_item) {

            $id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];

            if (Subscriptio_Subscription_Product::is_subscription($id)) {
                $product_post_meta = RightPress_Helper::unwrap_post_meta(get_post_meta($id));
                $all_subs[$id] = array('product_post_meta' => $product_post_meta);
            }
        }

        return Subscriptio_Subscription::check_compatibility($all_subs);
    }

    /**
     * Handle new WooCommerce orders
     *
     * @access public
     * @param int $order_id
     * @param array $posted
     * @return void
     */
    public function new_order_placed($order_id, $posted)
    {
        $order = RightPress_Helper::wc_get_order($order_id);
        $order_items = $order->get_items();
        $order_meta = RightPress_Helper::unwrap_post_meta(get_post_meta($order->id));

        // Any existing subscription(s) created for this order? This can be caused by multiple payment attempts or so
        if ($existing_subscriptions = self::get_subscriptions_from_order_id($order_id)) {

            // Starting from WC 2.6 orders are resumed only if no changes were made to cart so we can just keep existing subscription(s)
            if (RightPress_Helper::wc_version_gte('2.6')) {
                return;
            }
            // Up to WC 2.6 orders can be resumed even when there were changes made to cart so we need to delete existing subscription(s) and start fresh
            else {

                // Iterate over existing subscriptions
                foreach ($existing_subscriptions as $existing_subscription) {

                    // Delete subscription completely
                    $existing_subscription->delete();

                    // Add entry to transaction log
                    Subscriptio::post_deleted($existing_subscription->id, __('Unpaid WooCommerce order resumed, deleting pending subscription in favor of a new subscription.', 'subscriptio'));
                }
            }
        }

        // Array for found subscription products
        $all_subs = array();

        // Iterate over all items and look for subscription products purchased
        foreach ($order_items as $order_item_key => $order_item) {

            // Get appropriate product and variation IDs
            $variation_id = (isset($order_item['variation_id']) && !empty($order_item['variation_id'])) ? $order_item['variation_id'] : null;
            $product_id = $order_item['product_id'];
            $product_post_meta = RightPress_Helper::unwrap_post_meta(get_post_meta($variation_id ? $variation_id : $product_id));

            // Proceed only if this product is a subscription
            if (!isset($product_post_meta['_subscriptio']) || $product_post_meta['_subscriptio'] != 'yes') {
                continue;
            }

            // Collect all subscription products
            $all_subs[] = array(
                'product_id'        => $product_id,
                'variation_id'      => $variation_id,
                'product_post_meta' => $product_post_meta,
                'order_item_key'    => $order_item_key,
                'order_item'        => $order_item,
            );
        }

        // Proceed only if there's subscriptions found
        if (!empty($all_subs)) {

            // Check if need to create one subscription for all products
            $multiproduct_subscription = (Subscriptio::multiproduct_mode() && Subscriptio_Subscription::check_compatibility($all_subs)) ? true : false;

            // Set how many subscriptions we need to create
            $iterations = $multiproduct_subscription ? 1 : count($all_subs);

            for ($i = 0; $i < $iterations; $i++) {

                // Set variables
                $product_id = $all_subs[$i]['product_id'];
                $variation_id = $all_subs[$i]['variation_id'];
                $order_item_key = $all_subs[$i]['order_item_key'];
                $order_item = $all_subs[$i]['order_item'];
                $product_post_meta = $all_subs[$i]['product_post_meta'];
                $product = RightPress_Helper::wc_version_gte('2.2') ? wc_get_product($product_id) : get_product($product_id);

                // Start logging transaction
                $transaction = new Subscriptio_Transaction(null, 'new_order', null, $order_id, $order_item['product_id'], $variation_id);

                // Check if this subscription product looks ok
                if (!Subscriptio_Subscription_Product::is_ok($product_post_meta)) {
                    $transaction->update_result('error');
                    $transaction->update_note(__('Invalid subscription product configuration.', 'subscriptio'));
                    continue;
                }

                // Check if we have renewal order properties
                if (!isset($this->renewal_orders['by_line_item_id'][$order_item_key])) {
                    $transaction->update_result('error');
                    $transaction->update_note(__('Failed saving subscription properties for renewal orders.', 'subscriptio'));
                    continue;
                }

                // Everything seems to be ok, lets create a new subscription
                try {
                    $subscription = new Subscriptio_Subscription();

                    // Check if multiproduct subscription activated and products are compatible
                    if ($multiproduct_subscription) {
                        $subscription_id = $subscription->create_from_all_order_items($order, $order_meta, $all_subs, $this->renewal_orders['by_line_item_id'][$order_item_key]);
                    }

                    // If not, create the usual subscription(s)
                    else {
                        $subscription_id = $subscription->create_from_order_item($order, $order_meta, $order_item_key, $order_item, $product, $product_post_meta, $this->renewal_orders['by_line_item_id'][$order_item_key]);
                    }

                    $transaction->update_result('success');
                    $transaction->add_subscription_id($subscription_id);
                    $transaction->update_note(__('Subscription successfully created.', 'subscriptio'));
                }
                catch (Exception $e) {
                    $transaction->update_result('error');
                    $transaction->update_note($e->getMessage());
                }
            }

            // Reset temporary storage
            $this->renewal_orders = array(
                'by_cart_item_key'  => array(),
                'by_line_item_id'   => array(),
            );
        }
    }

    /**
     * Check if order has been paid
     *
     * @access public
     * @param mixed $order
     * @return bool
     */
    public static function order_is_paid($order)
    {
        // Load order if order id was passed in
        if (!is_object($order)) {
            $order = RightPress_Helper::wc_get_order($order);
        }

        // Check if order was loaded
        if (!$order) {
            return false;
        }

        // Check if order is paid
        if (RightPress_Helper::wc_version_gte('2.5')) {
            return $order->is_paid();
        }
        else {

            // Get paid statuses
            $paid_statuses = apply_filters('woocommerce_order_is_paid_statuses', array('processing', 'completed'));

            // Check if order has paid status
            if (RightPress_Helper::wc_version_gte('2.2')) {
                $has_paid_status = $order->has_status($paid_statuses);
            }
            else {
                $order_status = apply_filters('woocommerce_order_get_status', 'wc-' === substr($order->post_status, 0, 3) ? substr($order->post_status, 3) : $order->post_status, $order);
                $has_paid_status = apply_filters('woocommerce_order_has_status', (is_array($paid_statuses) && in_array($order_status, $paid_statuses) ) || $order_status === $paid_statuses ? true : false, $order, $paid_statuses);
            }

            return apply_filters('woocommerce_order_is_paid', $has_paid_status, $order);
        }
    }

    /**
     * Automatic or manual payment received - activate or renew subscription (if this hasn't been done yet)
     *
     * @access public
     * @param int $order_id
     * @param bool $manual
     * @return void
     */
    public function payment_received($order_id, $manual = false)
    {
        // Iterate over subscriptions and activate or renew
        foreach (self::get_subscriptions_from_order_id($order_id) as $subscription) {

            // Make sure we don't run this multiple times on status changes
            if (!$subscription->paid_by_order($order_id)) {

                // Log the transaction
                $transaction = new Subscriptio_Transaction(null, 'payment_received', $subscription->id, $order_id, $subscription->product_id, $subscription->variation_id);

                if ($manual) {
                    $transaction->update_note(__('Triggered by status change.', 'subscriptio'));
                }

                // Unset any pending reminder, suspension and cancellation events (don't move this below pay_by_order!)
                Subscriptio_Event_Scheduler::unschedule_multiple(array(
                    'payment', 'reminder', 'suspension', 'cancellation',
                ), $subscription->id);

                // Apply payment to subscription
                $subscription->pay_by_order($order_id, $transaction);

                // Clear suspended_since property if set
                if (!empty($subscription->suspended_since)) {
                    $subscription->clear_subscription_details(array('suspended_since'));
                }
            }
        }
    }

    /**
     * Activate or renew subscription after status change (if this hasn't been done yet)
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function payment_received_status($order_id)
    {
        $this->payment_received($order_id, true);
    }

    /**
     * Create renewal order
     * Based on the WooCommerce procedure found in class-wc-checkout.php
     *
     * @access public
     * @param object $subscription
     * @return int
     */
    public static function create_renewal_order($subscription)
    {
        // Since WooCommerce 2.2 order statuses are stored as post statuses
        $post_status = RightPress_Helper::wc_version_gte('2.2') ? 'wc-pending' : 'publish';

        // Prepare post properties
        $order_data = array(
            'post_type' 	=> 'shop_order',
            'post_title' 	=> sprintf(__('Order &ndash; %s', 'subscriptio'), strftime(_x('%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'subscriptio'))),
            'post_status' 	=> $post_status,
            'ping_status'	=> 'closed',
            'post_excerpt' 	=> !empty($subscription->renewal_customer_note) ? $subscription->renewal_customer_note : __('No customer note.', 'subscriptio'),
            'post_author' 	=> 1,
            'post_password'	=> uniqid('order_'),
        );

        // Insert post into database
        $order_id = wp_insert_post($order_data, true);

        // Successfully inserted order post?
        if (is_wp_error($order_id)) {
            throw new Exception(__('Unable to create renewal order - failed inserting post.', 'subscriptio'));
        }

        // Check if need to charge shipping
        $charge_shipping = (Subscriptio::option('shipping_renewal_charge') == 1) ? true : false;

        // Load user meta
        $user_meta = RightPress_Helper::unwrap_post_meta(get_user_meta($subscription->user_id));

        // Insert billing and shipping details
        $billing_shipping_fields = array(
            'billing'  => array(
                '_first_name',
                '_last_name',
                '_company',
                '_address_1',
                '_address_2',
                '_city',
                '_state',
                '_postcode',
                '_country',
                '_email',
                '_phone',
            ),
        );

        // Check if subscription needs shipping
        if ($subscription->needs_shipping()) {
            $billing_shipping_fields['shipping'] = array(
                '_first_name',
                '_last_name',
                '_company',
                '_address_1',
                '_address_2',
                '_city',
                '_state',
                '_postcode',
                '_country',
            );
        }

        // Iterate over billing/shipping fields and save them
        foreach ($billing_shipping_fields as $type => $fields) {
            foreach ($fields as $field) {

                // Billing fields
                if ($type == 'billing' && isset($user_meta[$type . $field])) {
                    $field_value = $user_meta[$type . $field];
                }

                // Shipping fields
                else if ($type == 'shipping' && isset($subscription->shipping_address['_' . $type . $field])) {
                    $field_value = $subscription->shipping_address['_' . $type . $field];
                }

                // In case some field does not exist
                else {
                    $field_value = '';
                }

                // Save field to post meta
                update_post_meta($order_id, '_' . $type . $field, $field_value);
            }
        }

        // Temporary solution for developers who need to change prices in renewal orders
        $renewal_order_tax      = apply_filters('subscriptio_renewal_order_tax', $subscription->renewal_order_tax, $subscription);
        $renewal_order_subtotal = apply_filters('subscriptio_renewal_order_subtotal', $subscription->renewal_order_subtotal, $subscription);
        $renewal_order_total    = apply_filters('subscriptio_renewal_order_total', $subscription->renewal_order_total, $subscription);

        // Add other meta fields
        $other_meta_fields = array(
            // Set the shipping to 0 if shipping charge is off for renewal orders
            '_order_shipping'       => $charge_shipping ? $subscription->renewal_order_shipping : 0,
            '_order_shipping_tax'   => $charge_shipping ? $subscription->renewal_order_shipping_tax : 0,
            '_order_tax'            => $renewal_order_tax,
            '_order_subtotal'       => $renewal_order_subtotal,
            // Use subtotal instead of total if shipping charge is off for renewal orders
            '_order_total'          => $charge_shipping ? $renewal_order_total : $renewal_order_subtotal,
            '_customer_user'        => $subscription->user_id,
            '_order_currency'       => $subscription->renewal_order_currency,
            '_order_key'            => 'wc_' . apply_filters('woocommerce_generate_order_key', uniqid('order_')),
            '_prices_include_tax'   => $subscription->renewal_prices_include_tax,
            '_customer_ip_address'  => $subscription->renewal_customer_ip_address,
            '_customer_user_agent'  => $subscription->renewal_customer_user_agent,
            '_payment_method'       => '',  // Not yet paid
            '_payment_method_title' => '',  // Not yet paid
            '_subscriptio_renewal'  => 'yes',
        );

        foreach ($other_meta_fields as $field_key => $field_value) {
            update_post_meta($order_id, $field_key, $field_value);
        }

        // Add any possible custom data for order
        foreach ($subscription->renewal_all_order_meta as $meta_key => $meta_value) {
            if (!isset($billing_shipping_fields[$meta_key]) && !isset($other_meta_fields[$meta_key]) && !preg_match('/_discount/', $meta_key)) {
                update_post_meta($order_id, $meta_key, maybe_unserialize($meta_value));
            }
        }

        // The product handling start

        // Check if can create one subscription for all products
        $multiproduct_subscription = (Subscriptio::multiproduct_mode() && is_array($subscription->products_multiple)) ? true : false;

        // Set how many subscription products we need to add (if multiple - add as many as needed)
        $iterations = $multiproduct_subscription ? count($subscription->products_multiple) : 1;

        // Set variable
        $all_subs = $subscription->products_multiple;

        // Start iterations
        for ($i = 0; $i < $iterations; $i++) {

            // Get product ids
            if (!empty($all_subs)) {
                $product_id     = !empty($all_subs[$i]['product_id']) ? $all_subs[$i]['product_id'] : '';
                $variation_id   = !empty($all_subs[$i]['variation_id']) ? $all_subs[$i]['variation_id'] : '';
                $quantity       = !empty($all_subs[$i]['quantity']) ? $all_subs[$i]['quantity'] : 1;
            }
            else {
                $product_id     = !empty($subscription->product_id) ? $subscription->product_id : '';
                $variation_id   = !empty($subscription->variation_id) ? $subscription->variation_id : '';
                $quantity       = !empty($subscription->quantity) ? $subscription->quantity : 1;
            }

            // Set the current product id
            $product_id_to_use = !empty($variation_id) ? $variation_id : $product_id;

            // Check if product still exists
            if (Subscriptio::product_is_active($product_id_to_use)) {

                // Load product object
                $product = RightPress_Helper::wc_version_gte('2.2') ? wc_get_product($product_id_to_use) : get_product($product_id_to_use);

                // Get product name
                $product_title = $product->get_title();

                // Update product name on subscription if it was changed
                if ($product_title != $subscription->product_name) {
                    $subscription->update_subscription_details(array(
                        'product_name'  => $product_title,
                    ));
                }
            }

            // If not - use saved product "snapshot" from previous order
            else {
                $product_title = $subscription->product_name;
            }

            // Add line item (product) to order
            $item_id = wc_add_order_item($order_id, array(
                'order_item_name'   => $product_title,
                'order_item_type'   => 'line_item',
            ));

            if (!$item_id) {
                throw new Exception(__('Unable to add product to renewal order.', 'subscriptio'));
            }

            // Get totals. Filters are temporary solution for developers who need to change prices in renewal orders
            $renewal_line_subtotal      = apply_filters('subscriptio_renewal_order_line_subtotal', $subscription->renewal_line_subtotal, $subscription, $i);
            $renewal_line_subtotal_tax  = apply_filters('subscriptio_renewal_order_line_subtotal_tax', $subscription->renewal_line_subtotal_tax, $subscription, $i);
            $renewal_line_total         = apply_filters('subscriptio_renewal_order_line_total', $subscription->renewal_line_total, $subscription, $i);
            $renewal_line_tax           = apply_filters('subscriptio_renewal_order_line_tax', $subscription->renewal_line_tax, $subscription, $i);

            // Add line item meta
            $item_meta = array(
                '_qty'                  => $quantity,
                '_tax_class'            => $subscription->renewal_tax_class[$product_id_to_use],
                '_product_id'           => $product_id_to_use,
                '_variation_id'         => $variation_id,
                '_line_subtotal'        => is_array($renewal_line_subtotal) ? wc_format_decimal($renewal_line_subtotal[$product_id_to_use]) : wc_format_decimal($renewal_line_subtotal),
                '_line_subtotal_tax'    => is_array($renewal_line_subtotal_tax) ? wc_format_decimal($renewal_line_subtotal_tax[$product_id_to_use]) : wc_format_decimal($renewal_line_subtotal_tax),
                '_line_total'           => is_array($renewal_line_total) ? wc_format_decimal($renewal_line_total[$product_id_to_use]) : wc_format_decimal($renewal_line_total),
                '_line_tax'             => is_array($renewal_line_tax) ? wc_format_decimal($renewal_line_tax[$product_id_to_use]) : wc_format_decimal($renewal_line_tax),
            );

            foreach ($item_meta as $item_meta_key => $item_meta_value) {
                wc_add_order_item_meta($item_id, $item_meta_key, maybe_unserialize($item_meta_value));
            }

            // Add any possible custom data for item
            if (isset($subscription->renewal_all_items_meta[$product_id]) && is_array($subscription->renewal_all_items_meta[$product_id])) {
                foreach ($subscription->renewal_all_items_meta[$product_id]['item_meta'] as $item_meta_key => $item_meta_value) {
                    if (!isset($item_meta[$item_meta_key])) {
                        foreach ($item_meta_value as $meta_value) {
                            wc_add_order_item_meta($item_id, $item_meta_key, maybe_unserialize($meta_value));
                        }
                    }
                }
            }

            // Save shipping info (if any)
            if (!empty($subscription->shipping)) {
                $shipping_item_id = wc_add_order_item($order_id, array(
                    'order_item_name'   => $subscription->shipping['name'],
                    'order_item_type'   => 'shipping',
                ));

                wc_add_order_item_meta($shipping_item_id, 'method_id', $subscription->shipping['method_id']);

                // Maybe change shipping cost
                $shipping_cost = $charge_shipping ? $subscription->shipping['cost'] : 0;
                wc_add_order_item_meta($shipping_item_id, 'cost', wc_format_decimal($shipping_cost));
            }

        } // Product handling end

        // Save taxes (if any)
        if (is_array($subscription->taxes)) {
            foreach ($subscription->taxes as $tax) {
                $tax_item_id = wc_add_order_item($order_id, array(
                    'order_item_name'   => $tax['name'],
                    'order_item_type'   => 'tax',
                ));

                wc_add_order_item_meta($tax_item_id, 'rate_id', $tax['rate_id']);
                wc_add_order_item_meta($tax_item_id, 'label', $tax['label']);
                wc_add_order_item_meta($tax_item_id, 'compound', $tax['compound']);
                wc_add_order_item_meta($tax_item_id, 'tax_amount', wc_format_decimal($tax['tax_amount'], 4));

                // Maybe change shipping tax
                $shipping_tax = $charge_shipping ? $tax['shipping_tax_amount'] : 0;
                wc_add_order_item_meta($tax_item_id, 'shipping_tax_amount', wc_format_decimal($shipping_tax, 4));
            }
        }

        // Try to get scheduled payment timestamp
        $payment_due_timestamp = Subscriptio_Event_Scheduler::get_scheduled_event_timestamp('payment', $subscription->id);

        // If set, schedule payment due reminders
        if ($payment_due_timestamp) {
            foreach ($subscription->get_reminders('pre_payment_due', $payment_due_timestamp) as $timestamp) {
                Subscriptio_Event_Scheduler::schedule_reminder($subscription->id, $timestamp);
            }
        }

        // Update appropriate subscription fields with new order id
        $subscription->update_subscription_details(array(
            'last_order_id' => $order_id,
            'all_order_ids' => $order_id,
        ));

        // Allow developers to work their magic
        do_action('subscriptio_created_renewal_order', $subscription, $order_id);

        // Create a new order object
        $order = RightPress_Helper::wc_get_order($order_id);

        // Send New Order email
        Subscriptio_Mailer::send('new_order', $order);

        // If renewal order's total is zero or the site is demo - change status to processing
        if ($order->order_total == 0 || RightPress_Helper::is_demo()) {
            $order->update_status('processing');
        }

        // Otherwise set status to pending (pre- WooCommerce 2.2; automatically set in 2.2+)
        else if (!RightPress_Helper::wc_version_gte('2.2')) {
            $order->update_status('pending');
        }

        return $order_id;
    }

    /**
     * Cancel pending subscriptions when orders are cancelled
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function order_cancelled($order_id)
    {
        // Iterate over subscriptions and cancel if they are still pending
        foreach (self::get_subscriptions_from_order_id($order_id) as $subscription) {
            if ($subscription->status == 'pending') {

                // Write transaction
                $transaction = new Subscriptio_Transaction(null, 'order_cancellation');
                $transaction->add_subscription_id($subscription->id);
                $transaction->add_order_id($order_id);
                $transaction->add_product_id($subscription->product_id);
                $transaction->add_variation_id($subscription->variation_id);

                try {
                    // Cancel subscription
                    $subscription->cancel();

                    // Update transaction
                    $transaction->update_result('success');
                    $transaction->update_note(__('Pending subscription cancelled due to cancelled order.', 'subscriptio'), true);
                }
                catch (Exception $e) {
                    $transaction->update_result('error');
                    $transaction->update_note($e->getMessage());
                }
            }
        }
    }

    /**
     * Maybe cancel subscription when order gets refunded in full
     *
     * @access public
     * @param int $refunded_order_id
     * @return void
     */
    public function order_refunded_maybe_cancel_subscription($refunded_order_id)
    {
        // Iterate over subscriptions from order
        foreach (self::get_subscriptions_from_order_id($refunded_order_id) as $subscription) {

            // Iterate over order ids related to one subscription
            foreach ($subscription->all_order_ids as $subscription_order_id) {

                $order = RightPress_Helper::wc_get_order($subscription_order_id);

                // If there's still pending renewal order found, cancel the subscription
                if (($order->status == 'pending' || !$subscription->paid_by_order($order->id)) && self::order_is_renewal($order->id)) {

                    // Write transaction
                    $transaction = new Subscriptio_Transaction(null, 'order_refund');
                    $transaction->add_subscription_id($subscription->id);
                    $transaction->add_order_id($order->id);

                    try {
                        // Cancel subscription
                        $subscription->cancel();

                        // Update transaction
                        $transaction->update_result('success');
                        $transaction->update_note(__('Subscription cancelled due to refunded order.', 'subscriptio'), true);
                    }
                    catch (Exception $e) {
                        $transaction->update_result('error');
                        $transaction->update_note($e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Get related Subscriptions from Order ID
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    public static function get_subscriptions_from_order_id($order_id)
    {
        $subscriptions = array();

        // Search for related subscription post ids
        $subscription_post_ids = get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'subscription',
            'meta_query'        => array(
                array(
                    'key'       => 'all_order_ids',
                    'value'     => $order_id,
                    'compare'   => '=',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Iterate over ids and create objects
        foreach ($subscription_post_ids as $id) {
            if ($subscription = Subscriptio_Subscription::get_by_id($id)) {
                $subscriptions[$id] = $subscription;
            }
        }

        return $subscriptions;
    }

    /**
     * Check if order contains subscription products
     *
     * @access public
     * @param int $order_id
     * @return bool
     */
    public static function contains_subscription($order_id)
    {
        $subscriptions = self::get_subscriptions_from_order_id($order_id);
        return !empty($subscriptions);
    }

    /**
     * Check if order is Subscriptio renewal order
     *
     * @access public
     * @param int $order_id
     * @return bool
     */
    public static function order_is_renewal($order_id)
    {
        if (get_post_meta($order_id, '_subscriptio_renewal', true) == 'yes') {
            return true;
        }

        return false;
    }

    /**
     * Prevent WooCommerce from cancelling unpaid renewal orders prematurely
     *
     * @access public
     * @param bool $old_value
     * @param string $order
     * @return bool
     */
    public function cancel_unpaid_order($old_value, $order)
    {
        // Try to find subscriptions
        $subscriptions = self::get_subscriptions_from_order_id($order->id);

        // If no subscriptions found, allow to proceed
        if (!empty($subscriptions) && self::order_is_renewal($order->id)) {
            return false;
        }

        return $old_value;
    }

    /**
     * Filter download links on Order page
     *
     * @access public
     * @param array $files
     * @param array $item
     * @param object $order
     * @return array
     */
    public function filter_download_links($files, $item, $order)
    {
        // Iterate over files
        foreach ($files as $file_key => $file) {

            // Parse product id from file download url
            $parts = parse_url($file['download_url']);
            parse_str($parts['query'], $query);
            $product_id = $query['download_file'];

            // Check if user has access to this product
            if (!Subscriptio_User::has_access_to_product_downloads($product_id)) {
                unset($files[$file_key]);
            }
        }

        return $files;
    }

    /**
     * Filter download links on My Account page
     *
     * @access public
     * @param array $downloads
     * @return array
     */
    public function filter_downloadable_products($downloads)
    {
        // Iterate over downloads and check if user has access to them
        foreach ($downloads as $download_key => $download) {
            if (!Subscriptio_User::has_access_to_product_downloads($download['product_id'])) {
                unset($downloads[$download_key]);
            }
        }

        return $downloads;
    }

    /**
     * Maybe prevent actual file download
     *
     * @access public
     * @param string $email
     * @param string $order_key
     * @param int $product_id
     * @param int $user_id
     * @param int $download_id
     * @param int $order_id
     * @return void
     */
    public function maybe_prevent_file_download($email, $order_key, $product_id, $user_id, $download_id, $order_id)
    {
        // Check if user has access to this product files
        if (!Subscriptio_User::has_access_to_product_downloads($product_id)) {

            // Add notice
            RightPress_Helper::wc_add_notice(__('You no longer have access to this file.', 'subscriptio'), 'error');

            // Redirect to My Account
            wp_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }
    }

}

new Subscriptio_Order_Handler();

}
