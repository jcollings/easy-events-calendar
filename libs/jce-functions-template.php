<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jce_get_template_part($template){

	$located = JCE()->plugin_dir . 'templates/'.$template.'.php';
	$template_file = get_stylesheet_directory() . '/jcevents/'.$template.'.php';
	if(is_file($template_file)){
		$located = $template_file;
	}

	return load_template( $located, false );
}

add_filter( 'post_type_link', 'jce_post_link', 10 , 3);
function jce_post_link($post_link, $post, $leavename){

	if( $post->post_type == 'event' && JCE()->event){
		
		$meta = JCE()->event->get_post_meta();
		if(isset($meta['_event_start_date'])){
			return add_query_arg(array('event_day' => date('d', strtotime($meta['_event_start_date'])), 'event_month' => date('m', strtotime($meta['_event_start_date'])), 'event_year' => date('Y', strtotime($meta['_event_start_date']))), $post_link);	
		}
	}

	return $post_link;
}

function jce_get_permalink($args = array()){

	if(isset($args['id'])){
		$id = $args['id'];
	}else{
		return false;
	}

	if(isset($args['date'])){
		$year = date('Y', strtotime($args['date']));
		$month = date('m', strtotime($args['date']));
		$day = date('d', strtotime($args['date']));
	}elseif( isset($args['d']) && isset($args['m']) && isset($args['y'])){
		$year = $args['y'];
		$month = $args['m'];
		$day = $args['d'];
	}else{
		return false;
	}

	return add_query_arg(array('event_day' => $day, 'event_month' => $month, 'event_year' => $year), get_permalink($id));
}

function jce_event_venue_meta($key = 'name', $echo = true){

	$event_id = JCE()->event->get_id();
	$term = wp_get_object_terms( $event_id, 'event_venue' );
	$result = '';

	if($term){
		$term = array_shift($term);
		switch($key){
			case 'name':
				$result = $term->name;
			break;
			case 'address':
			case 'city':
			case 'postcode':
				$t_id = $term->term_id;
				$meta = get_option( "event_venue_$t_id" );
				$result = isset($meta['venue_'.$key]) ? $meta['venue_'.$key] : '';
			break;
		}
	}

	if(!$echo)
		return $result;

	echo $result;
}

function jce_event_organiser_meta($key = 'name', $echo = true){

	$event_id = JCE()->event->get_id();
	$term = wp_get_object_terms( $event_id, 'event_organiser' );
	$result = '';

	if($term){
		$term = array_shift($term);
		switch($key){
			case 'name':
				$result = $term->name;
			break;
			case 'phone':
			case 'website':
			case 'email':
				$t_id = $term->term_id;
				$meta = get_option( "event_organiser_$t_id" );
				$result = isset($meta['organiser_'.$key]) ? $meta['organiser_'.$key] : '';
			break;
		}
	}

	if(!$echo)
		return $result;
	
	echo $result;
}

function jce_pagination($total_posts = false, $posts_per_page = false){

	if(!$total_posts){
		global $wp_query;
		$total_posts = $wp_query->found_posts;
		$posts_per_page = $wp_query->query_vars['posts_per_page'];
	}

	$current = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

	if($total_posts > $posts_per_page){

		echo "<div class=\"jce-pagination\">";

		// time for some pagination
		$page_count = ceil($total_posts / $posts_per_page);
		for($x = 1; $x <= $page_count; $x++){

			if($x == $current){
				echo '<span>'.$x.'</span>';
			}else{
				echo '<a href="'.add_query_arg('paged', $x).'">'.$x.'</a>';
			}
		}

		echo "</div>";
	}
}

function jce_event_start_date($format = 'd/m/Y', $echo = true){

	$event = JCE()->event->get_post_meta();
	$date = $event['_event_start_date'];
	$time = strtotime($date);
	$output = date($format, $time);

	if(!$echo)
		return $output;

	echo $output;
}

function jce_event_end_date($format = 'd/m/Y', $echo = true){

	$event = JCE()->event->get_post_meta();
	$date = $event['_event_end_date'];
	$time = strtotime($date);
	$output = date($format, $time);

	if(!$echo)
		return $output;

	echo $output;
}

// Add event title to event
add_action( 'jce/single_event_header', 'jce_add_event_title', 10 );
add_action( 'jce/event_header', 'jce_add_event_title', 10 );
function jce_add_event_title(){

	if(is_single()){
		jce_get_template_part('single-event/title');
	}else{
		jce_get_template_part('archive-event/title');
	}	
}

// Add event meta to event
add_action( 'jce/single_event_header', 'jce_add_event_meta', 20 );
add_action( 'jce/event_header', 'jce_add_event_meta', 20 );
function jce_add_event_meta(){
	?>
	<div class="jce-event-meta">
		<p>From: <?php jce_event_start_date('jS F Y g:i a'); ?> - <?php jce_event_end_date('jS F Y g:i a'); ?></p>
	</div>
	<?php
}

add_filter( 'jce/event_title', 'jce_update_event_title' );
function jce_update_event_title($title){

	$start_date = jce_event_start_date('jS', false);
	$end_date = jce_event_end_date('jS', false);

	if($start_date != $end_date){
		$title .= sprintf(": (%s - %s)", $start_date, $end_date);
	}else{
		$title .= sprintf(": %s", $start_date);
	}

	return $title;
}

/**
 * Event Archive Content
 */
add_action( 'jce/before_event_content', 'jce_add_archive_month');
function jce_add_archive_month(){

	global $current_month;

	// display month name in archive
	$month = jce_event_start_date('F', false);
	if($current_month != $month){
		$current_month = $month;

		$title = sprintf("%s %s", $current_month, jce_event_start_date('Y', false));
		$title = apply_filters( 'jce/archive_month_title', $title );
		echo sprintf("<h2 class=\"jce-archive-title\">%s</h2>", $title);
	}
}

add_action( 'jce/before_event_archive', 'jce_before_event_archive');
function jce_before_event_archive(){
	?>
	<div class="jce-event-archive">
	<?php
}

add_action( 'jce/after_event_archive', 'jce_after_event_archive');
function jce_after_event_archive(){
	?>
	</div>
	<?php
}

/**
 * Display monthly title in archive only
 */
function jce_output_monthly_archive_heading(){

	$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : date('Y');
	$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : date('m');

	if(($month - 1) <= 0){
		$prev_link = add_query_arg(array('cal_year' => ($year - 1), 'cal_month' => 12));
		$next_link = add_query_arg(array('cal_year' => $year, 'cal_month' => ($month+1)));
	}elseif(($month + 1) > 12){
		$prev_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month-1)));
		$next_link = add_query_arg(array('cal_year' => ($year+1), 'cal_month' => (1)));
	}else{
		$prev_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month-1)));
		$next_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month+1)));
	}

	$title = date('F Y', strtotime("$year-$month-01")); 

	echo "<h2 class=\"jce-archive-title\">".$title." - [<a href=\"".$prev_link."\">&lt;</a>][<a href=\"".$next_link."\">&gt;</a>]</h2>";
}

/**
 * Display monthly title in archive only
 */
function jce_output_daily_archive_heading(){

	$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : date('Y');
	$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : date('m');
	$day = get_query_var( 'cal_day' ) ? get_query_var( 'cal_day' ) : date('d');

	// if(($month - 1) <= 0){
	// 	$prev_link = add_query_arg(array('cal_year' => ($year - 1), 'cal_month' => 12));
	// 	$next_link = add_query_arg(array('cal_year' => $year, 'cal_month' => ($month+1)));
	// }elseif(($month + 1) > 12){
	// 	$prev_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month-1)));
	// 	$next_link = add_query_arg(array('cal_year' => ($year+1), 'cal_month' => (1)));
	// }else{
	// 	$prev_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month-1)));
	// 	$next_link = add_query_arg(array('cal_year' => ($year), 'cal_month' => ($month+1)));
	// }

	$title = date('l jS, F Y', strtotime("$year-$month-$day")); 

	echo "<h2 class=\"jce-archive-title\">".$title."</h2>";
}

/**
 * Do pagination
 */
add_action( 'jce/after_event_loop', 'jce_output_pagination' );
function jce_output_pagination(){
	jce_pagination();
}

/**
 * Single Event content
 */
add_action('jce/single_event_content', 'jce_add_single_event_content', 10);
function jce_add_single_event_content(){
	?>
	<div class="jce-event-content">
		<h2>Event Details</h2>
		<?php the_content(); ?>
	</div>
	<?php
}

add_action('jce/single_event_content', 'jce_add_single_event_venue', 15);
function jce_add_single_event_venue(){
	?>
	<div clas="jce-event-venue">
		<h2>Venue</h2>
		<p>Name: <?php jce_event_venue_meta(); ?><br />
		Address: <?php jce_event_venue_meta('address'); ?><br />
		City: <?php jce_event_venue_meta('city'); ?><br />
		Postcode: <?php jce_event_venue_meta('postcode'); ?></p>
	</div>
	<?php
}

add_action('jce/single_event_content', 'jce_add_single_event_organiser', 15);
function jce_add_single_event_organiser(){
	?>
	<div clas="jce-event-organiser">
		<h2>Organiser</h2>
		<p>Name: <?php jce_event_organiser_meta(); ?><br />
		Phone: <?php jce_event_organiser_meta('phone'); ?><br />
		Email: <?php jce_event_organiser_meta('email'); ?><br />
		Website: <?php jce_event_organiser_meta('website'); ?></p>
	</div>
	<?php
}

add_action('jce/before_event_loop', 'jce_add_single_back_btn');
function jce_add_single_back_btn(){

	if(!is_single())
		return false;
	?>
	<a href="<?php echo site_url('?post_type=event'); ?>">&lt; Back to Events</a>
	<?php
}

add_action('jce/after_event_calendar', 'jce_output_daily_archive');
function jce_output_daily_archive(){

	$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : date('Y');
	$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : date('m');
	$day = get_query_var( 'cal_day' ) ? get_query_var( 'cal_day' ) : date('d');

	echo do_shortcode('[jce_event_archive view="archive" year="'.$year.'" month="'.$month.'" day="'.$day.'" /]' );	
}