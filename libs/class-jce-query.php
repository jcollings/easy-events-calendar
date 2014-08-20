<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Query{

	public function __construct(){

		add_action( 'pre_get_posts', array($this, 'pre_get_posts'), 0);
		add_action('the_post', array($this, 'the_post'));
	}

	public function pre_get_posts($query){

		if($query->is_main_query() && $query->is_post_type_archive( 'event' )){

			// calendar or upcoming view
			add_filter( 'posts_clauses', array($this, 'setup_upcoming_query'), 10);
		}
	}

	public function setup_upcoming_query($query){

		global $wpdb;

		$date = date('Y-m-d');

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'event')
		AND ({$wpdb->prefix}posts.post_status = 'publish')
		AND 
		(
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date'
				AND mt3.meta_key = '_event_length'
				AND  (CAST({$wpdb->prefix}postmeta.meta_value AS DATE) >= '$date')
			)
			OR
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date'
				AND (mt3.meta_key = '_event_length' AND CAST(DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) >= '$date')
			)
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt2 ON ({$wpdb->prefix}posts.ID = mt2.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";

		$query = apply_filters( 'jce/setup_upcoming_query', $query);
		remove_filter( 'posts_clauses', array($this, 'setup_upcoming_query'), 10);

		return $query;
	}

	/**
	 * Setup current event in loop
	 * 
	 * @param  WP_Post $post
	 * @return void
	 */
	public function the_post($post){

		if($post->post_type == 'event'){

			if(is_singular('event' )){

				// set event date
				if(isset($_GET['ed']) && isset($_GET['em']) && isset($_GET['ey'])){
					$day = $_GET['ed'];
					$month = $_GET['em'];
					$year = $_GET['ey'];
					$date = sprintf("%d-%d-%d", $year, $month, $day);
				}else{
					$date = null;
				}

				JCE()->event = new JC_Event($post, $date);

			}else{
				JCE()->event = new JC_Event($post);
			}			
		}
	}
}

new JCE_Query();