<?php 
class CalendarAdmin{

	/**
	 * Setup Hooks
	 * 
	 * @param [type] &$config [description]
	 */
	public function __construct(&$config){
		$this->config = $config;

		add_action('init', array($this, 'register_tax'));

		if(is_admin()){
			add_action( 'event_cals_add_form_fields', array($this, 'add_new_meta_field'), 10, 2 );
			add_action( 'event_cals_edit_form_fields', array($this, 'edit_meta_field'), 10, 2 );
			add_action( 'edited_event_cals', array($this, 'save_custom_meta'), 10, 2 );  
			add_action( 'create_event_cals', array($this, 'save_custom_meta'), 10, 2 );
			add_action( 'admin_head', array($this, 'hide_slug_box')  );
			add_action( 'parent_file', array($this, 'menu_highlight'));
		}
	}
	/**
	 * Register Taxonomoy
	 * 
	 * @return void
	 */
	function register_tax(){
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
		register_taxonomy( 'event_cals', array($this->config->events_pt, $this->config->recurring_events_pt), array(
			'public' => false,
			'hierarchical' => true,
			'labels' => $labels,  
		));	
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
		$term_meta = get_option( "event_cals_$t_id" ); ?>
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
			$term_meta = get_option( "event_cals_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$value = $_POST['term_meta'][$key];
					$term_meta[$key] = $value;
				}
			}
			// Save the option array.
			update_option( "event_cals_$t_id", $term_meta );
		}
	} 

	/**
	 * Hide unnecessary Fields
	 * 
	 * @return void
	 */
	function hide_slug_box(){
	    global $pagenow;

	    if(is_admin() && $pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'event_cals'){
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
		if ($taxonomy == 'event_cals')
			$parent_file = 'edit.php?post_type=events';
		return $parent_file;
	}
}
?>