<?php
/*
	Plugin Name: JC Events Calendar
	Plugin URI: http://www.jamescollings.co.uk/jc-events
	Description: Easy to install and use events calendar
	Version: 0.0.2
	Author: James Collings
	Author URI: http://www.jamescollings.co.uk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JCEvents2 {

	public $version = '0.0.2';
	public $plugin_dir = false;
	public $plugin_url = false;
	public $event = null;
	public $query = null;
	public $default_view = 'calendar';

	/**
	 * Single instance of class
	 */
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

		$this->plugin_dir =  plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );

		// include required files
		$this->includes();

		add_action( 'query_vars' , array( $this, 'register_query_vars' ) );

		do_action( 'jcevents_loaded' );
	}

	public function includes(){
		
		// core includes
		include_once 'libs/class-jce-post-types.php';
		include_once 'libs/class-jce-event.php';
		include_once 'libs/class-jce-calendar.php';
		include_once 'libs/class-jce-templates.php';
		$this->query = include_once 'libs/class-jce-query.php';

		// admin includes
		include_once 'libs/admin/class-jce-admin-venues.php';
		include_once 'libs/admin/class-jce-admin-organisers.php';
		include_once 'libs/admin/class-jce-admin-calendars.php';
		include_once 'libs/admin/class-jce-admin-post-types.php';
		include_once 'libs/admin/class-jce-admin-recurring-events.php';

		// functions
		include_once 'libs/jce-functions-general.php';
		include_once 'libs/jce-functions-template.php';
	}

	public function register_query_vars($public_query_vars ){
        $public_query_vars[] = 'event_year';
        $public_query_vars[] = 'event_month';
        $public_query_vars[] = 'event_day';
        $public_query_vars[] = 'cal_month';
        $public_query_vars[] = 'cal_year';
        return $public_query_vars;
	}
}

function JCE() {
	return JCEvents2::instance();
}

// Global for backwards compatibility.
$GLOBALS['jcevents'] = JCE();