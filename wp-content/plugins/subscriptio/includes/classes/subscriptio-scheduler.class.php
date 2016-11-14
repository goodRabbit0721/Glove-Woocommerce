<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle subscription-related events
 *
 * NOTE: This is a legacy class only left here so that previously scheduled
 * events can be executed, see new class Subscriptio_Event_Scheduler
 *
 * @class Subscriptio_Scheduler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Scheduler')) {

class Subscriptio_Scheduler
{
    public static $scheduler_hooks = array(
        'subscriptio_scheduled_payment'     => 'Subscriptio_Scheduler::scheduled_payment',
        'subscriptio_scheduled_order'       => 'Subscriptio_Scheduler::scheduled_order',
        'subscriptio_scheduled_suspension'  => 'Subscriptio_Scheduler::scheduled_suspension',
        'subscriptio_scheduled_cancellation'  => 'Subscriptio_Scheduler::scheduled_cancellation',
        'subscriptio_scheduled_expiration'  => 'Subscriptio_Scheduler::scheduled_expiration',
        'subscriptio_scheduled_reminder'    => 'Subscriptio_Scheduler::scheduled_reminder',
        'subscriptio_scheduled_resume'    => 'Subscriptio_Scheduler::scheduled_resume',
    );

    // Singleton instance
    private static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Set up all hooks
        foreach (self::$scheduler_hooks as $hook => $callable) {
            add_action($hook, $callable, 10, 20);
        }
    }

    /**
     * Scheduled next payment event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_payment($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_payment($subscription_id);
    }

    /**
     * Scheduled renewal order event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_order($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_order($subscription_id);
    }

    /**
     * Scheduled suspension event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_suspension($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_suspension($subscription_id);
    }

    /**
     * Scheduled cancellation event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_cancellation($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_cancellation($subscription_id);
    }

    /**
     * Scheduled subscription expiration event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_expiration($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_expiration($subscription_id);
    }

    /**
     * Scheduled reminder event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_reminder($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_reminder($subscription_id);
    }

    /**
     * Scheduled resume event handler
     *
     * @access public
     * @param int $subscription_id
     * @return void
     */
    public static function scheduled_resume($subscription_id)
    {
        Subscriptio_Event_Scheduler::scheduled_resume($subscription_id);
    }

    /**
     * Unschedule possibly previously scheduled task(s)
     *
     * @access public
     * @param string $hook
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule($hook, $subscription_id = null, $timestamp = null)
    {
        // Specific single event?
        if ($timestamp) {

            // Match arguments?
            if ($subscription_id) {
                wp_unschedule_event($timestamp, $hook, array((int)$subscription_id));
            }
            else {
                wp_unschedule_event($timestamp, $hook);
            }
        }

        // All matching events?
        else {

            // Match arguments?
            if ($subscription_id) {
                wp_clear_scheduled_hook($hook, array((int)$subscription_id));
            }
            else {
                wp_clear_scheduled_hook($hook);
            }
        }
    }

    /**
     * Get all scheduled events' timestamps
     *
     * @access public
     * @param int $subscription_id
     * @return int
     */
    public static function get_scheduled_events_timestamps($subscription_id)
    {
        $events = array();

        foreach (self::$scheduler_hooks as $hook => $callable) {
            if ($timestamp = self::get_scheduled_event_timestamp($hook, $subscription_id)) {
                $events[] = array(
                    'hook'      => $hook,
                    'timestamp' => $timestamp,
                );
            }
        }

        return $events;
    }

    /**
     * Get scheduled event timestamp
     *
     * @access public
     * @param string $hook
     * @param int $subscription_id
     * @return int
     */
    public static function get_scheduled_event_timestamp($hook, $subscription_id)
    {
        return wp_next_scheduled($hook, array((int)$subscription_id));
    }

}
}

Subscriptio_Scheduler::get_instance();
