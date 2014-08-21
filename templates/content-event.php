<article>

	<header>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="event-meta">
		<p>From: <?php jce_event_start_date('jS F Y g:i a'); ?> - <?php jce_event_end_date('jS F Y g:i a'); ?></p>
		</div>
	</header>

	<div class="event-content">
		<?php the_excerpt(); ?>
	</div>

	<footer>
		<div class="event-meta">
			<?php var_dump(JCE()->event->get_post_meta()); ?>
		</div>
	</footer>

	<hr />

</article>