<?php 
$event = EventsModel::get_event($event_id);
if($event->have_posts()): ?>
	<?php while($event->have_posts()): $event->the_post(); ?>
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