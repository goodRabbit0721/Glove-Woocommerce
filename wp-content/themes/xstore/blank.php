<?php
/**
 * Template Name: Blank page
 *
 */

get_header();
?>

<div class="container content-page">
        <div class="row">

            <div class="content">
                <?php if(have_posts()): while(have_posts()) : the_post(); ?>

                    <?php the_content(); ?>

                <?php endwhile; else: ?>

                    <h3><?php esc_html_e('No pages were found!', 'xstore') ?></h3>

                <?php endif; ?>

            </div>

        </div><!-- end row-fluid -->

    </div>
</div><!-- end container -->

<?php
get_footer();
?>
