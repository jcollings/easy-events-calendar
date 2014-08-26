<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Shortcode_Archive{

	public function __construct(){

		add_shortcode( 'jce_event_archive', array($this, 'event_archive') );
	}

	public function event_archive($atts){

		extract( shortcode_atts( array(
			'view' => 'upcoming',
			'month' => date('m'),
			'year' => date('Y'),
			'day' => false
		), $atts, 'jce_event_archive' ) );

		switch($view){
			case 'upcoming':
				$events = JCE()->query->get_events(array('posts_per_page' => 10, 'paged' => get_query_var( 'paged' )));
			break;
			case 'archive':
				if(intval($day) > 0){
					add_action('jce/before_event_archive', 'jce_output_daily_archive_heading');
					$events = JCE()->query->get_daily_events($day, $month, $year);
				}else{
					add_action('jce/before_event_archive', 'jce_output_monthly_archive_heading');
					$events = JCE()->query->get_calendar($month, $year);
				}

				
				remove_action( 'jce/before_event_content', 'jce_add_archive_month');
				remove_action( 'jce/after_event_loop', 'jce_output_pagination' );
			break;
		}

		ob_start();

		do_action( 'jce/before_event_archive' );

		global $wp_query;
		$wp_query = $events;

		if(have_posts()): ?>

			<?php do_action( 'jce/before_event_loop' ); ?>

			<?php while(have_posts()): the_post(); ?>

				<?php jce_get_template_part('content-event'); ?>
				
			<?php endwhile; ?>

			<?php do_action( 'jce/after_event_loop' ); ?>

		<?php endif;

		wp_reset_query();

		do_action( 'jce/after_event_archive' );

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function add_month_title_links($title){
		global $current_month;

		$title .= ' - [<a href="'. add_query_arg('cal_month', date('m', strtotime(date('Y').'-'.$current_month.'-01' . " -1 MONTH"))) .'">&lt;</a>] [<a href="'. add_query_arg('cal_month', date('m', strtotime($current_month . " + 1 MONTH"))) .'">&gt;</a>]';
		return $title;
	}
}

new JCE_Shortcode_Archive();