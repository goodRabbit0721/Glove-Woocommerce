<?php
	// Prevent the direct loading

	if(!empty($_SERVER['SCRIPT-FILENAME']) && basename($_SERVER['SCRIPT-FILENAME']) == 'comments.php') {
		die(esc_html__('You cannot access this file', 'xstore'));
	}

	// Check if post is pwd protected

	if(post_password_required()){
		?>
		<p><?php esc_html_e('This post is password protected. Enter the password to view the comments.', 'xstore'); ?></p>
		<?php
		return;
	}

	if(have_comments()) :?>
		<div class="comments">
			<h4 class="title-alt"><span><?php comments_number(esc_html__('No Comments', 'xstore'), esc_html__('One Comment', 'xstore'), esc_html__('% Comments', 'xstore')); ?></span></h4>

			<ul class="comments-list">
				<?php wp_list_comments('callback=etheme_comments'); ?>
			</ul>

			<?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>

				<div class="comments-nav">
					<div class="pull-left"><?php previous_comments_link(esc_html__('&larr; Older Comments', 'xstore')); ?></div>
					<div class="pull-right"><?php next_comments_link(esc_html__('Newer Comments &rarr;', 'xstore')); ?></div>
					<div class="clear"></div>
				</div>

			<?php endif ?>

		</div>

	<?php elseif(!comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')) : ?>

		<p class="no-comments"><?php esc_html_e('Comments are closed', 'xstore') ?></p>

		<?php
	endif;

	// Display Comment Form
	comment_form(array('title_reply' => '<span>' . esc_html__('Leave a reply', 'xstore') . '</span>'));
?>