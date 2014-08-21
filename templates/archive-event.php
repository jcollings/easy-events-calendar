<?php

$current_month = false;

get_header(); ?>

<?php if(have_posts()): ?>

	<?php while(have_posts()): the_post(); ?>

		<?php
		// display month name in archive
		$month = jce_event_start_date('M', false);
		if($current_month != $month){
			$current_month = $month;
			echo sprintf("<h2 class=\"month-title\">%s</h2>", $current_month);
		}
		?>

		<?php jce_get_template_part('content-event'); ?>
		
	<?php endwhile; ?>

	<?php jce_pagination(); ?>
	
<?php endif; ?>

<?php get_footer(); ?>