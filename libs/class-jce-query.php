<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Query{

	private $cal_month = null;
	private $cal_year = null;
	private $cal_day = null;
	private $tax_counter = 0;

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
				add_action('jce/before_event_archive', 'jce_output_monthly_archive_heading');
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

	public function get_daily_events($day = null, $month = null, $year = null){

		if(!empty($day)){
			$this->cal_day = $day;
		}

		if(!empty($month)){
			$this->cal_month = $month;
		}
		
		if(!empty($year)){
			$this->cal_year = $year;
		}

		add_filter( 'posts_clauses', array($this, 'setup_day_query'), 10);
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

		// setup term queries and merge with sql
		$tax_query = $this->setup_term_queries();
		$query = $this->merge_query_keys($query, $tax_query);

		remove_filter( 'posts_clauses', array($this, 'setup_upcoming_query'), 10);

		return $query;
	}

	public function setup_day_query($query){

		global $wpdb;

		$year = get_query_var( 'cal_year' ) ? get_query_var( 'cal_year' ) : $this->cal_year;
		$month = get_query_var( 'cal_month' ) ? get_query_var( 'cal_month' ) : $this->cal_month;
		$day = get_query_var( 'cal_day' ) ? get_query_var( 'cal_day' ) : $this->cal_day;

		$date = "$year-$month-$day";

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'event')
		AND ({$wpdb->prefix}posts.post_status = 'publish')
		AND mt3.meta_key = '_event_length'
		AND 
		(
			{$wpdb->prefix}postmeta.meta_key = '_event_start_date' 
			AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) <= '$date'
			AND mt2.meta_key = '_event_end_date'  
			AND CAST(mt2.meta_value AS DATE) >= '$date' 
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt1 ON ({$wpdb->prefix}posts.ID = mt1.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt2 ON ({$wpdb->prefix}posts.ID = mt2.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";
		$query['limits'] = "";

		$query = apply_filters( 'jce/setup_day_query', $query, $day, $month, $year);

		// setup term queries and merge with sql
		$tax_query = $this->setup_term_queries();
		$query = $this->merge_query_keys($query, $tax_query);

		remove_filter( 'posts_clauses', array($this, 'setup_day_query'), 10);

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
				{$wpdb->prefix}postmeta.meta_key = '_event_end_date' 
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

		// setup term queries and merge with sql
		$tax_query = $this->setup_term_queries();
		$query = $this->merge_query_keys($query, $tax_query);

		remove_filter( 'posts_clauses', array($this, 'setup_month_query'), 10);

		return $query;
	}

	public function setup_term_queries(){

		$this->tax_counter = 0;
		$temp = array();
		
		// integrate calendar taxonomy
		$calendars = get_query_var('event_calendar' );
		if($calendars){

			// explod terms with ',' into array
			if(strpos($calendars, ',') !== false){
				$calendars = explode(',', $calendars);
			}

			// generate query
			$tax_query = new WP_Tax_Query(array(
				array(
					'taxonomy' => 'event_calendar',
					'field' => 'slug',
					'terms' => $calendars
				)
			));

			$result = $tax_query->get_sql('wp_posts', 'ID');
			$result = $this->setup_tax_alias($result);
			$temp = $this->merge_query_keys( $temp, $result );
		}

		// integrate venue taxonomy
		$venues = get_query_var('event_venue' );
		if($venues){

			// explod terms with ',' into array
			if(strpos($venues, ',') !== false){
				$venues = explode(',', $venues);
			}

			// generate query
			$tax_query = new WP_Tax_Query(array(
				array(
					'taxonomy' => 'event_venue',
					'field' => 'slug',
					'terms' => $venues
				)
			));

			$result = $tax_query->get_sql('wp_posts', 'ID');
			$result = $this->setup_tax_alias($result);
			$temp = $this->merge_query_keys( $temp, $result );
		}

		// integrate organiser taxonomy
		$organisers = get_query_var('event_organiser' );
		if($organisers){

			// explod terms with ',' into array
			if(strpos($organisers, ',') !== false){
				$organisers = explode(',', $organisers);
			}

			// generate query
			$tax_query = new WP_Tax_Query(array(
				array(
					'taxonomy' => 'event_organiser',
					'field' => 'slug',
					'terms' => $organisers
				)
			));

			$result = $tax_query->get_sql('wp_posts', 'ID');
			$result = $this->setup_tax_alias($result);
			$temp = $this->merge_query_keys( $temp, $result );
		}

		// integrate event category taxonomy
		$categories = get_query_var('event_category' );
		if($categories){

			// explod terms with ',' into array
			if(strpos($categories, ',') !== false){
				$categories = explode(',', $categories);
			}

			// generate query
			$tax_query = new WP_Tax_Query(array(
				array(
					'taxonomy' => 'event_category',
					'field' => 'slug',
					'terms' => $categories
				)
			));

			$result = $tax_query->get_sql('wp_posts', 'ID');
			$result = $this->setup_tax_alias($result);
			$temp = $this->merge_query_keys( $temp, $result );
		}

		// integrate event tag taxonomy
		$tags = get_query_var('event_tag' );
		if($tags){

			// explod terms with ',' into array
			if(strpos($tags, ',') !== false){
				$tags = explode(',', $tags);
			}

			// generate query
			$tax_query = new WP_Tax_Query(array(
				array(
					'taxonomy' => 'event_tag',
					'field' => 'slug',
					'terms' => $tags
				)
			));

			$result = $tax_query->get_sql('wp_posts', 'ID');
			$result = $this->setup_tax_alias($result);
			$temp = $this->merge_query_keys( $temp, $result );
		}

		return $temp;
	}

	/**
	 * Add table alias to queries
	 * @param  array $result query array
	 * @return array
	 */
	public function setup_tax_alias($result){
		global $wpdb;

		$this->tax_counter++;

		foreach($result as $k => &$v){
			if($k == 'join'){
				$v = preg_replace("/{$wpdb->term_relationships}/", "{$wpdb->term_relationships} AS jce_tr{$this->tax_counter}", $v, 1);	
			}
			$v = preg_replace("/{$wpdb->term_relationships}\./", "jce_tr{$this->tax_counter}.", $v);	
		}

		return $result;
	}

	/**
	 * Merge query arrays
	 * @param  array $main query array
	 * @param  array $new query array
	 * @return array
	 */
	public function merge_query_keys($main, $new){

		$keys = array_unique( array_merge( array_keys( $main ), array_keys( $new ) ) );
		$output = array();

		foreach($keys as $k){

			if(isset($main[$k])){
				$output[$k] .= $main[$k];
			}

			if(isset($new[$k])){
				$output[$k] .= $new[$k];
			}
		}

		return $output;

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
				if(isset($_GET['event_day']) && isset($_GET['event_month']) && isset($_GET['event_year'])){
					$day = $_GET['event_day'];
					$month = $_GET['event_month'];
					$year = $_GET['event_year'];
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