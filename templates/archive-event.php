<?php
get_header(); ?>

<?php do_action( 'jce/before_event_archive' ); ?>

<?php if(have_posts()): ?>

	<?php do_action( 'jce/before_event_loop' ); ?>

	<?php while(have_posts()): the_post(); ?>

		<?php jce_get_template_part('content-event'); ?>
		
	<?php endwhile; ?>

	<?php do_action( 'jce/after_event_loop' ); ?>
	
<?php endif; ?>

<?php do_action( 'jce/after_event_archive' ); ?>

<?php get_footer(); ?>