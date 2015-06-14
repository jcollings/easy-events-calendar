<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Calendars{

	public function __construct(){

		add_action( 'jce/register_taxonomies', array( $this, 'register_taxonomies' ) );
		add_action( 'jce/event_archive_filters' , array( $this, 'event_archive_filters') );

		add_filter( 'jce/calendar/inline_event_classes', array( $this, 'calendar_inline_event_classes'), 10, 2 );

		add_action( 'jce/calendar/inline_style', array( $this, 'calendar_inline_dynamic_css' ) );

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

	public function calendar_inline_event_classes($classes = '', $e){
		
		$post_event_cals = wp_get_post_terms( $e['id'], 'event_calendar');
		if($post_event_cals){
			foreach($post_event_cals as $event){
				$classes[] = $event->slug;
			}
		}

		return $classes;
	}

	public function calendar_inline_dynamic_css(){

		$cal_terms = get_terms( 'event_calendar', array('hide_empty' => false) );

		if($cal_terms){
			foreach($cal_terms as $term){
			    $term_meta = get_option( "event_calendar_{$term->term_id}" );
			    echo '.cal-day-wrapper li.event-list.'.$term->slug.'{'."\n\t";
			    echo 'background:' . $term_meta['calendar_colour'] . ';'."\n";
			    echo '}'."\n";
			}
		}
	}
}

new JCE_Calendars();