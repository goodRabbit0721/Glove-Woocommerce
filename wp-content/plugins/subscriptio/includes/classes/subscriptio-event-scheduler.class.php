<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Scheduler
 *
 * Runs every 5 minutes
 * Locked for 4 minutes to prevent accidental race conditions
 * Actual execution per batch of events is limited to 3 minutes
 *
 * @class Subscriptio_Event_Scheduler
 * @package Subscriptio
 * @author RightPress
 */
if (!class_exists('Subscriptio_Event_Scheduler')) {

class Subscriptio_Event_Scheduler
{
    // Event keys
    public static $event_keys = array(
        'payment', 'order', 'suspension', 'cancellation', 'expiration',
        'reminder', 'resume',
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
        // Add custom WordPress cron schedule
        add_filter('cron_schedules', array($this, 'add_custom_schedule'), 99);

        // Check if next processing time is registered with WordPress cron
        add_action('init', array('Subscriptio_Event_Scheduler', 'check_cron'), 99);

        // Register cron handler
        add_action('subscriptio_process_scheduled_events', array($this, 'process_scheduled_events'));
    }

    /**
     * Add custom WordPress cron schedule
     *
     * @access public
     * @return void
     */
    public function add_custom_schedule($schedules)
    {
        $schedules['subscriptio_five_minutes'] = array(
            'interval'  => 300,
            'display'   => __('Once every five minutes', 'subscriptio'),
        );

        return $schedules;
    }

    /**
     * Get scheduler database table name
     *
     * @access public
     * @return string
     */
    public static function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . 'subscriptio_scheduled_events';
    }

    /**
     * Check scheduler database table
     *
     * @access public
     * @return bool
     */
    public static function check_database()
    {
        global $wpdb;

        // Do not check more than once per request
        if (defined('SUBSCRIPTIO_DATABASE_CHECKED')) {
            return true;
        }

        define('SUBSCRIPTIO_DATABASE_CHECKED', true);

        // Scheduler table name
        $table_name = self::table_name();

        // Check if table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            return true;
        }

        // Get charset
        $charset_collate = $wpdb->get_charset_collate();

        // Format query
        $sql = "CREATE TABLE $table_name (
            event_id bigint(20) NOT NULL AUTO_INCREMENT,
            event_key varchar(100) NOT NULL,
            subscription_id bigint(20) NOT NULL,
            event_timestamp int(11) NOT NULL,
            event_meta longtext NULL,
            attempt_count int(11) NOT NULL DEFAULT 0,
            last_attempt_timestamp int(11) NULL,
            processing int(1) NOT NULL DEFAULT 0,
            PRIMARY KEY  (event_id)
        ) $charset_collate;";

        // Run query
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Table just created
        return true;
    }

    /**
     * Check cron entry and set up one if it does not exist or is invalid
     *
     * @access public
     * @return void
     */
    public static function check_cron()
    {
        // Get next scheduled event timestamp
        $scheduled = wp_next_scheduled('subscriptio_process_scheduled_events');

        // Get current timestamp
        $timestamp = time();

        // Cron is set and is valid
        if ($scheduled && $scheduled <= ($timestamp + 600)) {
            return;
        }

        // Remove all cron entries by key
        wp_clear_scheduled_hook('subscriptio_process_scheduled_events');

        // Add new cron entry
        wp_schedule_event(time(), 'subscriptio_five_minutes', 'subscriptio_process_scheduled_events');
    }

    /**
     * Cron lock
     *
     * @access public
     * @return bool
     */
    public static function lock()
    {
        global $wpdb;

        // Attempt to acquire lock
        $locked = $wpdb->query("
            UPDATE $wpdb->options
            SET option_name = 'subscriptio_cron_locked'
            WHERE option_name = 'subscriptio_cron_unlocked'
        ");

        // Failed acquiring lock
        if (!$locked && !self::release_lock()) {
            return false;
        }

        // Set last lock time
        update_option('subscriptio_cron_lock_time', time(), false);

        // Lock was acquired successfully
        return true;
    }

    /**
     * Cron unlock
     *
     * @access public
     * @return bool
     */
    public static function unlock()
    {
        global $wpdb;

        // Attempt to release lock
        $unlocked = $wpdb->query("
            UPDATE $wpdb->options
            SET option_name = 'subscriptio_cron_unlocked'
            WHERE option_name = 'subscriptio_cron_locked'
        ");

        // Failed releasing lock
        if (!$unlocked) {
            return false;
        }

        // Lock was released successfully
        return true;
    }

    /**
     * Checks if lock is stuck and releases it if needed
     * Also checks if lock option exists and creates it if not
     *
     * @access public
     * @return bool
     */
    public static function release_lock()
    {
        global $wpdb;

        // Get lock option entry
        $result = $wpdb->query("
            SELECT option_id
            FROM $wpdb->options
            WHERE option_name = 'subscriptio_cron_locked'
            OR option_name = 'subscriptio_cron_unlocked'
        ");

        // No lock entry - add it and skip this scheduler run
        if (!$result) {
            update_option('subscriptio_cron_unlocked', 1, false);
            return false;
        }

        // Attempt to reset lock time if four minutes passed
        $reset = $wpdb->query($wpdb->prepare("
            UPDATE $wpdb->options
            SET option_value = %d
            WHERE option_name = 'subscriptio_cron_lock_time'
            AND option_value <= %d
        ", time(), (time() - 240)));

        // Return reset result
        return (bool) $reset;
    }

    /**
     * Schedule next payment event for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_payment($subscription_id, $timestamp)
    {
        return self::schedule('payment', $subscription_id, $timestamp);
    }

    /**
     * Schedule renewal order for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_order($subscription_id, $timestamp)
    {
        return self::schedule('order', $subscription_id, $timestamp);
    }

    /**
     * Schedule suspension for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_suspension($subscription_id, $timestamp)
    {
        return self::schedule('suspension', $subscription_id, $timestamp);
    }

    /**
     * Schedule cancellation for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_cancellation($subscription_id, $timestamp)
    {
        return self::schedule('cancellation', $subscription_id, $timestamp);
    }

    /**
     * Schedule expiration for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_expiration($subscription_id, $timestamp)
    {
        return self::schedule('expiration', $subscription_id, $timestamp);
    }

    /**
     * Schedule reminder for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_reminder($subscription_id, $timestamp)
    {
        return self::schedule('reminder', $subscription_id, $timestamp);
    }

    /**
     * Schedule resume of paused subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function schedule_resume($subscription_id, $timestamp)
    {
        return self::schedule('resume', $subscription_id, $timestamp);
    }

    /**
     * Schedule single event
     *
     * @access public
     * @param string $event_key
     * @param int $subscription_id
     * @param int $timestamp
     * @return bool
     */
    public static function schedule($event_key, $subscription_id, $timestamp)
    {
        global $wpdb;

        // Backwards compatibility
        $event_key = str_replace('subscriptio_scheduled_', '', $event_key);

        // Make sure database table exists
        self::check_database();

        // Insert scheduled event to database
        $result = $wpdb->insert(
            self::table_name(),
            array(
                'event_key'         => $event_key,
                'subscription_id'   => $subscription_id,
                'event_timestamp'   => $timestamp,
            ),
            array(
                '%s', '%d', '%d',
            )
        );

        return (bool) $result;
    }

    /**
     * Unschedule next payment event for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_payment($subscription_id, $timestamp = null)
    {
        return self::unschedule('payment', $subscription_id, $timestamp);
    }

    /**
     * Unschedule renewal order for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_order($subscription_id, $timestamp = null)
    {
        return self::unschedule('order', $subscription_id, $timestamp);
    }

    /**
     * Unschedule suspension for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_suspension($subscription_id, $timestamp = null)
    {
        return self::unschedule('suspension', $subscription_id, $timestamp);
    }

    /**
     * Unschedule cancellation for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_cancellation($subscription_id, $timestamp = null)
    {
        return self::unschedule('cancellation', $subscription_id, $timestamp);
    }

    /**
     * Unschedule expiration for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_expiration($subscription_id, $timestamp = null)
    {
        return self::unschedule('expiration', $subscription_id, $timestamp);
    }

    /**
     * Unschedule reminder for a specific subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_reminder($subscription_id, $timestamp = null)
    {
        return self::unschedule('reminder', $subscription_id, $timestamp);
    }

    /**
     * Unschedule resume of paused subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_resume($subscription_id, $timestamp = null)
    {
        return self::unschedule('resume', $subscription_id, $timestamp);
    }

    /**
     * Unschedule any previously scheduled events
     *
     * @access public
     * @param string $event_key
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule($event_key, $subscription_id, $timestamp = null)
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Build where clause
        $where = array(
            'event_key'         => $event_key,
            'subscription_id'   => $subscription_id,
        );
        $where_format = array('%s', '%d');

        // Add timestamp if passed in
        if ($timestamp) {
            $where['event_timestamp'] = $timestamp;
            array_push($where_format, '%d');
        }

        // Delete matching rows
        $wpdb->delete(self::table_name(), $where, $where_format);

        // Backwards compatibility
        Subscriptio_Scheduler::unschedule('subscriptio_scheduled_' . $event_key, $subscription_id, $timestamp);
    }

    /**
     * Unschedule multiple events
     *
     * @access public
     * @param array $event_keys
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_multiple($event_keys, $subscription_id, $timestamp = null)
    {
        // Iterate over event keys and unschedule
        foreach ((array) $event_keys as $event_key) {
            self::unschedule($event_key, $subscription_id, $timestamp);
        }
    }

    /**
     * Unschedule all events for a subscription
     *
     * @access public
     * @param int $subscription_id
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_all($subscription_id, $timestamp = null)
    {
        self::unschedule_multiple(self::$event_keys, $subscription_id, $timestamp);
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
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Store events
        $events = array();

        // Scheduler table name
        $table_name = self::table_name();

        // Run query
        $results = $wpdb->get_results("SELECT event_key, event_timestamp FROM $table_name WHERE subscription_id = " . absint($subscription_id));

        // Iterate over results
        foreach ($results as $result) {
            $events[] = array(
                // Note: do not rename 'hook' to 'event' or something else on the line below (backwards compatibility)
                'hook'      => $result->event_key,
                'timestamp' => $result->event_timestamp,
            );
        }

        // Backwards compatibility
        $legacy_events = Subscriptio_Scheduler::get_scheduled_events_timestamps($subscription_id);

        // Make sure there are no duplicate events except reminders
        foreach ($legacy_events as $legacy_event_index => $legacy_event) {
            foreach ($events as $event) {
                if ($legacy_event['hook'] === 'subscriptio_scheduled_' . $event['hook'] && $event['hook'] !== 'reminder') {
                    unset($legacy_events[$legacy_event_index]);
                }
            }
        }

        // Return all event timestamps
        return array_merge($legacy_events, $events);
    }

    /**
     * Get scheduled event timestamp
     *
     * @access public
     * @param string $event_key
     * @param int $subscription_id
     * @return int
     */
    public static function get_scheduled_event_timestamp($event_key, $subscription_id)
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Scheduler table name
        $table_name = self::table_name();

        // Run query
        $timestamp = $wpdb->get_var("SELECT event_timestamp FROM $table_name WHERE event_key = '$event_key' AND subscription_id = " . absint($subscription_id));

        // Check if event is scheduled
        if ($timestamp !== null) {
            return $timestamp;
        }

        // Backwards compatibility
        return Subscriptio_Scheduler::get_scheduled_event_timestamp('subscriptio_scheduled_' . $event_key, $subscription_id);
    }

    /**
     * Get scheduled event datetime
     *
     * @access public
     * @param string $event_key
     * @param int $subscription_id
     * @return string|boolean
     */
    public static function get_scheduled_event_datetime($event_key, $subscription_id)
    {
        // Get timestamp of the scheduled event
        $timestamp = self::get_scheduled_event_timestamp($event_key, $subscription_id);

        if (!$timestamp) {
            return false;
        }

        return Subscriptio::get_adjusted_datetime($timestamp, null, $event_key);
    }

    /**
     * Process scheduled events
     * Invoked by WP cron every 5 minutes
     *
     * @access public
     * @return void
     */
    public function process_scheduled_events()
    {
        global $wpdb;

        // Scheduler table name
        $table_name = self::table_name();

        // Attempt to get cron lock
        if (!self::lock()) {
            return;
        }

        // Schedule next event
        self::check_cron();

        // Check database table
        self::check_database();

        // Reset PHP execution time limit and set it to 5 minutes from now
        @set_time_limit(300);

        // Get PHP execution time limit
        $php_time_limit = (int) @ini_get('max_execution_time');

        // If we can't get PHP execution time limit value, assume it's 15 seconds
        $php_time_limit = $php_time_limit ? $php_time_limit : 15;

        // Subtract 5 seconds from PHP time limit as it may include time that has already passed until now
        $php_time_limit = $php_time_limit - 5;

        // Final time limit should not be longer than 3 minutes to avoid race conditions (we have a lock for 4 minutes only)
        $time_limit = $php_time_limit > 180 ? 180 : $php_time_limit;
        $start_time = time();
        $end_time = $start_time + $time_limit;

        // Prepare query
        $query = "SELECT * FROM $table_name WHERE event_timestamp <= $start_time AND processing = 0 ORDER BY event_timestamp";

        // Get next event
        $next_event = $wpdb->get_row($query, OBJECT);

        // Iterate over events from database
        while ($next_event !== null && time() < $end_time) {

            // Set a flag that this event is being processed to prevent executing the same event multiple times
            $flag_set = $wpdb->query("UPDATE $table_name SET processing = 1, attempt_count = " . ($next_event->attempt_count + 1) . ", last_attempt_timestamp = " . time() . " WHERE event_id = $next_event->event_id");

            // Check if event can be executed
            if ($flag_set && in_array($next_event->event_key, self::$event_keys, true)) {

                // Get event method name
                $method = 'scheduled_' . $next_event->event_key;

                // Execute event
                self::$method($next_event->subscription_id);

                // Remove this event from the scheduler database
                $wpdb->query("DELETE FROM $table_name WHERE event_id = $next_event->event_id");
            }

            // Get next event
            $next_event = $wpdb->get_row($query, OBJECT);
        }

        // Unlock cron lock
        self::unlock();
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'payment_due');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!isset($subscription->status)) {
            return;
        }

        // Load related order
        $order = RightPress_Helper::wc_get_order($subscription->last_order_id);

        if (!$order) {
            $transaction->update_result('error');
            $transaction->update_note(__('Renewal order not found.', 'subscriptio'), true);
            return;
        }

        // Make sure payment has not been received yet
        if (Subscriptio_Order_Handler::order_is_paid($order)) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment seems to be already received.', 'subscriptio'), true);
            return;
        }

        // Attempt to process automatic payment if this is main website
        if (apply_filters('subscriptio_process_automatic_payment', Subscriptio::is_main_site(), $order, $subscription)) {
            if (apply_filters('subscriptio_automatic_payment', false, $order, $subscription)) {
                return;
            }
        }

        // Now either set to overdue or suspend or cancel, depending on settings
        try {
            $overdue_end_time = $subscription->calculate_overdue_time();
            $suspension_end_time = $subscription->calculate_suspension_time();  // This will be "fake" time for now in case $overdue_end_time is set

            // Overdue
            if ($overdue_end_time > 0) {

                // Set subscription to overdue
                $subscription->overdue();

                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Payment not received. Subscription marked as overdue.', 'subscriptio'), true);

                // Schedule suspension and/or cancellation
                if ($suspension_end_time > 0) {
                    Subscriptio_Event_Scheduler::schedule_suspension($subscription->id, $overdue_end_time);
                    Subscriptio_Event_Scheduler::schedule_cancellation($subscription->id, $subscription->calculate_suspension_time($overdue_end_time));
                    $transaction->update_note(__('Suspension and cancellation scheduled.', 'subscriptio'), true);
                }
                else {
                    Subscriptio_Event_Scheduler::schedule_cancellation($subscription->id, $overdue_end_time);
                    $transaction->update_note(__('Cancellation scheduled.', 'subscriptio'), true);
                }

                // Send notifications
                Subscriptio_Mailer::send('payment_overdue', $subscription);
            }

            // Suspend
            else if ($suspension_end_time > 0) {

                // Not yet suspended? (can be suspended manually)
                if ($subscription->status != 'suspended') {

                    // Suspend suscription
                    $subscription->suspend();

                    // Update transaction
                    $transaction->update_result('success');
                    $transaction->update_note(__('Payment not received. Subscription suspended.', 'subscriptio'), true);
                }
                else {
                    $transaction->update_result('error');
                    $transaction->update_note(__('Payment not received but subscription is already suspended.', 'subscriptio'), true);
                }

                // Schedule cancellation
                Subscriptio_Event_Scheduler::schedule_cancellation($subscription->id, $suspension_end_time);
                $transaction->update_note(__('Cancellation scheduled.', 'subscriptio'), true);
            }

            // Cancel instantly (no overdue or suspension periods configured)
            else {

                // Cancel subscription
                $subscription->cancel();

                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Payment not received. Subscription cancelled.', 'subscriptio'), true);
            }
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'renewal_order');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Create renewal order
        try {
            $order_id = Subscriptio_Order_Handler::create_renewal_order($subscription);
            $transaction->add_order_id($order_id);
            $transaction->update_result('success');
            $transaction->update_note(__('New order created, status set to pending.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'suspension');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already suspended
        if ($subscription->status == 'suspended') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already suspended.', 'subscriptio'), true);
            return;
        }

        // Make sure that payment due is in the past (double check that the payment has not been received until now)
        if (time() < $subscription->payment_due) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment due date is in the future, no reason to suspend.', 'subscriptio'), true);
            return;
        }

        // Suspend subscription
        try {
            $subscription->suspend();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription suspended.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'automatic_cancellation');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already cancelled
        if ($subscription->status == 'cancelled') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
            return;
        }

        // Make sure that payment due is in the past (double check that the payment has not been received until now)
        if (time() < $subscription->payment_due) {
            $transaction->update_result('error');
            $transaction->update_note(__('Payment due date is in the future, no reason to cancel.', 'subscriptio'), true);
            return;
        }

        // Cancel subscription
        try {
            $subscription->cancel();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription cancelled.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'expiration');

        // Make sure we are not caught in an infinite loop
        define('SUBSCRIPTIO_DOING_EXPIRATION', 'yes');

        // Load subscription if it's still valid
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        // Got a valid subscription object?
        if (!$subscription) {
            return;
        }

        // Make sure that subscription is not already cancelled
        if ($subscription->status == 'cancelled') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already cancelled.', 'subscriptio'), true);
            return;
        }

        // Make sure that subscription is not already expired
        if ($subscription->status == 'expired') {
            $transaction->update_result('error');
            $transaction->update_note(__('Subscription is already expired.', 'subscriptio'), true);
            return;
        }

        // Expire subscription
        try {
            $subscription->expire();
            $transaction->update_result('success');
            $transaction->update_note(__('Subscription expired.', 'subscriptio'), true);
        } catch (Exception $e) {
            $transaction->update_result('error');
            $transaction->update_note($e->getMessage(), true);
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'payment_reminder');

        // Get subscription
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        if ($subscription) {

            // Send reminder
            try {
                Subscriptio_Mailer::send('payment_reminder', $subscription);
                $transaction->update_result('success');
                $transaction->update_note(__('Payment reminder sent.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }
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
        // Start transaction
        $transaction = new Subscriptio_Transaction(null, 'subscription_resume');

        // Get subscription
        $subscription = Subscriptio_Subscription::get_valid_subscription($subscription_id, $transaction);

        if ($subscription) {

            try {
                // Resume subscription
                $subscription->resume();
                // Update transaction
                $transaction->update_result('success');
                $transaction->update_note(__('Subscription was automatically resumed.', 'subscriptio'), true);
            } catch (Exception $e) {
                $transaction->update_result('error');
                $transaction->update_note($e->getMessage(), true);
            }
        }
    }


}
}

Subscriptio_Event_Scheduler::get_instance();
