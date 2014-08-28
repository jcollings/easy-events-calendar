<?php do_action( 'jce/before_event_content' ); ?>

<article class="jce-event">

	<header class="jce-event-header">
		<?php do_action( 'jce/event_header' ); ?>
	</header>

	<div class="jce-event-content">
		<?php do_action('jce/event_content'); ?>
		<?php the_excerpt(); ?>
	</div>

	<footer class="jce-event-footer">
		<?php do_action('jce/event_footer'); ?>
	</footer>

</article>

<?php do_action( 'jce/after_event_content' ); ?>