<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Admin_Venues{

	public function __construct(){

		if(is_admin()){
			add_action( 'event_venue_add_form_fields', array($this, 'add_new_meta_field'), 10, 2 );
			add_action( 'event_venue_edit_form_fields', array($this, 'edit_meta_field'), 10, 2 );
			add_action( 'edited_event_venue', array($this, 'save_custom_meta'), 10, 2 );  
			add_action( 'create_event_venue', array($this, 'save_custom_meta'), 10, 2 );
			add_action( 'admin_head', array($this, 'hide_slug_box')  );
			add_action( 'admin_menu' , array($this, 'remove_meta_box') );
		}
	}

	/**
	 * Add fields to create taxonomy screen
	 * 
	 * @return  void
	 */
	function add_new_meta_field(){
		?>
		<div class="form-field form-required">
			<label for="term_meta[venue_address]"><?php _e( 'Venue Address' ); ?></label>
			<input type="text" name="term_meta[venue_address]" id="term_meta[venue_address]" size="40" aria-required="true" />
			<p class="description"><?php _e( 'The full address of the Venue.'); ?></p>
		</div>
		<div class="form-field form-required">
			<label for="term_meta[venue_city]"><?php _e( 'Venue City' ); ?></label>
			<input type="text" name="term_meta[venue_city]" id="term_meta[venue_city]" size="40" aria-required="true" />
			<p class="description"><?php _e( 'The city the venue is at.'); ?></p>
		</div>
		<div class="form-field form-required">
			<label for="term_meta[venue_postcode]"><?php _e( 'Venue Postcode' ); ?></label>
			<input type="text" name="term_meta[venue_postcode]" id="term_meta[venue_postcode]" size="40" aria-required="true" />
			<p class="description"><?php _e( 'The postcode of the venue.'); ?></p>
		</div>
		<?php
	}

	/**
	 * Add fields to edit taxonomy screen
	 * 
	 * @return  void
	 */
	function edit_meta_field($term) {
 
		// put the term ID into a variable
		$t_id = $term->term_id;

		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "event_venue_$t_id" ); ?>
		<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="term_meta[venue_address]"><?php _e( 'Venue Address' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[venue_address]" id="term_meta[venue_address]" value="<?php echo $term_meta['venue_address'] ? $term_meta['venue_address'] : ''; ?>" />
				<p class="description"><?php _e( 'The full address of the Venue.'); ?></p>
			</td>
		</tr>
		<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="term_meta[venue_city]"><?php _e( 'Venue City' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[venue_city]" id="term_meta[venue_city]" value="<?php echo $term_meta['venue_city'] ? $term_meta['venue_city'] : ''; ?>" />
				<p class="description"><?php _e( 'The city the venue is at.'); ?></p>
			</td>
		</tr>
		<tr class="form-field form-required">
		<th scope="row" valign="top"><label for="term_meta[venue_postcode]"><?php _e( 'Venue Postcode' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[venue_postcode]" id="term_meta[venue_postcode]" value="<?php echo $term_meta['venue_postcode'] ? $term_meta['venue_postcode'] : ''; ?>" />
				<p class="description"><?php _e( 'The postcode of the venue.'); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save custom fields to options table
	 * 
	 * @param  int $term_id 
	 * @return void
	 */
	function save_custom_meta( $term_id ) {

		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "event_venue_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$value = $_POST['term_meta'][$key];
					$term_meta[$key] = $value;
				}
			}
			// Save the option array.
			update_option( "event_venue_$t_id", $term_meta );
		}
	}

	/**
	 * Hide Taxonomy metabox from event
	 * @return void
	 */
	function remove_meta_box(){
		remove_meta_box( 'event_venuediv', 'event', 'side' );
	}

	/**
	 * Hide unnecessary Fields
	 * 
	 * @return void
	 */
	function hide_slug_box(){
	    global $pagenow;

	    if($pagenow == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'event_venue'){
	        echo "<script type='text/javascript'>
	            jQuery(document).ready(function($) {
	                $('#tag-slug, #parent, #tag-description').parent('div').hide();
	                $('#slug, #parent, #description').parent().parent('tr').hide();
	            });
	            </script>
	        ";
	    }
	}
}

new JCE_Admin_Venues();