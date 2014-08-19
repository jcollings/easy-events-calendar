<?php
class JCE_Event{

	/**
	 * Event Id
	 * @var int
	 */
	public $ID = null;

	/**
	 * Event Title
	 * @var string
	 */
	public $title = null;

	/**
	 * Event Content
	 * @var string
	 */
	public $content = null;

	/**
	 * Event Start Date
	 * @var datetime
	 */
	public $start_date = null;

	/**
	 * Event End Date
	 * @var datetime
	 */
	public $end_date = null;

	/**
	 * Event Slug
	 * @var string
	 */
	public $slug = '';

	/**
	 * Setup event
	 * @param int/string $event event id or name
	 * @param int  $day   
	 * @param int  $month 
	 * @param int  $year
	 */
	public function __construct($event = false, $day = null, $month = null, $year = null){

		if($event === false || !$this->setup_event($event, $day, $month, $year)){
			return false;
		}
	}

	private function setup_event($event, $day = null, $month = null, $year = null){

		if($event instanceof WP_Post){
			$event = $event->ID;
		}

		// check to see if valid event date
		$args = array(
			'post_type' => 'events',
		);

		
		if($day && $month && $year){

			// check for date
			$args['meta_query'] = array(
				array(
					'key' => '_revent_start_date',
					'value' => "$year-$month-$day",
					'compare' => '=',
					'type' => 'DATE'
				),
			);
		}

		if(intval($event) > 0){
			$args['p'] = intval($event);
		}else{
			$args['name'] = $event;
		}

		$this->query = new WP_Query($args);

		if($this->query->found_posts == 1){			
			
			// set class data
			$post = $this->query->post;
			$this->ID = $post->ID;
			$this->title = $post->post_title;
			$this->content = $post->post_content;
			$this->slug = $post->post_name;

			// setup dates
			$length = get_post_meta( $this->ID, "_event_length", true );	// event length

			if($day && $month && $year){
				
				// event start
				$this->start_date = $this->get_start_date("$year-$month-$day");	
			}else{
				
				// get first date
				$this->start_date = $this->get_start_date();
			}

			// event end
			$this->end_date = date("Y-m-d h:i:s", strtotime($this->start_date) + $length);
			return true;
		}

		return false;
	}

	private function get_start_date($date = false){

		global $wpdb;
		
		if($date){

			$r = $wpdb->get_row("SELECT meta_value FROM wp_postmeta AS mt1 WHERE post_id={$this->ID} AND meta_key = '_revent_start_date' AND meta_value LIKE '$date %'");
			if($r && isset($r->meta_value)){
				return $r->meta_value;
			}
		}else{

			$r = $wpdb->get_row("SELECT meta_value FROM wp_postmeta AS mt1 WHERE post_id={$this->ID} AND meta_key = '_revent_start_date' ORDER BY meta_value ASC");
			if($r && isset($r->meta_value)){
				return $r->meta_value;
			}
		}
		
	}

	public function get_wpquery(){
		return $this->query;
	}
}