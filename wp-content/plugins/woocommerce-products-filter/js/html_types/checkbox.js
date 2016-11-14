function woof_init_checkboxes() {
    
    if (icheck_skin != 'none') {
        jQuery('.woof_checkbox_term').iCheck('destroy');
        
        jQuery('.woof_checkbox_term').iCheck({
            
            checkboxClass: 'icheckbox_' + icheck_skin.skin + '-' + icheck_skin.color,
            //checkboxClass: 'icheckbox_square-green'
        });

        jQuery('.woof_checkbox_term').on('ifChecked', function (event) {
            
            var current_selector = jQuery(this).parent().parent().parent().find('li');
            var tax = jQuery(this).data('tax');
            
            if (tax != 'pa_features'){
                jQuery(current_selector).each(function(object, callback){
                    var flag = jQuery(this).find('div').hasClass('checked');
                    if (flag){
                        jQuery(this).find('div').removeClass('checked');
                    }

                });
            }
            jQuery(this).attr("checked", true);
            woof_checkbox_process_data(this, true);
        });

        jQuery('.woof_checkbox_term').on('ifUnchecked', function (event) {
            jQuery(this).attr("checked", false);
            woof_checkbox_process_data(this, false);
        });
    } else {
        jQuery('.woof_checkbox_term').on('change', function (event) {
            if (jQuery(this).is(':checked')) {
                jQuery(this).attr("checked", true);
                woof_checkbox_process_data(this, true);
            } else {
                jQuery(this).attr("checked", false);
                woof_checkbox_process_data(this, false);
            }
        });
    }
}
function woof_checkbox_process_data(_this, is_checked) {

    var tax = jQuery(_this).data('tax');
    var name = jQuery(_this).attr('name');
    var term_id = jQuery(_this).data('term-id');
    woof_checkbox_direct_search(term_id, name, tax, is_checked);
}
function woof_checkbox_direct_search(term_id, name, tax, is_checked) {


    var values = '';
    var checked = true;
    if (is_checked) {
        
        if (tax == 'pa_features'){
            if (tax in woof_current_values) {
                woof_current_values[tax] = woof_current_values[tax] + ',' + name;
            } else {
                woof_current_values[tax] = name;
            }
        }
        else
            woof_current_values[tax] = name;
        checked = true;
    } else {
        if (tax == 'pa_features'){
        values = woof_current_values[tax];
        values = values.split(',');
        var tmp = [];
        jQuery.each(values, function (index, value) {
            if (value != name) {
                tmp.push(value);
            }
        });
        values = tmp;
        if (values.length) {
            woof_current_values[tax] = values.join(',');
        } else {
            delete woof_current_values[tax];
        }
        }
        else 
            delete woof_current_values[tax];
        checked = false;
    }
    
    jQuery('.woof_checkbox_term_' + term_id).attr('checked', checked);
   
    woof_ajax_page_num = 1;
    if (woof_autosubmit) {
        woof_submit_link(woof_get_submit_link()+'#gallery');
    }
}



