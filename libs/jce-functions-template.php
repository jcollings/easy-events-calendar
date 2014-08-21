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

		// time for some pagination
		$page_count = ceil($total_posts / $posts_per_page);
		for($x = 1; $x <= $page_count; $x++){

			if($x == $current){
				echo '<span>'.$x.'</span>';
			}else{
				echo '<a href="'.add_query_arg('paged', $x).'">'.$x.'</a>';
			}
		}
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