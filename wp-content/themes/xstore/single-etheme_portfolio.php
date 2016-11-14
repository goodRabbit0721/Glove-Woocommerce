<?php
/**
 * The Template for displaying single portfolio project.
 *
 */

get_header();

?>

<?php do_action( 'etheme_page_heading' ); ?>

	<div class="container">
		<div class="page-content sidebar-position-without">
			<div class="row">
				<div class="content col-md-12">

					<?php if ( have_posts() ) : ?>

						<?php while ( have_posts() ) : the_post(); ?>

							<div class="portfolio-single-item">
								<?php the_content(); ?>
							</div>

						<?php endwhile; // End the loop. Whew. ?>

					<?php else: ?>

						<h3><?php esc_html_e('No pages were found!', 'xstore') ?></h3>

					<?php endif; ?>

					<div class="clear"></div>

					<?php
					if(etheme_get_option('recent_projects')) {
						echo etheme_get_recent_portfolio(8, esc_html__('Recent Works', 'xstore'), $post->ID);
					}

					if(etheme_get_option('portfolio_comments')) {
						comments_template( '', true );
					}
					?>

				</div>
			</div>

		</div>
	</div>

<?php
get_footer();
?>