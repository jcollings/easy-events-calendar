<?php 
add_shortcode( 'easy_event_cal', 'jc_events_calendar' );
function jc_events_calendar($atts = array()){

	/**
	 * View: calendar, archive, upcoming
	 */

	extract(shortcode_atts(array(
		'cal' => 'all',
		'view' => 'calendar',
		'limit' => 5
	), $atts));

	switch($view){
		case 'archive':
			ob_start();
			$temp_file = get_template_directory() . DIRECTORY_SEPARATOR . 'simple-events-calendar' . DIRECTORY_SEPARATOR . 'archive.php';
			if(is_file($temp_file)){
				require $temp_file;
			}else{
				require plugin_dir_path( __FILE__ ).'views/public/archive.php';
			}
			$output = ob_get_contents();
			ob_clean();
			return $output;
		break;
		case 'calendar':
		default:
			$jc_events_calendar = new jc_events_calendar();
			
			$year = isset($_GET['xyear']) ? $_GET['xyear'] : false;
			$month = isset($_GET['xmonth']) ? $_GET['xmonth'] : false;
			$jc_events_calendar->prev_year_link = false;
			$jc_events_calendar->next_year_link = false;
			$jc_events_calendar->set_week_start('Sun');

			EventsModel::$calendar = $cal;
			$jc_events_calendar->set_month($year,$month);

			return $jc_events_calendar->render();
		break;
	}
}
?>