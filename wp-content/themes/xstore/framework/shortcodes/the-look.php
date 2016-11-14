<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! The look
// **********************************************************************// 

function etheme_the_look_shortcode($atts, $content) {
    global $woocommerce_loop;

    if ( ! class_exists('Woocommerce') ) return false;

    $output = '';

    $atts = shortcode_atts(array(
        'post_type'  => 'product',
        'include'  => '',
        'custom_query'  => '',
        'taxonomies'  => '',
        'items_per_page'  => 10,
        'columns' => 3,
        'banner_double' => 0,
        'orderby'  => 'date',
        'order'  => 'DESC',
        'meta_key'  => '',
        'exclude'  => '',
        'class'  => '',
        'product_view' => '',
        'product_view_color' => '',
        'align'  => 'center',
        'valign'  => 'bottom',
        'link'  => '#',
        'img' => '',
        'img_size' => '360x790',
        'banner_pos' => 1,
        'css' => ''
    ), $atts);

    extract($atts);

    $paged = (get_query_var('page')) ? get_query_var('page') : 1;

    $args = array(
      'post_type' => 'product',
      'status' => 'published',
      'paged' => $paged,  
      'posts_per_page' => $items_per_page
    );

    if($post_type == 'ids' && $include != '') {
      $args['post__in'] = explode(',', $include);
      $orderby = 'post__in';
    }

    if(!empty( $exclude ) ) {
      $args['post__not_in'] = explode(',', $exclude);
    }


    if(!empty( $taxonomies )) {
      $terms = get_terms( array('product_cat', 'product_tag'), array(
        'orderby' => 'name',
        'include' => $taxonomies
      ));

      if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $args['tax_query'] = array('relation' => 'OR');
        foreach ($terms as $key => $term) {
          $args['tax_query'][] = array(
                'taxonomy' => $term->taxonomy,                //(string) - Taxonomy.
                'field' => 'slug',                    //(string) - Select taxonomy term by ('id' or 'slug')
                'terms' => array( $term->slug ),    //(int/string/array) - Taxonomy term(s).
                'include_children' => true,           //(bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
                'operator' => 'IN'  
          );
        }
      }
    }

    if(!empty( $order )) {
      $args['order'] = $order;
    }

    if(!empty( $meta_key )) {
      $args['meta_key'] = $meta_key;
    }

    if(!empty( $orderby )) {
      $args['orderby'] = $orderby;
    }

    $output = '';

    ob_start();

    $products = new WP_Query( $args );

    $class = $title_output = $images_class = '';

    $shop_url = get_permalink(woocommerce_get_page_id('shop'));

    $woocommerce_loop['columns'] = $columns;
    $woocommerce_loop['isotope'] = true;
    //$woocommerce_loop['size'] = 'shop_catalog_alt';
    $woocommerce_loop['product_view'] = $product_view;
    $woocommerce_loop['product_view_color'] = $product_view_color;

    if( ! empty($css) && function_exists( 'vc_shortcode_custom_css_class' )) {
        $images_class = vc_shortcode_custom_css_class( $css );
        $images_style = explode('{', $css);
        $images_style = '[data-class="' . $images_class . '"] .product-content-image img {' . $images_style[1];
        $css = '<style>' . $images_style . '</style>';
    }

    if( $banner_double ) {
      $columns = $columns / 2;
    }

    if ( $products->have_posts() ) : ?>
      <div class="et-look" data-class="<?php echo esc_attr($images_class); ?>">
        <?php woocommerce_product_loop_start(); ?>
            <?php $i=0; while ( $products->have_posts() ) : $products->the_post(); ?>
                <?php 
                    $i++;
                    if( $banner_pos == $i ) {
                        unset($atts['css']);
                      $class = etheme_get_product_class( $columns );
                      echo '<div class="' . $class . ' et-isotope-item"><div class="content-product">';
                        echo etheme_banner_shortcode( $atts, $content );
                      echo '</div></div>';
                    } 
                    woocommerce_get_template_part( 'content', 'product' );
                ?>
            <?php endwhile; // end of the loop. ?>
        <?php woocommerce_product_loop_end(); ?>
        <?php
          echo $css;
          unset($woocommerce_loop['columns']); 
          unset($woocommerce_loop['isotope']); 
          unset($woocommerce_loop['size']); 
          unset($woocommerce_loop['product_view']); 
          unset($woocommerce_loop['product_view_color']); 
        ?>
      </div>
    <?php endif;

    wp_reset_postdata();

    $output = ob_get_clean();
      
    return $output;
}

// **********************************************************************// 
// ! Register New Element: The Look
// **********************************************************************//
add_action( 'init', 'etheme_register_the_look');
if(!function_exists('etheme_register_the_look')) {
	function etheme_register_the_look() {
		if(!function_exists('vc_map')) return;

      add_filter( 'vc_autocomplete_et_the_look_include_callback',
        'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
      add_filter( 'vc_autocomplete_et_the_look_include_render',
        'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

      // Narrow data taxonomies
      add_filter( 'vc_autocomplete_et_the_look_taxonomies_callback',
        'vc_autocomplete_taxonomies_field_search', 10, 1 );
      add_filter( 'vc_autocomplete_et_the_look_taxonomies_render',
        'vc_autocomplete_taxonomies_field_render', 10, 1 );

      // Narrow data taxonomies for exclude_filter
      add_filter( 'vc_autocomplete_et_the_look_exclude_filter_callback',
        'vc_autocomplete_taxonomies_field_search', 10, 1 );
      add_filter( 'vc_autocomplete_et_the_look_exclude_filter_render',
        'vc_autocomplete_taxonomies_field_render', 10, 1 );

      add_filter( 'vc_autocomplete_et_the_look_exclude_callback',
        'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
      add_filter( 'vc_autocomplete_et_the_look_exclude_render',
    'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)


      $post_types_list = array();
      $post_types_list[] = array( 'product', esc_html__( 'Product', 'xstore' ) );
      //$post_types_list[] = array( 'custom', esc_html__( 'Custom query', 'xstore' ) );
      $post_types_list[] = array( 'ids', esc_html__( 'List of IDs', 'xstore' ) );

	    $params = array(
	      'name' => '[8THEME] The Look',
	      'base' => 'et_the_look',
	      'icon' => 'icon-wpb-etheme',
	      'category' => 'Eight Theme',
        'content_element' => true,
        'icon' => ETHEME_CODE_IMAGES . 'vc/el-lookbook.png',
        'as_child' => array('only' => 'et_looks'),            
        'is_container' => false,
	      'params' => array(
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Data source', 'xstore' ),
            'param_name' => 'post_type',
            'value' => $post_types_list,
            'description' => esc_html__( 'Select content type for your grid.', 'xstore' )
          ),
          array(
            'type' => 'autocomplete',
            'heading' => esc_html__( 'Include only', 'xstore' ),
            'param_name' => 'include',
            'description' => esc_html__( 'Add posts, pages, etc. by title.', 'xstore' ),
            'settings' => array(
              'multiple' => true,
              'sortable' => true,
              'groups' => true,
            ),
            'dependency' => array(
              'element' => 'post_type',
              'value' => array( 'ids' ),
              //'callback' => 'vc_grid_include_dependency_callback',
            ),
          ),
          // Custom query tab
          array(
            'type' => 'textarea_safe',
            'heading' => esc_html__( 'Custom query', 'xstore' ),
            'param_name' => 'custom_query',
            'description' => __( 'Build custom query according to <a href="http://codex.wordpress.org/Function_Reference/query_posts">WordPress Codex</a>.', 'xstore' ),
            'dependency' => array(
              'element' => 'post_type',
              'value' => array( 'custom' ),
            ),
          ),
          array(
            'type' => 'autocomplete',
            'heading' => esc_html__( 'Narrow data source', 'xstore' ),
            'param_name' => 'taxonomies',
            'settings' => array(
              'multiple' => true,
              // is multiple values allowed? default false
              // 'sortable' => true, // is values are sortable? default false
              'min_length' => 1,
              // min length to start search -> default 2
              // 'no_hide' => true, // In UI after select doesn't hide an select list, default false
              'groups' => true,
              // In UI show results grouped by groups, default false
              'unique_values' => true,
              // In UI show results except selected. NB! You should manually check values in backend, default false
              'display_inline' => true,
              // In UI show results inline view, default false (each value in own line)
              'delay' => 500,
              // delay for search. default 500
              'auto_focus' => true,
              // auto focus input, default true
              // 'values' => $taxonomies_for_filter,
            ),
            'param_holder_class' => 'vc_not-for-custom',
            'description' => esc_html__( 'Enter categories, tags or custom taxonomies.', 'xstore' ),
            'dependency' => array(
              'element' => 'post_type',
              'value_not_equal_to' => array( 'ids', 'custom' ),
            ),
          ),
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Columns number', 'xstore' ),
            'param_name' => 'columns',
            'value' => array(
              3 => 3,
              4 => 4,
            ),

          ),
          array(
            'type' => 'textfield',
            'heading' => esc_html__( 'Items per page', 'xstore' ),
            'param_name' => 'items_per_page',
            'description' => esc_html__( 'Number of items to show per page.', 'xstore' ),
            'value' => '10',
            /*'dependency' => array(
              'element' => 'style',
              'value' => array( 'lazy', 'load-more', 'pagination' ),
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_column',*/
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Product View", 'xstore'),
            "param_name" => "product_view",
            "value" => array( "", 
              'Default' => 'default',
              'Buttons on hover' => 'mask',
              'Information mask' => 'info',
            )
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Product View Color", 'xstore'),
            "param_name" => "product_view_color",
            "value" => array( "", 
              'White' => 'white',
              'Dark' => 'dark',
            )
          ),
          // Data settings
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Order by', 'xstore' ),
            'param_name' => 'orderby',
            'value' => array(
              esc_html__( 'Date', 'xstore' ) => 'date',
              esc_html__( 'Order by post ID', 'xstore' ) => 'ID',
              esc_html__( 'Author', 'xstore' ) => 'author',
              esc_html__( 'Title', 'xstore' ) => 'title',
              esc_html__( 'Last modified date', 'xstore' ) => 'modified',
              esc_html__( 'Post/page parent ID', 'xstore' ) => 'parent',
              esc_html__( 'Number of comments', 'xstore' ) => 'comment_count',
              esc_html__( 'Menu order/Page Order', 'xstore' ) => 'menu_order',
              esc_html__( 'Meta value', 'xstore' ) => 'meta_value',
              esc_html__( 'Meta value number', 'xstore' ) => 'meta_value_num',
              // esc_html__('Matches same order you passed in via the 'include' parameter.', 'js_composer') => 'post__in'
              esc_html__( 'Random order', 'xstore' ) => 'rand',
            ),
            'description' => esc_html__( 'Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'xstore' ),
            'group' => esc_html__( 'Data Settings', 'xstore' ),
            'param_holder_class' => 'vc_grid-data-type-not-ids',
            'dependency' => array(
              'element' => 'post_type',
              'value_not_equal_to' => array( 'ids', 'custom' ),
            ),
          ),
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Sorting', 'xstore' ),
            'param_name' => 'order',
            'group' => esc_html__( 'Data Settings', 'xstore' ),
            'value' => array(
              esc_html__( 'Descending', 'xstore' ) => 'DESC',
              esc_html__( 'Ascending', 'xstore' ) => 'ASC',
            ),
            'param_holder_class' => 'vc_grid-data-type-not-ids',
            'description' => esc_html__( 'Select sorting order.', 'xstore' ),
            'dependency' => array(
              'element' => 'post_type',
              'value_not_equal_to' => array( 'ids', 'custom' ),
            ),
          ),
          array(
            'type' => 'textfield',
            'heading' => esc_html__( 'Meta key', 'xstore' ),
            'param_name' => 'meta_key',
            'description' => esc_html__( 'Input meta key for grid ordering.', 'xstore' ),
            'group' => esc_html__( 'Data Settings', 'xstore' ),
            'param_holder_class' => 'vc_grid-data-type-not-ids',
            'dependency' => array(
              'element' => 'orderby',
              'value' => array( 'meta_value', 'meta_value_num' ),
            ),
          ),
          array(
            'type' => 'autocomplete',
            'heading' => esc_html__( 'Exclude', 'xstore' ),
            'param_name' => 'exclude',
            'description' => esc_html__( 'Exclude posts, pages, etc. by title.', 'xstore' ),
            'group' => esc_html__( 'Data Settings', 'xstore' ),
            'settings' => array(
              'multiple' => true,
            ),
            'param_holder_class' => 'vc_grid-data-type-not-ids',
            'dependency' => array(
              'element' => 'post_type',
              'value_not_equal_to' => array( 'ids', 'custom' ),
              'callback' => 'vc_grid_exclude_dependency_callback',
            ),
          ),
          array(
            'type' => 'attach_image',
            "heading" => esc_html__("Banner Image", 'xstore'),
            "param_name" => "img",
            'group' => esc_html__( 'Banner', 'xstore' ),
          ),
          array(
            "type" => "textfield",
            "heading" => esc_html__("Banner size", 'xstore' ),
            "param_name" => "img_size",
            "description" => esc_html__("Enter image size. Example in pixels: 200x100 (Width x Height).", 'xstore'),
            'group' => esc_html__( 'Banner', 'xstore' ),
          ),
          array(
            "type" => "textfield",
            "heading" => esc_html__("Link", 'xstore'),
            "param_name" => "link",
            'group' => esc_html__( 'Banner', 'xstore' ),
          ),
          array(
            "type" => "textarea_html",
            "holder" => "div",
            "heading" => "Banner Mask Text",
            "param_name" => "content",
            'group' => esc_html__( 'Banner', 'xstore' ),
          ),
          array(
            "type" => "textfield",
            "heading" => esc_html__("Banner position", 'xstore'),
            "param_name" => "banner_pos",
            "description" => esc_html__("Banner position number. From 1 to number of products.", 'xstore'),
            'group' => esc_html__( 'Banner', 'xstore' ),
          ),
          array(
            'type' => 'checkbox',
            'heading' => esc_html__( 'Banner double size', 'xstore' ),
            'param_name' => 'banner_double',
            'group' => esc_html__( 'Banner', 'xstore' ),
            'value' => 1
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Horizontal align", 'xstore'),
            "param_name" => "align",
            'group' => esc_html__( 'Banner', 'xstore' ),
            "value" => array( "", esc_html__("Left", 'xstore') => "left", esc_html__("Center", 'xstore') => "center", esc_html__("Right", 'xstore') => "right")
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Vertical align", 'xstore'),
            "param_name" => "valign",
            'group' => esc_html__( 'Banner', 'xstore' ),
            "value" => array( esc_html__("Top", 'xstore') => "top", esc_html__("Middle", 'xstore') => "middle", esc_html__("Bottom", 'xstore') => "bottom")
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Banner design", 'xstore'),
            "param_name" => "type",
            'group' => esc_html__( 'Banner', 'xstore' ),
            "value" => array( "", 
                esc_html__("Design 1", 'xstore') => 1,
                esc_html__("Design 2", 'xstore') => 2,
              )
          ),
          array(
            "type" => "dropdown",
            "heading" => esc_html__("Font style", 'xstore'),
            "param_name" => "font_style",
            'group' => esc_html__( 'Banner', 'xstore' ),
            "value" => array( "", esc_html__("light", 'xstore') => "light", esc_html__("dark", 'xstore') => "dark")
          ),
          array(
            'type' => 'css_editor',
            'heading' => esc_html__( 'CSS box', 'xstore' ),
            'param_name' => 'css',
            'group' => esc_html__( 'Design for product images', 'xstore' )
          ),
	      )
	
	    );  
	
	    vc_map($params);
	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_ET_The_Look extends WPBakeryShortCode {
    }
}
