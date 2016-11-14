<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Subscriptio PayPal Express Checkout NVP class
 *
 * @class Subscriptio_PayPal_EC_NVP
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_PayPal_EC_NVP')) {

class Subscriptio_PayPal_EC_NVP
{

    /**
     * Constructor class
     *
     * @access public
     * @param mixed $id
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Convert NVP string to array
     *
     * @access public
     * @param mixed $nvp_response
     * @return array
     */
    public static function convert_nvp_to_array($nvp_response)
    {
        // If it's already an array (not NVP)
        if (is_array($nvp_response)) {
            return $nvp_response;
        }

        $nvp_array = array();

        // Break down the line to pairs and iterate through them
        foreach (explode('&', $nvp_response) as $pair) {

            // Break the pair to key and value
            $pair_arr = explode('=', $pair);
            $name = $pair_arr[0];
            $value = $pair_arr[1];

            // Decode and save the value
            $nvp_array[$name] = urldecode($value);
        }

        return $nvp_array;
    }

    /**
     * Convert request array to NVP string
     *
     * @access public
     * @param array $request_array
     * @return string
     */
    public static function create_nvp_from_array($request_array)
    {
        if (!is_array($request_array)) {
            return false;
        }

        $nvp_string = '';

        foreach ($request_array as $field_key => $value) {
            $nvp_string .= !empty($value) ? '&' . $field_key . '=' . urlencode($value) : '';
        }

        return $nvp_string;
    }

    /**
     * Convert payment request array to NVP string
     *
     * @access public
     * @param array $request_array
     * @return string
     */
    public static function create_nvp_from_payment_request_array($request_array)
    {
        if (!is_array($request_array)) {
            return false;
        }

        $nvp_string = '';
        $num = 0; // only one payment per order

        foreach ($request_array as $field_key => $value) {

            if ($field_key == '_items') {
                $item_num = 0;
                foreach ($value as $item) {

                    foreach ($item as $item_field => $item_value) {
                        $nvp_string .= !empty($value) ? '&L_PAYMENTREQUEST_' . $num . '_' . $item_field . $item_num .  '=' . urlencode($item_value) : '';
                    }
                    $item_num++;
                }
            }
            else {
                $nvp_string .= !empty($value) ? '&PAYMENTREQUEST_' . $num . '_' . $field_key . '=' . urlencode($value) : '';
            }
        }

        return $nvp_string;
    }


}
}
