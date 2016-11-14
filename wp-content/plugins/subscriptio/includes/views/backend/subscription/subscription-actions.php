<?php

/**
 * View for Subscription Edit page Subscription Actions block
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="subscription_actions">
    <select name="subscriptio_subscription_actions">

        <option value=""><?php _e('Actions', 'subscriptio'); ?></option>

        <?php if ($subscription->can_be_paused()): ?>
            <option value="pause"><?php _e('Pause Subscription', 'subscriptio'); ?></option>
        <?php endif; ?>

        <?php if ($subscription->can_be_resumed()): ?>
            <option value="resume"><?php _e('Resume Subscription', 'subscriptio'); ?></option>
        <?php endif; ?>

        <?php if ($subscription->can_be_cancelled()): ?>
            <option value="cancel"><?php _e('Cancel Subscription', 'subscriptio'); ?></option>
        <?php endif; ?>

        <?php if (in_array($subscription->status, array('cancelled', 'expired', 'failed'))): ?>
            <option value="" disabled="disabled"><?php _e('- No actions available -', 'subscriptio'); ?></option>
        <?php endif; ?>

    </select>
</div>
<div class="subscription_actions_footer submitbox">
    <div class="subscriptio_subscription_delete">
        <?php if (current_user_can('delete_post', $subscription->id)): ?>
            <a class="submitdelete deletion" href="<?php echo esc_url(get_delete_post_link($subscription->id)); ?>"><?php echo (!EMPTY_TRASH_DAYS ? __('Delete Permanently', 'subscriptio') : __('Move to Trash', 'subscriptio')); ?></a>
        <?php endif; ?>
    </div>

    <button type="submit" class="button button-primary" title="<?php _e('Process', 'subscriptio'); ?>" name="subscriptio_subscription_button" value="actions"><?php _e('Process', 'subscriptio'); ?></button>
</div>
<div style="clear: both;"></div>
