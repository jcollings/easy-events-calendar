<?php
get_header(); ?>

<?php do_action('before_theme_content'); ?>

<?php do_action( 'jce/before_single_event' ); ?>

<?php if(have_posts()): ?>

	<?php do_action( 'jce/before_event_loop' ); ?>

	<?php while(have_posts()): the_post(); ?>

		<?php jce_get_template_part('content-single-event'); ?>
		
	<?php endwhile; ?>

	<?php do_action( 'jce/after_event_loop' ); ?>
	
<?php endif; ?>

<?php do_action( 'jce/after_single_event' ); ?>

<?php do_action('after_theme_content'); ?>

<?php get_footer(); ?>