<?php
global $post;
$row_counter = 0;
$curr_month = false;
$events = EventsModel::get_month($this->year, $this->month);
$events = $this->generate_event_list($events->posts);
$sorted_events = array();
if(isset($events['events'])){
	foreach($events['events'] as $e){
		$sorted_events[$e['days']][] = $e;
	}
}
?>

<style type="text/css">
<?php 
$cal_terms = get_terms( 'event_cals', array('hide_empty' => false) );
if($cal_terms){
foreach($cal_terms as $term){
    $term_meta = get_option( "event_cals_$term->term_id" );
    echo '.cal-day-wrapper li.event-list.'.$term->slug.'{'."\n\t";
    echo 'background:' . $term_meta['calendar_colour'] . ';'."\n";
    echo '}'."\n";
}
}
?>

</style>
	
<div class="cal">
	<?php echo $this->output_cal_header(); ?>
	<?php echo $this->output_cal_weekdays(); ?>
	
	<?php foreach($this->cal_tiles as $tile): ?>
	<?php $row_counter++; ?>
	
	<?php
	if($tile == 1 && $curr_month == true){
		$curr_month = false;
	}elseif($tile == 1 && $curr_month == false){
		$curr_month = true;
	}
	?>

	<?php if($row_counter == 1): ?><div class="cal-days-row"><?php endif; ?>
		<div class="<?php if(isset($events['tiles'][$tile]) && $curr_month == true): ?>has-event<?php endif; ?> cal-day <?php if($curr_month == false): ?>prev-next<?php endif; ?>">
			<div class="cal-day-wrapper">
			<span class="date" title=""><?php echo $tile; ?></span>
			<?php 
			if(isset($sorted_events[$tile]) && !empty($sorted_events[$tile]) && $curr_month == true){
				echo '<ul>'."\n";
				foreach($sorted_events[$tile] as $e){

					$temp = array();
					$post_event_cals = wp_get_post_terms( $e['id'], 'event_cals');
					foreach($post_event_cals as $event){
						$temp[] = $event->slug;
					}

					if($e['parent'] == 0){
						$parent = $e['id'];
					}else{
						$parent = $e['parent'];
					}

					if(!empty($temp)){
						echo '<li class="event-list '.implode(' ', $temp ).'"><a href="post.php?post='.$parent.'&action=edit">'.$e['title'].'</a></li>'."\n";	
					}else{
						echo '<li class="event-list">'.$e['title'].'</li>'."\n";	
					}
				}
				echo '</ul>'."\n";
			}
			?>
			</div><!-- .cal-day-wrapper -->
		</div><!-- /.cal-day -->
	<?php if($row_counter == 7): $row_counter = 0; ?></div><!-- /.cal-days-row --><?php endif; ?>
	<?php endforeach; ?>
	
</div><!-- /.cal -->