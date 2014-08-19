<?php 
$events = EventsModel::get_events($limit, get_query_var('paged' ));;
if($events->have_posts()): ?>
	<ul class="events_archive upcoming">
	<?php foreach($events->posts as $event): ?>
		<li>
			<h1><a href="<?php echo eec_get_permalink(array('id' => $event->ID, 'date' => $event->start_date)); ?>"><?php echo $event->post_title; ?></a></h1>
			<p>Dates: <?php echo $event->start_date . ' - '. $event->end_date; ?></p>
			<?php the_content(); ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php 
endif;

eec_pagination($events->found_posts, $events->query_vars['posts_per_page']);

wp_reset_postdata();
?>