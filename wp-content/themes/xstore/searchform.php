<?php
/**
 * The template for displaying search forms
 *
 */
?>

<form action="<?php echo home_url( '/' ); ?>" id="searchform" class="hide-input" method="get">
	<input type="text" name="s" id="s" class="form-control" placeholder="<?php esc_attr_e( 'Search...', 'xstore' ); ?>" />
	<input type="hidden" name="post_type" value="post" />
	<button type="submit" class="btn medium-btn btn-black"><?php esc_html_e( 'Search', 'xstore' ); ?><i class="fa fa-search"></i></button>
</form>