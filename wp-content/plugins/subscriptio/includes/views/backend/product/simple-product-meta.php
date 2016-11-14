<?php

/**
 * View for WooCommerce Product Page Subscription Settings (Simple Product)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="options_group show_if_subscriptio_simple" style="display: none;">

    <p class="form-field _subscriptio_price_time_field">
        <label for="_subscriptio_price_time_value"><?php _e('Price is per', 'subscriptio'); ?></label>
        <input type="text" class="input-text subscriptio_product_page_half_width" id="_subscriptio_price_time_value" name="_subscriptio_price_time_value" placeholder="<?php _e('e.g. 7', 'subscriptio'); ?>" value="<?php echo $_subscriptio_price_time_value; ?>">
        <select id="_subscriptio_price_time_unit" name="_subscriptio_price_time_unit" class="select subscriptio_product_page_half_width" style="margin-left: 3px;">
            <?php foreach (Subscriptio::get_time_units() as $unit_key => $unit): ?>
                <option value="<?php echo $unit_key; ?>" <?php echo $_subscriptio_price_time_unit == $unit_key ? 'selected="selected"' : ''; ?>><?php echo call_user_func($unit['translation_callback'], $unit_key, 2); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p class="form-field _subscriptio_free_trial_field">
        <label for="_subscriptio_free_trial_time_value"><?php _e('Free trial', 'subscriptio'); ?></label>
        <input type="text" class="input-text subscriptio_product_page_half_width" id="_subscriptio_free_trial_time_value" name="_subscriptio_free_trial_time_value" placeholder="<?php _e('e.g. 3', 'subscriptio'); ?>" value="<?php echo $_subscriptio_free_trial_time_value; ?>">
        <select id="_subscriptio_free_trial_time_unit" name="_subscriptio_free_trial_time_unit" class="select subscriptio_product_page_half_width" style="margin-left: 3px;">
            <?php foreach (Subscriptio::get_time_units() as $unit_key => $unit): ?>
                <option value="<?php echo $unit_key; ?>" <?php echo $_subscriptio_free_trial_time_unit == $unit_key ? 'selected="selected"' : ''; ?>><?php echo call_user_func($unit['translation_callback'], $unit_key, 2); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p class="form-field _subscriptio_signup_fee_field">
        <label for="_subscriptio_signup_fee"><?php _e('Sign-up fee', 'subscriptio'); ?> <?php echo (function_exists('get_woocommerce_currency_symbol') ? '(' . get_woocommerce_currency_symbol() . ')' : ''); ?></label>
        <input type="text" class="short" name="_subscriptio_signup_fee" id="_subscriptio_signup_fee" placeholder="<?php _e('e.g. 9.99', 'subscriptio'); ?>" value="<?php echo $_subscriptio_signup_fee; ?>">
    </p>

    <p class="form-field _subscriptio_max_length_field">
        <label for="_subscriptio_max_length_time_value"><?php _e('Max length', 'subscriptio'); ?></label>
        <input type="text" class="input-text subscriptio_product_page_half_width" id="_subscriptio_max_length_time_value" name="_subscriptio_max_length_time_value" placeholder="<?php _e('e.g. 90', 'subscriptio'); ?>" value="<?php echo $_subscriptio_max_length_time_value; ?>">
        <select id="_subscriptio_max_length_time_unit" name="_subscriptio_max_length_time_unit" class="select subscriptio_product_page_half_width" style="margin-left: 3px;">
            <?php foreach (Subscriptio::get_time_units() as $unit_key => $unit): ?>
                <option value="<?php echo $unit_key; ?>" <?php echo $_subscriptio_max_length_time_unit == $unit_key ? 'selected="selected"' : ''; ?>><?php echo call_user_func($unit['translation_callback'], $unit_key, 2); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

</div>
