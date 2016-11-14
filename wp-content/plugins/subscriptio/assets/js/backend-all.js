/**
 * Subscriptio Plugin Backend Scripts (loaded on all pages)
 */
jQuery(document).ready(function() {

    /**
     * Toggle subscription settings fields for simple product
     */
    function toggle_subscriptio_simple_product_fields() {
        if (jQuery('select#product-type').val() === 'simple') {
            if (jQuery('input#_subscriptio').is(':checked')) {
                jQuery('.show_if_subscriptio_simple').show();
            }
            else {
                jQuery('.show_if_subscriptio_simple').hide();
            }
        }
        else {
            jQuery('.show_if_subscriptio_simple').hide();
        }
    }

    toggle_subscriptio_simple_product_fields();

    jQuery('body').bind('woocommerce-product-type-change',function() {
        toggle_subscriptio_simple_product_fields();
    });

    jQuery('input#_subscriptio').change(function() {
        toggle_subscriptio_simple_product_fields();
    });

    /**
     * Toggle subscription settings fields for variable product
     */
    function toggle_subscriptio_variable_product_fields() {
        if (jQuery('select#product-type').val() === 'variable') {
            jQuery('input._subscriptio_variable').each(function() {

                // Set different elements in variable depending on WC version
                if (subscriptio_vars.wc_version_23 === '1') {
                    var variation_fields = jQuery(this).closest('div.woocommerce_variation').find('div.show_if_subscriptio_variable');
                }
                else {
                    var variation_fields = jQuery(this).closest('tbody').find('tr.show_if_subscriptio_variable');
                }

                if (jQuery(this).is(':checked')) {

                    // Display subscription options
                    variation_fields.each(function() {
                        jQuery(this).show();
                    });

                    // Write "Subscription" on variable product handle (if not present)
                    if (jQuery(this).closest('div.woocommerce_variation').find('.subscriptio_variable_product_handle_icon').length == 0) {
                        jQuery(this).closest('div.woocommerce_variation').find('h3').first().find('select').last().after('<i style="margin-left:10px;" class="fa fa-repeat subscriptio_variable_product_handle_icon" title="' + subscriptio_vars.title_subscription_product + '"></i>');
                    }
                }
                else {

                    // Hide subscription options
                    variation_fields.each(function() {
                        jQuery(this).hide();
                    });

                    // Remove "Subscription" from variable product handle
                    jQuery(this).closest('div.woocommerce_variation').find('.subscriptio_variable_product_handle_icon').remove();
                }
            });
        }
    }

    toggle_subscriptio_variable_product_fields();

    jQuery('input._subscriptio_variable').each(function() {
        jQuery(this).change(function() {
            toggle_subscriptio_variable_product_fields();
        });
    });

    // Make sure method is applied when variations loaded via AJAX in WC 2.4+
    jQuery(document).on('change', '#variable_product_options', function(){
        toggle_subscriptio_variable_product_fields();
    });
    jQuery(document).on('click', '._subscriptio_variable', function(){
        toggle_subscriptio_variable_product_fields();
    });

    jQuery('#variable_product_options').on('woocommerce_variations_added', function() {
        toggle_subscriptio_variable_product_fields();

        jQuery('input._subscriptio_variable').last().each(function() {
            jQuery(this).change(function() {
                toggle_subscriptio_variable_product_fields();
            });
        });
    });

    /**
     * Display admin shipping address edit fields
     */
    jQuery('#subscriptio_admin_edit_address').click(function(e) {
        e.preventDefault();
        jQuery(this).hide();
        jQuery('.subscriptio_admin_address').hide();
        jQuery('.subscriptio_admin_address_fields').show();
    });
    jQuery('#subscriptio_cancel_address_edit').click(function(e) {
        e.preventDefault();
        jQuery('.subscriptio_admin_address_fields').hide();
        jQuery('.subscriptio_admin_address').show();
        jQuery('#subscriptio_admin_edit_address').show();
    });

    /**
     * Show or hide pause fields
     */
    jQuery('#subscriptio_customer_pausing_allowed').each(function() {
        if (!jQuery(this).is(':checked')) {
            jQuery('#subscriptio_max_pauses').parent().parent().hide();
            jQuery('#subscriptio_max_pause_duration').parent().parent().hide();
        }
    });

    jQuery('#subscriptio_customer_pausing_allowed').change(function() {
        if (jQuery(this).is(':checked')) {
            jQuery('#subscriptio_max_pauses').parent().parent().show();
            jQuery('#subscriptio_max_pause_duration').parent().parent().show();
        }
        else {
            jQuery('#subscriptio_max_pauses').parent().parent().hide();
            jQuery('#subscriptio_max_pause_duration').parent().parent().hide();
        }
    });


    /**
     * Handle expiration date change
     */
    jQuery('.subscriptio_date_change_link').click(function(e) {

        e.preventDefault();

        // Get nearest date input field and show it (for short time)
        var date_field = jQuery(this).closest('p').find('input[name=subscription_date]').first().show();

        // Get current date
        var current_date_field = jQuery(this).closest('p').find('input[name=subscription_default_date]').first();
        var current_date = current_date_field.val();

        // Datepicker configuration
        var datepicker_config = {
            showButtonPanel:    true,
            currentText:        subscriptio_vars.current_text,
            closeText:          subscriptio_vars.close_text,
            dateFormat:         'yy-mm-dd'
        };

        // Set current date (if any)
        if (current_date !== '') {
            datepicker_config.defaultDate = current_date;
            date_field.val(current_date);
        }

        // On select
        datepicker_config.onSelect = function(date) {
            subscriptio_date_changed(date, jQuery(this));
            current_date_field.val(date);
            date_field.datepicker('destroy');
        };

        // On close
        datepicker_config.onClose = function() {
            date_field.datepicker('destroy');
        };

        // Initialize datepicker
        date_field.datepicker(datepicker_config);

        // Show the datepicker and hide the field
        date_field.datepicker('show').hide();
    });

    /**
     * Handle expiration date change
     */
    function subscriptio_date_changed(date, field)
    {
        var block = field.parent('p');

        jQuery.post(
            ajaxurl,
            {
                'action':          'change_scheduled_date',
                'user_id':         block.find('input[name=subscription_user_id]').first().val(),
                'subscription_id': block.find('input[name=subscription_id]').first().val(),
                'date_type':       block.find('input[name=subscription_date_type]').first().val(),
                'date':            date
            },
            function(response) {
                var result = jQuery.parseJSON(response);

                // Update date in view
                if (typeof result.newdate !== 'undefined') {

                    // Update the date if no errors caused
                    if (result.newdate !== 'error') {
                        block.find('.subscriptio_date_change_link').html(result.newdate);
                    }
                    else {
                        alert(subscriptio_vars.date_change_alert);
                    }

                    // Reload the page to update transactions list
                    location.reload();
                }
            }
        );
    }




});
