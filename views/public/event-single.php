<?php 
$event = EventsModel::get_event($event_id);
if($event->have_posts()): ?>
	<p><a href="<?php echo remove_query_arg( 'event_id' ); ?>">&larr;Back to events</a></p>
	<?php while($event->have_posts()): $post = $event->the_post();  var_dump($post); ?>
		<article>
			<div class="meta-head">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="meta-content">
				<p>Dates: <?php echo EventsModel::get_start_date(); ?> - <?php echo EventsModel::get_end_date(); ?></p>
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
<?php 
endif;
wp_reset_postdata();
?>