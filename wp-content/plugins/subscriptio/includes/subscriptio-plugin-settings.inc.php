<?php

/*
 * Returns settings for this plugin
 *
 * @return array
 */
if (!function_exists('subscriptio_plugin_settings')) {
function subscriptio_plugin_settings()
{
    return array(
        'general' => array(
            'title' => __('General', 'subscriptio'),
            'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
            'children' => array(
                'general' => array(
                    'title' => __('General Settings', 'subscriptio'),
                    'children' => array(
                        'shipping_renewal_charge' => array(
                            'title' => __('Charge shipping for renewal orders', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'multiproduct_subscription' => array(
                            'title' => __('Enable multi-product subscriptions', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If checked, will create one subscription with multiple products, if not - will create separate subscription for each product.</p>', 'subscriptio'),
                        ),
                        'limit_subscriptions' => array(
                            'title' => __('Limit Subscriptions', 'subscriptio'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Do not limit subscriptions', 'subscriptio'),
                                '1' => __('One active subscription of specific product per customer', 'subscriptio'),
                                '2' => __('One active subscription per customer', 'subscriptio'),
                            ),
                            'hint' => __('<p>How to limit the amount of subscriptions.</p>', 'subscriptio'),
                        ),
                        'limit_trials' => array(
                            'title' => __('Subscription trial handling', 'subscriptio'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Do not limit trials', 'subscriptio'),
                                '1' => __('One trial per product per customer', 'subscriptio'),
                                '2' => __('One trial per site per customer', 'subscriptio'),
                            ),
                            'hint' => __('<p>How to limit the amount of trials.</p>', 'subscriptio'),
                        ),
                    ),
                ),
                'cancel' => array(
                    'title' => __('Cancelling', 'subscriptio'),
                    'children' => array(
                        'customer_cancelling_allowed' => array(
                            'title' => __('Allow customers to cancel subscriptions', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'pause_resume' => array(
                    'title' => __('Pause & Resume', 'subscriptio'),
                    'children' => array(
                        'customer_pausing_allowed' => array(
                            'title' => __('Allow customers to pause subscriptions', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'max_pauses' => array(
                            'title' => __('Max number of pauses', 'subscriptio'),
                            'type' => 'text',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'max_pause_duration' => array(
                            'title' => __('Max duration of a pause', 'subscriptio'),
                            'after' => __('day(s)', 'subscriptio'),
                            'type' => 'text',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
            ),
        ),
        'frontend' => array(
            'title' => __('Frontend', 'subscriptio'),
            'icon' => '<i class="fa fa-list-alt" style="font-size: 0.8em;"></i>',
            'children' => array(
                'labels' => array(
                    'title' => __('Labels', 'subscriptio'),
                    'children' => array(
                        'add_to_cart' => array(
                            'title' => __('Add to cart', 'subscriptio'),
                            'type' => 'text',
                            'default' => '',
                            'placeholder' => __('No change', 'subscriptio'),
                            'validation' => array(
                                'rule' => 'default',
                                'empty' => true
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'product_display' => array(
                    'title' => __('Product Display', 'subscriptio'),
                    'children' => array(
                        'cheapest_price_method' => array(
                            'title' => __('Cheapest Price Method', 'subscriptio'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Show subscription with cheapest per-day price', 'subscriptio'),
                                '1' => __('Show subscription with cheapest price', 'subscriptio'),
                            ),
                            'hint' => __('<p>How to display the price in variable/grouped subscription products (i.e. after "From:" text).</p>', 'subscriptio'),
                        ),
                    ),
                ),
            ),
        ),
        'flow' => array(
            'title' => __('Flow', 'subscriptio'),
            'icon' => '<i class="fa fa-arrow-right" style="font-size: 0.8em;"></i>',
            'children' => array(
                'subscription_flow' => array(
                    'title' => __('Subscription Flow', 'subscriptio'),
                    'children' => array(
                    ),
                ),
                'renewal_orders' => array(
                    'title' => __('Renewal Orders', 'subscriptio'),
                    'children' => array(
                        'renewal_order_day_offset' => array(
                            'title' => __('Generate renewal orders', 'subscriptio'),
                            'after' => __('day(s) before payment due date', 'subscriptio'),
                            'type' => 'text',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'reminders' => array(
                    'title' => __('Payment Reminders', 'subscriptio'),
                    'children' => array(
                        'reminders_enabled' => array(
                            'title' => __('Enable payment reminders', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'reminders_days' => array(
                            'title' => __('Send reminders before', 'subscriptio'),
                            'after' => __('day(s) (separate values by comma)', 'subscriptio'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => true
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'overdue' => array(
                    'title' => __('Overdue Period', 'subscriptio'),
                    'children' => array(
                        'overdue_enabled' => array(
                            'title' => __('Enable overdue period', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'overdue_length' => array(
                            'title' => __('Overdue period length', 'subscriptio'),
                            'after' => __('day(s)', 'subscriptio'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => true
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'suspensions' => array(
                    'title' => __('Suspensions', 'subscriptio'),
                    'children' => array(
                        'suspensions_enabled' => array(
                            'title' => __('Enable suspensions', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                        'suspensions_length' => array(
                            'title' => __('Suspension period length', 'subscriptio'),
                            'after' => __('day(s)', 'subscriptio'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => true
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
            ),
        ),
        'payments' => array(
            'title' => __('Payments', 'subscriptio'),
            'icon' => '<i class="fa fa-dollar" style="font-size: 0.8em;"></i>',
            'children' => array(
                'stripe_gateway' => array(
                    'title' => __('Stripe', 'subscriptio'),
                    'children' => array(
                        'stripe_enabled' => array(
                            'title' => __('Enable Stripe', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false,
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'paypal_gateway' => array(
                    'title' => __('PayPal Adaptive Payments (deprecated)', 'subscriptio'),
                    'children' => array(
                        'paypal_enabled' => array(
                            'title' => __('Enable PayPal Adaptive Payments', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false,
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
                'paypal_ec_gateway' => array(
                    'title' => __('PayPal Express Checkout', 'subscriptio'),
                    'children' => array(
                        'paypal_ec_enabled' => array(
                            'title' => __('Enable PayPal Express Checkout', 'subscriptio'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false,
                            ),
                            'hint' => __('<p></p>', 'subscriptio'),
                        ),
                    ),
                ),
            ),
        ),
    );
}
}
