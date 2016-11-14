<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************//
// ! Brands
// **********************************************************************//

function etheme_brands_shortcode($atts) {
    extract( shortcode_atts( array(
        'number'     => 12,
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => 1,
        'columns' => 3,
        'parent'     => '',
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

    // get terms and workaround WP bug with parents/pad counts
    $args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'pad_counts' => true,
        'include'    => $ids,
        'number' => $number
    );

    $product_brands = get_terms( 'brand', $args );

    $box_id = rand(1000,10000);

    ob_start();

    $class .= ' owl-carousel carousel-area';

    echo '<div class="brands-carousel '.$class.' slider-'.$box_id.'">';

    foreach ( $product_brands as $brand ) {
        $thumbnail_id 	= absint( get_woocommerce_term_meta( $brand->term_id, 'thumbnail_id', true ) );
        ?>
            <div class="slide-item brand-slide">
                <div class="categories-mask">
                    <?php if( $thumbnail_id ) : ?>
                        <?php
                            $image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
                            $src = $image[0];
                        ?>
                        <a href="<?php echo esc_url( get_term_link( $brand ) ); ?>" title="<?php sprintf(__('View all products from %s', 'xstore'), $brand->name); ?>"><img src="<?php echo esc_url($src); ?>" title="<?php echo esc_attr( $brand->name ); ?>"/></a>
                    <?php else: ?>
                        <h3><a href="<?php echo esc_url( get_term_link( $brand ) ); ?>" title="<?php sprintf(__('View all products from %s', 'xstore'), $brand->name); ?>"><?php echo $brand->name; ?></a></h3>
                    <?php endif; ?>
                </div>
            </div>
        <?php
    }

    echo '</div>';

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

    return ob_get_clean();
}

// **********************************************************************//
// ! Register New Element: scslug
// **********************************************************************//
add_action( 'init', 'etheme_register_brands_categories');
if(!function_exists('etheme_register_brands_categories')) {
    function etheme_register_brands_categories() {
        if(!function_exists('vc_map')) return;
        add_filter( 'vc_autocomplete_etheme_brands_ids_callback', 'etheme_productBrandBrandAutocompleteSuggester', 10, 1 ); // Get suggestion(find). Must return an array
        add_filter( 'vc_autocomplete_etheme_brands_ids_render', 'etheme_productBrandBrandRenderByIdExact', 10, 1 ); // Render exact category by id. Must return an array (label,value)
        
        $params = array(
            'name' => '[8theme] Brands carousel',
            'base' => 'etheme_brands',
            'icon' => 'icon-wpb-etheme',
            'icon' => ETHEME_CODE_IMAGES . 'vc/el-categories.png',
            'category' => 'Eight Theme',
            'params' => array_merge(array(
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Number of brands", 'xstore'),
                    "param_name" => "number"
                ),
                array(
                  'type' => 'autocomplete',
                  'heading' => esc_html__( 'Brands', 'xstore' ),
                  'param_name' => 'ids',
                  'settings' => array(
                    'multiple' => true,
                    'sortable' => true,
                  ),
                  'save_always' => true,
                  'description' => esc_html__( 'List of product brands', 'xstore' ),
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

if( ! function_exists( 'etheme_productBrandBrandAutocompleteSuggester' ) ) {
    function etheme_productBrandBrandAutocompleteSuggester( $query, $slug = false ) {
        global $wpdb;
        $cat_id = (int) $query;
        $query = trim( $query );
        $post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.term_id AS id, b.name as name, b.slug AS slug
                        FROM {$wpdb->term_taxonomy} AS a
                        INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
                        WHERE a.taxonomy = 'brand' AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", $cat_id > 0 ? $cat_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

        $result = array();
        if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
            foreach ( $post_meta_infos as $value ) {
                $data = array();
                $data['value'] = $slug ? $value['slug'] : $value['id'];
                $data['label'] = __( 'Id', 'js_composer' ) . ': ' . $value['id'] . ( ( strlen( $value['name'] ) > 0 ) ? ' - ' . __( 'Name', 'js_composer' ) . ': ' . $value['name'] : '' ) . ( ( strlen( $value['slug'] ) > 0 ) ? ' - ' . __( 'Slug', 'js_composer' ) . ': ' . $value['slug'] : '' );
                $result[] = $data;
            }
        }

        return $result;
    }
}

if( ! function_exists( 'etheme_productBrandBrandRenderByIdExact' ) ) {
    function etheme_productBrandBrandRenderByIdExact( $query ) {
            $query = $query['value'];
            $cat_id = (int) $query;
            $term = get_term( $cat_id, 'brand' );

            return etheme_productBrandTermOutput( $term );
    }
}

if( ! function_exists( 'etheme_productBrandTermOutput' ) ) {
    function etheme_productBrandTermOutput( $term ) {
        $term_slug = $term->slug;
        $term_title = $term->name;
        $term_id = $term->term_id;

        $term_slug_display = '';
        if ( ! empty( $term_slug ) ) {
            $term_slug_display = ' - ' . __( 'Sku', 'js_composer' ) . ': ' . $term_slug;
        }

        $term_title_display = '';
        if ( ! empty( $term_title ) ) {
            $term_title_display = ' - ' . __( 'Title', 'js_composer' ) . ': ' . $term_title;
        }

        $term_id_display = __( 'Id', 'js_composer' ) . ': ' . $term_id;

        $data = array();
        $data['value'] = $term_id;
        $data['label'] = $term_id_display . $term_title_display . $term_slug_display;

        return ! empty( $data ) ? $data : false;
    }
}

