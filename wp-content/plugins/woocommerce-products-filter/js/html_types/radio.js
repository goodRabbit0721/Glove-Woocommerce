function woof_init_radios() {
    if (icheck_skin != 'none') {
        jQuery('.woof_radio_term').iCheck('destroy');

        jQuery('.woof_radio_term').iCheck({
            radioClass: 'iradio_' + icheck_skin.skin + '-' + icheck_skin.color,
            //radioClass: 'iradio_square-green'        
        });

        jQuery('.woof_radio_term').on('ifChecked', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).data('slug');
            var name = jQuery(this).attr('name');
            var term_id = jQuery(this).data('term-id');
            woof_radio_direct_search(term_id, name, slug);
        });


    } else {
        jQuery('.woof_radio_term').on('change', function (event) {
            jQuery(this).attr("checked", true);
            var slug = jQuery(this).data('slug');
            var name = jQuery(this).attr('name');
            var term_id = jQuery(this).data('term-id');
            woof_radio_direct_search(term_id, name, slug);
        });
    }

    //***

    jQuery('.woof_radio_term_reset').click(function () {
        woof_radio_direct_search(jQuery(this).data('term-id'), jQuery(this).attr('name'), 0);
        return false;
    });
}

function woof_radio_direct_search(term_id, name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
        if (index == name) {
            delete woof_current_values[name];
            return;
        }
    });
    console.log('slug', slug);
    if (slug != 0) {
        woof_current_values[name] = slug;
        jQuery('a.woof_radio_term_reset_' + term_id).hide();
        jQuery('woof_radio_term_' + term_id).filter(':checked').parents('li').find('a.woof_radio_term_reset').show();
        jQuery('woof_radio_term_' + term_id).parents('ul.woof_list_radio').find('label').css({'fontWeight': 'normal'});
        jQuery('woof_radio_term_' + term_id).filter(':checked').parents('li').find('label.woof_radio_label_' + slug).css({'fontWeight': 'bold'});
    } else {
        jQuery('a.woof_radio_term_reset_' + term_id).hide();
        jQuery('woof_radio_term_' + term_id).attr('checked', false);
        jQuery('woof_radio_term_' + term_id).parent().removeClass('checked');
        jQuery('woof_radio_term_' + term_id).parents('ul.woof_list_radio').find('label').css({'fontWeight': 'normal'});
    }
    
    woof_ajax_page_num = 1;
    if (woof_autosubmit) {
        woof_submit_link(woof_get_submit_link());
    }
}

