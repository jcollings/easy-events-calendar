<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Calendars{

	public function __construct(){

		add_action( 'jce/register_taxonomies', array( $this, 'register_taxonomies' ) );
		add_action( 'jce/event_archive_filters' , array( $this, 'event_archive_filters') );

		if(is_admin()){

			// load admin functions
			require_once JCE()->plugin_dir . 'libs/admin/class-jce-admin-calendars.php';
		}
	}

	public function register_taxonomies(){

		/**
		 * Register Calendar Taxonomy
		 */
		
		$labels = array(
		    'name'                => _x( 'Calendars', 'taxonomy general name' ),
		    'singular_name'       => _x( 'Calendar', 'taxonomy singular name' ),
		    'search_items'        => __( 'Search Calendars' ),
		    'all_items'           => __( 'All Calendars' ),
		    'parent_item'         => __( 'Parent Calendar' ),
		    'parent_item_colon'   => __( 'Parent Calendar:' ),
		    'edit_item'           => __( 'Edit Calendar' ), 
		    'update_item'         => __( 'Update Calendar' ),
		    'add_new_item'        => __( 'Add New Calendar' ),
		    'new_item_name'       => __( 'New Calendar Name' ),
		    'menu_name'           => __( 'Calendar' )
		); 
		
		register_taxonomy( 'event_calendar', array('event'), array(
			'public' => false,
			'hierarchical' => true,
			'labels' => $labels,  
		));	
	}

	public function event_archive_filters(){

		// get calendar list
		$temp = get_terms( 'event_calendar');
		$calendars = array();
		foreach($temp as $x){
			$calendars[$x->term_id] = $x->name;
		}

		if( !is_tax( 'event_calendar' )): ?>
		<div class="input select">
			<label for="calendar">Calendar</label>
			<select name="calendar" id="calendar">
				<option value="">All Calendars</option>
				<?php foreach($calendars as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php selected( get_query_var('event_calendar' ), $key, true ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif;
	}
}

new JCE_Calendars();