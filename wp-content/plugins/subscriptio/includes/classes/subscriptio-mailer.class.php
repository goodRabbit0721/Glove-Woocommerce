<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Email handling class
 *
 * @class Subscriptio_Mailer
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Mailer')) {

class Subscriptio_Mailer
{
    public static $aliases = array(
        'customer_new_order'        => 'Subscriptio_Email_Customer_Subscription_New_Order',
        'customer_processing_order' => 'Subscriptio_Email_Customer_Subscription_Processing_Order',
        'customer_completed_order'  => 'Subscriptio_Email_Customer_Subscription_Completed_Order',
        'customer_payment_reminder' => 'Subscriptio_Email_Customer_Subscription_Payment_Reminder',
        'customer_payment_overdue'  => 'Subscriptio_Email_Customer_Subscription_Payment_Overdue',
        'customer_paused'           => 'Subscriptio_Email_Customer_Subscription_Paused',
        'customer_resumed'          => 'Subscriptio_Email_Customer_Subscription_Resumed',
        'customer_suspended'        => 'Subscriptio_Email_Customer_Subscription_Suspended',
        'customer_cancelled'        => 'Subscriptio_Email_Customer_Subscription_Cancelled',
        'customer_expired'          => 'Subscriptio_Email_Customer_Subscription_Expired',
    );

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Add email classes
        add_action('woocommerce_email_classes', array($this, 'add_email_classes'));

        // Override default WooCommerce emails for renewal orders
        add_action('woocommerce_init', array($this, 'remove_woocommerce_emails'));
        add_action('woocommerce_init', array($this, 'add_woocommerce_emails'));
    }

    /**
     * Add more email classes besides standard WooCommerce email classess
     *
     * @access public
     * @param array $emails
     * @return array
     */
    public function add_email_classes($emails)
    {
        // Load parent email class first
        require SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/emails/subscriptio-email.class.php';

        // Load child email classes (parent will be skipped when loading)
        foreach (glob(SUBSCRIPTIO_PLUGIN_PATH . 'includes/classes/emails/*.class.php') as $filename)
        {
            require $filename;
        }

        // Customer emails
        $emails['Subscriptio_Email_Customer_Subscription_New_Order']        = new Subscriptio_Email_Customer_Subscription_New_Order();
        $emails['Subscriptio_Email_Customer_Subscription_Processing_Order'] = new Subscriptio_Email_Customer_Subscription_Processing_Order();
        $emails['Subscriptio_Email_Customer_Subscription_Completed_Order']  = new Subscriptio_Email_Customer_Subscription_Completed_Order();
        $emails['Subscriptio_Email_Customer_Subscription_Payment_Reminder'] = new Subscriptio_Email_Customer_Subscription_Payment_Reminder();
        $emails['Subscriptio_Email_Customer_Subscription_Payment_Overdue']  = new Subscriptio_Email_Customer_Subscription_Payment_Overdue();
        $emails['Subscriptio_Email_Customer_Subscription_Paused']           = new Subscriptio_Email_Customer_Subscription_Paused();
        $emails['Subscriptio_Email_Customer_Subscription_Resumed']          = new Subscriptio_Email_Customer_Subscription_Resumed();
        $emails['Subscriptio_Email_Customer_Subscription_Suspended']        = new Subscriptio_Email_Customer_Subscription_Suspended();
        $emails['Subscriptio_Email_Customer_Subscription_Cancelled']        = new Subscriptio_Email_Customer_Subscription_Cancelled();
        $emails['Subscriptio_Email_Customer_Subscription_Expired']          = new Subscriptio_Email_Customer_Subscription_Expired();

        return $emails;
    }

    /**
     * Send selected email
     *
     * @access public
     * @param string $alias
     * @param object $object
     * @param array $args
     * @param array $customer_email
     * @return void
     */
    public function send_email($alias, $object, $args = array(), $customer_email = false)
    {
        // Cancel sending emails if this is a duplicate website
        if (!apply_filters('subscriptio_send_email', Subscriptio::is_main_site(), $alias, $object, $args)) {
            return;
        }

        // Cancel sending email if we don't have such email
        if (!isset(self::$aliases[$alias])) {
            return;
        }

        global $woocommerce;

        $woocommerce_mailer = $woocommerce->mailer();
        $emails = $woocommerce_mailer->get_emails();
        $emails[self::$aliases[$alias]]->trigger($object, $args);

        // Check if we need to send a copy of customer email to admin
        if ($customer_email && $emails[self::$aliases[$alias]]->send_to_admin) {
            $emails[self::$aliases[$alias]]->trigger($object, $args, true);
        }
    }

    /**
     * Select proper email and send it
     *
     * @access public
     * @param string $alias
     * @param object $object
     * @param array $args
     * @return void
     */
    public static function send($alias, $object, $args = array())
    {
        $mailer = new Subscriptio_Mailer();
        $mailer->send_email('customer_' . $alias, $object, $args, true);
        $mailer->send_email('admin_' . $alias, $object, $args);
    }

    /**
     * Remove default WooCommerce emails
     *
     * @access public
     * @return void
     */
    public function remove_woocommerce_emails()
    {
        // List all hooks to remove
        $hooks = array(

            // New Order notification
            'woocommerce_order_status_pending_to_processing',
            'woocommerce_order_status_pending_to_completed',
            'woocommerce_order_status_pending_to_on-hold',
            'woocommerce_order_status_failed_to_processing',
            'woocommerce_order_status_failed_to_completed',
            'woocommerce_order_status_failed_to_on-hold',

            // Processing Order notification
            'woocommerce_order_status_pending_to_processing',
            'woocommerce_order_status_pending_to_on-hold',
            'woocommerce_order_status_on-hold_to_processing',

            // Completed Order notification
            'woocommerce_order_status_completed',
        );

        // Hook our function
        foreach ($hooks as $hook) {
            add_action($hook, array($this, 'remove_woocommerce_email'), 9);
        }
    }

    /**
     * Override some of default WooCommerce emails with our own
     *
     * @access public
     * @return void
     */
    public function add_woocommerce_emails()
    {
        // List all hooks to intercept
        $hooks = array(

            // Processing Order notification
            'woocommerce_order_status_pending_to_processing',
            'woocommerce_order_status_pending_to_on-hold',
            'woocommerce_order_status_on-hold_to_processing',

            // Completed Order notification
            'woocommerce_order_status_completed',
        );

        // Hook our function
        foreach ($hooks as $hook) {
            add_action($hook, array($this, 'send_custom_email'), 10);
        }
    }

    /**
     * Get WooCommerce email type from hook
     *
     * @access public
     * @return string|bool
     */
    public function get_woocommerce_email_type($hook)
    {
        switch ($hook) {
            case 'woocommerce_order_status_pending_to_processing':
            case 'woocommerce_order_status_failed_to_processing':
            case 'woocommerce_order_status_on-hold_to_processing':
                return 'processing_order';
                break;

            case 'woocommerce_order_status_pending_to_completed':
            case 'woocommerce_order_status_pending_to_on-hold':
            case 'woocommerce_order_status_failed_to_completed':
            case 'woocommerce_order_status_failed_to_on-hold':
                return 'new_order';
                break;

            case 'woocommerce_order_status_completed':
                return 'completed_order';
                break;

            default:
                break;
        }
    }

    /**
     * Remove default WooCommerce email
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function remove_woocommerce_email($order_id)
    {
        $order = RightPress_Helper::wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Subscription order?
        if (Subscriptio_Order_Handler::order_is_renewal($order_id)) {
            global $woocommerce;
            remove_action(current_filter(), array($woocommerce, 'send_transactional_email'));
        }
    }

    /**
     * Send custom Subscriptio email
     *
     * @access public
     * @param int $order_id
     * @return void
     */
    public function send_custom_email($order_id)
    {
        $order = RightPress_Helper::wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Subscription order?
        if (Subscriptio_Order_Handler::order_is_renewal($order_id)) {
            Subscriptio_Mailer::send($this->get_woocommerce_email_type(current_filter()), $order);
        }
    }

}

new Subscriptio_Mailer();

}
