<?php get_header(); ?>

<?php 
if(have_posts()): ?>
	<p><a href="<?php echo eec_get_permalink(); ?>">&larr;Back to events</a></p>
	<?php while(have_posts()): the_post(); ?>
		<article>
			<div class="meta-head">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="meta-content">
				<p>Dates: <?php echo EventsModel::get_start_date('d/m/Y'); ?> - <?php echo EventsModel::get_end_date('d/m/Y'); ?></p>
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
<?php 
endif;
wp_reset_postdata();
?>

<?php get_footer(); ?>