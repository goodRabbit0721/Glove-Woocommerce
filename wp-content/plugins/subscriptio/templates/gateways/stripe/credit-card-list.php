<?php

/**
 * Subscriptio Stripe Credit Card List
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<h2><?php _e('Linked Credit Cards', 'subscriptio-stripe'); ?></h2>

<?php do_action('subscriptio_stripe_before_card_list'); ?>

<table class="shop_table subscriptio_stripe_card_list my_account_orders">

    <thead>
        <tr>
            <th class="subscriptio_stripe_list_type"><?php _e('Type', 'subscriptio-stripe'); ?></th>
            <th class="subscriptio_stripe_list_ending"><?php _e('Ending with', 'subscriptio-stripe'); ?></th>
            <th class="subscriptio_stripe_list_expires"><?php _e('Expires', 'subscriptio-stripe'); ?></th>
            <th class="subscriptio_stripe_list_default"><?php _e('Default', 'subscriptio-stripe'); ?></th>
            <th class="subscriptio_stripe_list_actions">&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($cards as $card_id => $card): ?>

        <tr class="subscriptio_stripe_card_list_card">
            <td class="subscriptio_stripe_list_type"><?php echo $card['brand']; ?></td>
            <td class="subscriptio_stripe_list_ending"><?php echo $card['last4']; ?></td>
            <td class="subscriptio_stripe_list_expires"><?php echo Subscriptio_Stripe::format_expiration_date($card['exp_month'], $card['exp_year']); ?></td>
            <td class="subscriptio_stripe_list_default"><?php echo ($default == $card_id ? __('Yes', 'subscriptio-stripe') : ''); ?></td>
            <td class="subscriptio_stripe_list_actions">
                <a href="<?php echo site_url('/?subscriptio_stripe_delete_card=' . urlencode($card_id)); ?>" class="button subscriptio_stripe_button_delete"><?php _e('Delete', 'subscriptio-stripe'); ?></a>
                <?php if ($default != $card_id): ?>
                    <a href="<?php echo site_url('/?subscriptio_stripe_card_make_default=' . urlencode($card_id)); ?>" class="button subscriptio_stripe_button_default"><?php _e('Make Default', 'subscriptio-stripe'); ?></a>
                <?php endif; ?>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<?php do_action('subscriptio_stripe_after_card_list'); ?>
