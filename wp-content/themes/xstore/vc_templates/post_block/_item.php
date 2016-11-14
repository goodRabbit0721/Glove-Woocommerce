<?php
$block = $block_data[0];
$settings = $block_data[1];
$link_setting = empty($settings[0]) ? '' : $settings[0];
?>
<?php if($block === 'title'): ?>
<h2 class="post-title">
    <?php echo empty($link_setting) || $link_setting!='no_link' ? $this->getLinked($post, $post->title, $link_setting, 'link_title') : $post->title ?>
</h2>
<?php if($show_meta == 'yes'): ?>
    <div class="meta-post">
        <span><?php the_time(get_option('date_format')); ?> </span>
        <span><?php esc_html_e('by', 'xstore');?> <?php the_author_posts_link(); ?></span>
        <span class="category-list">
            <?php // Display Comments 

                    echo get_the_category_list( esc_html__( ', ', 'xstore' ), '', $post->id );

             ?>
        </span>
    </div>
<?php endif; ?>
<?php elseif($block === 'image' && !empty($post->thumbnail)): ?>
<div class="post-thumb">
    <?php echo empty($link_setting) || $link_setting!='no_link' ? $this->getLinked($post, $post->thumbnail, $link_setting, 'link_image') : $post->thumbnail ?>
</div>
<?php elseif($block === 'text'): ?>
<div class="entry-content">
    <?php echo empty($link_setting) || $link_setting==='text' ?  $post->content : $post->excerpt; ?>
</div>
<?php elseif($block === 'link'): ?>
<a href="<?php echo $post->link ?>" class="vc_read_more"
   title="<?php echo esc_attr( sprintf( esc_html__( 'Permalink to %s', 'xstore' ), $post->title_attribute ) ); ?>"<?php echo $this->link_target ?>><?php esc_html_e( 'Read more', 'xstore' ) ?></a>
<?php endif; ?>