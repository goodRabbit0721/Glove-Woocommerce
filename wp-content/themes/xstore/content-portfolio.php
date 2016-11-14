<?php

global $et_portfolio_loop;

if( empty($et_portfolio_loop['loop']))
	$et_portfolio_loop['loop'] = 0;

$et_portfolio_loop['loop']++;

$postId = get_the_ID();
$port_first_wide = etheme_get_option('port_first_wide');
$classes = array('portfolio-item');

$columns = etheme_get_option('portfolio_columns');

if( $et_portfolio_loop['loop'] == 1 && $port_first_wide ) {
	$columns /= 2;
}

$classes[] = etheme_get_product_class($columns);

$classes[] = 'columns-count-'.$columns;
$classes[] = 'port-style-'.etheme_get_option('portfolio_style');
$classes[] = (etheme_get_option('portfolio_fullwidth')) ? 'item-full-width' : 'no-full-width';


if( $et_portfolio_loop['loop'] == 2 && $port_first_wide ) {
	$classes[] = 'grid-sizer';
} else if($et_portfolio_loop['loop'] == 1 && ! $port_first_wide) {
	$classes[] = 'grid-sizer';
}
?>
<div <?php post_class( $classes ); ?>>
	<div class="portfolio-item-inner">
		<?php if (has_post_thumbnail( $postId ) ): ?>
			<div class="portfolio-image">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'large' ); ?>
				</a>
				<div class="zoom">
					<a href="<?php $link = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'full'); echo $link[0]; ?>" class="btn btn-lightbox" rel="lightbox"><span><?php esc_html_e('View large', 'xstore'); ?></span></a>
					<a href="<?php the_permalink(); ?>" class="btn btn-view-more"><span><?php esc_html_e('More details', 'xstore'); ?></span></a>
				</div>
			</div>
		<?php endif; ?>
		<div class="portfolio-descr">
			<span class="posted-in"><?php etheme_project_categories($postId); ?></span>
			<p class="project-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
		</div>
	</div>
</div>