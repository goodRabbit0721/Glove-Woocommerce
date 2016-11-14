<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Featured posts Widget
// **********************************************************************// 
class ETheme_Featured_Posts_Widget extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'etheme_widget_featured', 'description' => esc_html__( "Show featured posts", 'xstore') );
        parent::__construct('etheme-featured-posts', '8theme - '.__('Featured Posts', 'xstore'), $widget_ops);
        $this->alt_option_name = 'etheme_widget_featured';
    }

    function widget($args, $instance) {
        global $et_loop;
        extract($args);

        $box_id = rand(1000,10000);

        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
        $ids = empty($instance['ids']) ? '' : $instance['ids'];
        $excerpt = empty($instance['excerpt']) ? false : true;

        $args = array(
            'posts_per_page' => 10, 
            'post_type' => 'post', 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => 1
        );

        if( ! empty( $ids ) ) {
            $args['post__in'] = explode(',', $ids);
        }

        $query = new WP_Query( $args );

        $size = 'medium';
        $read_more = etheme_get_read_more();

        $et_loop['blog_layout'] = 'default';

        if ($query->have_posts()) : ?>

            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>

            <div class="featured-posts-widget <?php if( $excerpt ) echo 'hide-excerpt'; ?>">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php get_template_part( 'content' ); ?>
                <?php endwhile; ?>
            </div>
             
            <?php echo $after_widget; ?>

        <?php endif;

        unset($et_loop);

        wp_reset_query();  // Restore global post data stomped by the_post().
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['ids'] = strip_tags($new_instance['ids']);
        $instance['excerpt'] = (int) @$new_instance['excerpt'];

        return $instance;
    }


    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'ids' => '',
        ) );

        $title = $instance['title'];
        $ids = $instance['ids'];
        $excerpt = (int) @$instance['excerpt'];

        etheme_widget_input_text( esc_html__('Title:', 'xstore'), $this->get_field_id('title'), $this->get_field_name('title'), $title );
        etheme_widget_input_text( esc_html__('Post IDs, separated by commas:', 'xstore'), $this->get_field_id('ids'), $this->get_field_name('ids'), $ids );
        etheme_widget_input_checkbox(__('Hide excerpt', 'xstore'), $this->get_field_id('excerpt'), $this->get_field_name('excerpt'),checked($excerpt, true, false), 1);

    }
}