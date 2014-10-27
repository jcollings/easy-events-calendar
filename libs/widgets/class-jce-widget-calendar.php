<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Widget_Calendar extends WP_Widget{

	public function __construct(){

		$id_base = 'jce-calendar';
		$name = __('Event Calendar Widget', 'jcevents');
		$widget_options = array(
			'description' => 'Display clickable events calendar widget'
		);
		$control_options = array();

		parent::__construct( $id_base, $name, $widget_options, $control_options);

		add_action( 'wp_ajax_get_cal_month', array( $this, 'get_cal_month_callback' ) );
		add_action( 'wp_ajax_nopriv_get_cal_month', array( $this, 'get_cal_month_callback' ) );

	}


	public function widget($args, $instance){

		$atts = array();

		extract( shortcode_atts( array(
			'month' => date('m'),
			'year' => date('Y'),
			'day' =>date('d')
		), $atts, 'jce_event_calendar' ) );

		echo "<div class=\"jce-widget-calendar\">";
		
		do_action( 'jce/widget/before_event_calendar' );

		$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : $year;
		$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : $month;
		$day = get_query_var( 'cal_day' ) ? get_query_var( 'cal_day' ) : $day;

		$events = JCE()->query->get_calendar($month, $year);

		$calendar = new JCE_Calendar();
		$calendar->inline_events = false;
		$calendar->set_week_start('Mon');
		$calendar->set_month($year, $month);
		$calendar->render($events->posts, array('widget' => true));

		do_action( 'jce/widget/after_event_calendar' );

		echo "</div>";
	}

	public function get_cal_month_callback(){

		$temp = array();

		$url = parse_url($_POST['url']);
		parse_str($url['query'], $temp);

		$year = date('Y');
		$month = date('m');

		$year = $temp['cal_year'] ? $temp['cal_year'] : $year;
		$month = $temp['cal_month'] ? $temp['cal_month'] : $month;

		set_query_var( 'cal_year', $year );
		set_query_var( 'cal_month', $month );

		// remove_action('jce/before_event_calendar', 'jce_output_event_filters', 11);
		do_action( 'jce/widget/before_event_calendar' );

		$events = JCE()->query->get_calendar($month, $year);

		$calendar = new JCE_Calendar();
		$calendar->inline_events = false;
		$calendar->set_week_start('Mon');
		$calendar->set_month($year, $month);
		$calendar->render($events->posts, array(
			'output_css' => true,
			'headings' => true,
			'widget' => true
		));

		do_action( 'jce/widget/after_event_calendar' );

		die();
	}

}

function jce_load_calendar_widget() {
	register_widget( 'JCE_Widget_Calendar' );
}

add_action( 'widgets_init', 'jce_load_calendar_widget' );