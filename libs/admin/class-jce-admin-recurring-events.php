<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Admin_RecurringEvents{

	private $meta_id = 'jcevents';
	private $meta_key = 'jcevents';

	public function __construct(){

		add_action( 'jce/admin_meta_fields', array( $this, 'show_metabox' ), 10, 2 );
		add_action('jce/save_event', array( $this, 'save_event'), 10, 1);

		add_filter( 'jce/setup_day_query', array( $this, 'setup_day_query'), 10 , 4);
		add_filter( 'jce/setup_month_query', array( $this, 'setup_month_query'), 10 , 3);
		add_filter( 'jce/setup_upcoming_query', array( $this, 'setup_upcoming_query'), 10 , 1);

		// admin events columns
		add_filter('manage_event_posts_columns', array($this, 'admin_columns_head'), 15);
		add_action('manage_event_posts_custom_column', array($this, 'admin_columns_content'), 15); 
	}

	public function show_metabox($object, $box){

		$_recurrence_type = get_post_meta( $object->ID, '_recurrence_type', true );
		$_recurrence_space = get_post_meta( $object->ID, '_recurrence_space', true );
		$_recurrence_end = get_post_meta( $object->ID, '_recurrence_end', true );
		?>
		<div class="input select required">
			<label>Recurrance:</label>
			<select name="<?php echo $this->meta_id; ?>[_recurrence_type]" id="<?php echo $this->meta_id; ?>_recurrence_type">
				<option value="none">None</option>
				<option value="day" <?php if($_recurrence_type == 'day'): ?>selected="selected"<?php endif; ?>>Daily</option>
				<option value="week" <?php if($_recurrence_type == 'week'): ?>selected="selected"<?php endif; ?>>Weekly</option>
				<option value="month" <?php if($_recurrence_type == 'month'): ?>selected="selected"<?php endif; ?>>Monthly</option>
				<option value="year" <?php if($_recurrence_type == 'year'): ?>selected="selected"<?php endif; ?>>Yearly</option>
			</select>
		</div>
		<fieldset id="recurrence_fields">
			<?php /* Occurences */ ?>
			<div class="input select recurrence_year recurrence_specific">
				<p>Every <select id="<?php echo $this->meta_id; ?>_recurrence_space" name="<?php echo $this->meta_id; ?>[_recurrence_year_space]">
					<?php for($x = 1; $x <= 30; $x++): ?>
					<option value="<?php echo $x; ?>" <?php if($_recurrence_space == $x): ?>selected="selected"<?php endif; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select> Years</p>
			</div>
			<div class="input select recurrence_month recurrence_specific">
				<p>Every <select id="<?php echo $this->meta_id; ?>_recurrence_space" name="<?php echo $this->meta_id; ?>[_recurrence_month_space]">
					<?php for($x = 1; $x <= 30; $x++): ?>
					<option value="<?php echo $x; ?>" <?php if($_recurrence_space == $x): ?>selected="selected"<?php endif; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select> Months</p>
			</div>
			<div class="input select recurrence_week recurrence_specific">
				<p>Every <select id="<?php echo $this->meta_id; ?>_recurrence_space" name="<?php echo $this->meta_id; ?>[_recurrence_week_space]">
					<?php for($x = 1; $x <= 51; $x++): ?>
					<option value="<?php echo $x; ?>" <?php if($_recurrence_space == $x): ?>selected="selected"<?php endif; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select> Weeks</p>
			</div>
			<div class="input select recurrence_day recurrence_specific">
				<p>Every <select id="<?php echo $this->meta_id; ?>_recurrence_space" name="<?php echo $this->meta_id; ?>[_recurrence_day_space]">
					<?php for($x = 1; $x <= 6; $x++): ?>
					<option value="<?php echo $x; ?>" <?php if($_recurrence_space == $x): ?>selected="selected"<?php endif; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select> Days</p>
			</div>

			<?php /* Last Occurence */ ?>
			<div class="input select">
				<p>Ends after <select id="<?php echo $this->meta_id; ?>_recurrence_end" name="<?php echo $this->meta_id; ?>[_recurrence_end]">
					<?php for($x = 1; $x <= 30; $x++): ?>
					<option value="<?php echo $x; ?>" <?php if($_recurrence_end == $x): ?>selected="selected"<?php endif; ?>><?php echo $x; ?></option>
					<?php endfor; ?>
				</select> Occurances</p>
			</div>
		</fieldset>
		<?php

	}

	public function save_event($event_id){

		global $wpdb;

		// remove all existing recurring start dates
		$wpdb->delete($wpdb->postmeta, array('post_id' => $event_id, 'meta_key' => '_revent_start_date'));

		// add start date for every event
		add_post_meta( $event_id, '_revent_start_date', $_POST[$this->meta_id]['_event_start_date']);

		$event_length = strtotime($_POST[$this->meta_id]['_event_end_date']) - strtotime($_POST[$this->meta_id]['_event_start_date']);
		$start_day = $_POST[$this->meta_id]['_event_start_date'];

		if(!empty($_POST[$this->meta_id]['_recurrence_type'])){
			switch($_POST[$this->meta_id]['_recurrence_type']){
				case 'year':
				$_POST[$this->meta_id]['_recurrence_space'] = $_POST[$this->meta_id]['_recurrence_year_space'];	
				break;
				case 'month':
				$_POST[$this->meta_id]['_recurrence_space'] = $_POST[$this->meta_id]['_recurrence_month_space'];	
				break;
				case 'week':
				$_POST[$this->meta_id]['_recurrence_space'] = $_POST[$this->meta_id]['_recurrence_week_space'];	
				break;
				case 'day':
				$_POST[$this->meta_id]['_recurrence_space'] = $_POST[$this->meta_id]['_recurrence_day_space'];	
				break;
			}
		}

		// add recuring event start dates
		if(!empty($_POST[$this->meta_id]['_recurrence_type']) && $_POST[$this->meta_id]['_recurrence_type'] != 'none'){
			$ocurrences = intval($_POST[$this->meta_id]['_recurrence_end']);

			for($i=1; $i < $ocurrences; $i++){

				$rec = $i * $_POST[$this->meta_id]['_recurrence_space'];

				switch($_POST[$this->meta_id]['_recurrence_type']){
					case 'year':
						$new_start_day = date('Y-m-d H:i:s', strtotime($start_day.' + '.$rec.' year'));
					break;
					case 'month':
						$new_start_day = date('Y-m-d H:i:s', strtotime($start_day.' + '.$rec.' month'));
					break;
					case 'week':
						$new_start_day = date('Y-m-d H:i:s', strtotime($start_day.' + '.$rec.' week'));
					break;
					case 'day':
						$new_start_day = date('Y-m-d H:i:s', strtotime($start_day.' + '.$rec.' day'));
					break;
				}

				add_post_meta( $event_id, '_revent_start_date', $new_start_day);
			}
		}

		$my_keys = array(
			'_recurrence_type',
			'_recurrence_space',
			'_recurrence_end',
			'_event_length'
		);

		// set event length for checking end date on recurring events
		$_POST[$this->meta_id]['_event_length'] = $event_length;

		foreach($my_keys as $key)
		{
			$name = isset( $_POST[$this->meta_id][$key] ) ?  $_POST[$this->meta_id][$key] : '' ;
			$value = get_post_meta( $event_id, $key, true );
			
			if ( $name && '' == $value )
				add_post_meta( $event_id, $key, $name, true );
			elseif ( $name && $name != $value )
				update_post_meta( $event_id, $key, $name );
			elseif ( '' == $name && $value )
				delete_post_meta( $event_id, $key, $value );
		}
	}

	/**
	 * Update get_month query to include recurring events
	 * @param  array $query 
	 * @param  int $month 
	 * @param  int $year  
	 * @return array WP_Query query
	 */
	public function setup_month_query($query, $month, $year){

		global $wpdb;

		$start_date = "$year-$month-01";
		$end_date = "$year-$month-31";

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type = 'event') 
		AND ({$wpdb->prefix}posts.post_status = 'publish') 
		AND 
		( 
			( 
			{$wpdb->prefix}postmeta.meta_key = '_revent_start_date' 
			AND mt3.meta_key = '_event_length'
			AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) >= '$start_date' 
			AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) <= '$end_date' 
			) 
			OR
		 	( 
			{$wpdb->prefix}postmeta.meta_key = '_revent_start_date' 
			AND (mt3.meta_key = '_event_length' AND CAST(DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) >= '$start_date') 
			AND (mt3.meta_key = '_event_length' AND CAST(DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) <= '$end_date') 
			) 
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";
		return $query;
	}

	public function setup_day_query($query, $day, $month, $year){

		global $wpdb;

		$date = "$year-$month-$day";

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type = 'event') 
		AND ({$wpdb->prefix}posts.post_status = 'publish') 
		AND 
		( 
			{$wpdb->prefix}postmeta.meta_key = '_revent_start_date' 
			AND CAST({$wpdb->prefix}postmeta.meta_value AS DATE) <= '$date'
			AND (mt3.meta_key = '_event_length' AND CAST(DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) >= '$date')  
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";
		return $query;
	}

	public function setup_upcoming_query($query, $date = false){

		global $wpdb;

		if($date === false){
			$date = date('Y-m-d');
		}

		$query['where'] = "
		AND ({$wpdb->prefix}posts.post_type  = 'event')
		AND ({$wpdb->prefix}posts.post_status = 'publish')
		AND 
		(
			(
				{$wpdb->prefix}postmeta.meta_key = '_revent_start_date'
				AND mt3.meta_key = '_event_length'
				AND  (CAST({$wpdb->prefix}postmeta.meta_value AS DATE) >= '$date')
				)
			OR
			(
					{$wpdb->prefix}postmeta.meta_key = '_revent_start_date'
					AND (mt3.meta_key = '_event_length' AND CAST(DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS DATE) >= '$date')
				)
		)";

		$query['groupby'] = "{$wpdb->prefix}postmeta.meta_id";
		$query['orderby'] = "{$wpdb->prefix}postmeta.meta_value ASC";
		$query['join'] = "INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt2 ON ({$wpdb->prefix}posts.ID = mt2.post_id)
		INNER JOIN {$wpdb->prefix}postmeta AS mt3 ON ({$wpdb->prefix}posts.ID = mt3.post_id)";
		$query['fields'] = "{$wpdb->prefix}postmeta.meta_value AS start_date, DATE_ADD({$wpdb->prefix}postmeta.meta_value, INTERVAL mt3.meta_value SECOND) AS end_date, mt3.meta_value AS event_length, {$wpdb->prefix}posts.*";
		return $query;
	}

	/**
	 * Add admin columns
	 * @param  array $defaults 
	 * @return array
	 */
	public function admin_columns_head($defaults)
	{
		$defaults['recurrence'] = 'Recurrence';
	    return $defaults;
	}

	/**
	 * Display column contents
	 * @param  string $column 
	 * @return void
	 */
	public function admin_columns_content( $column ) {
		global $post;

		switch ( $column ) {
			case 'recurrence':
				$type = get_post_meta( $post->ID, '_recurrence_type', true );
				if(!$type){
					$type = 'None';
				}
				echo $type . ' ('.count(get_post_meta( $post->ID, '_revent_start_date')).')';
			break;
		}
	}
}

new JCE_Admin_RecurringEvents();