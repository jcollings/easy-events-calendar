<?php
get_header(); ?>

<?php do_action('before_theme_content'); ?>

<?php do_action( 'jce/before_event_archive' ); ?>

<?php if(have_posts()): ?>

	<?php do_action( 'jce/before_event_loop' ); ?><div class="jce-events">

	<?php while(have_posts()): the_post(); ?>

		<?php jce_get_template_part('content-event'); ?>
		
	<?php endwhile; ?>

	</div><?php do_action( 'jce/after_event_loop' ); ?>
	
<?php else: ?>
	<article class="jce-event"><p>No Events have been found</p></article>
<?php endif; ?>

<?php do_action( 'jce/after_event_archive' ); ?>

<?php do_action('after_theme_content'); ?>

<?php get_footer(); ?>