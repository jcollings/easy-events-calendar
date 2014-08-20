<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCE_Templates{

	public function __construct(){

		add_filter( 'template_include', array($this, 'template_include') );
		add_action( 'template_redirect', array($this, 'template_redirect'));
	}

	/**
	 * Load event templates
	 * @param  string $template 
	 * @return string template to load
	 */
	public function template_include($template = ''){

		if(is_post_type_archive( 'event' )){
			
			// load event archive template
			$located = JCE()->plugin_dir . 'templates/archive-event.php';
			$template_file = get_stylesheet_directory() . '/jcevents/archive-event.php';
			if(is_file($template_file)){
				$located = $template_file;
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

			if(isset($_GET['ed']) && isset($_GET['em']) && isset($_GET['ey'])){
				$day = $_GET['ed'];
				$month = $_GET['em'];
				$year = $_GET['ey'];
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