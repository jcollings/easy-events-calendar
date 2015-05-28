<?php
/*
	Plugin Name: JC Events Calendar
	Plugin URI: http://www.jamescollings.co.uk/jc-events
	Description: JC Events Calendar is an event calendar built with developers in mind, giving you calendar, archive and upcoming views with the power of repeating events right out of the box. 
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
	public $default_view = 'archive';
	public $disable_css = false;

	protected $settings = false;

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

		$this->load_settings();

		add_action( 'query_vars' , array( $this, 'register_query_vars' ) );

		add_action( 'init', array( $this, 'init' ) );

		register_activation_hook( __FILE__, array( $this, 'plugin_activation') );

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
		include_once 'libs/admin/class-jce-admin-settings.php';

		// shortcodes
		include_once 'libs/shortcodes/class-jce-shortcode-archive.php';
		include_once 'libs/shortcodes/class-jce-shortcode-calendar.php';

		// widgets
		// include_once 'libs/widgets/class-jce-widget-calendar.php';

		// functions
		include_once 'libs/jce-functions-general.php';
		include_once 'libs/jce-functions-template.php';

		// ajax funcs
		// include_once 'libs/class-jce-ajax.php';
	}

	public function load_settings(){

		// load values from jce_config
		$config = get_option('jce_config');
		foreach($config as $key => $value){
			$this->settings[$key] = $value;
		}

		if(isset($config['event_archive_view'])){
			$this->default_view = $config['event_archive_view'];
		}	
	}

	public function init(){

		$this->disable_css = apply_filters( 'jce/disable_css', false );
	}

	public function get_settings($key){

		if($this->settings && isset($this->settings[$key])){
			return $this->settings[$key];
		}elseif($this->settings && $key == null){
			return $this->settings;
		}

		return false;
	}

	public function register_query_vars($public_query_vars ){
        $public_query_vars[] = 'event_year';
        $public_query_vars[] = 'event_month';
        $public_query_vars[] = 'event_day';
        $public_query_vars[] = 'cal_month';
        $public_query_vars[] = 'cal_year';
        $public_query_vars[] = 'cal_day';
        return $public_query_vars;
	}

	public function plugin_activation(){
		flush_rewrite_rules();
	}
}

function JCE() {
	return JCEvents2::instance();
}

// Global for backwards compatibility.
$GLOBALS['jcevents'] = JCE();