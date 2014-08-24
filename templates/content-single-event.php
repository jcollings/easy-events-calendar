<style type="text/css">
<!--
.jce-single-event > header, .jce-single-event > div{
	margin-bottom: 20px;
}
-->
</style>

<article <?php post_class("jce-event jce-single-event"); ?>>

	<header class="jce-event-header">
		<?php do_action( 'jce/single_event_header' ); ?>
	</header>

	<?php do_action( 'jce/single_event_content' ); ?>

</article>