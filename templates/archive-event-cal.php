<?php
get_header(); ?>

<?php 
global $wp_query;

$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : date('Y');
$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : date('m');

$calendar = new JCE_Calendar();

// todo: set week start has to be set before the month
$calendar->set_week_start('Mon');
$calendar->set_month($year, $month);

$calendar->render($wp_query->posts);
?>

<?php get_footer(); ?>