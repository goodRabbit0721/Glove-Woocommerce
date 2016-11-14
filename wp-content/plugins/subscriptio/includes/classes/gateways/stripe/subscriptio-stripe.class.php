<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Subscriptio_Stripe')) {

/**
 * Stripe Payment Gateway Extension For Subscriptio
 *
 * @class Subscriptio_Stripe
 * @package Subscriptio
 * @author RightPress
 */
class Subscriptio_Stripe
{
    /**
     * Get Credit Card expiration months for display
     *
     * @access public
     * @return array
     */
    public static function get_months()
    {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $date = new DateTime();
            $date->setDate($date->format('Y'), $i, $date->format('d'));
            $format = apply_filters('subscriptio_stripe_month_format', 'F');
            $months[$i] = apply_filters('subscriptio_stripe_month', $date->format($format));
        }

        return $months;
    }

    /**
     * Get Credit Card expiration years for display
     *
     * @access public
     * @return array
     */
    public static function get_years()
    {
        $years = array();

        $date = new DateTime();
        $plus_years = apply_filters('subscriptio_stripe_plus_years', 15);
        $format = apply_filters('subscriptio_stripe_year_format', 'Y');

        for ($i = 0; $i <= $plus_years; $i++) {
            $year = $date->format($format) + $i;
            $years[$year] = apply_filters('subscriptio_stripe_year', $year);
        }

        return $years;
    }

    /**
     * Format credit card expiration date
     *
     * @access public
     * @param int $month
     * @param int $year
     * @return string
     */
    public static function format_expiration_date($month, $year)
    {
        $month = apply_filters('subscriptio_stripe_card_list_month', $month);
        $year = apply_filters('subscriptio_stripe_card_list_year', $year);
        return apply_filters('subscriptio_stripe_card_list_expiration', ($month . '/' . $year), $month, $year);
    }

}
}
