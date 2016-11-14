<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! etheme_categories
// **********************************************************************// 

function etheme_categories_shortcode($atts) {
    global $woocommerce_loop;

    extract( shortcode_atts( array(
        'number'     => null,
        'title'      => '',
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => 1,
        'columns' => 3,
        'parent'     => '',
        'display_type' => 'grid',
        'valign' => 'center',
        'no_space' => 0,
        'text_color' => 'white',
        'style' => 'default',
        'ids'        => '',
        'large' => 4,
        'notebook' => 3,
        'tablet_land' => 2,
        'tablet_portrait' => 2,
        'mobile' => 1,
        'slider_autoplay' => false,
        'slider_speed' => 10000,
        'hide_pagination' => false,
        'hide_buttons' => false,
        'class'      => ''
    ), $atts ) );

    if ( isset( $atts[ 'ids' ] ) ) {
        $ids = explode( ',', $atts[ 'ids' ] );
        $ids = array_map( 'trim', $ids );
    } else {
        $ids = array();
    }

    $title_output = '';

    if($title != '') {
        $title_output = '<h3 class="title"><span>' . $title . '</span></h3>';
    }

    $hide_empty = ( $hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

    // get terms and workaround WP bug with parents/pad counts
    $args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
        'include'    => $ids,
        'pad_counts' => true,
        'child_of'   => $parent
    );

    $product_categories = get_terms( 'product_cat', $args );

    if ( $parent !== "" ) {
        $product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
    }

    if ( $hide_empty && ! is_wp_error( $product_categories ) ) {
        foreach ( $product_categories as $key => $category ) {
            if ( $category->count == 0 ) {
                unset( $product_categories[ $key ] );
            }
        }
    }

    if ( $number ) {
        $product_categories = array_slice( $product_categories, 0, $number );
    }

    //$woocommerce_loop['columns'] = $columns;



    $box_id = rand(1000,10000);

    ob_start();

    // Reset loop/columns globals when starting a new loop
    $woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';

    $woocommerce_loop['display_type'] = $display_type;
    if(! empty( $atts['columns'] ) ) 
      $woocommerce_loop['categories_columns'] = $atts['columns'];

    if ( $product_categories ) {

        
        if($display_type == 'menu') {
        	$instance = array(
        		'title' => $title,
        		'hierarchical' => 1,
	            'orderby'    => $orderby,
	            'order'      => $order,
	            'hide_empty' => $hide_empty,
	            'include'    => $ids,
	            'pad_counts' => true,
	            'child_of'   => $parent
        	);
        	$args = array();
            echo '<div class="categories-menu-element '.$class.'">';
        	the_widget( 'WC_Widget_Product_Categories', $instance, $args );
            echo '</div>';
        } else {

            if($display_type == 'slider') {
                $class .= 'categoriesCarousel owl-carousel carousel-area';
            } else {
                $class .= 'categories-grid row';
            }

            $class .= ($no_space) ? ' no-space' : '';

            $styles = array();

            $styles['style'] = $style;
            $styles['text_color'] = $text_color;
            $styles['valign'] = $valign;

        	echo $title_output;

            echo '<div class="'.$class.' slider-'.$box_id.'">';

            foreach ( $product_categories as $category ) {

                wc_get_template( 'content-product_cat.php', array(
                    'category' => $category,
                    'styles' => $styles
                ) );

            }

            echo '</div>';
            
        }


        if($display_type == 'slider') {
            echo '
                <script type="text/javascript">
                    (function() {
                        var options = {
                            items:5,
                            autoPlay: ' . (($slider_autoplay == "yes") ? $slider_speed : "false" ). ',
                            pagination: ' . (($hide_pagination == "yes") ? "false" : "true") . ',
                            navigation: ' . (($hide_buttons == "yes") ? "false" : "true" ). ',
                            navigationText:false,
                            rewindNav: ' . (($slider_autoplay == "yes") ? "true" : "false" ). ',
                            itemsCustom: [[0, ' . esc_js($mobile) . '], [479, ' . esc_js($tablet_portrait) . '], [619, ' . esc_js($tablet_portrait) . '], [768, ' . esc_js($tablet_land) . '],  [1200, ' . esc_js($notebook) . '], [1600, ' . esc_js($large) . ']]
                        };

                        jQuery(".slider-'.$box_id.'").owlCarousel(options);

                        var owl = jQuery(".slider-'.$box_id.'").data("owlCarousel");

                        jQuery( window ).bind( "vc_js", function() {
                            owl.reinit(options);
                        } );
                    })();
                </script>
            ';
        }

    }

    woocommerce_reset_loop();

    return ob_get_clean();
}

// **********************************************************************// 
// ! Register New Element: scslug
// **********************************************************************//
add_action( 'init', 'etheme_register_etheme_categories');
if(!function_exists('etheme_register_etheme_categories')) {
  if( class_exists('Vc_Vendor_Woocommerce')) {
    $Vc_Vendor_Woocommerce = new Vc_Vendor_Woocommerce();
    add_filter( 'vc_autocomplete_etheme_categories_ids_callback', array($Vc_Vendor_Woocommerce, 'productCategoryCategoryAutocompleteSuggester', ), 10, 1 ); // Get suggestion(find). Must return an array
    add_filter( 'vc_autocomplete_etheme_categories_ids_render', array($Vc_Vendor_Woocommerce, 'productCategoryCategoryRenderByIdExact',), 10, 1 ); // Render exact category by id. Must return an array (label,value)
  }

	function etheme_register_etheme_categories() {
		if(!function_exists('vc_map')) return;
      $order_by_values = array(
        '',
        esc_html__( 'ID', 'xstore' ) => 'ID',
        esc_html__( 'Title', 'xstore' ) => 'name',
        esc_html__( 'Modified', 'xstore' ) => 'modified',
        esc_html__( 'Products count', 'xstore' ) => 'count',
          esc_html__( 'As IDs provided order', 'xstore' ) => 'include',
      );

      $order_way_values = array(
        '',
        esc_html__( 'Descending', 'xstore' ) => 'DESC',
        esc_html__( 'Ascending', 'xstore' ) => 'ASC',
      );
      
	    $params = array(
	      'name' => '[8theme] Product categories',
	      'base' => 'etheme_categories',
	      'icon' => 'icon-wpb-etheme',
        'icon' => ETHEME_CODE_IMAGES . 'vc/el-categories.png',
	      'category' => 'Eight Theme',
	      'params' => array_merge(array(
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Title", 'xstore'),
	          "param_name" => "title"
	        ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Number of categories", 'xstore'),
	          "param_name" => "number"
	        ),
            array(
              'type' => 'autocomplete',
              'heading' => esc_html__( 'Categories', 'xstore' ),
              'param_name' => 'ids',
              'settings' => array(
                'multiple' => true,
                'sortable' => true,
              ),
              'save_always' => true,
              'description' => esc_html__( 'List of product categories', 'xstore' ),
            ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Parent ID", 'xstore'),
	          "param_name" => "parent",
              "description" => esc_html__('Get direct children of this term (only terms whose explicit parent is this value). If 0 is passed, only top-level terms are returned. Default is an empty string.', 'xstore')
		    ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Style", 'xstore'),
              "param_name" => "style",
              "value" => array( 
                  'Default' => 'default',
                  'Title with background' => 'with-bg',
                  'Zoom' => 'zoom',
                  'Diagonal' => 'diagonal',
                  'Classic' => 'classic',
                ),
            ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Text color", 'xstore'),
              "param_name" => "text_color",
              "value" => array(
                  '' => '',
                  'White' => 'white',
                  'Dark' => 'dark',
                ),
            ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Vertical align for text", 'xstore'),
              "param_name" => "valign",
              "value" => array( 
                  'Center' => 'center',
                  'Top' => 'top',
                  'Bottom' => 'bottom',
                ),
            ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Display type", 'xstore'),
              "param_name" => "display_type",
              "value" => array( 
                  esc_html__("Grid", 'xstore') => 'grid',
                  esc_html__("Slider", 'xstore') => 'slider',
                  esc_html__("Menu", 'xstore') => 'menu'
                )
            ),
            array(
              "type" => "dropdown",
              "heading" => esc_html__("Columns", 'xstore'),
              "param_name" => "columns",
              "value" => array( 
                  esc_html__("2", 'xstore') => 2,
                  esc_html__("3", 'xstore') => 3,
                  esc_html__("4", 'xstore') => 4,
                  esc_html__("5", 'xstore') => 5,
                  esc_html__("6", 'xstore') => 6,
                ),
              "dependency" => array('element' => "display_type", 'value' => array('grid'))
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__( 'Remove space between items', 'xstore' ),
                'param_name' => 'no_space',
                'value' => 1,
            ),
            array(
              'type' => 'dropdown',
              'heading' => esc_html__( 'Order by', 'xstore' ),
              'param_name' => 'orderby',
              'value' => $order_by_values,
              'save_always' => true,
              'description' => sprintf( esc_html__( 'Select how to sort retrieved products. More at %s.', 'xstore' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
            ),
            array(
              'type' => 'dropdown',
              'heading' => esc_html__( 'Sort order', 'xstore' ),
              'param_name' => 'order',
              'value' => $order_way_values,
              'save_always' => true,
              'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'xstore' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
            ),
	        array(
	          "type" => "textfield",
	          "heading" => esc_html__("Extra Class", 'xstore'),
	          "param_name" => "class"
	        )
          ), etheme_get_slider_params())
	    );  
	
	    vc_map($params);
	}
}
