<?php
get_header();

$page_content = etheme_get_option('404_text');

?>

<?php do_action( 'etheme_page_heading' ); ?>

	<div class="container">
		<div class="page-content page-404">
			<div class="row">
				<div class="col-md-12">
					<?php if ( ! empty( $page_content ) ): ?>
						<?php echo do_shortcode( $page_content ); ?>
					<?php else: ?>
						<h2 class="largest">404</h2>
						<h1><?php esc_html_e('Oops! Page not found', 'xstore') ?></h1>
						<hr class="horizontal-break">
						<p><?php esc_html_e('Sorry, but the page you are looking for is not found. Please, make sure you have typed the current URL.', 'xstore') ?></p>
						<?php get_search_form( true ); ?>
						<a href="<?php echo esc_url( home_url() ); ?>" class="button medium"><?php esc_html_e('Go to home page', 'xstore'); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

<?php
get_footer();
?>