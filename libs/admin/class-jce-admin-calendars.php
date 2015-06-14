<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Admin_Calendars{

	public function __construct(){

		if(is_admin()){
			add_action( 'event_calendar_add_form_fields', array($this, 'add_new_meta_field'), 10, 2 );
			add_action( 'event_calendar_edit_form_fields', array($this, 'edit_meta_field'), 10, 2 );
			add_action( 'edited_event_calendar', array($this, 'save_custom_meta'), 10, 2 );  
			add_action( 'create_event_calendar', array($this, 'save_custom_meta'), 10, 2 );
			add_action( 'admin_head', array($this, 'hide_slug_box')  );
			add_action( 'parent_file', array($this, 'menu_highlight'));
			add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );

			add_action( 'jce/admin_meta_fields', array( $this, 'show_meta_field' ), 10, 2);
			add_action('jce/save_event', array( $this, 'save_meta_field' ), 10, 1);
		}
	}

	public function register_menu_pages(){
		add_submenu_page( 'edit.php?post_type=event', 'Calendars', 'Calendars', 'add_users', 'calendar', array($this, 'admin_calendar_page'));
	}

	public function admin_calendar_page(){
		/**
		 * Add new Calendar
		 */
		if(isset($_POST['action'])){
		    switch($_POST['action']){
		        case 'manage_calendars':
		            $cal_name = isset($_POST['cal_name']) && !empty($_POST['cal_name']) ? $_POST['cal_name'] : $_POST['cal_name'];
		            wp_insert_term( $cal_name, 'event_calendar');
		        break;
		    }
		    
		}
		?>
		<div class="wrap">
		    <div id="icon-edit" class="icon32 icon32-posts-events"><br></div>
			<h2>Events Calendar</h2>

		    <div id="poststuff" class="simple_events">
		        <div id="post-body" class="metabox-holder columns-2">
		            <div id="post-body-content">
		                <?php
		                $cal = new JCE_Calendar();
		                
		                $year = isset($_GET['cal_year']) ? $_GET['cal_year'] : date('Y');
						$month = isset($_GET['cal_month']) ? $_GET['cal_month'] : date('m');

		                $cal->prev_year_link = false;
		                $cal->next_year_link = false;
		                $cal->set_week_start('Mon');
		                $cal->set_month($year,$month);
		                $events = JCE()->query->get_calendar($month, $year);
		                echo $cal->render($events->posts);
		                ?>
		            </div>

		            <div id="postbox-container-1" class="postbox-container">
		                <div id="postimagediv" class="postbox ">
		                    <h3 class="hndle"><span>Calendars</span> - <a href="edit-tags.php?taxonomy=event_calendar&post_type=event">Manage</a></h3>
		                    <div class="inside">
		                        <form method="post">
		                            <input type="hidden" name="action" value="manage_calendars">
		                            <div id="category-all">
		                            <?php 
		                            $calendars = get_terms( 'event_calendar', array('hide_empty' => false));
		                            foreach($calendars as $calendar){
		                                ?>
		                                <div class="input checkbox">
		                                    <input type="checkbox" name="<?php echo $calendar->slug; ?>" value="1" checked="checked" />
		                                    <label><?php echo $calendar->name; ?></label>
		                                </div>
		                                <?php
		                            }
		                            ?>
		                            </div>
		                            <label>Add Calendar: </label>
		                            <input type="text" name="cal_name" />
		                        </form>
		                    </div>
		                </div><!-- /#postimagediv -->
		            </div>
		        </div>
		    </div>

		</div>
		<?php
	}

	/**
	 * Add Fields to Add Events Calendar Taxonomy
	 * 
	 * @return void
	 */
	function add_new_meta_field(){
		?>
		<div class="form-field form-required">
			<label for="term_meta[calendar_colour]"><?php _e( 'Calendar Colour' ); ?></label>
			<input type="text" name="term_meta[calendar_colour]" id="term_meta[calendar_colour]" size="40" aria-required="true" />
			<p class="description"><?php _e( 'Enter the hexadecimal colour for the event background.'); ?></p>
		</div>
		<?php
	}

	/**
	 * Add Fields to Edit Events Calendar Taxonomy
	 * 
	 * @param  WP_Term $term 
	 * @return void
	 */
	function edit_meta_field($term) {
 
		// put the term ID into a variable
		$t_id = $term->term_id;

		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "event_calendar_$t_id" ); ?>
		<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="term_meta[calendar_colour]"><?php _e( 'Calendar Colour' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[calendar_colour]" id="term_meta[calendar_colour]" value="<?php echo $term_meta['calendar_colour'] ? $term_meta['calendar_colour'] : ''; ?>" />
				<p class="description"><?php _e( 'Enter the hexadecimal colour for the event background.'); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Events Calendar Fields
	 * 
	 * @param  int $term_id 
	 * @return void
	 */
	function save_custom_meta( $term_id ) {

		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "event_calendar_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$value = $_POST['term_meta'][$key];
					$term_meta[$key] = $value;
				}
			}
			// Save the option array.
			update_option( "event_calendar_$t_id", $term_meta );
		}
	} 

	/**
	 * Hide unnecessary Fields
	 * 
	 * @return void
	 */
	function hide_slug_box(){
	    global $pagenow;

	    if(is_admin() && $pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'event_calendar'){
	        echo "<script type='text/javascript'>
	            jQuery(document).ready(function($) {
	                $('#tag-slug, #parent, #tag-description').parent('div').hide();
	                $('#slug, #parent, #description').parent().parent('tr').hide();
	            });
	            </script>
	        ";
	    }
	}

	/**
	 * Set Active Menu Item
	 * 
	 * Fix to dsiplay the correct menu item for events calendar taxonomy
	 * 
	 * @param  string $parent_file 
	 * @return string
	 */
	public function menu_highlight($parent_file) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;
		if ($taxonomy == 'event_calendar')
			$parent_file = 'edit.php?post_type=events';
		return $parent_file;
	}

	public function show_meta_field($object, $box){

		$temp = array();
		$post_event_cals = wp_get_post_terms( $object->ID, 'event_calendar');
		foreach($post_event_cals as $e){
			$temp[] = $e->slug;
		}

		?>
		<div class="input radio">
			<label>Calendar:</label>
			<?php
			$calendars = get_terms( 'event_calendar', array('hide_empty' => false));
		    foreach($calendars as $calendar): ?>
			<div class="option">
				<input type="checkbox" id="jcevents" name="jcevents[_event_calendar][]" value="<?php echo $calendar->slug ?>" <?php if( in_array($calendar->slug, $temp) || count($calendars) == 1): ?> checked="checked"<?php endif; ?> />
				<label><?php echo $calendar->name; ?></label>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public function save_meta_field($post_id){

		// add events to events_cal
		$_event_cals = array();
		if(!empty($_POST['jcevents']['_event_calendar'])){
			foreach($_POST['jcevents']['_event_calendar'] as $cal){
				$_event_cals[] = $cal;
			}
		}
		wp_set_object_terms( $post_id, $_event_cals, 'event_calendar');
	}
}

new JCE_Admin_Calendars();