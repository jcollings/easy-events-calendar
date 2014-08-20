<?php
get_header(); ?>

<?php if(have_posts()): ?>

	<?php while(have_posts()): the_post(); ?>

		<?php jce_get_template_part('content-event'); ?>
		
	<?php endwhile; ?>
	
<?php endif; ?>

<?php get_footer(); ?>