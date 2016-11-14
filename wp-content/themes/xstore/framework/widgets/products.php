<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************//
// ! Products Widget
// **********************************************************************//
if( ! class_exists( 'WC_Widget' ) ) return;
class ETheme_Products_Widget extends WC_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'etheme_widget_products';
        $this->widget_description = __( 'Display a list of your products on your site.', 'xstore' );
        $this->widget_id          = 'etheme_widget_products';
        $this->widget_name        = '8theme - ' . __('Products Widget', 'xstore');
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Products', 'xstore' ),
                'label' => __( 'Title', 'xstore' )
            ),
            'number' => array(
                'type'  => 'number',
                'step'  => 1,
                'min'   => 1,
                'max'   => '',
                'std'   => 5,
                'label' => __( 'Number of products to show', 'xstore' )
            ),
            'show' => array(
                'type'  => 'select',
                'std'   => '',
                'label' => __( 'Show', 'xstore' ),
                'options' => array(
                    ''         => __( 'All Products', 'xstore' ),
                    'featured' => __( 'Featured Products', 'xstore' ),
                    'onsale'   => __( 'On-sale Products', 'xstore' ),
                )
            ),
            'orderby' => array(
                'type'  => 'select',
                'std'   => 'date',
                'label' => __( 'Order by', 'xstore' ),
                'options' => array(
                    'date'   => __( 'Date', 'xstore' ),
                    'price'  => __( 'Price', 'xstore' ),
                    'rand'   => __( 'Random', 'xstore' ),
                    'sales'  => __( 'Sales', 'xstore' ),
                )
            ),
            'order' => array(
                'type'  => 'select',
                'std'   => 'desc',
                'label' => _x( 'Order', 'Sorting order', 'xstore' ),
                'options' => array(
                    'asc'  => __( 'ASC', 'xstore' ),
                    'desc' => __( 'DESC', 'xstore' ),
                )
            ),
            'slider' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Slider widget', 'xstore' )
            ),
            'hide_free' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Hide free products', 'xstore' )
            ),
            'show_hidden' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show hidden products', 'xstore' )
            )
        );

        parent::__construct();
    }

    /**
     * Query the products and return them.
     * @param  array $args
     * @param  array $instance
     * @return WP_Query
     */
    public function get_products( $args, $instance ) {
        $number  = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
        $show    = ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : $this->settings['show']['std'];
        $orderby = ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
        $order   = ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];

        $query_args = array(
            'posts_per_page' => $number,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'no_found_rows'  => 1,
            'order'          => $order,
            'meta_query'     => array()
        );

        if ( empty( $instance['show_hidden'] ) ) {
            $query_args['meta_query'][] = WC()->query->visibility_meta_query();
            $query_args['post_parent']  = 0;
        }

        if ( ! empty( $instance['hide_free'] ) ) {
            $query_args['meta_query'][] = array(
                'key'     => '_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'DECIMAL',
            );
        }

        $query_args['meta_query'][] = WC()->query->stock_status_meta_query();
        $query_args['meta_query']   = array_filter( $query_args['meta_query'] );

        switch ( $show ) {
            case 'featured' :
                $query_args['meta_query'][] = array(
                    'key'   => '_featured',
                    'value' => 'yes'
                );
                break;
            case 'onsale' :
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $query_args['post__in'] = $product_ids_on_sale;
                break;
        }

        switch ( $orderby ) {
            case 'price' :
                $query_args['meta_key'] = '_price';
                $query_args['orderby']  = 'meta_value_num';
                break;
            case 'rand' :
                $query_args['orderby']  = 'rand';
                break;
            case 'sales' :
                $query_args['meta_key'] = 'total_sales';
                $query_args['orderby']  = 'meta_value_num';
                break;
            default :
                $query_args['orderby']  = 'date';
        }

        return new WP_Query( apply_filters( 'woocommerce_products_widget_query_args', $query_args ) );
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        ob_start();

        if ( ( $products = $this->get_products( $args, $instance ) ) && $products->have_posts() ) {
            $this->widget_start( $args, $instance );

            $id = rand(100,999);

            if( $instance['slider'] ) {
                echo '<div class="products-widget-slider slider-' . $id . '">';
            }

            echo apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' );

            $_i = 0;
            $slide_count = 3;
            while ( $products->have_posts() ) {
                $products->the_post();
                wc_get_template( 'content-widget-product.php', array( 'show_rating' => false ) );
                $_i++;
                if( $_i > 1 && $products->post_count != $_i && $_i%$slide_count == 0 ) {
                    echo apply_filters( 'woocommerce_after_widget_product_list', '</ul>' );
                    echo apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' );
                }
            }

            echo apply_filters( 'woocommerce_after_widget_product_list', '</ul>' );

            if( $instance['slider'] ) {
                echo '</div>';
                echo '<script type="text/javascript">';
                etheme_owl_init( '.slider-' . $id, array(
                    'singleItem' => 'true'
                ) );
                echo '</script>';
            }


            $this->widget_end( $args );
        }

        wp_reset_postdata();

        echo $this->cache_widget( $args, ob_get_clean() );
    }
}