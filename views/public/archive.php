<?php 
$events = EventsModel::get_events($limit);
if($events->have_posts()): ?>
	<ul class="events_archive">
	<?php while($events->have_posts()): $events->the_post(); ?>
		<li>
			<h1><a href="<?php echo add_query_arg('event_id', get_the_ID()); ?>"><?php the_title(); ?></a></h1>
			<p>Dates: <?php echo EventsModel::get_start_date('d/m/Y'); ?> - <?php echo EventsModel::get_end_date('d/m/Y'); ?></p>
			<?php the_content(); ?>
		</li>
	<?php endwhile; ?>
	</ul>
<?php 
endif;
wp_reset_postdata();
?>