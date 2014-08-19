<?php  
/*
	Plugin Name: JC Events
	Plugin URI: http://www.jamescollings.co.uk
	Description: Easy to install and use events calendar
	Version: 0.0.1
	Author: James Collings
	Author URI: http://www.jamescollings.co.uk
 */

/*  Copyright 2013  James Collings  (email : james@jclabs.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'class-jce-event.php';

$GLOBALS['jcevents'] = new JCEvents();



require_once 'addons' . DIRECTORY_SEPARATOR . 'recurring-events.php';

class JCEvents{

	var $version = '0.0.1';
	var $plugin_dir = false;
	var $plugin_url = false;
	var $events_pt = 'events';

	function __construct(){

		$this->plugin_dir =  plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );
		

		add_action('init', array($this, 'register'));
		add_action('query_vars', array($this, 'register_query_vars') );
		
		// http://codex.wordpress.org/Function_Reference/WP_Rewrite
		// add_action('init', array($this, 'events_rewrite'));
		// add_filter('post_type_link', array($this, 'events_permalink'), 1, 3);

		add_filter( 'template_include', array($this, 'template_include') );

		register_activation_hook( __FILE__, array($this, 'activation') );
		add_action('admin_init',array($this, 'load_plugin'));

		$this->load_modules();
		$this->load_settings();
	}

	function register(){

		register_post_type( $this->events_pt, 
			array(
				'capability_type' => 'post',
				'rewrite' => array('slug' => 'events'),
				'query_var' => true,
				'has_archive' => true,
				'show_in_nav_menus' => true,
				'labels' => array(
					'name' => __('Events'),
				    'singular_name' => __('Event'),
				    'add_new' => _x('Add New', 'event'),
				    'add_new_item' => __('Add New Event'),
				    'edit_item' => __('Edit Event'),
				    'new_item' => __('New Event'),
				    'all_items' => __('All Event'),
				    'view_item' => __('View Event'),
				    'search_items' => __('Search Events'),
				    'not_found' =>  __('No events found'),
				    'not_found_in_trash' => __('No events found in Trash'), 
				    'parent_item_colon' => '',
				    'menu_name' => __('Events')
				),
				'exclude_from_search' => true,
				'publicly_queryable' => true,
				'public' => true,
				'taxonomies' => array('event_cals'),
				'supports' => array('title', 'editor', 'thumbnail')
			)
		);		
	}

	/**
	 * Register Query Vars
	 * 
	 * @param  array $public_query_vars 
	 * @return array
	 */
	function register_query_vars($public_query_vars) {
		$public_query_vars[] = 'xyear';
		$public_query_vars[] = 'xmonth';
		$public_query_vars[] = 'xday';
		$public_query_vars[] = 'xname';
		$public_query_vars[] = 'event_id';
		$public_query_vars[] = 'event_title';
		$public_query_vars[] = 'view';
		return $public_query_vars;
	}

	function template_include($template){
		global $post, $wp_query, $wp_rewrite;

		// only change templates if not single or archive events
		if(!is_post_type_archive('events') && !is_singular( 'events' )){
			return $template;
		}
		
		$single_event = false;
		$month = get_query_var( 'xmonth' );
		$year = get_query_var( 'xyear' );
		$day = get_query_var( 'xday' );
		$name = get_query_var( 'xname' );

		if($month > 0 && $year > 0){
			if($day > 0){

				global $jce_event;
				$jce_event = new JCE_Event($name, $day, $month, $year);
				
				// set wp_query and post
				$wp_query = $jce_event->get_wpquery();
				$post = $wp_query->post;
			}else{
				$query = new WP_Query(array(
					'post_type' => array('events'),
					'name' => $name,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => '_event_start_date',
							'value' => $year.'-'.$month.'-01', // '20/'.$month.'/'.$year,
							'compare' => '>=',
							'type' => 'DATE'
						),
						array(
							'key' => '_event_start_date',
							'value' => $year.'-'.$month.'-31', // '20/'.$month.'/'.$year,
							'compare' => '<=',
							'type' => 'DATE'
						)
					)
				));
				if(isset($query->post))
					$post = $query->post;

				$temp_file = get_template_directory() . DIRECTORY_SEPARATOR . 'simple-events-calendar' . DIRECTORY_SEPARATOR . 'event-index-template.php';
				if(is_file($temp_file)){
					return $temp_file;
				}else{
					return plugin_dir_path( __FILE__ ).'views/public/event-index-template.php';
				}
			}
		}

		if($post && is_event($post->ID)){

			if(is_single($post->ID) || $single_event){

				global $jce_event;
				$jce_event = new JCE_Event($post->ID, $day, $month, $year);

				$temp_file = get_template_directory() . DIRECTORY_SEPARATOR . 'simple-events-calendar' . DIRECTORY_SEPARATOR . 'event-single-template.php';
				if(is_file($temp_file)){
					return $temp_file;
				}else{
					return plugin_dir_path( __FILE__ ).'views/public/event-single-template.php';
				}
			}else{
				$temp_file = get_template_directory() . DIRECTORY_SEPARATOR . 'simple-events-calendar' . DIRECTORY_SEPARATOR . 'event-index-template.php';
				if(is_file($temp_file)){
					return $temp_file;
				}else{
					return plugin_dir_path( __FILE__ ).'views/public/event-index-template.php';
				}
			}
		}
		return $template;	
	}

	function activation(){
		add_option('Activated_Plugin','easy-events-calendar');
	}

	function load_plugin() {
	    if(is_admin()&&get_option('Activated_Plugin')=='easy-events-calendar') {
	     	delete_option('Activated_Plugin');
	     	/* do stuff once right after activation */
	     	$terms = get_terms( 'event_cals', array('hide_empty' => false) );
	     	if(!$terms){
	     		wp_insert_term( 'default', 'event_cals' );
	     	}
	     	
	    }
	}

	function load_settings(){

	}

	function load_modules(){
		
		include 'functions.php';
		include 'calendar.php';
		include 'shortcodes.php';

		include 'EventsModel.php';
		EventsModel::init($this);

		include 'CalendarAdmin.php';
		new CalendarAdmin($this);

		include 'EventsAdmin.php';
		new EventsAdmin($this);
	}

}

/**
 * Temp Work around WIP
 */
global $my_rewrite_rules;
$my_rewrite_rules = array(
	'events/([0-9]+)/([0-9]+)/?$' => 'events/%xyear%/%xmonth%/',
	'events/([0-9]+)/([0-9]+)/([0-9]+)/(.+?)/?$' => 'events/%xyear%/%xmonth%/%xday%/%xname%/'
);

function add_rewrite_rules( $wp_rewrite ) 
{
	global $my_rewrite_rules;
	$new_rules = array(
		'events/([0-9]+)/([0-9]+)/?$' => 'index.php?post_type=events&xyear='. $wp_rewrite->preg_index(1).'&xmonth='. $wp_rewrite->preg_index(2),
		'events/([0-9]+)/([0-9]+)/([0-9]+)/(.+?)/?$' => 'index.php?xyear='. $wp_rewrite->preg_index(1).'&xmonth='. $wp_rewrite->preg_index(2).'&xday='. $wp_rewrite->preg_index(3).'&xname='. $wp_rewrite->preg_index(4),
	);
    
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'add_rewrite_rules');



function change_event_links($post_link, $id=0){
	global $wp_rewrite, $wp, $my_rewrite_rules;

	$post = get_post($id);

	if( is_object($post) &&$post->post_type == 'events'){
		$time = strtotime(get_post_meta( $post->ID, '_event_start_date', true ));

		if($post->post_parent > 0){
			$q = new WP_Query(array(
				'p' => $post->post_parent,
				'post_type' => array('events', 'recurring_events')
			));
			$name = $q->post->post_name;
		}else{
			$name = $post->post_name;
		}

		if(empty($wp_rewrite->permalink_structure)){
			$url = '?post_type=events&xyear=%xyear%&xmonth=%xmonth%&xday=%xday%&xname=%xname%';
		}else{
			$url = 'events/%xyear%/%xmonth%/%xday%/%xname%/';	
		}

		
		$url = str_replace('%xyear%', date('Y', $time), $url);
		$url = str_replace('%xmonth%', date('m', $time), $url);
		$url = str_replace('%xday%', date('d', $time), $url);
		$url = str_replace('%xname%', $name, $url);
		return home_url($url);
	}

	return $post_link;
}
add_filter('post_type_link', 'change_event_links', 1, 3);
?>