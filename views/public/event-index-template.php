<?php get_header(); ?>

<?php
$view = get_query_var( 'view' );
if($view){
	echo jc_events_calendar(array('view' => $view));
}else{
	echo jc_events_calendar();
}
?>

<?php get_footer(); ?>