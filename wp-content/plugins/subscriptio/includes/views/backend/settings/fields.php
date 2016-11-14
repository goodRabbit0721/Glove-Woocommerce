<?php

/**
 * View for General Settings page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="subscriptio_settings">
    <div class="subscriptio_settings_container">
        <input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />
        <?php settings_fields('subscriptio_opt_group_' . $current_tab); ?>
        <?php do_settings_sections('subscriptio-admin-' . str_replace('_', '-', $current_tab)); ?>
        <div></div>
        <?php submit_button(); ?>
    </div>
</div>
