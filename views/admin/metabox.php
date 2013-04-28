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

<div class="input select required">
	<label>Recurrance:</label>
	<select name="<?php echo $this->meta_id; ?>[_recurrence_type]" id="<?php echo $this->meta_id; ?>_recurrence_type">
		<option value="0">None</option>
		<option value="day" <?php if($_recurrence_type == 'day'): ?>selected="selected"<?php endif; ?>>Daily</option>
		<option value="week" <?php if($_recurrence_type == 'week'): ?>selected="selected"<?php endif; ?>>Weekly</option>
		<option value="month" <?php if($_recurrence_type == 'month'): ?>selected="selected"<?php endif; ?>>Monthly</option>
		<option value="year" <?php if($_recurrence_type == 'year'): ?>selected="selected"<?php endif; ?>>Yearly</option>
	</select>
</div>
<fieldset id="recurrence_fields">
	<?php /* ?>
	<div class="input checkbox recurrence_week recurrence_specific">
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="1" />
			<label>M</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="2" />
			<label>Tu</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="3" />
			<label>W</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="4" />
			<label>Th</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="5" />
			<label>F</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="6" />
			<label>Sa</label>
		</div>
		<div class="option">
			<input type="checkbox" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_day_num][]" value="7" />
			<label>Su</label>
		</div>
	</div>
	<div class="input checkbox recurrence_month recurrence_specific">
		<div class="option">
			<input type="radio" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_month_num][]" value="1" checked="checked" />
			<label>Day</label>
		</div>
		<div class="option">
			<input type="radio" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_recurrence_month_num][]" value="1" />
			<label>Date</label>
		</div>
	</div>
	<?php */ ?>
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

<!--<h2>Event Location</h2>
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

<h2>Event Cost</h2>
<div class="input text">
	<label>Price of Event:</label>
	<input type="text" id="<?php echo $this->meta_id; ?>" name="<?php echo $this->meta_id; ?>[_event_price]" value="<?php echo $_event_price; ?>" />
</div>-->