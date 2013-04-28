<?php 
$events = EventsModel::get_events($limit);
if($events->have_posts()): ?>
	<ul class="events_archive">
	<?php while($events->have_posts()): $events->the_post(); ?>
		<li>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</li>
	<?php endwhile; ?>
	</ul>
<?php 
endif;
wp_reset_postdata();
?>