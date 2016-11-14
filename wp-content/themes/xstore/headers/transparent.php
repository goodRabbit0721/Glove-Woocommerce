<?php 
	$ht = etheme_get_header_type();
?>

<div class="header-wrapper header-<?php echo $ht; ?>">
		<header class="header main-header">
			    <?php if (etheme_get_option('top_bar')): ?>
					<div class="top-bar">
						<div class="container">
							<div class="container-wrapper">
								<div class="languages-area">
									<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('languages-sidebar'))): ?>
										<div class="languages">
											<ul class="links">
												<li class="active">EN</li>
												<li><a href="#">FR</a></li>
												<li><a href="#">GE</a></li>
											</ul>
										</div>
										<div class="currency">
											<ul class="links">
												<li><a href="#">£</a></li>
												<li><a href="#">€</a></li>
												<li class='active'>$</li>
											</ul>
										</div>
									<?php endif; ?>	
								</div>
								<div class="top-links">
									<?php etheme_top_links(); ?>
									<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('top-bar-right'))): ?>
									<?php endif; ?>	
								</div>
								<!-- Header navbar -->
								<div class="navbar-header">
									<?php if(class_exists('Woocommerce') && current_theme_supports('woocommerce') && !etheme_get_option('just_catalog') && etheme_get_option('cart_widget')): ?>
					                    <?php etheme_top_cart(); ?>
						            <?php endif ;?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<div class="container">
				<!-- Mobile menu button -->
				<a href="#" class="navbar-toggle">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
	            </a>
				<!-- Header Logo -->
				<div class="header-logo"><?php etheme_logo(); ?></div>
				<!-- Main Menu -->
				<div class="menu-wrapper"><?php etheme_get_main_menu(); ?></div>
				<?php if(etheme_get_option('search_form')): ?>
					<?php etheme_search_form(); ?>
				<?php endif; ?>
			</div>
		</header>
</div>