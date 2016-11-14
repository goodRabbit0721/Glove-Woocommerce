<?php 
    $ht = etheme_get_header_type();
?>

<div class="header-wrapper header-<?php echo esc_attr( $ht ); ?>">
		<?php get_template_part('headers/parts/top-bar'); ?>
		<header class="header main-header">
			<div class="container">
				<div class="container-wrapper">
					<!-- Mobile menu button -->
					<a href="#" class="navbar-toggle">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
		            </a>
					<!-- Header Custom Block -->
					<div class="header-custom"><?php etheme_option('header_custom_block'); ?></div>

					<!-- Header Logo -->
					<div class="header-logo"><?php etheme_logo(); ?></div>

					<!-- Main Menu -->
					<div class="menu-wrapper"><?php etheme_get_main_menu(); ?></div>

					<!-- Header navbar -->
					<div class="navbar-header">
						<label for="s" class="header-search-trigger hidden-md hidden-lg"><i class="fa fa-search"></i></label>
			            <?php if(etheme_get_option('search_form')): ?>
							<?php etheme_search_form(); ?>
						<?php endif; ?>
						<?php if(class_exists('Woocommerce') && current_theme_supports('woocommerce') && !etheme_get_option('just_catalog') && etheme_get_option('cart_widget')): ?>
		                    <?php etheme_top_cart(); ?>
			            <?php endif ;?>
					</div>
				</div>
			</div>
		</header>
</div>