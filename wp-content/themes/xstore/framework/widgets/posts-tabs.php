<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Recent + Popular posts Widget
// **********************************************************************// 
class ETheme_Posts_Tabs_Widget extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'etheme_widget_entries_tabs', 'description' => esc_html__( "The most recent and popular posts on your blog", 'xstore') );
        parent::__construct('etheme-posts-tabs', '8theme - '.__('Posts Tabs Widget', 'xstore'), $widget_ops);
        $this->alt_option_name = 'etheme_widget_entries_tabs';
    }

    function widget($args, $instance) {

        ob_start();

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? false : $instance['title']);

        if ( !$number = (int) $instance['number'] )
                $number = 10;
        else if ( $number < 1 )
                $number = 1;
        else if ( $number > 15 )
                $number = 15;

        echo $before_widget;

        if ( $title ) echo $before_title . $title . $after_title; 

        ?>

            <div class="tabs">
                <a href="#" id="tab-recent" class="tab-title opened">
                    <?php esc_html_e('Recent', 'xstore'); ?>
                </a>
                <a href="#" id="tab-popular" class="tab-title">
                    <?php esc_html_e('Popular', 'xstore'); ?>
                </a>

                <div id="content_tab-recent" class="tab-content" style="display:block;">
                    <?php the_widget( 'ETheme_Recent_Posts_Widget', array(
                        'number' => $number,
                        'image' => true
                    )); ?>
                </div>
                <div id="content_tab-popular" class="tab-content">
                    <?php the_widget( 'ETheme_Recent_Posts_Widget', array(
                        'number' => $number,
                        'image' => true,
                        'query' => 'popular'
                     )); ?>
                </div>
            </div>

        <?php 

        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];

        return $instance;
    }

    function form( $instance ) {
        $title = @esc_attr($instance['title']);
        if ( !$number = (int) @$instance['number'] )
            $number = 5;

        etheme_widget_input_text( esc_html__('Title:', 'xstore'), $this->get_field_id('title'), $this->get_field_name('title'), $title );
        etheme_widget_input_text( esc_html__('Number of posts:', 'xstore'), $this->get_field_id('number'), $this->get_field_name('number'), $number );

    }
}