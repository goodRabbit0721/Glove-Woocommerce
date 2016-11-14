<?php
get_header();
?>

<?php
global $post;
$l = etheme_page_config();

?>

<?php do_action( 'etheme_page_heading' ); ?>

<div class="container content-page">
    <div class="sidebar-position-<?php echo esc_attr( $l['sidebar'] ); ?>">
        <div class="row">

            <div class="content <?php echo esc_attr( $l['content-class'] ); ?>">
                <?php if(have_posts()): while(have_posts()) : the_post(); ?>

                    <?php the_content(); ?>

                    <div class="post-navigation"><?php wp_link_pages(); ?></div>

                    <?php if ($post->ID != 0 && current_user_can('edit_post', $post->ID)): ?>
                        <?php edit_post_link( esc_html__('Edit this', 'xstore'), '<p class="edit-link">', '</p>' ); ?>
                    <?php endif ?>

                <?php endwhile; else: ?>

                    <h3><?php esc_html_e('No pages were found!', 'xstore') ?></h3>

                <?php endif; ?>

                <?php comments_template('', true); ?>

            </div>

            <?php get_sidebar(); ?>

        </div><!-- end row-fluid -->

    </div>
</div><!-- end container -->

<?php
get_footer();
?>
