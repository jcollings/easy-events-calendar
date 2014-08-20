<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Register Post types and taxonomies
 */
class JCE_Post_Types{

	public function __construct(){

		add_action('init', array($this, 'register_post_types'), 9);
		add_action('init', array($this, 'register_taxonomies'), 9);
	}

	public function register_post_types(){

		register_post_type( 'event', 
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

	public function register_taxonomies(){

		/**
		 * Register Category Taxonomy
		 */

		$labels = array(
			'name'					=> _x( 'Categories', 'Taxonomy plural name', 'jcevents' ),
			'singular_name'			=> _x( 'Category', 'Taxonomy singular name', 'jcevents' ),
			'search_items'			=> __( 'Search Categories', 'jcevents' ),
			'popular_items'			=> __( 'Popular Categories', 'jcevents' ),
			'all_items'				=> __( 'All Categories', 'jcevents' ),
			'parent_item'			=> __( 'Parent Category', 'jcevents' ),
			'parent_item_colon'		=> __( 'Parent Category', 'jcevents' ),
			'edit_item'				=> __( 'Edit Category', 'jcevents' ),
			'update_item'			=> __( 'Update Category', 'jcevents' ),
			'add_new_item'			=> __( 'Add New Category', 'jcevents' ),
			'new_item_name'			=> __( 'New Category Name', 'jcevents' ),
			'add_or_remove_items'	=> __( 'Add or remove Categories', 'jcevents' ),
			'choose_from_most_used'	=> __( 'Choose from most used jcevents', 'jcevents' ),
			'menu_name'				=> __( 'Categories', 'jcevents' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => 'event_category',
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( 'event_category', array( 'event' ), $args );	

		/**
		 * Register Calendar Taxonomy
		 */
		
		$labels = array(
		    'name'                => _x( 'Calendars', 'taxonomy general name' ),
		    'singular_name'       => _x( 'Calendar', 'taxonomy singular name' ),
		    'search_items'        => __( 'Search Calendars' ),
		    'all_items'           => __( 'All Calendars' ),
		    'parent_item'         => __( 'Parent Calendar' ),
		    'parent_item_colon'   => __( 'Parent Calendar:' ),
		    'edit_item'           => __( 'Edit Calendar' ), 
		    'update_item'         => __( 'Update Calendar' ),
		    'add_new_item'        => __( 'Add New Calendar' ),
		    'new_item_name'       => __( 'New Calendar Name' ),
		    'menu_name'           => __( 'Calendar' )
		); 
		
		register_taxonomy( 'event_calendar', array('event'), array(
			'public' => false,
			'hierarchical' => true,
			'labels' => $labels,  
		));	
		
		/**
		 * Register Tag Taxonomy
		 */
		
		$labels = array(
			'name'					=> _x( 'Tags', 'Taxonomy plural name', 'jcevents' ),
			'singular_name'			=> _x( 'Tag', 'Taxonomy singular name', 'jcevents' ),
			'search_items'			=> __( 'Search Tags', 'jcevents' ),
			'popular_items'			=> __( 'Popular Tags', 'jcevents' ),
			'all_items'				=> __( 'All Tags', 'jcevents' ),
			'parent_item'			=> __( 'Parent Tag', 'jcevents' ),
			'parent_item_colon'		=> __( 'Parent Tag', 'jcevents' ),
			'edit_item'				=> __( 'Edit Tag', 'jcevents' ),
			'update_item'			=> __( 'Update Tag', 'jcevents' ),
			'add_new_item'			=> __( 'Add New Tag', 'jcevents' ),
			'new_item_name'			=> __( 'New Tag Name', 'jcevents' ),
			'add_or_remove_items'	=> __( 'Add or remove Tags', 'jcevents' ),
			'choose_from_most_used'	=> __( 'Choose from most used jcevents', 'jcevents' ),
			'menu_name'				=> __( 'Tags', 'jcevents' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => 'event_tag',
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( 'event_tag', array( 'event' ), $args );

		/**
		 * Register Venue Taxonomy
		 */
		
		$labels = array(
			'name'					=> _x( 'Venues', 'Taxonomy plural name', 'jcevents' ),
			'singular_name'			=> _x( 'Venue', 'Taxonomy singular name', 'jcevents' ),
			'search_items'			=> __( 'Search Venues', 'jcevents' ),
			'popular_items'			=> __( 'Popular Venues', 'jcevents' ),
			'all_items'				=> __( 'All Venues', 'jcevents' ),
			'parent_item'			=> __( 'Parent Venue', 'jcevents' ),
			'parent_item_colon'		=> __( 'Parent Venue', 'jcevents' ),
			'edit_item'				=> __( 'Edit Venue', 'jcevents' ),
			'update_item'			=> __( 'Update Venue', 'jcevents' ),
			'add_new_item'			=> __( 'Add New Venue', 'jcevents' ),
			'new_item_name'			=> __( 'New Venue Name', 'jcevents' ),
			'add_or_remove_items'	=> __( 'Add or remove Venues', 'jcevents' ),
			'choose_from_most_used'	=> __( 'Choose from most used jcevents', 'jcevents' ),
			'menu_name'				=> __( 'Venues', 'jcevents' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => 'event_venue',
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( 'event_venue', array( 'event' ), $args );

		/**
		 * Register Organiser Taxonomy
		 */

		$labels = array(
			'name'					=> _x( 'Organisers', 'Taxonomy plural name', 'jcevents' ),
			'singular_name'			=> _x( 'Organiser', 'Taxonomy singular name', 'jcevents' ),
			'search_items'			=> __( 'Search Organisers', 'jcevents' ),
			'popular_items'			=> __( 'Popular Organisers', 'jcevents' ),
			'all_items'				=> __( 'All Organisers', 'jcevents' ),
			'parent_item'			=> __( 'Parent Organiser', 'jcevents' ),
			'parent_item_colon'		=> __( 'Parent Organiser', 'jcevents' ),
			'edit_item'				=> __( 'Edit Organiser', 'jcevents' ),
			'update_item'			=> __( 'Update Organiser', 'jcevents' ),
			'add_new_item'			=> __( 'Add New Organiser', 'jcevents' ),
			'new_item_name'			=> __( 'New Organiser Name', 'jcevents' ),
			'add_or_remove_items'	=> __( 'Add or remove Organisers', 'jcevents' ),
			'choose_from_most_used'	=> __( 'Choose from most used jcevents', 'jcevents' ),
			'menu_name'				=> __( 'Organisers', 'jcevents' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => 'event_organiser',
			'rewrite'           => true,
			'capabilities'      => array(),
		);

		register_taxonomy( 'event_organiser', array( 'event' ), $args );
	}
}

new JCE_Post_Types();