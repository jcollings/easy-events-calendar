<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Shortcode_Calendar{

	private $cal_year = false;
	private $cal_month = false;

	public function __construct(){

		add_shortcode( 'jce_event_calendar', array($this, 'event_calendar') );
	}

	public function event_calendar($atts){

		extract( shortcode_atts( array(
			'month' => date('m'),
			'year' => date('Y')
		), $atts, 'jce_event_calendar' ) );

		ob_start();

		$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : $year;
		$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : $month;

		$events = JCE()->query->get_calendar($month, $year);

		$calendar = new JCE_Calendar();
		$calendar->set_week_start('Mon');
		$calendar->set_month($year, $month);
		$calendar->render($events->posts);

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

new JCE_Shortcode_Calendar();