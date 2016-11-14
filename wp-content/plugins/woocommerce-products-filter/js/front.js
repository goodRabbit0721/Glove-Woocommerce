var woof_redirect = '';//if we use redirect attribute in shortcode [woof]

//***

jQuery(function () {
    jQuery('body').append('<div id="woof_html_buffer" class="woof_info_popup" style="display: none;"></div>');
    jQuery.fn.life = function (types, data, fn) {
	jQuery(this.context).on(types, this.selector, data, fn);
	return this;
    };

    //http://stackoverflow.com/questions/2389540/jquery-hasparent
    jQuery.extend(jQuery.fn, {
	within: function (pSelector) {
	    // Returns a subset of items using jQuery.filter
	    return this.filter(function () {
		// Return truthy/falsey based on presence in parent
		return jQuery(this).closest(pSelector).length;
	    });
	}
    });

    //+++

    if (jQuery('#woof_results_by_ajax').length > 0) {
	woof_is_ajax = 1;
    }

    //listening attributes in shortcode [woof]
    woof_autosubmit = parseInt(jQuery('.woof').eq(0).data('autosubmit'), 10);
    woof_ajax_redraw = parseInt(jQuery('.woof').eq(0).data('ajax-redraw'), 10);



    //+++

    woof_ext_init_functions = jQuery.parseJSON(woof_ext_init_functions);

    //fix for native woo price range
    woof_init_native_woo_price_filter();


    jQuery('body').bind('price_slider_change', function (event, min, max) {
	if (woof_autosubmit && !woof_show_price_search_button) {
	    jQuery('.widget_price_filter form').trigger('submit');
	} else {
	    var min_price = jQuery(this).find('.price_slider_amount #min_price').val();
	    var max_price = jQuery(this).find('.price_slider_amount #max_price').val();
	    woof_current_values.min_price = min_price;
	    woof_current_values.max_price = max_price;
	}
    });

    jQuery('.woof_price_filter_dropdown').life('change', function () {
	var val = jQuery(this).val();
	if (parseInt(val, 10) == -1) {
	    delete woof_current_values.min_price;
	    delete woof_current_values.max_price;
	} else {
	    var val = val.split("-");
	    woof_current_values.min_price = val[0];
	    woof_current_values.max_price = val[1];
	}

	if (woof_autosubmit || jQuery(this).within('.woof').length == 0) {
	    woof_submit_link(woof_get_submit_link());
	}
    });

    //ion-slider price range slider
    woof_init_ion_sliders();

    //***

    woof_init_show_auto_form();
    woof_init_hide_auto_form();

    //***
    woof_remove_empty_elements();

    woof_init_search_form();
    woof_init_pagination();
    woof_init_orderby();
    woof_init_reset_button();
    woof_init_beauty_scroll();
    //+++
    woof_draw_products_top_panel();
    woof_shortcode_observer();


//+++
    //if we use redirect attribute in shortcode [woof is_ajax=0]
    //not for ajax, for redirect mode only
    if (!woof_is_ajax) {
	woof_redirect_init();
    }

    woof_init_toggles();

});

//if we use redirect attribute in shortcode [woof is_ajax=0]
//not for ajax, for redirect mode only
function woof_redirect_init() {

    try {
	if (jQuery('.woof').length) {
	    //https://wordpress.org/support/topic/javascript-error-in-frontjs?replies=1
	    if (undefined !== jQuery('.woof').val()) {
		woof_redirect = jQuery('.woof').eq(0).data('redirect');//default value
		if (woof_redirect.length > 0) {
		    woof_shop_page = woof_current_page_link = woof_redirect;
		}


		//***
		/*
		 var events = ['click', 'change', 'ifChecked', 'ifUnchecked'];
		 
		 for (var i = 0; i < events.length; i++) {
		 
		 jQuery('div.woof input, div.woof option, div.woof div, div.woof label').live(events[i], function (e) {
		 try {
		 if (jQuery(this).parents('.woof').data('redirect').length > 0) {
		 woof_redirect = jQuery(this).parents('.woof').data('redirect');
		 }
		 } catch (e) {
		 console.log('Error: attribute redirection doesn works!');
		 }
		 e.stopPropagation();
		 });
		 
		 }
		 */
		//***


		return woof_redirect;
	    }
	}
    } catch (e) {
	console.log(e);
    }

}

function woof_init_orderby() {
    jQuery('form.woocommerce-ordering').life('submit', function () {
	return false;
    });
    jQuery('form.woocommerce-ordering select.orderby').life('change', function () {
	woof_current_values.orderby = jQuery(this).val();
	woof_ajax_page_num = 1;
	woof_submit_link(woof_get_submit_link());
	return false;
    });
}

function woof_init_reset_button() {
    jQuery('.woof_reset_search_form').life('click', function () {
	//var link = jQuery(this).data('link');
	woof_ajax_page_num = 1;
	if (woof_is_permalink) {
		
	    woof_current_values = {};
	    woof_submit_link(woof_get_submit_link().split("page/")[0]+'#gallery');
	    //woof_submit_link(woof_get_submit_link());
	} else {
		
	    var link = woof_shop_page;
	    if (woof_current_values.hasOwnProperty('page_id')) {
		link = location.protocol + '//' + location.host + "/?page_id=" + woof_current_values.page_id;
		woof_current_values = {'page_id': woof_current_values.page_id};
		woof_get_submit_link();
	    }
	    //***
	    woof_submit_link(link);
	    if (woof_is_ajax) {
		history.pushState({}, "", link);
		if (woof_current_values.hasOwnProperty('page_id')) {
		    woof_current_values = {'page_id': woof_current_values.page_id};
		} else {
		    woof_current_values = {};
		}
	    }
	}
	return false;
    });
}

function woof_init_pagination() {
    if (woof_is_ajax === 1) {
	//jQuery('.woocommerce-pagination ul.page-numbers a.page-numbers').life('click', function () {
	jQuery('a.page-numbers').life('click', function () {
	    var l = jQuery(this).attr('href');

	    if (woof_ajax_first_done) {
		//http://woocommerce-filter.pluginus.net/wp-admin/admin-ajax.php?paged=2
		var res = l.split("paged=");
		if (typeof res[1] !== 'undefined') {
		    woof_ajax_page_num = parseInt(res[1]);
		} else {
		    woof_ajax_page_num = 1;
		}
	    } else {
		//http://woocommerce-filter.pluginus.net/tester/page/2/
		var res = l.split("page/");
		if (typeof res[1] !== 'undefined') {
		    woof_ajax_page_num = parseInt(res[1]);
		} else {
		    woof_ajax_page_num = 1;
		}
	    }

	    //+++

	    //if (woof_autosubmit) - pagination doesn need pressing any submit button!!
	    {
		woof_submit_link(woof_get_submit_link());
	    }

	    return false;
	});
    }
}

function woof_init_search_form() {
    woof_init_checkboxes();
    woof_init_mselects();
    woof_init_radios();
    woof_init_selects();


    //for extensions
    if (woof_ext_init_functions !== null) {
	jQuery.each(woof_ext_init_functions, function (type, func) {
	    eval(func + '()');
	});
    }
    //+++
    //var containers = jQuery('.woof_container');

    //+++
    jQuery('.woof_submit_search_form').click(function () {
	if (woof_ajax_redraw) {
	    //[woof redirect="http://www.dev.woocommerce-filter.com/test-all/" autosubmit=1 ajax_redraw=1 is_ajax=1 tax_only="locations" by_only="none"]
	    woof_ajax_redraw = 0;
	    woof_is_ajax = 0;
	}
	//***
	woof_submit_link(woof_get_submit_link());
	return false;
    });



    //***
    jQuery('ul.woof_childs_list').parent('li').addClass('woof_childs_list_li');

    //***

    woof_remove_class_widget();
    woof_checkboxes_slide();
}


function woof_submit_link(link) {
    woof_show_info_popup(woof_lang_loading);
    if (woof_is_ajax === 1 && !woof_ajax_redraw) {
	woof_ajax_first_done = true;
	var data = {
	    action: "woof_draw_products",
	    link: link,
	    page: woof_ajax_page_num,
	    shortcode: jQuery('#woof_results_by_ajax').data('shortcode'),
	    woof_shortcode: jQuery('div.woof').data('shortcode')
	};
	jQuery.post(woof_ajaxurl, data, function (content) {
	    content = jQuery.parseJSON(content);
	    if (jQuery('.woof_results_by_ajax_shortcode').length) {
		jQuery('#woof_results_by_ajax').replaceWith(content.products);
	    } else {
		jQuery('.woof_shortcode_output').replaceWith(content.products);
	    }

	    jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
	    woof_remove_empty_elements();
	    woof_init_search_form();
	    woof_hide_info_popup();
	    woof_draw_products_top_panel();
	    woof_init_beauty_scroll();
	    woof_init_ion_sliders();
	    woof_reinit_native_woo_price_filter();//native woo price range slider reinit
	    //removing id woof_results_by_ajax - multi in ajax mode sometimes
	    //when uses shorcode woof_products in ajax and in settings try ajaxify shop is Yes
	    jQuery.each(jQuery('#woof_results_by_ajax'), function (index, item) {
		if (index == 0) {
		    return;
		}

		jQuery(item).removeAttr('id');
	    });

	    //*** script after ajax loading here
	    woof_js_after_ajax_done();
	});
    } else {
	if (woof_ajax_redraw) {
	    //redrawing [woof ajax_redraw=1] only
	    var data = {
		action: "woof_draw_products",
		link: link,
		page: 1,
		shortcode: 'woof_nothing', //we do not need get any products, serach form data only
		woof_shortcode: jQuery('div.woof').eq(0).data('shortcode')
	    };
	    jQuery.post(woof_ajaxurl, data, function (content) {
		content = jQuery.parseJSON(content);
		jQuery('div.woof_redraw_zone').replaceWith(jQuery(content.form).find('.woof_redraw_zone'));
		woof_remove_empty_elements();
		woof_init_search_form();
		woof_hide_info_popup();
		woof_init_beauty_scroll();
		woof_init_ion_sliders();
		woof_reinit_native_woo_price_filter();//native woo price range slider reinit
	    });
	} else {
	    window.location = link;
	}
    }
}

function woof_remove_empty_elements() {
    // lets check for empty drop-downs
    jQuery.each(jQuery('.woof_container select'), function (index, select) {
	var size = jQuery(select).find('option').size();
	if (size === 0) {
	    jQuery(select).parents('.woof_container').remove();
	}
    });
    //+++
    // lets check for empty checkboxes, radio, color conatiners
    jQuery.each(jQuery('ul.woof_list'), function (index, ch) {
	var size = jQuery(ch).find('li').size();
	if (size === 0) {
	    jQuery(ch).parents('.woof_container').remove();
	}
    });
}

function woof_get_submit_link() {
//filter woof_current_values values
    if (woof_is_ajax) {
	woof_current_values.page = woof_ajax_page_num;
    }
//+++
    if (Object.keys(woof_current_values).length > 0) {
	jQuery.each(woof_current_values, function (index, value) {
	    if (index == swoof_search_slug) {
		delete woof_current_values[index];
	    }
	    if (index == 's') {
		delete woof_current_values[index];
	    }
	    if (index == 'product') {
//for single product page (when no permalinks)
		delete woof_current_values[index];
	    }
	    if (index == 'really_curr_tax') {
		delete woof_current_values[index];
	    }
	});
    }


    //***
    if (Object.keys(woof_current_values).length === 2) {
	if (('min_price' in woof_current_values) && ('max_price' in woof_current_values)) {
	    var l = woof_current_page_link + '?min_price=' + woof_current_values.min_price + '&max_price=' + woof_current_values.max_price;
	    if (woof_is_ajax) {
		history.pushState({}, "", l);
	    }
	    return l;
	}
    }



    //***

    if (Object.keys(woof_current_values).length === 0) {
	if (woof_is_ajax) {
	    history.pushState({}, "", woof_current_page_link);
	}
	return woof_current_page_link;
    }
    //+++
    if (Object.keys(woof_really_curr_tax).length > 0) {
	woof_current_values['really_curr_tax'] = woof_really_curr_tax.term_id + '-' + woof_really_curr_tax.taxonomy;
    }
    //+++
    var link = woof_current_page_link + "?" + swoof_search_slug + "=1";
    //console.log(woof_current_page_link);
    //just for the case when no permalinks enabled
    if (!woof_is_permalink) {

	if (woof_redirect.length > 0) {
	    link = woof_redirect + "?" + swoof_search_slug + "=1";
	    if (woof_current_values.hasOwnProperty('page_id')) {
		delete woof_current_values.page_id;
	    }
	} else {
	    link = location.protocol + '//' + location.host + "?" + swoof_search_slug + "=1";
	    /*
	     if (!woof_is_ajax) {
	     link = location.protocol + '//' + location.host + "?" + swoof_search_slug + "=1";
	     }
	     
	     if (woof_current_values.hasOwnProperty('page_id')) {
	     link = location.protocol + '//' + location.host + "?" + swoof_search_slug + "=1";
	     }
	     */
	}
    }
    //console.log(link);
    //throw('STOP!');

    //any trash for different sites, useful for quick support
    var woof_exclude_accept_array = ['path'];

    if (Object.keys(woof_current_values).length > 0) {
	jQuery.each(woof_current_values, function (index, value) {
	    if (index == 'page' && woof_is_ajax) {
		index = 'paged';//for right pagination if copy/paste this link and send somebody another by email for example
	    }

	    //http://www.dev.woocommerce-filter.com/?swoof=1&woof_author=3&woof_sku&woof_text=single
	    //avoid links where values is empty
	    if (typeof value !== 'undefined') {
		if ((typeof value && value.length > 0) || typeof value == 'number')
		{
		    if (jQuery.inArray(index, woof_exclude_accept_array) == -1) {
			link = link + "&" + index + "=" + value;
			console.log('link1=', link);
		    }
		}
	    }

	});
    }

    //+++
    //remove wp pagination like 'page/2'
    link = link.replace(new RegExp(/page\/(\d+)\//), "");

    if (woof_is_ajax) {
	history.pushState({}, "", link);
    }

    //throw ("STOP!");
    return link;//+"#gallery";
}

function woof_show_info_popup(text) {
    if (woof_overlay_skin == 'default') {
	jQuery("#woof_html_buffer").text(text);
	jQuery("#woof_html_buffer").fadeTo(200, 0.9);
    } else {
	//http://jxnblk.com/loading/
	switch (woof_overlay_skin) {
	    case 'loading-balls':
	    case 'loading-bars':
	    case 'loading-bubbles':
	    case 'loading-cubes':
	    case 'loading-cylon':
	    case 'loading-spin':
	    case 'loading-spinning-bubbles':
	    case 'loading-spokes':
		jQuery('body').plainOverlay('show', {progress: function () {
			return jQuery('<div id="woof_svg_load_container"><img style="height: 100%;width: 100%" src="' + woof_link + 'img/loading-master/' + woof_overlay_skin + '.svg" alt=""></div>');
		    }});
		break;
	    default:
		jQuery('body').plainOverlay('show', {duration: -1});
		break;
	}
    }
}


function woof_hide_info_popup() {
    if (woof_overlay_skin == 'default') {
	window.setTimeout(function () {
	    jQuery("#woof_html_buffer").fadeOut(400);
	}, 200);
    } else {
	jQuery('body').plainOverlay('hide');
    }
}

function woof_draw_products_top_panel() {
    if (jQuery('.woof_products_top_panel').length) {

	if (woof_is_ajax) {
	    jQuery('#woof_results_by_ajax').prev('.woof_products_top_panel').remove();
	}

	var panel = jQuery('.woof_products_top_panel');

	panel.html('');
	if (Object.keys(woof_current_values).length > 0) {
	    panel.show();
	    panel.html('<ul></ul>');
	    var is_price_in = false;
	    //lets show this on the panel
	    jQuery.each(woof_current_values, function (index, value) {

		//lets filter data for the panel
		if (jQuery.inArray(index, woof_accept_array) == -1) {
		    return;
		}


		//***


		if ((index == 'min_price' || index == 'max_price') && is_price_in) {
		    return;
		}

		if ((index == 'min_price' || index == 'max_price') && !is_price_in) {
		    is_price_in = true;
		    index = 'price';
		    value = woof_lang_pricerange;
		}
		//+++
		value = value.toString().trim();
		if (value.search(',')) {
		    value = value.split(',');
		}
		//+++
		jQuery.each(value, function (i, v) {
		    if (index == 'page') {
			return;
		    }

		    if (index == 'post_type') {
			return;
		    }

		    var txt = v;
		    if (index == 'orderby') {
			if (woof_lang[v] !== undefined) {
			    txt = woof_lang.orderby + ': ' + woof_lang[v];
			} else {
			    txt = woof_lang.orderby + ': ' + v;
			}
		    } else if (index == 'perpage') {
			txt = woof_lang.perpage;
		    } else if (index == 'price') {
			txt = woof_lang.pricerange;
		    } else {

			var is_in_custom = false;
			if (Object.keys(woof_lang_custom).length > 0) {
			    jQuery.each(woof_lang_custom, function (i, v) {
				if (i == index) {
				    is_in_custom = true;
				    txt = v;
				}
			    });
			}

			if (!is_in_custom) {
			    try {
				//txt = jQuery('.woof_n_' + index + '_' + v).val();
				txt = jQuery("input[data-anchor='woof_n_" + index + '_' + v + "']").val();
			    } catch (e) {
				console.log(e);
			    }

			    if (typeof txt === 'undefined')
			    {
				txt = v;
			    }
			}


			/* hidden feature
			 if (jQuery('input[name=woof_t_' + index + ']').length > 0) {
			 txt = jQuery('input[name=woof_t_' + index + ']').val() + ': ' + txt;
			 }
			 */


		    }

		    panel.find('ul').append(
			    jQuery('<li>').append(
			    jQuery('<a>').attr('href', v).attr('data-tax', index).append(
			    jQuery('<span>').attr('class', 'woof_remove_ppi').append(txt)
			    )));

		});


	    });
	}


	if (jQuery(panel).find('li').size() == 0) {
	    panel.hide();
	}

	//+++
	panel.find('ul li a').click(function () {
	    var tax = jQuery(this).data('tax');
	    var name = jQuery(this).attr('href');

	    //***
	    if (tax != 'price') {
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
	    } else {
		delete woof_current_values['min_price'];
		delete woof_current_values['max_price'];
	    }

	    woof_ajax_page_num = 1;
	    //if (woof_autosubmit)
	    {
		woof_submit_link(woof_get_submit_link());
	    }
	    jQuery('.woof_products_top_panel').find("[data-tax='" + tax + "'][href='" + name + "']").hide(333);
	    return false;
	});
    }
}

//control conditions if proucts shortcode uses on the page
function woof_shortcode_observer() {
    if (jQuery('.woof_shortcode_output').length) {
	woof_current_page_link = location.protocol + '//' + location.host + location.pathname;
    }

    if (jQuery('#woof_results_by_ajax').length) {
	woof_is_ajax = 1;
    }
}



function woof_init_beauty_scroll() {
    if (woof_use_beauty_scroll) {
	try {
	    jQuery(".woof_section_scrolled").mCustomScrollbar('destroy');
	    jQuery(".woof_section_scrolled").mCustomScrollbar({
		scrollButtons: {
		    enable: true
		},
		advanced: {
		    updateOnContentResize: true,
		    updateOnBrowserResize: true
		},
		theme: "dark-2",
		horizontalScroll: false,
		mouseWheel: true,
		scrollType: 'pixels',
		contentTouchScroll: true
	    });
	} catch (e) {
	    console.log(e);
	}
    }
}

//just for inbuilt price range widget
function woof_remove_class_widget() {
    jQuery('.woof_container_inner').find('.widget').removeClass('widget');
}

function woof_init_show_auto_form() {
    jQuery('.woof_show_auto_form').unbind('click');
    jQuery('.woof_show_auto_form').click(function () {
	var _this = this;
	jQuery(_this).addClass('woof_hide_auto_form').removeClass('woof_show_auto_form');
	jQuery(".woof_auto_show").show().animate(
		{
		    height: (jQuery(".woof_auto_show_indent").height() + 20) + "px",
		    opacity: 1
		}, 377, function () {
	    //jQuery(_this).text(woof_lang_hide_products_filter);
	    woof_init_hide_auto_form();
	    jQuery('.woof_auto_show').removeClass('woof_overflow_hidden');
	    jQuery('.woof_auto_show_indent').removeClass('woof_overflow_hidden');
	    jQuery(".woof_auto_show").height('auto');
	});


	return false;
    });


}

function woof_init_hide_auto_form() {
    jQuery('.woof_hide_auto_form').unbind('click');
    jQuery('.woof_hide_auto_form').click(function () {
	var _this = this;
	jQuery(_this).addClass('woof_show_auto_form').removeClass('woof_hide_auto_form');
	jQuery(".woof_auto_show").show().animate(
		{
		    height: "1px",
		    opacity: 0
		}, 377, function () {
	    //jQuery(_this).text(woof_lang_show_products_filter);
	    jQuery('.woof_auto_show').addClass('woof_overflow_hidden');
	    jQuery('.woof_auto_show_indent').addClass('woof_overflow_hidden');
	    woof_init_show_auto_form();
	});

	return false;
    });


}

//if we have mode - child checkboxes closed - append openers buttons by js
function woof_checkboxes_slide() {
    if (woof_checkboxes_slide_flag == true) {
	var childs = jQuery('ul.woof_childs_list');
	if (childs.size()) {
	    jQuery.each(childs, function (index, ul) {
		var span_class = 'woof_is_closed';
		if (jQuery(ul).find('input[type=checkbox],input[type=radio]').is(':checked')) {
		    jQuery(ul).show();
		    span_class = 'woof_is_opened';
		}

		jQuery(ul).before('<a href="javascript:void(0);" class="woof_childs_list_opener"><span class="' + span_class + '"></span></a>');
	    });

	    jQuery.each(jQuery('a.woof_childs_list_opener'), function (index, a) {
		jQuery(a).click(function () {
		    var span = jQuery(this).find('span');
		    if (span.hasClass('woof_is_closed')) {
			//lets open
			jQuery(this).parent().find('ul.woof_childs_list').first().show(333);
			span.removeClass('woof_is_closed');
			span.addClass('woof_is_opened');
		    } else {
			//lets close
			jQuery(this).parent().find('ul.woof_childs_list').first().hide(333);
			span.removeClass('woof_is_opened');
			span.addClass('woof_is_closed');
		    }

		    return false;
		});
	    });
	}
    }
}

function woof_init_ion_sliders() {
    jQuery.each(jQuery('.woof_range_slider'), function (index, input) {
	try {
	    jQuery(input).ionRangeSlider({
		min: jQuery(input).data('min'),
		max: jQuery(input).data('max'),
		from: jQuery(input).data('min-now'),
		to: jQuery(input).data('max-now'),
		type: 'double',
		prefix: jQuery(input).data('slider-prefix'),
		postfix: jQuery(input).data('slider-postfix'),
		prettify: true,
		hideMinMax: false,
		hideFromTo: false,
		grid: true,
		step: jQuery(input).data('step'),
		onFinish: function (ui) {
		    woof_current_values.min_price = parseInt(ui.from, 10);
		    woof_current_values.max_price = parseInt(ui.to, 10);
		    //woocs adaptation
		    if (typeof woocs_current_currency !== 'undefined') {
			woof_current_values.min_price = Math.ceil(woof_current_values.min_price / parseFloat(woocs_current_currency.rate));
			woof_current_values.max_price = Math.ceil(woof_current_values.max_price / parseFloat(woocs_current_currency.rate));
		    }
		    //***
		    woof_ajax_page_num = 1;
		    //jQuery(input).within('.woof').length -> if slider is as shortcode
		    if (woof_autosubmit || jQuery(input).within('.woof').length == 0) {
			woof_submit_link(woof_get_submit_link());
		    }
		    return false;
		}
	    });
	} catch (e) {

	}
    });
}

function woof_init_native_woo_price_filter() {
    jQuery('.widget_price_filter form').unbind('submit');
    jQuery('.widget_price_filter form').submit(function () {
	var min_price = jQuery(this).find('.price_slider_amount #min_price').val();
	var max_price = jQuery(this).find('.price_slider_amount #max_price').val();
	woof_current_values.min_price = min_price;
	woof_current_values.max_price = max_price;
	woof_ajax_page_num = 1;
	if (woof_autosubmit || jQuery(input).within('.woof').length == 0) {
	    //comment next code row to avoid endless ajax requests
	    woof_submit_link(woof_get_submit_link());
	}
	return false;
    });

}

//we need after ajax redrawing of the search form
function woof_reinit_native_woo_price_filter() {

    // woocommerce_price_slider_params is required to continue, ensure the object exists
    if (typeof woocommerce_price_slider_params === 'undefined') {
	return false;
    }

    // Get markup ready for slider
    jQuery('input#min_price, input#max_price').hide();
    jQuery('.price_slider, .price_label').show();

    // Price slider uses jquery ui
    var min_price = jQuery('.price_slider_amount #min_price').data('min'),
	    max_price = jQuery('.price_slider_amount #max_price').data('max'),
	    current_min_price = parseInt(min_price, 10),
	    current_max_price = parseInt(max_price, 10);

    if (woof_current_values.hasOwnProperty('min_price')) {
	current_min_price = parseInt(woof_current_values.min_price, 10);
	current_max_price = parseInt(woof_current_values.max_price, 10);
    } else {
	if (woocommerce_price_slider_params.min_price) {
	    current_min_price = parseInt(woocommerce_price_slider_params.min_price, 10);
	}
	if (woocommerce_price_slider_params.max_price) {
	    current_max_price = parseInt(woocommerce_price_slider_params.max_price, 10);
	}
    }

    //***

    jQuery(document.body).bind('price_slider_create price_slider_slide', function (event, min, max) {

	if (typeof woocs_current_currency !== 'undefined') {
	    var label_min = min;
	    var label_max = max;

	    if (woocs_current_currency.rate !== 1) {
		label_min = Math.ceil(label_min * parseFloat(woocs_current_currency.rate));
		label_max = Math.ceil(label_max * parseFloat(woocs_current_currency.rate));
	    }

	    //+++
	    label_min = number_format(label_min, 2, '.', ',');
	    label_max = number_format(label_max, 2, '.', ',');
	    if (jQuery.inArray(woocs_current_currency.name, woocs_array_no_cents) || woocs_current_currency.hide_cents == 1) {
		label_min = label_min.replace('.00', '');
		label_max = label_max.replace('.00', '');
	    }
	    //+++

	    if (woocs_current_currency.position === 'left') {

		jQuery('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + label_min);
		jQuery('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + label_max);

	    } else if (woocs_current_currency.position === 'left_space') {

		jQuery('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + " " + label_min);
		jQuery('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + " " + label_max);

	    } else if (woocs_current_currency.position === 'right') {

		jQuery('.price_slider_amount span.from').html(label_min + woocommerce_price_slider_params.currency_symbol);
		jQuery('.price_slider_amount span.to').html(label_max + woocommerce_price_slider_params.currency_symbol);

	    } else if (woocs_current_currency.position === 'right_space') {

		jQuery('.price_slider_amount span.from').html(label_min + " " + woocommerce_price_slider_params.currency_symbol);
		jQuery('.price_slider_amount span.to').html(label_max + " " + woocommerce_price_slider_params.currency_symbol);

	    }

	} else {

	    if (woocommerce_price_slider_params.currency_pos === 'left') {

		jQuery('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + min);
		jQuery('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + max);

	    } else if (woocommerce_price_slider_params.currency_pos === 'left_space') {

		jQuery('.price_slider_amount span.from').html(woocommerce_price_slider_params.currency_symbol + ' ' + min);
		jQuery('.price_slider_amount span.to').html(woocommerce_price_slider_params.currency_symbol + ' ' + max);

	    } else if (woocommerce_price_slider_params.currency_pos === 'right') {

		jQuery('.price_slider_amount span.from').html(min + woocommerce_price_slider_params.currency_symbol);
		jQuery('.price_slider_amount span.to').html(max + woocommerce_price_slider_params.currency_symbol);

	    } else if (woocommerce_price_slider_params.currency_pos === 'right_space') {

		jQuery('.price_slider_amount span.from').html(min + ' ' + woocommerce_price_slider_params.currency_symbol);
		jQuery('.price_slider_amount span.to').html(max + ' ' + woocommerce_price_slider_params.currency_symbol);

	    }
	}

	jQuery(document.body).trigger('price_slider_updated', [min, max]);
    });

    jQuery('.price_slider').slider({
	range: true,
	animate: true,
	min: min_price,
	max: max_price,
	values: [current_min_price, current_max_price],
	create: function () {

	    jQuery('.price_slider_amount #min_price').val(current_min_price);
	    jQuery('.price_slider_amount #max_price').val(current_max_price);

	    jQuery(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
	},
	slide: function (event, ui) {

	    jQuery('input#min_price').val(ui.values[0]);
	    jQuery('input#max_price').val(ui.values[1]);

	    jQuery(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
	},
	change: function (event, ui) {
	    jQuery(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
	}
    });


    //***
    woof_init_native_woo_price_filter();
}

function woof_init_toggles() {
	jQuery('.woof_front_toggle').life('click', function () {
		if (jQuery(this).data('condition') == 'opened') {
		    jQuery(this).removeClass('woof_front_toggle_opened');
		    jQuery(this).addClass('woof_front_toggle_closed');
		    jQuery(this).data('condition', 'closed');
		    if (woof_toggle_type == 'text') {
			jQuery(this).text(woof_toggle_closed_text);
		    } else {
			jQuery(this).find('img').prop('src', woof_toggle_closed_image);
		    }
		} else {
		    jQuery(this).addClass('woof_front_toggle_opened');
		    jQuery(this).removeClass('woof_front_toggle_closed');
		    jQuery(this).data('condition', 'opened');
		    if (woof_toggle_type == 'text') {
			jQuery(this).text(woof_toggle_opened_text);
		    } else {
			jQuery(this).find('img').prop('src', woof_toggle_opened_image);
		    }
		}


		jQuery(this).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
		return false;
    });
}

