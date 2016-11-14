<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Subscriptio PayPal Express Checkout Payment Request class
 *
 * @class Subscriptio_PayPal_EC_Payment_Request
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_EC_Payment_Request')) {

class Subscriptio_PayPal_EC_Payment_Request
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct($order, $checkout_form = null, $address_override = false)
    {
        $this->order = $order;

        if (!is_null($checkout_form)) {
            $this->checkout_form = $checkout_form;
        }

        $this->address_override = $address_override;

        $this->payment = array(
            'AMT'           => Subscriptio_PayPal_EC_Gateway::number_format($order->order_total),
            'CURRENCYCODE'  => get_woocommerce_currency(),
            'PAYMENTACTION' => 'Sale',
            'DESC'          => '',
            'NOTIFYURL'     => WC()->api_request_url('Subscriptio_PayPal_EC_Gateway'),
            'NOTETEXT'      => isset($this->checkout_form['order_comments']) ? $this->checkout_form['order_comments'] : '',
            'CUSTOM'        => maybe_serialize(array('order_id' => $order->id)),
        );

        $this->maybe_add_shipping_data();
        $this->get_shipping();
        $this->get_taxes();
        $this->get_items();
    }

    /**
     * Checks if the call was successful
     *
     * @access public
     * @return void
     */
    public function maybe_add_shipping_data()
    {
        global $woocommerce;

        if ($this->address_override && !empty($this->checkout_form) && $woocommerce->cart->needs_shipping()) {

            // Set the type of field
            $field_type = !empty($this->checkout_form['ship_to_different_address']) ? 'shipping' : 'billing';

            // Get the address
            $address = array(
                'SHIPTONAME'        => $this->checkout_form[$field_type . '_first_name'] . ' ' . $this->checkout_form[$field_type . '_last_name'],
                'SHIPTOSTREET'      => $this->checkout_form[$field_type . '_address_1'],
                'SHIPTOSTREET2'     => isset($this->checkout_form[$field_type . '_address_2']) ? $this->checkout_form[$field_type . '_address_2'] : '',
                'SHIPTOCITY'        => isset($this->checkout_form[$field_type . '_city']) ? wc_clean(stripslashes($this->checkout_form[$field_type . '_city'])) : '',
                'SHIPTOSTATE'       => isset($this->checkout_form[$field_type . '_state']) ? $this->checkout_form[$field_type . '_state'] : '',
                'SHIPTOZIP'         => isset($this->checkout_form[$field_type . '_postcode']) ? $this->checkout_form[$field_type . '_postcode'] : '',
                'SHIPTOCOUNTRYCODE' => isset($this->checkout_form[$field_type . '_country']) ? $this->checkout_form[$field_type . '_country'] : '',
                'SHIPTOPHONENUM'    => isset($this->checkout_form[$field_type . '_phone']) ? $this->checkout_form[$field_type . '_phone'] : '',
            );

            // Add data to payment
            $this->payment = array_merge($this->payment, $address);
        }
    }

    /**
     * Get shipping amount
     *
     * @access public
     * @return double
     */
    public function get_shipping()
    {
        // Get it
        $shipping = get_option('woocommerce_prices_include_tax') == 'yes' ? ($this->order->get_total_shipping() + $this->order->get_shipping_tax()) : $this->order->get_total_shipping();

        // Set it
        $this->payment['SHIPPINGAMT'] = Subscriptio_PayPal_EC_Gateway::number_format($shipping);
    }

    /**
     * Get taxes amount
     *
     * @access public
     * @return double
     */
    public function get_taxes()
    {
        $this->payment['TAXAMT'] = Subscriptio_PayPal_EC_Gateway::number_format($this->order->get_total_tax());
    }

    /**
     * Get order items, fees and discounts
     *
     * @access public
     * @return array
     */
    public function get_items()
    {
        $items = $this->order->get_items();
        $itemamt = 0;
        $this->payment['ITEMAMT'] = 0;
        $all_items = array();

        foreach ($items as $item) {

            $_product = $this->order->get_product_from_item($item);

            $qty = absint($item['qty']);

            $item_meta = new WC_Order_Item_Meta($item,$_product);
            $meta = $item_meta->display(true, true);

            $sku = $_product->get_sku();
            $item['name'] = html_entity_decode($_product->get_title(), ENT_NOQUOTES, 'UTF-8');

            if ($_product->product_type == 'variation') {
                if (empty($sku)) {
                    $sku = $_product->parent->get_sku();
                }
                if (!empty($meta)) {
                    $item['name'] .= ' - ' . str_replace(', \n', ' - ', $meta);
                }
            }

            $Item = array(
                'NAME' => $item['name'],
                'QTY' => $qty,
                'AMT' => Subscriptio_PayPal_EC_Gateway::round($item['line_subtotal'] / $qty),
                'NUMBER' => $sku,
            );

            $all_items[] = $Item;

            $itemamt += Subscriptio_PayPal_EC_Gateway::round($item['line_subtotal'] / $qty) * $qty;
        }

        // Add cart fees
        $cart_fees = $this->get_cart_fees();

        // Get discounts
        $discounts = $this->get_discounts();

        // Add them to total and unset it
        $itemamt += $cart_fees['fees_sum'];
        unset($cart_fees['fees_sum']);

        // Save total discount
        $discounts_sum = $discounts['discounts_sum'];
        unset($discounts['discounts_sum']);

        // Merge all items, fees and discounts
        $all_items = array_merge($all_items, $cart_fees, $discounts);

        // Add otder items
        $this->payment['_items'] = $all_items;

        // Add the totals
        $this->payment['ITEMAMT'] = ($itemamt === 0) ? ($this->order->get_total() - $this->payment['TAXAMT'] - $this->payment['SHIPPINGAMT']) : Subscriptio_PayPal_EC_Gateway::number_format($itemamt + $discounts_sum);

        // Double-check the totals and maybe fix numbers to prevent rounding issues
        if (trim(Subscriptio_PayPal_EC_Gateway::number_format($this->order->get_total())) !== trim(Subscriptio_PayPal_EC_Gateway::number_format($this->payment['ITEMAMT'] + $this->payment['TAXAMT'] + $this->payment['SHIPPINGAMT']))) {

            // Count the difference
            $diff = $this->order->get_total() - ($this->payment['ITEMAMT'] + $this->payment['TAXAMT'] + $this->payment['SHIPPINGAMT']);

            // Add this either to shipping
            if ($this->payment['SHIPPINGAMT'] > 0) {
                $this->payment['SHIPPINGAMT'] = Subscriptio_PayPal_EC_Gateway::number_format($this->payment['SHIPPINGAMT'] + $diff);
            }

            // Or taxes
            elseif ($this->payment['TAXAMT'] > 0) {
                $this->payment['TAXAMT'] = Subscriptio_PayPal_EC_Gateway::number_format($this->payment['TAXAMT'] + $diff);
            }

            // Or to the first item
            else {
                $this->payment['ITEMAMT'] = Subscriptio_PayPal_EC_Gateway::number_format($this->payment['ITEMAMT'] + $diff);
                $this->payment['_items'][0]['AMT'] = Subscriptio_PayPal_EC_Gateway::number_format($this->payment['_items'][0]['AMT'] + $diff / $this->payment['_items'][0]['QTY']);
            }
        }
    }

    /**
     * Get cart fees
     *
     * @access public
     * @return array
     */
    public function get_cart_fees()
    {
        global $woocommerce;

        $cart_fees = array();
        $fees_amt_sum = 0;

        foreach ($woocommerce->cart->get_fees() as $fee) {

            $cart_fee = array(
                'NAME' => $fee->name,
                'QTY' => 1,
                'AMT' => Subscriptio_PayPal_EC_Gateway::number_format($fee->amount, 2, '.', ''),
                'NUMBER' => $fee->id,
            );

            $cart_fees[] = $cart_fee;
            $fees_amt_sum += Subscriptio_PayPal_EC_Gateway::round($fee->amount);
        }

        $cart_fees['fees_sum'] = $fees_amt_sum;

        return $cart_fees;
    }


    /**
     * Get order discounts
     *
     * @access public
     * @return array
     */
    public function get_discounts()
    {
        global $woocommerce;

        $discounts = array();
        $discounts_amt_sum = 0;

        // Newer WC versions
        if (RightPress_Helper::wc_version_gte('2.3')) {

            $wc_total_discount = $this->order->get_total_discount();

            if ($wc_total_discount > 0) {

                $discount = array(
                    'NAME'   => __('Total Discount', 'subscriptio-paypal-ec'),
                    'QTY'    => 1,
                    'AMT'    => - Subscriptio_PayPal_EC_Gateway::number_format($this->order->get_total_discount()),
                    'NUMBER' => join(', ', $this->order->get_used_coupons()),
                );

                $discounts[] = $discount;
                $discounts_amt_sum -= $wc_total_discount;
            }
        }

        // Old WC versions
        else {

            $wc_cart_discount = $this->order->get_cart_discount();
            $wc_order_discount = $this->order->get_order_discount();

            if ($wc_cart_discount > 0) {

                foreach ($woocommerce->cart->get_coupons('cart') as $coupon_code => $coupon_object) {

                    $discount = array(
                        'NAME'   => __('Cart Discount', 'subscriptio-paypal-ec'),
                        'QTY'    => 1,
                        'AMT'    => - Subscriptio_PayPal_EC_Gateway::number_format($woocommerce->cart->coupon_discount_amounts[$coupon_code]),
                        'NUMBER' => $coupon_code,
                    );

                    $discounts[] = $discount;
                }

                $discounts_amt_sum -= $wc_cart_discount;
            }

            if ($wc_order_discount > 0) {

                foreach ($woocommerce->cart->get_coupons('order') as $coupon_code => $coupon_object) {

                    $discount = array(
                        'NAME'   => __('Order Discount', 'subscriptio-paypal-ec'),
                        'QTY'    => 1,
                        'AMT'    => - Subscriptio_PayPal_EC_Gateway::number_format($woocommerce->cart->coupon_discount_amounts[$coupon_code]),
                        'NUMBER' => $coupon_code,
                    );

                    $discounts[] = $discount;
                }

                $discounts_amt_sum -= $wc_order_discount;
            }
        }

        $discounts['discounts_sum'] = $discounts_amt_sum;

        return $discounts;
    }


    /**
     * Return the main request array
     *
     * @access public
     * @return array
     */
    public function get_payment_array()
    {
        return $this->payment;
    }



}
}