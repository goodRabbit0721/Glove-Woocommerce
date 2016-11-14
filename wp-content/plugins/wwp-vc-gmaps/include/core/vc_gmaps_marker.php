<?php

add_action('init', 'create_gmaps_marker');
add_shortcode( 'wwp_vc_gmaps_marker', 'wwp_vc_gmaps_marker');

function create_gmaps_marker()
{
    if (!function_exists('vc_map'))
    {
        return;
    }

    vc_map(array(
        "name" => 'GMAPS Marker',
        'as_child' => array( 'only' => 'wwp_vc_gmaps' ),
        "base" => "wwp_vc_gmaps_marker",
        'content_element' => true,
        'icon' => 'marker',
        "show_settings_on_create" => true,
        "description" => __("Allows you to add markers on GMAPS for Visual Composer."),
        "category" => wwp_vc_gmaps_name,
        'params' => array(
            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => __("Location Type"),
                "param_name" => "location_type",
                "admin_label" => false,
                "value" => array( __("Coordinates") => "coordinates", __("Location") => "location"),
                "group" => "Location",
                "std" => "coordinates",
            ),
            array(
                "type" => "textfield",
                "class" => "",
                "heading" => __("Latitude"),
                "param_name" => "lat",
                "admin_label" => true,
                "value" => "",
                "group" => "Location",
                "dependency" => array('element'=>'location_type','value'=>"coordinates"),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                "type" => "textfield",
                "class" => "",
                "heading" => __("Longitude"),
                "param_name" => "lng",
                "admin_label" => true,
                "value" => "",
                "group" => "Location",
                "dependency" => array('element'=>'location_type','value'=>"coordinates"),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),

            array(
                "type" => "textfield",
                "class" => "",
                "heading" => __("Location"),
                "param_name" => "map_location",
                "admin_label" => true,
                "value" => "",
                "group" => "Location",
                "dependency" => array('element'=>'location_type','value'=>"location"),
            ),

            array(
                "type" => "textfield",
                "class" => "",
                "heading" => __("Location friendly name"),
                "param_name" => "marker_friendly_name",
                "group" => "Location",
                "description" => "This field is used when listing locations under the map",
            ),

            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => __("Marker Icon"),
                "param_name" => "marker_icon_option",
                "admin_label" => false,
                "value" => array( __("Predefined") => "predefined", __("Custom") => "custom"),
                "group" => "Marker",
                "std" => "predefined",
            ),
            array(
                "type" => "marker_icons",
                "class" => "",
                "heading" => __("Selected Marker Icon"),
                "param_name" => "predefined_marker_icon",
                "admin_label" => true,
                "value" => "blue",
                "group" => "Marker",
                "dependency" => array('element'=>'marker_icon_option','value'=>"predefined"),
            ),
            array(
                "type" => "attach_image",
                "class" => "",
                "heading" => __("Custom Marker Icon"),
                "param_name" => "pin_icon",
                "admin_label" => false,
                "group" => "Marker",
                "dependency" => array('element'=>'marker_icon_option','value'=>"custom"),
            ),

            array(
                "type" => "textarea_raw_html",
                "class" => "",
                "heading" => __("Marker Description"),
                "description" => "On click marker description",
                "param_name" => "marker_description",
                "admin_label" => false,
                "value" => "",
                "group" => "Marker"
            ),

            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => __("Marker Animation"),
                "param_name" => "marker_animation",
                "admin_label" => false,
                "value" => array( __("Drop") => "DROP", __("Bounce") => "BOUNCE"),
                "group" => "Marker",
                "std" => "DROP",
            ),
        )
    ));
}

function wwp_vc_gmaps_marker($atts, $content = null)
{
    global $WWP_GMAPS_SHORTCODE;

    extract(shortcode_atts(array(
        "lat" => "",
        "lng" => "",
        "marker_icon_option" => "predefined",
        "pin_icon" => "",
        "predefined_marker_icon" => "blue",
        "marker_description" => "",
        "marker_animation" => "DROP",
        "map_location" => "",
        "location_type" => "coordinates",
        "marker_friendly_name" => ""
    ), $atts));

    if($marker_icon_option == 'predefined')
    {
        $pin_icon = plugins_url( 'img/pins/pin_'.$predefined_marker_icon, __FILE__ ).'.png';
    }

    if($location_type == "location")
    {
        $response = wp_remote_get( 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($map_location) . '&sensor=false' );

        if ("OK" !== wp_remote_retrieve_response_message($response) || 200 !== wp_remote_retrieve_response_code($response))
        {
            return '';
        }

        $location_json = wp_remote_retrieve_body( $response );
        $location_data = json_decode( $location_json );

        if (isset($location_data->results[0]->geometry->location))
        {
            $lat = $location_data->results[0]->geometry->location->lat;
            $lng = $location_data->results[0]->geometry->location->lng;
        }
    }

    $marker_options = array(
        'lat' => $lat,
        'lng' => $lng,
        'icon_url' => $pin_icon,
        'marker_type' => $marker_icon_option,
        'description' => rawurldecode(base64_decode(strip_tags($marker_description))),
        'animation' => $marker_animation,
        'marker_friendly_name' => $marker_friendly_name,
    );

    $WWP_GMAPS_SHORTCODE['markers'][] = $marker_options;
}

