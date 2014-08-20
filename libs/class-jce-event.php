<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JC_Event{

	private $id = null;

	private $post = null;

	private $meta = null;

	public function __construct($event, $date = null){

		$this->id = null;
		$this->post = null;
		$this->meta = null;

		if(is_numeric($event)){
			$this->id = absint($event);
			$this->post = get_post($event);
		}elseif(isset($event->ID)){
			$this->id = absint($event->ID);
			$this->post = get_post($event);
		}

		if($this->post){

			// set event date from passed date
			if($date){
				$temp_date = $this->is_valid_event_date($date);
				if($temp_date){
					$event_length = get_post_meta( $this->id, '_event_length', true);
					$this->meta['_event_start_date'] = $temp_date;
					$this->meta['_event_end_date'] = date('Y-m-d H:i:s', strtotime($temp_date) + $event_length);
				}
			}

			if(isset($this->post->start_date)){
				$this->meta['_event_start_date'] = $this->post->start_date;
			}
			if(isset($this->post->end_date)){
				$this->meta['_event_end_date'] = $this->post->end_date;
			}
		}
	}

	public function get_id(){
		return $this->id;
	}

	private function is_valid_event_date($date){

		$temp_date = date("Y-m-d", strtotime($date));

		global $wpdb;
		$result = $wpdb->get_row("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id='{$this->id}' AND meta_key='_revent_start_date' AND meta_value LIKE '{$temp_date}%'");
		if($result){
			return $result->meta_value;
		}
		return false;
	}

	public function get_post_meta(){

		// default values
		$meta = array(
			'_event_start_date' => date('Y-m-d 09:00:00'),
			'_event_end_date' => date('Y-m-d 17:00:00'),
			'_event_all_day' => 'no',
		);

		// if no event start date has been set, fetch from postmeta
		if(!isset($this->meta['_event_start_date'])){
			$_event_start_date = get_post_meta( $this->id, '_event_start_date', true );
			if(!$_event_start_date){
				$this->meta['_event_start_date'] = $meta['_event_start_date'];
			}else{
				$this->meta['_event_start_date'] = $_event_start_date;
			}
		}

		// if no event end date has been set, fetch from postmeta
		if(!isset($this->meta['_event_end_date'])){
			$_event_end_date = get_post_meta( $this->id, '_event_end_date', true );
			if(!$_event_end_date){
				$this->meta['_event_end_date'] = $meta['_event_end_date'];
			}else{
				$this->meta['_event_end_date'] = $_event_end_date;
			}
		}

		// fetch event all day flag
		$_event_all_day = get_post_meta( $this->id, '_event_all_day', true );
		if(!$_event_all_day){
			$this->meta['_event_all_day'] = $meta['_event_all_day'];
		}else{
			$this->meta['_event_all_day'] = $_event_all_day;
		}

		return $this->meta;
	}
}