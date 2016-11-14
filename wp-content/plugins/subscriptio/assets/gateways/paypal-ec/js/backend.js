/**
 * Subscriptio PayPal EC Backend Scripts
 */
jQuery(document).ready(function() {

    /**
     * Toggle branding
     */

    jQuery('#woocommerce_subscriptio_paypal_ec_enable_branding').each(function() {
        toggle_branding(jQuery(this));
    });

    jQuery('#woocommerce_subscriptio_paypal_ec_enable_branding').change(function() {
        toggle_branding(jQuery(this));
    });

    function toggle_branding(checkbox) {
        if (checkbox.is(':checked')) {
            jQuery('.subscriptio_paypal_ec_branding').parent().parent().parent().show();
        }
        else {
            jQuery('.subscriptio_paypal_ec_branding').parent().parent().parent().hide();
        }
    }

});
