<?php if (etheme_get_option('top_bar')): ?>
	<div class="top-bar topbar-color-<?php echo etheme_get_option('top_bar_color'); ?>">
		<div class="container">
			<div>
				<div class="languages-area">
					<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('languages-sidebar'))): ?>
					<?php endif; ?>
				</div>

				<?php if ( is_active_sidebar('top-panel') && etheme_get_option('top_panel') ): ?>
					<div class="top-panel-open"><span><?php esc_html_e('Open panel', 'xstore'); ?></span></div>
				<?php endif ?>
					
				<div class="top-links">
					<?php etheme_top_links(); ?>
					<?php if((!function_exists('dynamic_sidebar') || !dynamic_sidebar('top-bar-right'))): ?>
					<?php endif; ?>	
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>