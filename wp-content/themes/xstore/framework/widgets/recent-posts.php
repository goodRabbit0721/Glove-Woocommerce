<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Recent posts Widget
// **********************************************************************// 
class ETheme_Recent_Posts_Widget extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'etheme_widget_recent_entries', 'description' => esc_html__( "The most recent posts on your blog (Etheme Edit)", 'xstore') );
        parent::__construct('etheme-recent-posts', '8theme - '.__('Posts Widget', 'xstore'), $widget_ops);
        $this->alt_option_name = 'etheme_widget_recent_entries';
    }

    function widget($args, $instance) {
        extract($args);

        $box_id = rand(1000,10000);

        $title = apply_filters('widget_title', empty($instance['title']) ? false : $instance['title']);
        $auto_play = empty($instance['auto_play']) ? false : $instance['auto_play'];

        if ( !$number = (int) $instance['number'] )
                $number = 10;
        else if ( $number < 1 )
                $number = 1;
        else if ( $number > 15 )
                $number = 15;

        $slider = (!empty($instance['slider'])) ? $instance['slider'] : false;
        $post_type = (!empty($instance['post_type'])) ? $instance['post_type'] : 'post';
        $query = (!empty($instance['query'])) ? $instance['query'] : 'recent';
        $image = (!empty($instance['image'])) ? (int) $instance['image'] : false;

        $query_args = array(
            'posts_per_page' => $number, 
            'post_type' => $post_type, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => 1
        );

        if( $query == 'popular' ) {
            $query_args['order'] = 'DESC';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = '_et_views_count';
        }

        $r = new WP_Query( $query_args );
       
        if ($r->have_posts()) : ?>
        <?php echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <div class="recent-posts-widget posts-widget-<?php echo esc_attr( $slider ); ?> posts-query-<?php echo esc_attr( $query ); ?> slider-<?php echo $box_id; ?>">
                <?php if( $slider == 'slider' ): ?>
                    <div class="slide-item">
                <?php endif; ?>
                    <?php $i=0;  while ($r->have_posts()) : $r->the_post(); $i++; ?>
                        <?php
                            if ( get_the_title() ) $title = get_the_title(); else $title = get_the_ID();
                            $title = etheme_trunc($title, 10);
                        ?>

                        <div class="post-widget-item">
                            <div class="media">
                                <a class="pull-left" href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() && $image ): ?>
                                        <?php the_post_thumbnail( array(100,100) ); ?>
                                    <?php endif ?>
                                    <?php if ( ! $image ): ?>
                                        <time class="date-event"><span class="number"><?php the_time('d'); ?></span> <?php the_time('M'); ?></time>
                                    <?php endif ?>
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading"><a href="<?php the_permalink() ?>"><?php echo $title; ?></a></h4>
                                    <span class="post-date"><?php the_time(get_option('date_format')); ?></span>
                                    <?php if ($post_type == 'post'): ?>
                                        <span class="post-comments visible-lg"><?php comments_popup_link(__('0 Comments', 'xstore'), esc_html__('1 Comment', 'xstore'), esc_html__('% Comments', 'xstore'), 'post-comments-count', esc_html__('No Comments', 'xstore')); ?></span>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>

                        <?php if( $slider == 'slider' && $i%2 == 0 && $i != $r->post_count): ?>
                            </div>
                            <div class="slide-item">
                        <?php endif; ?>

                    <?php endwhile; ?>
                <?php if( $slider == 'slider') :?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($slider): ?>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        jQuery(".slider-<?php echo $box_id; ?>").owlCarousel({
                            navigation: true,
                            lazyLoad: false,
                            rewindNav: true,
                            singleItem: true,
                            addClassActive: true,
                            <?php if($slider == 'creeping'): ?>
                                transitionStyle:"fade",
                            <?php endif; ?>
                            autoPlay: <?php echo (!empty( $auto_play )) ? $auto_play : 'false'; ?>
                        });
                    });
                </script>
            <?php endif; ?>
        <?php echo $after_widget; ?>

        <?php
            wp_reset_query();  // Restore global post data stomped by the_post().
        endif;
        
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['slider'] = strip_tags( $new_instance['slider'] );
        $instance['post_type'] = strip_tags( $new_instance['post_type'] );
        $instance['query'] = strip_tags( $new_instance['query'] );
        $instance['image'] = (int) $new_instance['image'];
        $instance['auto_play'] = (int) $new_instance['auto_play'];
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['etheme_widget_recent_entries']) )
            delete_option('etheme_widget_recent_entries');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('etheme_widget_recent_entries', 'widget');
    }

    function form( $instance ) {
        $title = @esc_attr($instance['title']);
        if ( !$number = (int) @$instance['number'] )
            $number = 5;

        $slider = strip_tags( @$instance['slider'] );
        $post_type = strip_tags( @$instance['post_type'] );
        $query = strip_tags( @$instance['query'] );
        $image = (int) @$instance['image'];
        $auto_play = (int) @$instance['auto_play'];

        ?>

        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'xstore'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of posts to show:', 'xstore'); ?></label>
        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
        <small><?php esc_html_e('(at most 15)', 'xstore'); ?></small></p>

        <?php etheme_widget_input_checkbox(__('Show images', 'xstore'), $this->get_field_id('image'), $this->get_field_name('image'),checked($image, true, false), 1); ?>
        
        <?php etheme_widget_input_dropdown(__('Post type', 'xstore'), $this->get_field_id('post_type'), $this->get_field_name('post_type'), $post_type, array(
            'post' => 'Posts',
            'etheme_portfolio' => 'Portfolio'
        )); ?>               

        <?php etheme_widget_input_dropdown(__('Use slider', 'xstore'), $this->get_field_id('slider'), $this->get_field_name('slider'), $slider, array(
            '' => 'No',
            'slider' => 'Sidebar slider',
            #'creeping' => 'Creeeping line'
        )); ?>        

        <?php etheme_widget_input_dropdown(__('Posts query', 'xstore'), $this->get_field_id('query'), $this->get_field_name('query'), $query, array(
            'recent' => 'Recent posts',
            'popular' => 'Most popular'
        )); ?>

        <?php etheme_widget_input_text( esc_html__('Slider autoplay time in ms', 'xstore'), $this->get_field_id('auto_play'), $this->get_field_name('auto_play'), $auto_play); ?>

        <?php
    }
}