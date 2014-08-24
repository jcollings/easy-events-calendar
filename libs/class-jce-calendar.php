<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Calendar{

	var $month_name;
	var $month_num;
	var $year;
	var $month;
	var $week_start = 0;
	var $headings = array(
		'Mon' => 'Mon', 
		'Tue' => 'Tue', 
		'Wed' => 'Wed', 
		'Thu' => 'Thu', 
		'Fri' => 'Fri', 
		'Sat' => 'Sat', 
		'Sun' => 'Sun'
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
			// $start = $event->
			$start = date('Y-m-d', strtotime($event->start_date));
			$start_atts = explode('-', $start);

			// get end date details
			$end = date('Y-m-d', strtotime($event->end_date));
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
							'parent' => 0,
							'days' => $sel_day,
							'start' => $start,
							'end' => $end,
							'thumb' => $thumb[0],
							'bg' => $main[0],
							'link' => jce_get_permalink(array('id' => $event->ID, 'date' => $event->start_date))
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
							$tempi = $i;
						}else{
							$tempi = $i;
						}
						$temp['events'][] = array(
							'title' => $event->post_title,
							'content' => $event->post_content,
							'id' => $event->ID,
							'parent' => 0,
							'days' => $tempi,
							'start' => $start,
							'end' => $end,
							'thumb' => $thumb[0],
							'bg' => $main[0],
							'link' => jce_get_permalink(array('id' => $event->ID, 'date' => $event->start_date))
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

			// todo: match chosen permalink structure
			// generate cal links
			if($this->month == 1){
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => 12, 'cal_year' => $this->year -1));
				$next_month_link = add_query_arg(array('cal_month' => $this->month+1, 'cal_year' => $this->year));
			}elseif($this->month == 12){
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => $this->month-1, 'cal_year' => $this->year));
				$next_month_link = add_query_arg(array('cal_month' => 1, 'cal_year' => $this->year+1));
			}else{
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => $this->month-1, 'cal_year' => $this->year));
				$next_month_link = add_query_arg(array('cal_month' => $this->month+1, 'cal_year' => $this->year));
			}

		}else{
			// generate cal links
			if($this->month == 1){
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => 12, 'cal_year' => $this->year -1));
				$next_month_link = add_query_arg(array('cal_month' => $this->month+1, 'cal_year' => $this->year));
			}elseif($this->month == 12){
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => $this->month-1, 'cal_year' => $this->year));
				$next_month_link = add_query_arg(array('cal_month' => 1, 'cal_year' => $this->year+1));
			}else{
				$prev_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year-1));
				$next_year_link = add_query_arg(array('cal_month' => $this->month, 'cal_year' => $this->year+1));
				$prev_month_link = add_query_arg(array('cal_month' => $this->month-1, 'cal_year' => $this->year));
				$next_month_link = add_query_arg(array('cal_month' => $this->month+1, 'cal_year' => $this->year));
			}
		}

		$output = '<div class="cal-nav">'."\n";
		if($this->prev_year_link)
			$output .= '<a href="'.$prev_year_link.'">'.$this->prev_year_link.'</a>'."\n";

		if($this->prev_month_link)
			$output .= '<a href="'.$prev_month_link.'">'.$this->prev_month_link. '</a>'."\n";

		$output .= '<span>'.ucfirst($this->month_name).' '.$this->year.'</span>'."\n";

		if($this->prev_month_link)
			$output .= '<a href="'.$next_month_link.'">'.$this->next_month_link. '</a>'."\n";

		if($this->next_year_link)
			$output .= '<a href="'.$next_year_link.'">'.$this->next_year_link.'</a>'."\n";

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

	function render($events = array()){

		$row_counter = 0;
		$curr_month = false; // true if tile is in the current month
		$events = $this->generate_event_list($events);
		$sorted_events = array();
		if(isset($events['events'])){
			foreach($events['events'] as $e){
				$sorted_events[$e['days']][] = $e;
			}
		}
		?>
		<style type="text/css">

		.event-list a{
			color:#FFF;
			text-decoration: none;
		}

		.cal{
			width:100%;
		}
		.cal-nav{
			width:100%;
			text-align: center;
		}
		.cal-nav a, .cal-nav span{
			margin:0 2px;
		}

		.prev-next .date{
			color:#DDD;
		}

		.cal-weekdays, .cal-days-row{
			width:100%;
			overflow: hidden;
		}

		.cal-weekday-item, .cal-day{
			width:14.28%;
			float:left;
		}

		.cal-weekday-item{
			font-weight: bold;
			padding:5px 0;
		}

		.cal-day .cal-day-wrapper{
			height:100px;
			border:1px solid #EEE;
			display: block;
			padding:5px;
		}

		.cal ul, .cal li{
			margin: 0;
			padding: 0;
			list-style: none;
		}

		.cal-day-wrapper li{
			background: red;
			color:#FFF;
			padding:1px;
			padding: 2px 5px;
			font-size: 10px;
			margin-bottom:2px;
			line-height: 1.1;
		}

		.cal-day-wrapper li a, .cal-day-wrapper li a:visited{
			color: #FFF;
		}

		<?php 
		$cal_terms = get_terms( 'event_calendar', array('hide_empty' => false) );

		if($cal_terms){
			foreach($cal_terms as $term){
			    $term_meta = get_option( "event_calendar_{$term->term_id}" );
			    echo '.cal-day-wrapper li.event-list.'.$term->slug.'{'."\n\t";
			    echo 'background:' . $term_meta['calendar_colour'] . ';'."\n";
			    echo '}'."\n";
			}
		}
		?>

		</style>
		<div class="cal">
			<?php echo $this->output_cal_header(); ?>
			<?php echo $this->output_cal_weekdays(); ?>
			
			<?php foreach($this->cal_tiles as $tile): ?>
				<?php $row_counter++; ?>
				
				<?php
				if($tile == 1 && $curr_month == true){
					$curr_month = false;
				}elseif($tile == 1 && $curr_month == false){
					$curr_month = true;
				}

				$classes = array('cal-day');
				if(isset($events['tiles'][$tile]) && $curr_month == true){
					$classes[] = 'has-event';
				}
				if($curr_month == false){
					$classes[] = 'prev-next';
				}
				?>

				<?php if($row_counter == 1): ?><div class="cal-days-row"><?php endif; ?>
					<div class="<?php echo implode(' ', $classes); ?>">
						<div class="cal-day-wrapper">
						<span class="date" title="">
						<?php if($curr_month == true): ?>
							<a href="<?php echo add_query_arg(array( 'cal_day' => $tile, 'cal_month' => $this->month, 'cal_year' => $this->year)); ?>"><?php echo $tile; ?></a>
						<?php else: ?>
							<?php echo $tile; ?>
						<?php endif; ?>
						</span>
						<?php 
						if(isset($sorted_events[$tile]) && !empty($sorted_events[$tile]) && $curr_month == true){
							echo '<ul>'."\n";
							foreach($sorted_events[$tile] as $e){

								$temp = array();
								$post_event_cals = wp_get_post_terms( $e['id'], 'event_calendar');
								foreach($post_event_cals as $event){
									$temp[] = $event->slug;
								}

								if($e['parent'] == 0){
									$parent = $e['id'];
								}else{
									$parent = $e['parent'];
								}
								// add_query_arg('event_id', $e['id'])
								// if(!empty($temp)){
								// 	echo '<li class="event-list '.implode(' ', $temp ).'"><a href="'. $e['link'] .'">'.$e['title'].'</a></li>'."\n";	
								// }else{
								// 	echo '<li class="event-list">'.$e['title'].'</li>'."\n";	
								// }

								echo '<li class="event-list '.implode(' ', $temp ).'"><a href="'. $e['link'] .'">'.$e['title'].'</a></li>'."\n";
							}
							echo '</ul>'."\n";
						}
						?>
						</div><!-- .cal-day-wrapper -->
					</div><!-- /.cal-day -->
				<?php if($row_counter == 7): $row_counter = 0; ?></div><!-- /.cal-days-row --><?php endif; ?>
			<?php endforeach; ?>
			
		</div><!-- /.cal -->
		<?php
	}

}