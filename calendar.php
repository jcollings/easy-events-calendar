<?php 
class jc_events_calendar{

	var $month_name;
	var $month_num;
	var $year;
	var $week_start = 0;
	var $headings = array(
		'Mon' => 'Monday', 
		'Tue' => 'Tuesday', 
		'Wed' => 'Wednesday', 
		'Thu' => 'Thursday', 
		'Fri' => 'Friday', 
		'Sat' => 'Saturday', 
		'Sun' => 'Sunday'
	);
	var $months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
	var $cal_tiles = array();
	var $prev_year_link = '&laquo;';
	var $next_year_link = '&raquo;';
	var $prev_month_link = '&lt;';
	var $next_month_link = '&gt;';

	function set_week_start($day = 'Mon'){

		if(!array_key_exists($day, $this->headings))
			return false;

		$move = array();
		$start = array();

		foreach($this->headings as $key => $heading){
			if($key == $day){
				$start[$key] = $heading;
			}else{
				if(!empty($start)){
					$start[$key] = $heading;
				}else{
					$move[$key] = $heading;
				}
			}
		}

		$this->headings = array_merge($start, $move);
	}

	function set_month($year = false, $month = false){

		$this->month = !$month ? date('m') : $month;
		$this->month_name = $this->months[(int)$this->month - 1] ;
		$this->year = !$year ? date('Y') : $year;
		$this->set_tiles();
	}

	function set_tiles(){

		$time = strtotime('01-'.$this->month.'-'.$this->year);
		$start_day = date('D', $time);

		$prev_month = $this->month == 1 ? 12 : $this->month - 1;
		$prev_year = $this->month == 1 ? $this->year - 1 : $this->year;
		$prev_month_count = cal_days_in_month(CAL_GREGORIAN, $prev_month, $prev_year);
		$curr_month_count = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

		$row_offset = 0;
		$counter = 0;
		foreach($this->headings as $key => $heading){
			if($start_day == $key){
				$row_offset = $counter;
				break;
			}
			$counter++;
		}

		$month_plus_offset = ($curr_month_count+$row_offset);
		$tile_total_remainder = $month_plus_offset % 7;
		$tile_total = $tile_total_remainder > 0 ? ($month_plus_offset + 7) - $tile_total_remainder : $month_plus_offset;
		
		for($x = 1; $x <= $tile_total; $x++ ){
			if($x <= $row_offset){
				$this->cal_tiles[] = ($prev_month_count - $row_offset)+$x;
			}elseif($x > $month_plus_offset){
				$this->cal_tiles[] = $x - $month_plus_offset;
			}else{
				$this->cal_tiles[] = $x - $row_offset;	
			}
			
		}
	}

	function generate_event_list($events){

		$temp = array();
		
		$my_events = array();
		$tile_images = array();

		foreach($events as $event){

			// event images
			$thumb = wp_get_attachment_image_src(get_post_thumbnail_id( $event->ID ), 'cal-thumb' );
			$main = wp_get_attachment_image_src(get_post_thumbnail_id( $event->ID ), 'cal-main' );	

			// get start date details
			$start = EventsModel::get_start_date('Y-m-d', $event->ID, false);
			$start_atts = explode('-', $start);

			// get end date details
			$end = EventsModel::get_end_date('Y-m-d', $event->ID, false);
			$end_atts = explode('-', $end);

			// work out event length
			$event_length = floor((strtotime($end) - strtotime($start))/(60*60*24));

			// do stuff
			if($start_atts[0] == $this->year && $start_atts[1] == $this->month){
				// start date is in current month
				$month_length = cal_days_in_month(CAL_GREGORIAN, $start_atts[1], $start_atts[0]);
				
				for($i = 0; $i <= $event_length; $i++){
					$sel_day = ($i+$start_atts[2]);
					if($sel_day <= $month_length){

						$temp['events'][] = array(
							'title' => $event->post_title,
							'content' => $event->post_content,
							'id' => $event->ID,
							'parent' => $event->post_parent,
							'days' => $sel_day,
							'start' => $start,
							'end' => $end,
							'thumb' => $thumb[0],
							'bg' => $main[0]
						);
						if(!isset($temp['tiles'][$sel_day])){
							$temp['tiles'][$sel_day] = $thumb[0];
						}
					}
				}
			}else{

				if($end_atts[0] == $this->year && $end_atts[1] == $this->month){
					// end date is in current month but not start date
					$month_length = cal_days_in_month(CAL_GREGORIAN, $end_atts[1], $end_atts[0]);

					for($i = 1; $i <= $end_atts[2]; $i++){
						if($i < 10){
							$tempi = '0'.$i;
						}else{
							$tempi = $i;
						}
						$temp['events'][] = array(
							'title' => $event->post_title,
							'content' => $event->post_content,
							'id' => $event->ID,
							'days' => $tempi,
							'start' => $start,
							'end' => $end,
							'thumb' => $thumb[0],
							'bg' => $main[0]
						);
						if(!isset($temp['tiles'][$i])){
							$temp['tiles'][$i] = $thumb[0];
						}
						
					}
				}
			}	

		}

		return $temp;
	}

	function output_cal_header(){
		global $post, $wp_rewrite;

		if($wp_rewrite->permalink_structure){

			// generate cal links
			if($this->month == 1){
				$prev_year_link = home_url('events/'.($this->year-1) . '/'.$this->month.'/'); // add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = home_url('events/'.($this->year+1) . '/'.$this->month .'/'); // add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = home_url('events/'.($this->year-1) . '/12/'); //add_query_arg(array('xmonth' => 12, 'xyear' => $this->year -1));
				$next_month_link = home_url('events/'.$this->year . '/'. ($this->month+1) .'/');// add_query_arg(array('xmonth' => $this->month+1, 'xyear' => $this->year));
			}elseif($this->month == 12){
				$prev_year_link = home_url('events/' . ($this->year-1) . '/' . ($this->month) . '/'); //add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = home_url('events/' . ($this->year+1) . '/' . ($this->month) . '/');//add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = home_url('events/' . ($this->year-1) . '/' . ($this->month-1) . '/');//add_query_arg(array('xmonth' => $this->month-1, 'xyear' => $this->year));
				$next_month_link = home_url('events/' . ($this->year+1) . '/' . (1) . '/');//add_query_arg(array('xmonth' => 1, 'xyear' => $this->year+1));
			}else{
				$prev_year_link = home_url('events/' . ($this->year-1) . '/' . ($this->month) . '/');//add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = home_url('events/' . ($this->year+1) . '/' . ($this->month) . '/');//add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = home_url('events/' . ($this->year) . '/' . ($this->month-1) . '/');//add_query_arg(array('xmonth' => $this->month-1, 'xyear' => $this->year));
				$next_month_link = home_url('events/' . ($this->year) . '/' . ($this->month+1) . '/');//add_query_arg(array('xmonth' => $this->month+1, 'xyear' => $this->year));
			}

		}else{
			// generate cal links
			if($this->month == 1){
				$prev_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = add_query_arg(array('xmonth' => 12, 'xyear' => $this->year -1));
				$next_month_link = add_query_arg(array('xmonth' => $this->month+1, 'xyear' => $this->year));
			}elseif($this->month == 12){
				$prev_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = add_query_arg(array('xmonth' => $this->month-1, 'xyear' => $this->year));
				$next_month_link = add_query_arg(array('xmonth' => 1, 'xyear' => $this->year+1));
			}else{
				$prev_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year-1));
				$next_year_link = add_query_arg(array('xmonth' => $this->month, 'xyear' => $this->year+1));
				$prev_month_link = add_query_arg(array('xmonth' => $this->month-1, 'xyear' => $this->year));
				$next_month_link = add_query_arg(array('xmonth' => $this->month+1, 'xyear' => $this->year));
			}
		}

		$output = '<div class="cal-nav">'."\n";
		if($this->prev_year_link)
			$output .= '<a href="'.$prev_year_link.'">'.$this->prev_year_link.'</a>'."\n";

		if($this->prev_month_link)
			$output .= '<a href="'.$prev_month_link.'">'.$this->prev_month_link. '</a>'."\n";

		$output .= '<span>'.ucfirst($this->month_name).' '.$this->year.'</span>'."\n";

		if($this->next_year_link)
			$output .= '<a href="'.$next_year_link.'">'.$this->next_year_link.'</a>'."\n";

		if($this->prev_month_link)
			$output .= '<a href="'.$next_month_link.'">'.$this->next_month_link. '</a>'."\n";

		$output .= '</div><!-- /.cal-nav -->'."\n";
		return $output;
	}

	function output_cal_weekdays(){
		$output = '<div class="cal-weekdays">'."\n";
		foreach($this->headings as $heading){
			$output .= '<div class="cal-weekday-item">'.$heading.'</div>'."\n";
		}
		$output .= '</div><!-- /.cal-weekdays -->'."\n";
		return $output;
	}

	function render(){
		if(!is_admin()){
			$temp_file = get_template_directory() . DIRECTORY_SEPARATOR . 'simple-events-calendar' . DIRECTORY_SEPARATOR . 'calendar.php';
			if(is_file($temp_file)){
				require $temp_file;
			}else{
				require plugin_dir_path( __FILE__ ).'views/public/calendar.php';
			}
		}else{
			require plugin_dir_path( __FILE__ ).'views/admin/calendar.php';
		}

	}

}
?>