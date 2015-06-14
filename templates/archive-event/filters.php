<?php
// get venue list
$temp = get_terms( 'event_venue');
$venues = array();
foreach($temp as $x){
	$venues[$x->term_id] = $x->name;
}

// get organiser list
$temp = get_terms( 'event_organiser');
$organisers = array();
foreach($temp as $x){
	$organisers[$x->term_id] = $x->name;
}

// get tag list
$temp = get_terms( 'event_tag');
$tags = array();
foreach($temp as $x){
	$tags[$x->term_id] = $x->name;
}

// get category list
$temp = get_terms( 'event_category');
$categories = array();
foreach($temp as $x){
	$categories[$x->term_id] = $x->name;
}

// todo: doesn't follow passed shortcode arguments
$month = get_query_var('cal_month') ? get_query_var('cal_month') : date('m');
$year = get_query_var('cal_year') ? get_query_var('cal_year') : date('Y');
$view = isset($_GET['view']) ? $_GET['view'] : JCE()->default_view;

?>
<div class="jce-archive-filters">
	<form action="<?php echo add_query_arg(array('cal_month' => $month, 'cal_year' => $year, 'view' => $view)); ?>" method="GET">

		<?php if(!get_option('permalink_structure') && is_post_type_archive('event')):?>
		<input type="hidden" name="post_type" value="event" />
		<?php elseif(!get_option('permalink_structure') && is_page()): ?>
		<input type="hidden" name="page_id" value="<?php echo get_query_var('page_id'); ?>" />
		<?php endif; ?>

		<input type="hidden" name="cal_month" value="<?php echo $month; ?>" />
		<input type="hidden" name="cal_year" value="<?php echo $year; ?>" />
		<input type="hidden" name="view" value="<?php echo $view; ?>" />

		<?php do_action( 'jce/event_archive_filters' ); ?>

		<?php if( !is_tax( 'event_venue' )): ?>
		<div class="input select">
			<label for="venue">Venue</label>
			<select name="venue" id="venue">
				<option value="">All Venues</option>
				<?php foreach($venues as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php selected( get_query_var('event_venue' ), $key, true ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if( !is_tax( 'event_organiser' )): ?>
		<div class="input select">
			<label for="organiser">Organiser</label>
			<select name="organiser" id="organiser">
				<option value="">All Organisers</option>
				<?php foreach($organisers as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php selected( get_query_var('event_organiser' ), $key, true ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if( !is_tax( 'event_tag' )): ?>
		<div class="input select">
			<label for="tag">Tags</label>
			<select name="tag" id="tag">
				<option value="">All Tags</option>
				<?php foreach($tags as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php selected( get_query_var('event_tag' ), $key, true ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if( !is_tax( 'event_category' )): ?>
		<div class="input select">
			<label for="category">Categories</label>
			<select name="category" id="category">
				<option value="">All Categories</option>
				<?php foreach($categories as $key => $value): ?>
					<option value="<?php echo $key; ?>" <?php selected( get_query_var('event_category' ), $key, true ); ?>><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<div class="input submit">
			<input type="submit" value="filter" />
		</div>
	</form>
</div>