<?php get_header(); ?>

<?php
global $post;
$month = get_query_var( 'xmonth' );
$year = get_query_var( 'xyear' );
$day = get_query_var( 'xday' );
$name = get_query_var( 'xname' );

$query = new WP_Query(array(
	'post_type' => array('events', 'recurring_events'),
	'name' => $name,
	'meta_query' => array(
		array(
			'key' => '_event_start_date',
			'value' => $year.'-'.$month.'-'.$day, // '20/'.$month.'/'.$year,
			'compare' => '=',
			'type' => 'DATE'
		)
	)
));

?>

<?php 
if($query->have_posts()): ?>
	<p><a href="<?php echo eec_get_permalink(); ?>">&larr;Back to events</a></p>
	<?php while($query->have_posts()): $query->the_post(); ?>
		<article>
			<div class="meta-head">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="meta-content">
				<p>Dates: <?php echo EventsModel::get_start_date('d/m/Y'); ?> - <?php echo EventsModel::get_end_date('d/m/Y'); ?></p>
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
<?php 
endif;
wp_reset_postdata();
?>

<?php get_footer(); ?>