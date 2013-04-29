<?php get_header(); ?>

<?php
$view = get_query_var( 'view' );
if($view == 'archive'){
	echo jc_events_calendar(array('view' => 'archive'));
}else{
	echo jc_events_calendar();
}
?>

<?php get_footer(); ?>