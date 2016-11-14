/**
 * Scripts for Stripe inline credit card form
 */
jQuery(document).ready(function() {

    /**
     * Credit card detail field masking on inline form
     */
    function subscriptio_stripe_card_fields() {
        jQuery(function() {
            jQuery('.wc-credit-card-form-card-number').payment('formatCardNumber');
            jQuery('.wc-credit-card-form-card-cvc').payment('formatCardCVC');
        });
    }

    subscriptio_stripe_card_fields();

    jQuery('body').on('updated_checkout', function() {
        subscriptio_stripe_card_fields();
    });

    /**
     * Switch between cards
     */
    var form = jQuery('form#order_review').length > 0 ? jQuery('form#order_review') : jQuery('form.checkout');

    form.on('change', 'input[name=subscriptio_stripe_card_id]', function() {
        if (jQuery('input[name=subscriptio_stripe_card_id]:checked').val() === 'none') {
            jQuery('fieldset#subscriptio_stripe-cc-form').show();
        }
        else {
            jQuery('fieldset#subscriptio_stripe-cc-form').hide();
        }
    });

    /**
     * Set Stripe publishable key
     */
    Stripe.setPublishableKey(subscriptio_stripe_config.publishable_key);

    /**
     * Process new credit card data
     */
    function subscriptio_stripe_process_new_card() {
        var form = jQuery('form#order_review').length > 0 ? jQuery('form#order_review') : jQuery('form.checkout');

        // Revert to default action if not our method or customer is not adding a new card
        if (jQuery('input[name=payment_method]:checked').val() !== 'subscriptio_stripe' || (jQuery('input[type=radio][name=subscriptio_stripe_card_id]:checked').val() !== 'none' && jQuery('input[type=hidden][name=subscriptio_stripe_card_id]').val() !== 'none') || jQuery('input[name=subscriptio_stripe_token]').length !== 0) {
            return true;
        }

        // Should we use billing address from the form?
        var use_new_address = jQuery('#billing_first_name').val() || typeof subscriptio_stripe_billing_details === 'undefined' ? true : false;

        // Get form data
        var card_data = {
            number:             jQuery('#subscriptio_stripe-card-number').val(),
            exp_month:          jQuery('#subscriptio_stripe-card-expiry-month').val(),
            exp_year:           jQuery('#subscriptio_stripe-card-expiry-year').val(),
            cvc:                jQuery('#subscriptio_stripe-card-cvc').val(),
            name:               use_new_address ? jQuery('#billing_first_name').val() + ' ' + jQuery('#billing_last_name').val() : subscriptio_stripe_billing_details.billing_full_name,
            address_line1:      use_new_address ? jQuery('#billing_address_1').val() : subscriptio_stripe_billing_details.billing_address_1,
            address_line2:      use_new_address ? jQuery('#billing_address_2').val() : subscriptio_stripe_billing_details.billing_address_2,
            address_city:       use_new_address ? jQuery('#billing_city').val() : subscriptio_stripe_billing_details.billing_city,
            address_state:      use_new_address ? jQuery('#billing_state').val() : subscriptio_stripe_billing_details.billing_state,
            address_zip:        use_new_address ? jQuery('#billing_postcode').val() : subscriptio_stripe_billing_details.billing_postcode,
            address_country:    use_new_address ? jQuery('#billing_country').val() : subscriptio_stripe_billing_details.billing_country
        };

        // Block form in WooCommerce style
        if (typeof form.block === 'function' && typeof wc_checkout_params !== 'undefined' && wc_checkout_params.hasOwnProperty('ajax_loader_url')) {
            form.block({ message: null, overlayCSS: {background: '#fff url(' + wc_checkout_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
        }

        // Send card data to Stripe
        Stripe.createToken(card_data, subscriptio_stripe_stripe_response_handler);

        return false;
    }

    jQuery('form.checkout').on('checkout_place_order_subscriptio_stripe', function() {
        return subscriptio_stripe_process_new_card();
    });
    jQuery('form#order_review').submit(function() {
        return subscriptio_stripe_process_new_card();
    });

    /**
     * Process response from Stripe
     */
    function subscriptio_stripe_stripe_response_handler(status, response) {
        var form = jQuery('form#order_review').length > 0 ? jQuery('form#order_review') : jQuery('form.checkout');

        // Error returned?
        if (response.error) {

            // Remove previous errors if any and reset hidden values
            subscriptio_stripe_stripe_form_reset();

            // Show new errors
            var error_message = response.error.hasOwnProperty('code') && subscriptio_stripe_config.hasOwnProperty('error_' + response.error.code) ? subscriptio_stripe_config['error_' + response.error.code] : response.error.message;
            form.prepend('<ul class="woocommerce-error"><li>' + error_message + '</li></ul>');

            // Unblock form
            if (typeof form.unblock === 'function') {
                form.unblock();
            }

            // Lose focus from all fields
            form.find('.input-text, select').blur();

            // Scroll to errors
            jQuery('html, body').animate({
                scrollTop: (form.offset().top - 100)
            }, 1000);
        }
        else {

            // Save card token and other details
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_token" />').val(response.id));
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_new_card_month" />').val(response.card.exp_month));
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_new_card_year" />').val(response.card.exp_year));
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_new_card_last" />').val(response.card.last4));
            form.append(jQuery('<input type="hidden" name="subscriptio_stripe_new_card_brand" />').val(response.card.brand));

            // Submit form programatically
            form.submit();
        }
    }

    /**
     * Hide warnings when user edits fields
     */
    jQuery('#subscriptio_stripe-card-number, #subscriptio_stripe-card-expiry-month, #subscriptio_stripe-card-expiry-year, #subscriptio_stripe-card-cvc, #subscriptio_stripe_card_id').on('change', function() {
        subscriptio_stripe_stripe_form_reset();
    });

    /**
     * Reset warnings and hidden fields
     */
    function subscriptio_stripe_stripe_form_reset() {
        jQuery('.woocommerce-error, .woocommerce-message').remove();
        jQuery('input[name=subscriptio_stripe_token]').remove();
    }

});
