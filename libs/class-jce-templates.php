<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Templates{

	public function __construct(){

		add_filter( 'template_include', array($this, 'template_include') );
		add_action( 'template_redirect', array($this, 'template_redirect'));
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts' ));
	}

	public function enqueue_scripts(){

		
		wp_enqueue_script('jcevents-admin-js', JCE()->plugin_url .'/assets/js/public.js', array('jquery'), '1.0', true);
		wp_localize_script( 'jcevents-admin-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );

		if(JCE()->disable_css || is_admin())
			return;

		wp_enqueue_style( 'jce-font-awsome', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );
		wp_enqueue_style('jce-skeleton', JCE()->plugin_url . 'assets/css/skeleton.css');
	}

	/**
	 * Load event templates
	 * @param  string $template 
	 * @return string template to load
	 */
	public function template_include($template = ''){

		if(is_post_type_archive( 'event' )){

			// calendar or upcoming view
			$view = get_query_var('view') ? get_query_var('view' ) : JCE()->default_view;
			if($view == 'calendar'){
				
				// load event calendar template
				$located = JCE()->plugin_dir . 'templates/archive-event-cal.php';
				$template_file = get_stylesheet_directory() . '/jcevents/archive-event-cal.php';
				if(is_file($template_file)){
					$located = $template_file;
				}
			}else{

				// load event archive template
				$located = JCE()->plugin_dir . 'templates/archive-event.php';
				$template_file = get_stylesheet_directory() . '/jcevents/archive-event.php';
				if(is_file($template_file)){
					$located = $template_file;
				}
			}

			
			return $located;

		}elseif(is_single() && get_post_type() == 'event'){

			// load single event template
			$located = JCE()->plugin_dir . 'templates/single-event.php';
			$template_file = get_stylesheet_directory() . '/jcevents/single-event.php';
			if(is_file($template_file)){
				$located = $template_file;
			}
			return $located;
		}

		return $template;
	}

	/**
	 * Set 404 response if not valid event date
	 * @return void
	 */
	public function template_redirect(){

		global $post, $wpdb, $wp_query;

		if(is_singular( 'event' )){

			$day = get_query_var('event_day');
			$month = get_query_var('event_month');
			$year = get_query_var('event_year');
			
			if($day && $month && $year){
				$date = sprintf("%d-%d-%d", $year, $month, $day);
				$temp_date = date('Y-m-d', strtotime($date));

				$result = $wpdb->get_row("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id='{$post->ID}' AND meta_key='_revent_start_date' AND meta_value LIKE '{$temp_date}%'");
				
				if(!$result){
					$wp_query->set_404();
					status_header(404);
				}
			}
		}
	}
}

new JCE_Templates();