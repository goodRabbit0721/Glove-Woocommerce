<?php

/**
 * View for site URL mismatch notification
 * Displayed on development/staging websites or when user changes main website URL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="message" class="error subscriptio_url_mismatch">
    <p>
        <strong><?php _e('Subscriptio URL mismatch', 'subscriptio'); ?></strong>
    </p>
    <p>
        <?php _e('Your website URL has recently been changed. Automatic payments and customer emails have been disabled to prevent live transactions originating from development or staging servers.', 'subscriptio'); ?><br />
        <?php _e('If you have moved this website permanently and would like to re-enable these features, select appropriate action below.', 'subscriptio'); ?>
    </p>
    <form action="" method="post">
        <button class="button-primary" name="subscriptio_url_mismatch_action" value="ignore"><?php _e('Hide this warning', 'subscriptio'); ?></button>
        <button class="button" name="subscriptio_url_mismatch_action" value="change"><?php _e('Make current URL primary', 'subscriptio'); ?></button>
    </form>
</div>
