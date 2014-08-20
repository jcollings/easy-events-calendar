<?php 

class EventsModel{
	
	static $config;
	static $keys = array('_event_start_date', '_event_end_date', '_event_venue', '_event_address', '_event_city', '_event_postcode', '_organizer_name', '_organizer_phone', '_organizer_website', '_organizer_email', '_event_price', '_event_all_day' /*,'_recurrence_type', '_recurrence_num', '_recurrence_space', '_recurrence_end', '_event_calendar'*/ );
	static $curr_year;
	static $curr_month;
	static $event = array();
	static $events = array();
	static $calendar = 'all';


	/**
	 * Setup the config
	 * 
	 * Called to setup the global config
	 * 
	 * @param  Class &$config 
	 * @return void
	 */
	static function init(&$config){
		self::$config = $config;
	}

	static function trash_child_events($post_id = 0){
		if(intval($post_id) <= 0)
			return false;

		$query = new WP_Query(array(
			'post_parent' => $post_id,
			// 'post_type' => 'recurring_events',
			'post_type' => 'events',
			'nopaging' => true
		));

		foreach($query->posts as $post){
			wp_update_post( array('ID' => $post->ID, 'post_status' => 'trash' ) );
		}
	}

	static function get_month($year, $month, $options = array()){

		if(isset($options['cal']) && !empty($options['cal']) ){
			self::$calendar = $options['cal'];
		}

		$args = array(
			'post_type' => 'events',
			'post_status'	=> 'publish',
			'order'		=> 'ASC',
			'orderby'	=> 'meta_value',
			'meta_key' 	=> '_event_start_date',
			'nopaging' => true,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_revent_start_date',
					'value' => $year.'-0'.$month.'-01', // '20/'.$month.'/'.$year,
					'compare' => '>=',
					'type' => 'DATE'
				),
				array(
					'key' => '_revent_start_date',
					'value' => $year.'-0'.$month.'-31', // '20/'.$month.'/'.$year,
					'compare' => '<=',
					'type' => 'DATE'
				)
			)
		);

		self::$curr_year = $year;
		self::$curr_month = $month;

		add_filter( 'posts_clauses', array(__CLASS__, 'setup_month_query'), 10);
		$result = new WP_Query( $args );
		remove_filter( 'posts_clauses', array(__CLASS__, 'setup_month_query'), 10);
		return $result;
	}


	static function setup_month_query($query){

		global $wpdb;

		$year = self::$curr_year;
		$month = self::$curr_month;

		$start_date = "$year-$month-01";
		$end_date = "$year-$month-31";

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'events')
		AND ({$wpdb->prefix}posts.post_status = 'publish')
		AND 
		(
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date'
				AND mt3.meta_key = '_event_length'
				AND  (mt1.meta_key = '_event_start_date' AND CAST(mt1.meta_value AS DATE) >= '$start_date')
					AND  (mt2.meta_key = '_event_start_date' AND CAST(mt2.meta_value AS DATE) <= '$end_date') 
			)
			OR
			(
				{$wpdb->prefix}postmeta.meta_key = '_event_start_date'
				AND (mt1.meta_key = '_event_start_date' AND mt3.meta_key = '_event_length' AND CAST(DATE_ADD(mt1.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) >= '$start_date')
				AND (mt1.meta_key = '_event_start_date' AND mt3.meta_key = '_event_length' AND CAST(DATE_ADD(mt1.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) <= '$end_date')
			)
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt1 ON ({$wpdb->prefix}posts.ID = mt1.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt2 ON ({$wpdb->prefix}posts.ID = mt2.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";

		$query = apply_filters( 'jce/setup_month_query', $query, $month, $year);
		return $query;
	}

	static function get_events($limit = 0, $offset = 0)
	{
		$args = array(
			'post_type' => 'events',
			'post_status'	=> 'publish',
			'order'		=> 'ASC',
			'orderby'	=> 'meta_value',
			'meta_key' 	=> '_event_start_date',
		);
		if(intval($limit) > 0)
			$args['posts_per_page'] = intval($limit);

		if(intval($offset) > 0)
			$args['paged'] = $offset;
		
		return new WP_Query( $args );
	}

	static function get_upcoming_events($limit = 0, $offset = 0)
	{
		$args = array(
			'post_type' => 'events',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => '_event_start_date',
					'value' => date('Y-m-d'), // '20/'.$month.'/'.$year,
					'compare' => '>=',
					'type' => 'DATE'
				),
				array(
					'key' => '_event_end_date',
					'value' => date('Y-m-d'), // '20/'.$month.'/'.$year,
					'compare' => '>=',
					'type' => 'DATE'
				),
			),
			'post_status'	=> 'publish',
			'order'		=> 'ASC',
			'orderby'	=> 'meta_value',
			'meta_key' 	=> '_event_start_date',
			
		);
		if(intval($limit) > 0)
			$args['posts_per_page'] = intval($limit);

		if(intval($offset) > 0)
			$args['paged'] = intval($offset);
		
		add_filter( 'posts_clauses', array(__CLASS__, 'setup_upcoming_query'), 10);
		$result = new WP_Query( $args );
		remove_filter( 'posts_clauses', array(__CLASS__, 'setup_upcoming_query'), 10);

		return $result;
	}

	static function setup_upcoming_query($query){

		global $wpdb;

		$date = date('Y-m-d');

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'events')
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

		return $query;
	}

	static function get_event($event_id)
	{
		$args = array(
			'post_type' => array('events', 'recurring_events'),
			'post_status'	=> 'publish',
			'p' => $event_id,
			'posts_per_page' => 1
		);		
		return new WP_Query( $args );
	}

	static function get_event_meta($post_id)
	{
		$values = array();
		$default = array(
			'_event_start_date' => date('Y-m-d H:00:00'),
			'_event_end_date' => date('Y-m-d H:30:00'),
			'_event_venue' => null,
			'_event_address' => null,
			'_event_city' => null,
			'_event_postcode' => null,
			'_organizer_name' => null,
			'_organizer_phone' => null,
			'_organizer_website' => null,
			'_organizer_email' => null,
			'_event_price' => null,
			'_recurrence_type' => null,
			'_recurrence_num' => null,
			'_recurrence_space' => null,
			'_recurrence_end' => null,
			'_event_calendar' => null,
			'_event_all_day' => 'no'
		);

		foreach(self::$keys as $key){
			if($key != '_event_end_date'){
				$values[$key] = get_post_meta( $post_id, $key, true );		
			}
		}

		$values['_event_end_date'] = get_post_meta( $post_id, '_event_end_date', true );		
		
		if(strtotime($values['_event_start_date']) > strtotime($values['_event_end_date'])){
			$values['_event_end_date'] = $values['_event_start_date'];
		}
		
		return is_array($values) ? array_merge($default, $values) : $default;
	}

	static function get_recurrence_type($output = true){
		global $post;
		
		$temp = get_post_meta( $post->ID, '_recurrence_type', true );
		if(empty($temp)){
			$temp = 'None';
		}

		if($output){
			echo ucfirst($temp);
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Start Date
	 *
	 * Return formated Event Start date if output is false, otherwise output.
	 * @param  string  $format date format string
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_start_date($format = 'd-m-Y H:i:s', $id = 0, $output = true)
	{
		if($id <= 0){

			global $jce_event, $post;
			if($jce_event){
				$temp = $jce_event->start_date;
			}else{
				$temp = get_post_meta( $post->ID, '_event_start_date', true );
			}
		}else{

			$temp = get_post_meta( $id, '_event_start_date', true );
		}
		
		$temp = date($format, strtotime($temp));
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event End Date
	 *
	 * Return formated Event End date if output is false, otherwise output.
	 * @param  string  $format date format string
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_end_date($format = 'd-m-Y H:i:s', $id = 0,  $output = true)
	{
		if($id <= 0){
			global $jce_event, $post;
			if($jce_event){
				$temp = $jce_event->end_date;
			}else{
				$temp = get_post_meta( $post->ID, '_event_end_date', true );
			}
		}else{
			$temp = get_post_meta( $id, '_event_end_date', true );
		}
		
		$temp = date($format, strtotime($temp));
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Venue
	 *
	 * Return Event Venue if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_venue($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_event_venue', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Address
	 *
	 * Return Event Address if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_address($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_event_address', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event City
	 *
	 * Return Event City if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_city($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_event_city', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Postcode
	 *
	 * Return Event Postcode if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_postcode($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_event_postcode', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Organizer Name
	 *
	 * Return Event Organizer Name if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_organizer_name($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_organizer_name', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Phone
	 *
	 * Return Event Phone if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_organizer_phone($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_organizer_phone', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Website
	 *
	 * Return Event Website if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_organizer_website($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_organizer_website', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Email
	 *
	 * Return Event Email if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_organizer_email($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_organizer_email', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

	/**
	 * Output Event Price
	 *
	 * Return Event Price if output is false, otherwise output.
	 * @param  boolean $output true = echo, false = return value
	 * @return string
	 */
	static function get_price($output = true)
	{
		global $post;
		$temp = get_post_meta( $post->ID, '_event_price', true );
		if($output){
			echo $temp;
		}else{
			return $temp;
		}
	}

}
?>