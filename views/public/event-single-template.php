<?php get_header(); ?>

<?php 

if(have_posts()):
	while(have_posts()): the_post(); ?>

		<article>
			<div class="meta-head">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="meta-content">
				<p>Dates: <?php echo EventsModel::get_start_date(); ?> - <?php echo EventsModel::get_end_date(); ?></p>
				<?php the_content(); ?>
			</div>
		</article>

		<?php
	endwhile;
endif;
?>

<?php get_footer(); ?>