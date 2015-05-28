<?php
class JCE_Ajax{

	public function __construct(){

		add_action( 'wp_ajax_get_cal_month', array( $this, 'get_cal_month_callback' ) );
		add_action( 'wp_ajax_nopriv_get_cal_month', array( $this, 'get_cal_month_callback' ) );
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

		$view = isset($temp['view']) ? $temp['view'] : null;
		$event_venue = isset($temp['event_venue']) ? $temp['event_venue'] : null;
		$event_organiser = isset($temp['event_organiser']) ? $temp['event_organiser'] : null;
		$event_calendar = isset($temp['event_calendar']) ? $temp['event_calendar'] : null;
		$event_tag = isset($temp['event_tag']) ? $temp['event_tag'] : null;
		$event_category = isset($temp['event_category']) ? $temp['event_category'] : null;

		// set query vars
		$q_vars = array(
			'view' => $view,
			'event_venue' => $event_venue,
			'event_organiser' => $event_organiser,
			'event_calendar' => $event_calendar,
			'event_tag' => $event_tag,
			'event_category' => $event_category,
			'cal_year' => $year,
			'cal_month' => $month,
		);

		// clear out empty before setting
		foreach($q_vars as $k => $var){
			if(!is_null($var)){
				JCE()->query->query_vars[$k] = $var;
			}
		}


		if($view == 'calendar'){

			// remove wrapper div
			// remove_action('jce/widget/before_event_calendar', 'jce_output_calendar_wrapper_open', 0);
			// remove_action('jce/widget/after_event_calendar', 'jce_output_calendar_wrapper_close', 999);

			// remove_action('jce/before_event_calendar', 'jce_output_event_filters', 11);
			// do_action( 'jce/widget/before_event_calendar' );
			
			do_action( 'jce/before_event_calendar' );

			$events = JCE()->query->get_calendar($month, $year);

			// $calendar = new JCE_Calendar();
			// $calendar->inline_events = false;
			// $calendar->set_week_start('Mon');
			// $calendar->set_month($year, $month);
			// $calendar->render($events->posts, array(
			// 	'output_css' => true,
			// 	'headings' => true,
			// 	'widget' => true
			// ));
			
			$calendar = new JCE_Calendar();
			$calendar->inline_events = true;
			$calendar->set_week_start('Mon');
			$calendar->set_month($year, $month);
			$calendar->render($events->posts);

			// do_action( 'jce/widget/after_event_calendar' );
			do_action( 'jce/after_event_calendar' );

		}else{

			remove_action( 'jce/before_event_archive', 'jce_before_event_archive');
			remove_action( 'jce/after_event_archive', 'jce_after_event_archive');

			echo do_shortcode('[jce_event_archive view="' . $view . '" year="'.$year.'" month="'.$month.'" widget="0" /]' );		

			add_action( 'jce/before_event_archive', 'jce_before_event_archive');
			add_action( 'jce/after_event_archive', 'jce_after_event_archive');
		}		

		die();
	}
}

new JCE_Ajax();