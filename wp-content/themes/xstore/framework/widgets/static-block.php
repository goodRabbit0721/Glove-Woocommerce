<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Static block Widget
// **********************************************************************// 
class ETheme_StatickBlock_Widget extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'etheme_widget_satick_block', 'description' => esc_html__( "Insert a static block", 'xstore') );
        parent::__construct('etheme-static-block', '8theme - '.__('Static Block', 'xstore'), $widget_ops);
        $this->alt_option_name = 'etheme_widget_satick_block';
    }

    function widget($args, $instance) {
        extract($args);

        $block_id = $instance['block_id'];
        
        etheme_show_block($block_id);

    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['block_id'] = $new_instance['block_id'];

        return $instance;
    }

    function form( $instance ) {
        $block_id = 0;
        if(!empty($instance['block_id']))
            $block_id = esc_attr($instance['block_id']);

?>
        <p><label for="<?php echo $this->get_field_id('block_id'); ?>"><?php esc_html_e('Block name:', 'xstore'); ?></label>
            <?php $sb = etheme_get_static_blocks(); ?>
            <select name="<?php echo $this->get_field_name('block_id'); ?>" id="<?php echo $this->get_field_id('block_id'); ?>">
                <option>--Select--</option>
                <?php if (count($sb > 0)): ?>
                    <?php foreach ($sb as $key): ?>
                        <option value="<?php echo $key['value']; ?>" <?php selected( $block_id, $key['value'] ); ?>><?php echo $key['label'] ?></option>
                    <?php endforeach ?>
                <?php endif ?>
            </select>
        </p>
<?php
    }
}


