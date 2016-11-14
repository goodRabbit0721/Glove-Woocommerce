<?php
get_header();
?>

<?php
global $post;
$l = etheme_page_config();
$post_template = etheme_get_post_template();

?>

<?php do_action( 'etheme_page_heading' ); ?>

	<div class="container">
		<div class="page-content sidebar-position-<?php echo esc_attr( $l['sidebar'] ); ?>">
			<div class="row">

				<div class="content <?php echo esc_attr( $l['content-class'] ); ?>">
					<?php if(have_posts()): while(have_posts()) : the_post(); ?>
						<?php
						$post_format 	= get_post_format();
						$slider_id 		= rand(100,10000);
						$post_content 	= get_the_content();
						$gallery_filter = etheme_gallery_from_content( $post_content );
						$classes 		= array();
						$classes[] 		= 'blog-post';
						$classes[] 		= 'post-single';
						$classes[] 		= 'post-template-' . $post_template;
						?>
						<article <?php post_class( $classes ); ?> id="post-<?php the_ID(); ?>" >

							<?php echo wp_get_attachment_image( $post->ID, 'large' ); ?>

							<div class="post-heading">
								<h2><?php the_title(); ?></h2>
								<?php etheme_byline(); ?>
							</div>

							<?php if(etheme_get_option('post_share')): ?>
								<div class="share-post">
									<?php echo do_shortcode('[share title="'.__('Share Post', 'xstore').'"]'); ?>
								</div>
							<?php endif; ?>

							<div class="clear"></div>

						</article>

					<?php endwhile; else: ?>

						<h1><?php esc_html_e('No posts were found!', 'xstore') ?></h1>

					<?php endif; ?>


				</div>

				<?php get_sidebar(); ?>

			</div>

		</div>
	</div>

<?php
get_footer();
?>