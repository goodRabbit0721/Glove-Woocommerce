/**
 * Subscriptio Plugin Frontend Scripts
 */
jQuery(document).ready(function() {

    /**
     * Prompt user to confirm actions
     */
    jQuery('#subscriptio_button_pause_subscription').click(function(e) {
        e.preventDefault();
        if (confirm(subscriptio_vars.confirm_pause)) {
            window.location = this.href;
        }
    });
    jQuery('#subscriptio_button_resume_subscription').click(function(e) {
        e.preventDefault();
        if (confirm(subscriptio_vars.confirm_resume)) {
            window.location = this.href;
        }
    });
    jQuery('#subscriptio_button_cancel_subscription').click(function(e) {
        e.preventDefault();
        if (confirm(subscriptio_vars.confirm_cancel)) {
            window.location = this.href;
        }
    });

});
