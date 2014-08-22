<?php do_action( 'jce/before_event_content' ); ?>

<article class="jce-event">

	<header class="jce-event-header">
		<?php do_action( 'jce/event_header' ); ?>
	</header>

	<div class="jce-event-content">
		<?php the_excerpt(); ?>
	</div>

	<footer class="jce-event-footer">
		<div class="jce-event-meta">
			<?php // var_dump(JCE()->event->get_post_meta()); ?>
		</div>
	</footer>

</article>

<?php do_action( 'jce/after_event_content' ); ?>