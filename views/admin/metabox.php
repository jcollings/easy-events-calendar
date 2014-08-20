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
$post_event_cals = wp_get_post_terms( $post->ID, 'event_cals');
foreach($post_event_cals as $e){
	$temp[] = $e->slug;
}

// get current event organiser
$post_organiser = wp_get_post_terms( $post->ID, 'jce_organiser');
$current_post_organiser = '';
foreach($post_organiser as $e){
	$current_post_organiser = $e->slug;
}

// get current event organiser
$post_location = wp_get_post_terms( $post->ID, 'jce_venue');
$current_post_location = '';
foreach($post_location as $e){
	$current_post_location = $e->slug;
}
?>

<div class="input radio">
	<label>Calendar:</label>
	<?php
	$calendars = get_terms( 'event_cals', array('hide_empty' => false));
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
	
	<?php $terms = get_terms( 'jce_venue', array('hide_empty' => false) ); ?>
	<select name="<?php echo $this->meta_id; ?>[_jce_venue]; ?>" id="">
		<option value="">Add new Organiser</option>
		<?php foreach($terms as $term): ?>
			<option value="<?php echo $term->slug; ?>" <?php selected( $term->slug, $current_post_location, true ); ?>><?php echo $term->name; ?></option>
		<?php endforeach; ?>
	</select>
</div>
<div class="input text">
	<label>Venue:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_venue]" value="<?php echo $_event_venue; ?>" />
</div>
<div class="input text">
	<label>Address:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_address]" value="<?php echo $_event_address; ?>" />
</div>
<div class="input text">
	<label>City:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_city]" value="<?php echo $_event_city; ?>" />
</div>
<div class="input text">
	<label>Postcode:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_postcode]" value="<?php echo $_event_postcode; ?>" />
</div>

<h2>Event Organizer</h2>
<div class="input select">
	<label>Existing</label>
	<?php $terms = get_terms( 'jce_organiser', array('hide_empty' => false) ); ?>
	<select name="<?php echo $this->meta_id; ?>[_jce_organiser]; ?>" id="">
		<option value="">Add new Organiser</option>
		<?php foreach($terms as $term): ?>
			<option value="<?php echo $term->slug; ?>" <?php selected( $term->slug, $current_post_organiser, true ); ?>><?php echo $term->name; ?></option>
		<?php endforeach; ?>
	</select>
</div>
<div class="input text">
	<label>Organizer Name:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_name]" value="<?php echo $_organizer_name; ?>" />
</div>
<div class="input text">
	<label>Phone:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_phone]" value="<?php echo $_organizer_phone; ?>" />
</div>
<div class="input text">
	<label>Website:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_website]" value="<?php echo $_organizer_website; ?>" />
</div>
<div class="input text">
	<label>Email:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_organizer_email]" value="<?php echo $_organizer_email; ?>" />
</div>

<!-- <h2>Event Cost</h2>
<div class="input text">
	<label>Price of Event:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_price]" value="<?php echo $_event_price; ?>" />
</div> -->