<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Query{

	private $cal_month = null;
	private $cal_year = null;

	public function __construct(){

		$this->cal_month = date('m');
		$this->cal_year = date('Y');

		if(!is_admin()){
			add_action( 'pre_get_posts', array($this, 'pre_get_posts'), 0);
			add_action('the_post', array($this, 'the_post'));
		}
	}

	public function pre_get_posts($query){

		if($query->is_main_query() && $query->is_post_type_archive( 'event' )){

			// calendar or upcoming view
			$view = get_query_var('view') ? get_query_var('view' ) : JCE()->default_view;
			if($view == 'calendar'){
				add_filter( 'posts_clauses', array($this, 'setup_month_query'), 10);
				remove_action( 'jce/after_event_loop', 'jce_output_pagination' );
			}elseif($view == 'archive'){
				remove_action( 'jce/before_event_content', 'jce_add_archive_month');
				add_action('jce/before_event_archive', 'output_archive_heading');
				add_filter( 'posts_clauses', array($this, 'setup_month_query'), 10);
				remove_action( 'jce/after_event_loop', 'jce_output_pagination' );
			}else{
				add_filter( 'posts_clauses', array($this, 'setup_upcoming_query'), 10);
			}
		}
	}

	public function get_events($args = array()){
		add_filter( 'posts_clauses', array($this, 'setup_upcoming_query'), 10);
		
		$query_args = array(
			'post_type' => 'event',
			'posts_per_page' => 5
		);

		if(isset($args['posts_per_page'])){
			$query_args['posts_per_page'] = $args['posts_per_page'];
		}

		if(isset($args['paged'])){
			$query_args['paged'] = $args['paged'];
		}

		return new WP_Query($query_args);
	}

	public function get_calendar($month = null, $year = null, $args = array()){

		if(!empty($month)){
			$this->cal_month = $month;
		}
		
		if(!empty($year)){
			$this->cal_year = $year;
		}

		add_filter( 'posts_clauses', array($this, 'setup_month_query'), 10);
		return new WP_Query(array(
			'post_type' => 'event'
		));
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

	public function setup_month_query($query){

		global $wpdb;

		$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : $this->cal_year;
		$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : $this->cal_month;

		$start_date = "$year-$month-01";
		$end_date = "$year-$month-31";

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'event')
		AND ({$wpdb->prefix}posts.post_status = 'publish')
		AND mt3.meta_key = '_event_length'
		AND 
		(
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date' 
				AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) >= '$start_date' 
				AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) <= '$end_date' 
			)
			OR
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date' 
				AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) >= '$start_date' 
				AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) <= '$end_date' 
			)
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt1 ON ({$wpdb->prefix}posts.ID = mt1.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt2 ON ({$wpdb->prefix}posts.ID = mt2.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";
		$query['limits'] = "";

		$query = apply_filters( 'jce/setup_month_query', $query, $month, $year);
		remove_filter( 'posts_clauses', array($this, 'setup_month_query'), 10);

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

				JCE()->event = new JCE_Event($post, $date);

			}else{
				JCE()->event = new JCE_Event($post);
			}			
		}
	}
}

return new JCE_Query();