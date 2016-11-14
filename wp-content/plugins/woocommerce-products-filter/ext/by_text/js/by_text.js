var woof_text_do_submit = false;
function woof_init_text() {
    jQuery('.woof_show_text_search').keyup(function (e) {
	var val = jQuery(this).val();
	var uid = jQuery(this).data('uid');

	if (e.keyCode == 13 /*&& val.length > 0*/) {
	    woof_text_do_submit = true;
	    woof_text_direct_search('woof_text', val);
	    return true;
	}

	//save new word into woof_current_values
	if (woof_autosubmit) {
	    woof_current_values['woof_text'] = val;
	} else {
	    woof_text_direct_search('woof_text', val);
	}


	//if (woof_is_mobile == 1) {
	if (val.length > 0) {
	    jQuery('.woof_text_search_go.' + uid).show(222);
	} else {
	    jQuery('.woof_text_search_go.' + uid).hide();
	}
	//}

	//http://easyautocomplete.com/examples
	if (val.length >= 3 && woof_text_autocomplete) {
	    //http://stackoverflow.com/questions/1574008/how-to-simulate-target-blank-in-javascript
	    jQuery('.easy-autocomplete a').life('click', function () {
		window.open(jQuery(this).attr('href'), '_blank');
		return false;
	    });
	    //***
	    var input_id = jQuery(this).attr('id');
	    var options = {
		url: function (phrase) {
		    return woof_ajaxurl;
		},
		//theme: "square",
		getValue: function (element) {
		    return element.name;
		},
		ajaxSettings: {
		    dataType: "json",
		    method: "POST",
		    data: {
			action: "woof_text_autocomplete",
			dataType: "json"
		    }
		},
		preparePostData: function (data) {
		    data.phrase = jQuery("#" + input_id).val();
		    return data;
		},
		template: {
		    type: woof_post_links_in_autocomplete ? 'links' : 'iconRight',
		    fields: {
			iconSrc: "icon",
			link: "link"
		    }
		},
		list: {
		    maxNumberOfElements: woof_text_autocomplete_items,
		    onChooseEvent: function () {
			woof_text_do_submit = true;

			if (woof_post_links_in_autocomplete) {
			    return false;
			} else {
			    woof_text_direct_search('woof_text', jQuery("#" + input_id).val());
			}

			return true;
		    },
		    showAnimation: {
			type: "fade", //normal|slide|fade
			time: 333,
			callback: function () {
			}
		    },
		    hideAnimation: {
			type: "slide", //normal|slide|fade
			time: 333,
			callback: function () {
			}
		    }

		},
		requestDelay: 400
	    };
	    try {
		jQuery("#" + input_id).easyAutocomplete(options);
	    } catch (e) {
		console.log(e);
	    }
	    jQuery("#" + input_id).focus();
	}
    });

    //+++
    jQuery('.woof_text_search_go').life('click', function () {
	var uid = jQuery(this).data('uid');
	woof_text_do_submit = true;
	woof_text_direct_search('woof_text', jQuery('.woof_show_text_search.' + uid).val());
    });
}

function woof_text_direct_search(name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
	if (index == name) {
	    delete woof_current_values[name];
	    return;
	}
    });

    if (slug != 0) {
	woof_current_values[name] = slug;
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit || woof_text_do_submit) {
	woof_text_do_submit = false;
	woof_submit_link(woof_get_submit_link());
    }
}


