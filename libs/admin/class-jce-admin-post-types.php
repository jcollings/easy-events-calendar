<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Admin_PostTypes{

	private $meta_id = 'jcevents';
	private $meta_key = 'jcevents';

	public function __construct(){

		if(is_admin()){
			// events metabox
			add_action( 'load-post.php', array($this, 'setup_metabox' ));
			add_action( 'load-post-new.php', array($this, 'setup_metabox' ));
			add_action( 'save_post', array($this, 'save_metabox'), 10, 2 );
			add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts' ));

			// admin events columns
			add_filter('manage_event_posts_columns', array($this, 'admin_columns_head'), 5);
			add_action('manage_event_posts_custom_column', array($this, 'admin_columns_content'), 5); 
		}
	}

	public function admin_enqueue_scripts(){

		global $wp_scripts;
		$jcevents = JCEvents2::instance();

		$ui = $wp_scripts->query('jquery-ui-core');
		$url = "http://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.css";

		wp_enqueue_style('jcevents-admin-css', $jcevents->plugin_url . 'assets/css/admin.css');
		wp_enqueue_style('jqueryui-smooth', $url);
		wp_enqueue_script('jcevents-admin-js', $jcevents->plugin_url .'/assets/js/main.js', array('jquery-ui-datepicker'), '1.0', true);
	}

	public function setup_metabox(){
		add_action( 'add_meta_boxes', array($this, 'add_metabox' ));
	}

	public function add_metabox(){
		add_meta_box($this->meta_id, esc_html__( 'Events Details'), array($this, 'show_metabox'), 'event', 'normal', 'default');
	}

	public function show_metabox($object, $box){
		wp_nonce_field( basename( __FILE__ ), $this->meta_key.'_nonce' );

		$event = new JCE_Event($object);
		$meta = $event->get_post_meta();
		extract($meta);

		$start_time = strtotime($_event_start_date);
		$end_time = strtotime($_event_end_date);

		$_event_start_date = array(
			'day' => date('Y-m-d', $start_time),
			'hour' => date('H', $start_time),
			'minute' => date('i', $start_time),
			'second' => date('s', $start_time)
		);
		$_event_end_date = array(
			'day' => date('Y-m-d', $end_time),
			'hour' => date('H', $end_time),
			'minute' => date('i', $end_time),
			'second' => date('s', $end_time)
		);
		?>
		<h2>Event Information</h2>
		<div class="input radio">
			<label>All Day Event:</label>
			<div class="option">
				<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_all_day]" value="yes" <?php checked( $_event_all_day, 'yes', true ); ?>  />
			</div>
		</div>
		<div class="input select required">
			<label>Start Date:</label>
			<input type="text" id="event_start_date" name="<?php echo $this->meta_id; ?>[_event_start_date_day]" value="<?php echo $_event_start_date['day']; ?>" />
			 at <select id="event_start_date_hour" name="<?php echo $this->meta_id; ?>[_event_start_date_hour]" class="time">
				<?php 
				for($i=0;$i<24;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_start_date['hour'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
			 : <select id="event_start_date_minute" name="<?php echo $this->meta_id; ?>[_event_start_date_minute]" class="time">
				<?php 
				for($i=0;$i<60;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_start_date['minute'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
			 : <select id="event_start_date_second" name="<?php echo $this->meta_id; ?>[_event_start_date_second]" class="time">
				<?php 
				for($i=0;$i<60;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_start_date['second'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
		</div>
		<div class="input select required">
			<label>End Date:</label>
			<input type="text" id="event_end_date" name="<?php echo $this->meta_id; ?>[_event_end_date_day]" value="<?php echo $_event_end_date['day']; ?>" />
			 at <select id="event_end_date_hour" name="<?php echo $this->meta_id; ?>[_event_end_date_hour]" class="time">
				<?php 
				for($i=0;$i<24;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_end_date['hour'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
			 : <select id="event_end_date_minute" name="<?php echo $this->meta_id; ?>[_event_end_date_minute]" class="time">
				<?php 
				for($i=0;$i<60;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_end_date['minute'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
			 : <select id="event_end_date_second" name="<?php echo $this->meta_id; ?>[_event_end_date_second]" class="time">
				<?php 
				for($i=0;$i<60;$i++)
				{
					$selected = '';
					if(strlen($i) == 1){
						$output = '0'.$i;
					}else{
						$output = $i;
					}

					if($output == $_event_end_date['second'])
						$selected = 'selected="selected"';

					echo '<option value="'.$output.'" '.$selected.'>'.$output.'</option>';
				}
				?>
			</select>
		</div>

		<?php 
		global $post;

		$temp = array();
		$post_event_cals = wp_get_post_terms( $post->ID, 'event_calendar');
		foreach($post_event_cals as $e){
			$temp[] = $e->slug;
		}

		// get current event organiser
		$post_organiser = wp_get_post_terms( $post->ID, 'event_organiser');
		$current_post_organiser = '';
		foreach($post_organiser as $e){
			$current_post_organiser = $e->slug;
		}

		// get current event organiser
		$post_location = wp_get_post_terms( $post->ID, 'event_venue');
		$current_post_location = '';
		foreach($post_location as $e){
			$current_post_location = $e->slug;
		}
		?>

		<div class="input radio">
			<label>Calendar:</label>
			<?php
			$calendars = get_terms( 'event_calendar', array('hide_empty' => false));
		    foreach($calendars as $calendar): ?>
			<div class="option">
				<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_calendar][]" value="<?php echo $calendar->slug ?>" <?php if( in_array($calendar->slug, $temp) || count($calendars) == 1): ?> checked="checked"<?php endif; ?> />
				<label><?php echo $calendar->name; ?></label>
			</div>
			<?php endforeach; ?>
		</div>

		<?php 

		do_action( 'jce/admin_meta_fields', $object, $box); ?>

		<h2>Event Location</h2>
		<div class="input select">
			<label>Existing</label>
			
			<?php $terms = get_terms( 'event_venue', array('hide_empty' => false) ); ?>
			<select name="<?php echo $this->meta_id; ?>[_event_venue_id]; ?>" id="">
				<option value="">Add new Venue</option>
				<?php foreach($terms as $term): ?>
					<option value="<?php echo $term->slug; ?>" <?php selected( $term->slug, $current_post_location, true ); ?>><?php echo $term->name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="input text">
			<label>Venue:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_venue]" value="" />
		</div>
		<div class="input text">
			<label>Address:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_address]" value="" />
		</div>
		<div class="input text">
			<label>City:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_city]" value="" />
		</div>
		<div class="input text">
			<label>Postcode:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_postcode]" value="" />
		</div>

		<h2>Event Organizer</h2>
		<div class="input select">
			<label>Existing</label>
			<?php $terms = get_terms( 'event_organiser', array('hide_empty' => false) ); ?>
			<select name="<?php echo $this->meta_id; ?>[_event_organiser_id]; ?>" id="">
				<option value="">Add new Organiser</option>
				<?php foreach($terms as $term): ?>
					<option value="<?php echo $term->slug; ?>" <?php selected( $term->slug, $current_post_organiser, true ); ?>><?php echo $term->name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="input text">
			<label>Organizer Name:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_name]" value="" />
		</div>
		<div class="input text">
			<label>Phone:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_phone]" value="" />
		</div>
		<div class="input text">
			<label>Website:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_website]" value="" />
		</div>
		<div class="input text">
			<label>Email:</label>
			<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_email]" value="" />
		</div>
		<?php
	}

	public function save_metabox($post_id, $post){

		if ( !isset( $_POST[$this->meta_key.'_nonce'] ) || !wp_verify_nonce( $_POST[$this->meta_key.'_nonce'], basename( __FILE__ ) ) || $post->post_type != 'event' || $post->post_parent != 0)
			return $post_id;
		
		// Get the post type object. 
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post. 
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;


		// add events to events_cal
		if(!empty($_POST[$this->meta_id]['_event_calendar'])){
			$_event_cals = array();
			foreach($_POST[$this->meta_id]['_event_calendar'] as $cal){
				$_event_cals[] = $cal;
			}
			wp_set_object_terms( $post_id, $_event_cals, 'event_calendar');
		}

		// set all day event
		if(isset($_POST[$this->meta_id]['_event_all_day']) && $_POST[$this->meta_id]['_event_all_day'] == 'yes'){
			$_POST[$this->meta_id]['_event_start_date_hour'] = '00';
			$_POST[$this->meta_id]['_event_start_date_minute'] = '00';
			$_POST[$this->meta_id]['_event_start_date_second'] = '00';
			$_POST[$this->meta_id]['_event_end_date_hour'] = 23;
			$_POST[$this->meta_id]['_event_end_date_minute'] = 59;
			$_POST[$this->meta_id]['_event_end_date_second'] = 59;
		}else{
			$_POST[$this->meta_id]['_event_all_day'] = 'no';
		}

		// set event location
		if(empty($_POST[$this->meta_id]['_event_venue_id'])){

			// insert term
			$term = $_POST[$this->meta_id]['_event_venue'];
			if(!empty($term)){
				
				$venue_term = wp_insert_term( $term, 'event_venue');
				$venue_term_id = $venue_term['term_id'];
				add_option( "event_venue_{$venue_term_id}", array(
					'venue_address' => $_POST[$this->meta_id]['_event_address'],
					'venue_city' => $_POST[$this->meta_id]['_event_city'],
					'venue_postcode' => $_POST[$this->meta_id]['_event_postcode']
				));
			}
		}else{

			// get existing term
			$term = get_term_by( 'slug', $_POST[$this->meta_id]['_event_venue_id'], 'event_venue');
			$venue_term_id = $term->term_id;
		}

		if(isset($venue_term_id) && intval($venue_term_id) > 0){
			wp_set_object_terms( $post_id, $venue_term_id, 'event_venue');	
		}else{
			wp_set_object_terms( $post_id, null, 'event_venue');
		}

		// set event organiser
		if(empty($_POST[$this->meta_id]['_event_organiser_id'])){

			// insert term
			$term = $_POST[$this->meta_id]['_organizer_name'];
			if(!empty($term)){
				
				$organiser_term = wp_insert_term( $term, 'event_organiser');
				$organiser_term_id = $organiser_term['term_id'];
				add_option( "event_organiser_{$organiser_term_id}", array(
					'organiser_phone' => $_POST[$this->meta_id]['_organizer_phone'],
					'organiser_website' => $_POST[$this->meta_id]['_organizer_website'],
					'organiser_email' => $_POST[$this->meta_id]['_organizer_email']
				));
			}
		}else{

			// get existing term
			$term = get_term_by( 'slug', $_POST[$this->meta_id]['_event_organiser_id'], 'event_organiser');
			$organiser_term_id = $term->term_id;
		}

		if(isset($organiser_term_id) && intval($organiser_term_id) > 0){
			wp_set_object_terms( $post_id, $organiser_term_id, 'event_organiser');
		}else{
			wp_set_object_terms( $post_id, null, 'event_organiser');
		}

		$start_day = $_POST[$this->meta_id]['_event_start_date_day'];
		$start_hour = $_POST[$this->meta_id]['_event_start_date_hour'];
		$start_minute = $_POST[$this->meta_id]['_event_start_date_minute'];
		$start_second = $_POST[$this->meta_id]['_event_start_date_second'];
		$_POST[$this->meta_id]['_event_start_date'] = $start_day.' '.$start_hour.':'.$start_minute.':'.$start_second;

		$end_day = $_POST[$this->meta_id]['_event_end_date_day'];
		$end_hour = $_POST[$this->meta_id]['_event_end_date_hour'];
		$end_minute = $_POST[$this->meta_id]['_event_end_date_minute'];
		$end_second = $_POST[$this->meta_id]['_event_end_date_second'];
		$_POST[$this->meta_id]['_event_end_date'] = $end_day.' '.$end_hour.':'.$end_minute.':'.$end_second;

		$keys = array('_event_start_date', '_event_end_date', '_event_all_day');

		foreach($keys as $key)
		{
			$name = isset( $_POST[$this->meta_id][$key] ) ?  $_POST[$this->meta_id][$key] : '' ;
			$value = get_post_meta( $post_id, $key, true );
			
			if ( $name && '' == $value )
				add_post_meta( $post_id, $key, $name, true );
			elseif ( $name && $name != $value )
				update_post_meta( $post_id, $key, $name );
			elseif ( '' == $name && $value )
				delete_post_meta( $post_id, $key, $value );
		}

		do_action('jce/save_event', $post_id);

	}

	/**
	 * Add admin columns
	 * @param  array $defaults 
	 * @return array
	 */
	public function admin_columns_head($defaults)
	{
		$defaults['event_start'] = 'Start Date';
		$defaults['event_end'] = 'End Date';
	    return $defaults;
	}

	/**
	 * Display column contents
	 * @param  string $column 
	 * @return void
	 */
	public function admin_columns_content( $column ) {
		global $post;

		// setup event
		JCE()->event = new JCE_Event($post);

		switch ( $column ) {
			case 'event_start':
				jce_event_start_date('jS F Y g:i a');
			break;
			case 'event_end':
				jce_event_end_date('jS F Y g:i a');
			break;
		}
	}
}

new JCE_Admin_PostTypes();