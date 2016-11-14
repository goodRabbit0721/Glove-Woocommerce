<?php

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
$this->resetVariables( $atts, $content );

$output = '';

$tab_id = ( isset( $atts['tab_id'] ) ? $atts['tab_id'] : sanitize_title( $atts['title'] ) );
$image = '';
if( isset( $atts['img'] ) ) {
  $size = isset( $atts['img_size'] ) ? $atts['img_size'] : 'full';
  $image = etheme_get_image($atts['img'], $size );
}

$output .= '<div class="accordion-title" data-id="' . $tab_id . '">';
$output .= $image;
$output .= $this->getTemplateVariable( 'heading' );
$output .= '</div>';
$output .= '<div id="content_' . $tab_id . '" class="et-tab">';
$output .= '<div class="et-tab-content">';
$output .= $this->getTemplateVariable( 'content' );
$output .= '</div>';
$output .= '</div>';

echo $output;