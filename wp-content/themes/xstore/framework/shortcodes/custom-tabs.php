<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! The tabs
// **********************************************************************// 

if( ! function_exists('vc_path_dir') ) return;

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );
VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_ET_Tabs extends WPBakeryShortCode_VC_Tta_Accordion {
  public function getFileName() {
    return 'et_tabs';
  }

}


VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_ET_Tab extends WPBakeryShortCode_VC_Tta_Accordion {
  protected $controls_css_settings = 'tc vc_control-container';
  protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
  protected $backened_editor_prepend_controls = false;

  public function getFileName() {
    return 'et_tab';
  }


  public function getParamHeading( $atts, $content ) {
    $output = '';
    $output .= $this->getTemplateVariable( 'title' );

    return $output;
  }

  public function getParamTitle( $atts, $content ) {
    if ( isset( $atts['title'] ) && strlen( $atts['title'] ) > 0 ) {
      return '<span>' . $atts['title'] . '</span>';
    }

    return null;
  }
}

// **********************************************************************// 
// ! Register New Element: The Looks
// **********************************************************************//
add_action( 'init', 'etheme_register_tabs');
if(!function_exists('etheme_register_tabs')) {
	function etheme_register_tabs() {
		if(!function_exists('vc_map')) return;

      $params_tabs = array(
        'name' => __( '[8THEME] Tabs', 'xstore' ),
        'base' => 'et_tabs',
        'icon' => ETHEME_CODE_IMAGES . 'vc/el-tabs.png',
        'is_container' => true,
        'show_settings_on_create' => false,
        'as_parent' => array(
          'only' => 'et_tab',
        ),
        'category' => 'Eight Theme', 
        'params' => array(
          array(
            'type' => 'textfield',
            'heading' => __( 'Extra class name', 'js_composer' ),
            'param_name' => 'el_class',
            'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
          ),
        ),
        'js_view' => 'VcBackendTtaTabsView',
        'custom_markup' => '
      <div class="vc_tta-container" data-vc-action="collapse">
        <div class="vc_general vc_tta vc_tta-tabs vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
          <div class="vc_tta-tabs-container">'
                           . '<ul class="vc_tta-tabs-list">'
                           . '<li class="vc_tta-tab et-tab-label" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="et_tab"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>'
                           . '</ul>
          </div>
          <div class="vc_tta-panels vc_clearfix {{container-class}}">
            {{ content }}
          </div>
        </div>
      </div>',
        'default_content' => '
      [et_tab title="' . sprintf( '%s %d', __( 'Tab', 'xstore' ), 1 ) . '"][/et_tab]
      [et_tab title="' . sprintf( '%s %d', __( 'Tab', 'xstore' ), 2 ) . '"][/et_tab]
        ',
        'admin_enqueue_js' => array(
          vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ),
        )
      );
	
      vc_map($params_tabs);

      $params_tab = array(
        'name' => __( 'Tab', 'xstore' ),
        'base' => 'et_tab',
        'icon' => 'icon-wpb-ui-tta-section',
        'allowed_container_element' => 'vc_row',
        'is_container' => true,
        'show_settings_on_create' => false,
        'as_child' => array(
          'only' => 'et_tabs',
        ),
        'category' => 'Eight Theme', 
        'params' => array(
            array(
              'type' => 'textfield',
              'param_name' => 'title',
              'heading' => __( 'Title', 'js_composer' ),
              'description' => __( 'Enter section title (Note: you can leave it empty).', 'js_composer' ),
            ),
            array(
              'type' => 'attach_image',
              "heading" => esc_html__("Tab Image", 'xstore'),
              "param_name" => "img",
              "group" => "Image",
            ),
            array(
              "type" => "textfield",
              "heading" => esc_html__("Image size", 'xstore' ),
              "param_name" => "img_size",
              "description" => esc_html__("Enter image size. Example in pixels: 200x100 (Width x Height).", 'xstore'),
              "group" => "Image",
            ),
            array(
              'type' => 'el_id',
              'param_name' => 'tab_id',
              'settings' => array(
                'auto_generate' => true,
              ),
              'heading' => __( 'Section ID', 'js_composer' ),
              'description' => __( 'Enter section ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ),
            ),
            array(
              'type' => 'textfield',
              'heading' => __( 'Extra class name', 'js_composer' ),
              'param_name' => 'el_class',
              'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
            ),
        ),
        'js_view' => 'VcBackendTtaSectionView',
        'custom_markup' => '
          <div class="vc_tta-panel-heading">
              <h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left"><a href="javascript:;" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-accordion data-vc-container=".vc_tta-container"><span class="vc_tta-title-text">{{ section_title }}</span><i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i></a></h4>
          </div>
          <div class="vc_tta-panel-body">
            {{ editor_controls }}
            <div class="{{ container-class }}">
            {{ content }}
            </div>
          </div>',
        'default_content' => '',
      );


	    vc_map($params_tab);
	}
}