/**
 * Scripts for Stripe modal credit card form
 */
jQuery(document).ready(function() {

    /**
     * Hide new card form if saved cards exist
     */
    if (jQuery('input[name=subscriptio_stripe_card_id]').length < 2) {
        jQuery('fieldset#subscriptio_stripe-cc-form').hide();
    }

    /**
     * Switch between cards
     */
    jQuery('input[name=subscriptio_stripe_card_id]').on('change', function() {
        if (jQuery('input[name=subscriptio_stripe_card_id]:checked').val() === 'none') {
            jQuery('fieldset#subscriptio_stripe-cc-form').show();
        }
        else {
            jQuery('fieldset#subscriptio_stripe-cc-form').hide();
        }
    });

    /**
     * Open Stripe Checkout modal
     */
    function subscriptio_stripe_open_stripe_modal() {
        var form = jQuery('form#order_review').length > 0 ? jQuery('form#order_review') : jQuery('form.checkout');

        // Revert to default action if not our method or customer is not adding a new card
        if (jQuery('input[name=payment_method]:checked').val() !== 'subscriptio_stripe' || (jQuery('input[type=radio][name=subscriptio_stripe_card_id]:checked').val() !== 'none' && jQuery('input[type=hidden][name=subscriptio_stripe_card_id]').val() !== 'none') || jQuery('input[name=subscriptio_stripe_token]').length !== 0) {
            return true;
        }

        // Reset any previously set values
        subscriptio_stripe_stripe_form_reset();


        // Process response from Stripe
        var subscriptio_stripe_stripe_response_handler = function(response) {
            var form = jQuery('form#order_review').length > 0 ? jQuery('form#order_review') : jQuery('form.checkout');

            // Reset any previously set values
            subscriptio_stripe_stripe_form_reset();

            // Set token
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_token" />').val(response.id));

            // Submit form programatically
            form.submit();
        }

        // Open Stripe Checkout
        StripeCheckout.open({
            key:            subscriptio_stripe_config.publishable_key,
            name:           subscriptio_stripe_config.checkout_name,
            description:    subscriptio_stripe_config.checkout_description,
            panelLabel:     subscriptio_stripe_config.checkout_label,
            image:          subscriptio_stripe_config.checkout_image,
            amount:         jQuery('#subscriptio_stripe_card_amount').val() !== subscriptio_stripe_config.checkout_amount ? jQuery('#subscriptio_stripe_card_amount').val() : subscriptio_stripe_config.checkout_amount,
            currency:       subscriptio_stripe_config.checkout_currency,
            locale:         'auto',
            email:          jQuery('#billing_email').val() ? jQuery('#billing_email').val() : subscriptio_stripe_config.checkout_email,
            token:          subscriptio_stripe_stripe_response_handler
        });

        return false;
    }

    jQuery('form.checkout').on('checkout_place_order_subscriptio_stripe', function() {
        return subscriptio_stripe_open_stripe_modal();
    });
    jQuery('form#order_review').submit(function() {
        return subscriptio_stripe_open_stripe_modal();
    });

    /**
     * Reset warnings and hidden fields
     */
    function subscriptio_stripe_stripe_form_reset() {
        jQuery('input[name=subscriptio_stripe_token]').remove();
    }

});
