jQuery( window ).load(function() {
  
  container_width = jQuery('.container').width();
  document_width = jQuery(window).width();
  padding_left = (document_width-container_width)/2;
  jQuery('.product-profile-description').css('width', document_width);
  jQuery('.product-profile-description').css('margin-left', -padding_left);
 
});
jQuery( window ).resize(function() {
  
  container_width = jQuery('.container').width();
  document_width = jQuery(window).width();
  padding_left = (document_width-container_width)/2;
  jQuery('.product-profile-description').css('width', document_width);
  jQuery('.product-profile-description').css('margin-left', -padding_left);
 
});
jQuery(document).scroll(function(){
  var scrolltop_value = jQuery(document).scrollTop();
  var browser_width = jQuery(document).width();
  var product_div_heght= jQuery('.product-information').height();
  var default_value = 300;
  if (product_div_heght > 890 && product_div_heght < 930)
    default_value = 390;
  else if (product_div_heght > 935 && product_div_heght < 970)
    default_value = 410;
  else if (product_div_heght > 970 && product_div_heght < 1000)
    default_value = 440;
  else if (product_div_heght > 1000)
    default_value = 470;
  
  if (scrolltop_value < default_value && browser_width > 1024){
    jQuery(".product-scroll-down .images-wrapper").css({top: scrolltop_value + "px", "position": "absolute", "width" : "95%"});
  
  }

});

jQuery(document).ready(function(){
  
  /* Collection */
  if(!jQuery.cookie("collection_cookie")) jQuery.cookie("collection_cookie", "opened");
  if(jQuery.cookie("collection_cookie") == "closed") {
    var that = '.woof_container_collection .woof_front_toggle';
    jQuery(that).removeClass('woof_front_toggle_opened');
    jQuery(that).addClass('woof_front_toggle_closed');
    jQuery(that).data('condition', 'closed');
    if (woof_toggle_type == 'text') {
      jQuery(that).text(woof_toggle_closed_text);
    } else {
      jQuery(that).find('img').prop('src', woof_toggle_closed_image);
    }
    jQuery(that).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
  }

  /* Features */
  if(!jQuery.cookie("features_cookie")) jQuery.cookie("features_cookie", "opened");
  if(jQuery.cookie("features_cookie") == "closed") {
    var that = '.woof_container_features .woof_front_toggle';
    jQuery(that).removeClass('woof_front_toggle_opened');
    jQuery(that).addClass('woof_front_toggle_closed');
    jQuery(that).data('condition', 'closed');
    if (woof_toggle_type == 'text') {
      jQuery(that).text(woof_toggle_closed_text);
    } else {
      jQuery(that).find('img').prop('src', woof_toggle_closed_image);
    }
    jQuery(that).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
  }

  /* Palm Type */
  if(!jQuery.cookie("palm_type_cookie")) jQuery.cookie("palm_type_cookie", "opened");
  if(jQuery.cookie("palm_type_cookie") == "closed") {
    var that = '.woof_container_palmtype .woof_front_toggle';
    jQuery(that).removeClass('woof_front_toggle_opened');
    jQuery(that).addClass('woof_front_toggle_closed');
    jQuery(that).data('condition', 'closed');
    if (woof_toggle_type == 'text') {
      jQuery(that).text(woof_toggle_closed_text);
    } else {
      jQuery(that).find('img').prop('src', woof_toggle_closed_image);
    }
    jQuery(that).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
  }

  /* Color */
  if(!jQuery.cookie("color_cookie")) jQuery.cookie("color_cookie", "opened");
  if(jQuery.cookie("color_cookie") == "closed") {
    console.log('here color reload ');
    var that = '.woof_container_color .woof_front_toggle';
    jQuery(that).removeClass('woof_front_toggle_opened');
    jQuery(that).addClass('woof_front_toggle_closed');
    jQuery(that).data('condition', 'closed');
    if (woof_toggle_type == 'text') {
      jQuery(that).text(woof_toggle_closed_text);
    } else {
      jQuery(that).find('img').prop('src', woof_toggle_closed_image);
    }
    jQuery(that).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
  }

  /* Size */
  if(!jQuery.cookie("size_cookie")) jQuery.cookie("size_cookie", "opened");
  if(jQuery.cookie("size_cookie") == "closed") {
    var that = '.woof_container_size .woof_front_toggle';
    jQuery(that).removeClass('woof_front_toggle_opened');
    jQuery(that).addClass('woof_front_toggle_closed');
    jQuery(that).data('condition', 'closed');
    if (woof_toggle_type == 'text') {
      jQuery(that).text(woof_toggle_closed_text);
    } else {
      jQuery(that).find('img').prop('src', woof_toggle_closed_image);
    }
    jQuery(that).parents('.woof_container_inner').find('.woof_block_html_items').toggle(500);
  }
});


jQuery(document).on('click', '.woof_front_toggle', function () {
  var attribute_type_name = jQuery(this).parent().find('span').text().trim();
  
  /*attribute type is Collection*/
  if (attribute_type_name == 'Collection'){
    var collection_cookie = jQuery.cookie("collection_cookie");

    if (collection_cookie == 'closed') {
        jQuery.cookie("collection_cookie", "opened");
    } else {
        jQuery.cookie("collection_cookie", 'closed');
    }
  }

  /*attribute type is Features*/
  if (attribute_type_name == 'Features'){
    var features_cookie = jQuery.cookie("features_cookie");

    if (features_cookie == 'closed') {
        jQuery.cookie("features_cookie", "opened");
    } else {
        jQuery.cookie("features_cookie", 'closed');
    }
  }


  /*attribute type is palm type*/
  if (attribute_type_name == 'Palm Type'){
    var palm_type_cookie = jQuery.cookie("palm_type_cookie");

    if (palm_type_cookie == 'closed') {
        jQuery.cookie("palm_type_cookie", "opened");
    } else {
        jQuery.cookie("palm_type_cookie", 'closed');
    }
  }  

  /*attribute type is Color*/
  if (attribute_type_name == 'Color'){
    console.log('here color set cookie');
    var color_cookie = jQuery.cookie("color_cookie");

    if (color_cookie == 'closed') {
        jQuery.cookie("color_cookie", "opened");
    } else {
        jQuery.cookie("color_cookie", 'closed');
    }
  }  

  /*attribute type is Size*/
  if (attribute_type_name == 'Size'){
    var size_cookie = jQuery.cookie("size_cookie");

    if (size_cookie == 'closed') {
        jQuery.cookie("size_cookie", "opened");
    } else {
        jQuery.cookie("size_cookie", 'closed');
    }
  }  

});

/*&#174;*/