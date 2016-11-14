<?php
/**
 * The template for displaying search forms
 *
 */
$ajax = true;
$class = '';

if($ajax) {
	$class .= 'ajax-search-form';
}

?>
<?php if(class_exists('Woocommerce')) : ?>
	<form action="<?php echo esc_url( home_url( '/' ) ); ?>" id="searchform" class="<?php echo esc_attr($class); ?>" method="get">
		<div class="input-row">
			<input type="text" value="" placeholder="<?php esc_attr_e( 'Type here...', 'xstore' ); ?>" autocomplete="off" class="form-control" name="s" id="s" />
			<input type="hidden" name="post_type" value="product" />
			<button type="submit" class="btn filled"><?php esc_html_e( 'Search', 'xstore' ); ?><i class="fa fa-search"></i></button>
		</div>
		<?php if($ajax): ?>
			<div class="ajax-results-wrapper"><div class="ajax-results"></div></div>
		<?php endif ?>
	</form>
<?php else: ?>
	<?php get_template_part('searchform'); ?>
<?php endif ?>