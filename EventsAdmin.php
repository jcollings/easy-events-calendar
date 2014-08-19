<?php 
/**
 * Events Admin
 * 
 * Handles all administration functions
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @package Simple Calendar
 * @since 0.0.1
 */
class EventsAdmin{

	private $config = null;
	private $meta_id = 'wp-engine-meta-id';
	private $meta_key = 'wp-engine-meta-key';

	public function __construct(&$config){
		$this->config = $config;

		add_action( 'admin_enqueue_scripts', array($this, 'load_scripts'));

		//pages
		add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );
		add_action( 'admin_menu', array($this, 'add_user_menu_notifications'));

		// admin index
		add_filter('manage_events_posts_columns', array($this, 'admin_columns_head'));  
		add_action('manage_events_posts_custom_column', array($this, 'admin_columns_content')); 
		add_filter("manage_edit-events_sortable_columns", array($this, 'admin_columns_sort'));
		add_filter( 'request', array($this, 'admin_columns_orderby') );

		// events metabox
		add_action( 'load-post.php', array($this, 'setup_metabox' ));
		add_action( 'load-post-new.php', array($this, 'setup_metabox' ));
		add_action( 'save_post', array($this, 'save_metabox'), 10, 2 );

		// hide recuring events
		add_filter('parse_query',array($this, 'hide_recurring_events'));
	}


	function hide_recurring_events($query) {
	    global $pagenow;
	    $qv = &$query->query_vars;
	    if($pagenow == 'edit.php' && $qv['post_type'] == 'events'){
	    	$qv['post_parent'] = 0;
	    }
	}

	public function add_user_menu_notifications() {
		global $menu;
		global $submenu;

		if(isset($submenu['edit.php?post_type=events'])){
			$submenu['edit.php?post_type=events'][7] = $submenu['edit.php?post_type=events'][11];
			$submenu['edit.php?post_type=events'][8] = $submenu['edit.php?post_type=events'][10];
			unset($submenu['edit.php?post_type=events'][11]);
			unset($submenu['edit.php?post_type=events'][10]);
		}
	}

	public function register_menu_pages(){
		add_submenu_page( 'edit.php?post_type=events', 'Calendars', 'Calendars', 'add_users', 'calendar', array($this, 'show_cal'));
	}

	public function show_cal(){
		require $this->config->plugin_dir . 'views/admin/calendar_view.php';
	}

	public function load_scripts()
	{
		// get css from cdn
		global $wp_scripts;
		$ui = $wp_scripts->query('jquery-ui-core');
		$url = "https://ajax.aspnetcdn.com/ajax/jquery.ui/{$ui->ver}/themes/smoothness/jquery.ui.all.css";

		// attach files
		wp_enqueue_script('wpengine-events', $this->config->plugin_url .'/assets/js/main.js', array('jquery-ui-datepicker'), '1.0', true);
		wp_enqueue_style( 'jquery-ui-smoothness', $this->config->plugin_url .'/assets/js/jquery-ui-1.9.1.custom/css/smoothness/jquery-ui-1.9.1.custom.min.css');
		wp_enqueue_style('wpengine-event-admin', $this->config->plugin_url .'/assets/css/admin.css');
	}

	function show_metabox($object, $box)
	{
		wp_nonce_field( basename( __FILE__ ), $this->meta_key.'_nonce' );
		$values = EventsModel::get_event_meta( $object->ID);
		extract($values);

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
		require $this->config->plugin_dir. DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR .'admin' . DIRECTORY_SEPARATOR . 'metabox.php';
	}

	public function save_metabox($post_id, $post)
	{
		if ( !isset( $_POST[$this->meta_key.'_nonce'] ) || !wp_verify_nonce( $_POST[$this->meta_key.'_nonce'], basename( __FILE__ ) ) || $post->post_type != 'events' || $post->post_parent != 0)
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
			wp_set_object_terms( $post_id, $_event_cals, 'event_cals');
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

		foreach(EventsModel::$keys as $key)
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

	public function setup_metabox()
	{
		add_action( 'add_meta_boxes', array($this, 'add_metabox' ));
	}

	public function add_metabox()
	{
		add_meta_box($this->meta_id, esc_html__( 'Event Information'), array($this, 'show_metabox'), 'events', 'normal', 'default');
	}

	public function admin_columns_head($defaults)
	{
		$defaults['event_start'] = 'Start Date';
		$defaults['event_end'] = 'End Date';
		$output = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'calendar' => 'Calendar',
			'event_start' => 'Start Date',
			'event_end' => 'End Date',
			'date' => 'Date'
		);
	    return $output; // $defaults;  
	}

	public function admin_columns_content( $column ) {
		global $posts;

		switch ( $column ) {
			case 'event_end':
				EventsModel::get_end_date('jS M, Y');
			break;

			case 'event_start':
				EventsModel::get_start_date('jS M, Y');
			break;
			case 'calendar':
			// print_r($posts);
			global $post;
				$cals = wp_get_post_terms( $post->ID, 'event_cals' );
				$counter = 0;
				foreach($cals as $cal){

					if($counter > 0)
						echo ', '. $cal->name;
					else
						echo $cal->name;
					
					$counter++;
				}
			break;
		}
	}

	public function admin_columns_sort($columns){
		$custom = array(
			'event_start' 	=> 'event_start',
			'event_end' 		=> 'event_end'
		);
		return wp_parse_args($custom, $columns);
	}

	public function admin_columns_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && 'event_start' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_type' => 'DATE',
				'meta_key' => '_event_start_date',
				'orderby' => 'meta_value'
			) );
		}

		if ( isset( $vars['orderby'] ) && 'event_end'== $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_type' => 'DATE',
				'meta_key' => '_event_end_date',
				'orderby' => 'meta_value'
			) );
		}
		return $vars;
	}

}
?>