<?php

    $title = $el_class = '';
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    extract( $atts );

    $el_class = $this->getExtraClass( $el_class );

    // Extract tab titles
    preg_match_all( '/et_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
    $tab_titles = array();

    if ( isset( $matches[1] ) ) {
      $tab_titles = $matches[1];
    }
    $tabs_nav = '';
    $tabs_nav .= '<ul class="tabs-nav">';
    foreach ( $tab_titles as $tab ) {
      $tab_atts = shortcode_parse_atts( $tab[0] );
      if ( isset( $tab_atts['title'] ) ) {
        $tab_id = ( isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : sanitize_title( $tab_atts['title'] ) );
        $image = '';
        if( isset( $tab_atts['img'] ) ) {
          $size = isset( $tab_atts['img_size'] ) ? $tab_atts['img_size'] : 'full';
          $image = etheme_get_image($tab_atts['img'], $size );
        }
        $tabs_nav .= '<li>
                        <a href="#' . $tab_id . '" id="' . $tab_id . '" class="tab-title">' . $image . '<span>' . $tab_atts['title'] . '</span></a>
                      </li>';
      }
    }
    $tabs_nav .= '</ul>';

    $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim( ' ' . $el_class ), $this->settings['base'], $atts );

    $output = '
      <div class="et-tabs-wrapper ' . $css_class . '">
        <div class="tabs">' 
      . $tabs_nav
      . '<div class="tab-contents">'
      . wpb_js_remove_wpautop( $content ) .
       '</div>
       </div>
      </div>
    ';

    echo $output;