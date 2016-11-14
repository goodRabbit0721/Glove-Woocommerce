var location_name = '';

jQuery(document).ready(function()
{
    var all_markers = [];

    var Base64 = {

        _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        encode : function (input)
        {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length)
            {
                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);
                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2))
                {
                    enc3 = enc4 = 64;

                }
                else if (isNaN(chr3))
                {
                    enc4 = 64;
                }

                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
            }
            return output;
        },

        decode : function (input)
        {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length)
            {
                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));
                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;
                output = output + String.fromCharCode(chr1);

                if (enc3 != 64)
                {
                    output = output + String.fromCharCode(chr2);
                }

                if (enc4 != 64)
                {
                    output = output + String.fromCharCode(chr3);
                }
            }
            output = Base64._utf8_decode(output);
            return output;
        },

        _utf8_encode : function (string)
        {

            string = string.replace(/\r\n/g,"\n");

            var utftext = "";

            for (var n = 0; n < string.length; n++)
            {
                var c = string.charCodeAt(n);

                if (c < 128)
                {
                    utftext += String.fromCharCode(c);
                }

                else if((c > 127) && (c < 2048))
                {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else
                {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
            }

            return utftext;
        },

        _utf8_decode : function (utftext)
        {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while ( i < utftext.length )
            {
                c = utftext.charCodeAt(i);

                if (c < 128)
                {
                    string += String.fromCharCode(c);
                    i++;
                }

                else if((c > 191) && (c < 224))
                {
                    c2 = utftext.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }

                else
                {
                    c2 = utftext.charCodeAt(i+1);
                    c3 = utftext.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }
            }

            return string;
        }

    };

    function getBool(val)
    {
        var num = +val;
        return !isNaN(num) ? !!num : !!String(val).toLowerCase().replace(!!0,'');
    }

    function addInfoWindow(map, marker, message)
    {
        var infoWindow = new google.maps.InfoWindow(
        {
            content: message
        });

        google.maps.event.addListener(marker, "click", function ()
        {
            infoWindow.open(map, marker);
        });
    }

    function showLocationName(latlng, location_name_id, callback)
    {
        var geocoder = new google.maps.Geocoder,
            location_name = '';

        geocoder.geocode({'location': latlng}, function(results, status)
        {
            if(status === 'OK')
            {
                if (results[1])
                {
                    location_name = results[1].formatted_address;
                }
            }
            else
            {
                if(status === "OVER_QUERY_LIMIT")
                {
                    setTimeout(function()
                    {
                        showLocationName(latlng, location_name_id, callback);
                    }, 100);
                }
            }

            callback(location_name, location_name_id);
        });
    }

    function setMarkers(map, places, markers_cluster, all_markers, mapID, showLocationsBelowMap, showLocationImage, showLocationAddress, locationStyling)
    {
        var bounds = new google.maps.LatLngBounds();

        for (var i = 0; i < places.length; i++)
        {
            var place = places[i],
                image = "",
                location_name_id = '';

            if(place['pin_path'])
            {
                image = {
                    url: place['pin_path'],
                    size: new google.maps.Size(place['pin_width'], place['pin_height']),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(place['pin_width']/2, place['pin_height'])
                };
            }

            var myLatLng = new google.maps.LatLng(place['lat'], place['lng']),
                marker = new google.maps.Marker(
                {
                    position: myLatLng,
                    icon: image,
                    map: map,
                    zIndex: 1
                });

            if(showLocationsBelowMap === "yes")
            {
                var latlng = {lat: parseFloat(place['lat']), lng: parseFloat(place['lng'])};
                location_name_id = mapID+'-'+i;



                var show_location_image = '',
                    show_location_address = '';

                if(showLocationImage === "yes")
                {
                    show_location_image = '<div class="marker-location-image"><img src="'+place['pin_path']+'"></div>';
                }

                if(showLocationAddress === "yes")
                {
                    show_location_address = '<div class="marker-location-name"><span class="add-location-name-'+location_name_id+'"></span></div>';
                }

                var add_location_styling_class = '';

                if(locationStyling == 'rounded_border')
                {
                    add_location_styling_class = 'marker-location-rounded';
                }

                if(locationStyling == 'square_border')
                {
                    add_location_styling_class = 'marker-location-square';
                }


                jQuery('#wwp-gmaps-locations-'+mapID).append('<div class="marker-location-wrapper '+add_location_styling_class+'"><a class="marker-location-link" data-mapid="'+mapID+'" data-markerid="' + i + '" href="#">'+show_location_image+show_location_address+'</a></div>');

                if(place['marker_friendly_name'] === '')
                {
                    showLocationName(latlng, location_name_id, function (result, location_name_id)
                    {
                        if(result)
                        {
                            jQuery('.add-location-name-' + location_name_id).html(result);
                        }
                    });
                }
                else
                {
                    jQuery('.add-location-name-' + location_name_id).html(place['marker_friendly_name']);
                }
                
                all_markers[mapID].push(marker);
            }

            markers_cluster.push(marker);

            if(place['animation'] == "DROP")
            {
                marker.setAnimation(google.maps.Animation.DROP);
            }
            else
            {
                marker.setAnimation(google.maps.Animation.BOUNCE);

            }

            bounds.extend(marker.position);

            if(decodeURIComponent(place['description']) !== "")
            {
                addInfoWindow(map, marker, Base64.decode(place['description']));
            }
        }

        map.fitBounds(bounds);
    }

    function addLockBtn(map)
    {
        var draggable_icon_class = "";

        if (map.get("draggable") == false)
        {
            draggable_icon_class = "fa fa-lock";
        }
        else
        {
            draggable_icon_class = "fa fa-unlock";
        }

        var lock_btn = jQuery('<div class="lock-button-gmaps"><i class="fa '+draggable_icon_class+'"></i></div>');

        lock_btn.bind("click", function()
        {
            if (map.get("draggable"))
            {
                map.set("draggable", false);
                jQuery(".lock-button-gmaps i").removeClass("fa-unlock");
                jQuery(".lock-button-gmaps i").addClass("fa-lock");
            }
            else
            {
                map.set("draggable", true);
                jQuery(".lock-button-gmaps i").removeClass("fa-lock");
                jQuery(".lock-button-gmaps i").addClass("fa-unlock");
            }
        });

        return lock_btn[0];
    }

    jQuery('.wwp-vc-gmaps-map').each(function()
    {
        var instance = jQuery(this).data('instance');
        setGMAPS(instance);
    });


    function setGMAPS(instance)
    {
        var settingObj = window[instance];

        var markers_cluster = [];

        all_markers[settingObj.mapID] = [];

        var custom_map_styles =
        {
            apple_maps: [{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}],
            light_gray: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}],
            dark: [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"landscape","elementType":"labels.icon","stylers":[{"saturation":"-100"},{"lightness":"-54"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"on"},{"lightness":"0"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"saturation":"-89"},{"lightness":"-55"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"transit.station","elementType":"labels.icon","stylers":[{"visibility":"on"},{"saturation":"-100"},{"lightness":"-51"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}],
            neutral_blue: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#193341"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#2c5a71"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#29768a"},{"lightness":-37}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#3e606f"},{"weight":2},{"gamma":0.84}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"weight":0.6},{"color":"#1a3541"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#2c5a71"}]}],
            orange_ocean: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#edc200"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"},{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#f5a301"},{"visibility":"on"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]}],
            magenta: [{"featureType":"all","elementType":"geometry.stroke","stylers":[{"visibility":"simplified"}]},{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#a31645"}]},{"featureType":"landscape","elementType":"all","stylers":[{"weight":"3.79"},{"visibility":"on"},{"color":"#ffecf0"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry.stroke","stylers":[{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#a31645"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"saturation":"0"},{"lightness":"0"},{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#d89ca8"}]},{"featureType":"poi.business","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"poi.business","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"saturation":"0"}]},{"featureType":"poi.business","elementType":"labels","stylers":[{"color":"#a31645"}]},{"featureType":"poi.business","elementType":"labels.icon","stylers":[{"visibility":"simplified"},{"lightness":"84"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#d89ca8"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#fedce3"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]}],
            flat_map: [{"featureType":"all","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"on"},{"color":"#f3f4f4"}]},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"weight":0.9},{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#83cead"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"},{"color":"#ffffff"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"on"},{"color":"#fee379"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"on"},{"color":"#fee379"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#7fc8ed"}]}],
            winter: [{"stylers":[{"hue":"#007fff"},{"saturation":89}]},{"featureType":"water","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"off"}]}],
        };

        var streetview_control_position = google.maps.ControlPosition.RIGHT_BOTTOM;
        var zoom_control_position = google.maps.ControlPosition.RIGHT_BOTTOM;
        var map_type_control_position = google.maps.ControlPosition.LEFT_TOP;

        // Streetview Control
        if(settingObj.streetview_control_position === 'TOP_LEFT')
        {
            streetview_control_position = google.maps.ControlPosition.TOP_LEFT;
        }

        if(settingObj.streetview_control_position === 'TOP_CENTER')
        {
            streetview_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.streetview_control_position === 'TOP_RIGHT')
        {
            streetview_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.streetview_control_position === 'BOTTOM_LEFT')
        {
            streetview_control_position = google.maps.ControlPosition.BOTTOM_LEFT;
        }

        if(settingObj.streetview_control_position === 'BOTTOM_RIGHT')
        {
            streetview_control_position = google.maps.ControlPosition.BOTTOM_RIGHT;
        }

        if(settingObj.streetview_control_position === 'LEFT_TOP')
        {
            streetview_control_position = google.maps.ControlPosition.LEFT_TOP;
        }

        if(settingObj.streetview_control_position === 'LEFT_CENTER')
        {
            streetview_control_position = google.maps.ControlPosition.LEFT_CENTER;
        }

        if(settingObj.streetview_control_position === 'LEFT_BOTTOM')
        {
            streetview_control_position = google.maps.ControlPosition.LEFT_BOTTOM;
        }

        if(settingObj.streetview_control_position === 'RIGHT_TOP')
        {
            streetview_control_position = google.maps.ControlPosition.RIGHT_TOP;
        }

        if(settingObj.streetview_control_position === 'RIGHT_CENTER')
        {
            streetview_control_position = google.maps.ControlPosition.RIGHT_CENTER;
        }

        if(settingObj.streetview_control_position === 'RIGHT_BOTTOM')
        {
            streetview_control_position = google.maps.ControlPosition.RIGHT_BOTTOM;
        }

        // Zoom Control
        if(settingObj.zoom_control_position === 'TOP_LEFT')
        {
            zoom_control_position = google.maps.ControlPosition.TOP_LEFT;
        }

        if(settingObj.zoom_control_position === 'TOP_CENTER')
        {
            zoom_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.zoom_control_position === 'TOP_RIGHT')
        {
            zoom_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.zoom_control_position === 'BOTTOM_LEFT')
        {
            zoom_control_position = google.maps.ControlPosition.BOTTOM_LEFT;
        }

        if(settingObj.zoom_control_position === 'BOTTOM_RIGHT')
        {
            zoom_control_position = google.maps.ControlPosition.BOTTOM_RIGHT;
        }

        if(settingObj.zoom_control_position === 'LEFT_TOP')
        {
            zoom_control_position = google.maps.ControlPosition.LEFT_TOP;
        }

        if(settingObj.zoom_control_position === 'LEFT_CENTER')
        {
            zoom_control_position = google.maps.ControlPosition.LEFT_CENTER;
        }

        if(settingObj.zoom_control_position === 'LEFT_BOTTOM')
        {
            zoom_control_position = google.maps.ControlPosition.LEFT_BOTTOM;
        }

        if(settingObj.zoom_control_position === 'RIGHT_TOP')
        {
            zoom_control_position = google.maps.ControlPosition.RIGHT_TOP;
        }

        if(settingObj.zoom_control_position === 'RIGHT_CENTER')
        {
            zoom_control_position = google.maps.ControlPosition.RIGHT_CENTER;
        }

        if(settingObj.zoom_control_position === 'RIGHT_BOTTOM')
        {
            zoom_control_position = google.maps.ControlPosition.RIGHT_BOTTOM;
        }

        // Map Type
        if(settingObj.map_type_control_position === 'TOP_LEFT')
        {
            map_type_control_position = google.maps.ControlPosition.TOP_LEFT;
        }

        if(settingObj.map_type_control_position === 'TOP_CENTER')
        {
            map_type_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.map_type_control_position === 'TOP_RIGHT')
        {
            map_type_control_position = google.maps.ControlPosition.TOP_CENTER;
        }

        if(settingObj.map_type_control_position === 'BOTTOM_LEFT')
        {
            map_type_control_position = google.maps.ControlPosition.BOTTOM_LEFT;
        }

        if(settingObj.map_type_control_position === 'BOTTOM_RIGHT')
        {
            map_type_control_position = google.maps.ControlPosition.BOTTOM_RIGHT;
        }

        if(settingObj.map_type_control_position === 'LEFT_TOP')
        {
            map_type_control_position = google.maps.ControlPosition.LEFT_TOP;
        }

        if(settingObj.map_type_control_position === 'LEFT_CENTER')
        {
            map_type_control_position = google.maps.ControlPosition.LEFT_CENTER;
        }

        if(settingObj.map_type_control_position === 'LEFT_BOTTOM')
        {
            map_type_control_position = google.maps.ControlPosition.LEFT_BOTTOM;
        }

        if(settingObj.map_type_control_position === 'RIGHT_TOP')
        {
            map_type_control_position = google.maps.ControlPosition.RIGHT_TOP;
        }

        if(settingObj.map_type_control_position === 'RIGHT_CENTER')
        {
            map_type_control_position = google.maps.ControlPosition.RIGHT_CENTER;
        }

        if(settingObj.map_type_control_position === 'RIGHT_BOTTOM')
        {
            map_type_control_position = google.maps.ControlPosition.RIGHT_BOTTOM;
        }

        var map_types = (settingObj.map_name_show).split(",");
        var mapOptions =
        {
            scrollwheel: false,
            draggable: getBool(settingObj.draggable),
            fullscreenControl: getBool(settingObj.fullscreenControl),
            streetViewControl: getBool(settingObj.streetViewControl),
            streetViewControlOptions:
            {
                position: streetview_control_position
            },
            zoomControl: getBool(settingObj.zoomControl),
            zoomControlOptions:
            {
                position: zoom_control_position
            },
            mapTypeControl: getBool(settingObj.mapTypeControl),
            mapTypeControlOptions:
            {
                mapTypeIds: map_types,
                position: map_type_control_position
            }
        };

        var map = new google.maps.Map(document.getElementById(settingObj.mapID), mapOptions);

        map.setMapTypeId(google.maps.MapTypeId.HYBRID);

        if(settingObj.custom_map_style === 'custom')
        {
            var customStyledMap = new google.maps.StyledMapType(JSON.parse(Base64.decode(settingObj.styles)), { name: settingObj.map_name });
            map.mapTypes.set('map_style', customStyledMap);
            map.setMapTypeId('map_style');

        }
        else
        {
            var predefinedStyledMap = new google.maps.StyledMapType(custom_map_styles[settingObj.custom_map_style], { name: settingObj.map_name });
            map.mapTypes.set("map_style", predefinedStyledMap);
            map.setMapTypeId("map_style");
        }

        var places = jQuery.parseJSON(settingObj.add_places);

        setMarkers(map, places, markers_cluster, all_markers, settingObj.mapID, settingObj.show_locations_below_map, settingObj.show_location_marker, settingObj.show_location_address, settingObj.show_location_styling);

        if(settingObj.enable_marker_clustering === "yes")
        {
            var mcOptions =
            {
                imagePath: settingObj.marker_clustering_images_path
            };

            var mc = new MarkerClusterer(map, markers_cluster, mcOptions);
        }

        if(settingObj.draggingControl === "true")
        {
            if(settingObj.dragging_control_position === 'TOP_LEFT')
            {
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'TOP_CENTER')
            {
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'TOP_RIGHT')
            {
                map.controls[google.maps.ControlPosition.TOP_RIGHT].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'BOTTOM_LEFT')
            {
                map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'BOTTOM_RIGHT')
            {
                map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'LEFT_TOP')
            {
                map.controls[google.maps.ControlPosition.LEFT_TOP].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'LEFT_CENTER')
            {
                map.controls[google.maps.ControlPosition.LEFT_CENTER].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'LEFT_BOTTOM')
            {
                map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'RIGHT_TOP')
            {
                map.controls[google.maps.ControlPosition.RIGHT_TOP].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'RIGHT_CENTER')
            {
                map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(addLockBtn(map));
            }

            if(settingObj.dragging_control_position === 'RIGHT_BOTTOM')
            {
                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(addLockBtn(map));
            }
        }

        if(settingObj.transitLayer === "true")
        {
            var transitLayer = new google.maps.TransitLayer();
            transitLayer.setMap(map);
        }

        if(settingObj.trafficLayer === "true")
        {
            var trafficlayer = new google.maps.TrafficLayer();
            trafficlayer.setMap(map);
        }

        if(settingObj.bicyclingLayer === "true")
        {
            var bicyclinglayer = new google.maps.BicyclingLayer();
            bicyclinglayer.setMap(map);
        }

        if(settingObj.disable_auto_zoom === "false")
        {
            var listener = google.maps.event.addListener(map, "idle", function()
            {
                if (map.getZoom() > parseInt(settingObj.zoom))
                {
                    map.setZoom(parseInt(settingObj.zoom));
                }

                google.maps.event.removeListener(listener);
            });
        }

        jQuery(document).on("click", ".marker-location-link", function(e)
        {
            e.preventDefault();

            google.maps.event.trigger(all_markers[jQuery(this).data('mapid')][jQuery(this).data('markerid')], 'click');
        });
    }


});