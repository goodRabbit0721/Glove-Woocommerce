<?php

/**
 * View for Subscription Edit page Subscription Transactions block
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<ul class="subscriptio_subscription_transactions">
    <?php if (!empty($transactions)): ?>
        <?php foreach($transactions as $transaction): ?>
            <li>
                <div class="subscriptio_transaction_<?php echo $transaction->result; ?>">
                    <div class="subscriptio_transaction_heading">
                        <?php echo '<strong>' . $transaction->action_title . '</strong> - ' . $transaction->result_title . ''; ?>
                    </div>
                    <?php if ($transaction->note): ?>
                        <div class="subscriptio_transaction_note">
                            <?php echo $transaction->note; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="subscriptio_transaction_meta">
                    <?php echo Subscriptio::get_adjusted_datetime($transaction->time, null, 'subscription_edit_started'); ?>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="subscriptio_nothing_to_display"><?php _e('No transactions found.', 'subscriptio'); ?></p>
    <?php endif; ?>
</ul>
