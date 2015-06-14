<?php

// Add event title to event
add_action( 'jce/single_event_header', 'jce_add_event_title', 10 );
add_action( 'jce/event_header', 'jce_add_event_title', 10 );

// Add event meta to event
add_action( 'jce/event_header', 'jce_add_event_meta', 20 );

// show read more button
add_action( 'jce/event_footer', 'jce_add_event_footer', 20 );

/**
 * Event Archive Content
 */
add_action( 'jce/before_event_content', 'jce_add_archive_month');


add_action( 'jce/before_event_archive', 'jce_before_event_archive');


add_action( 'jce/after_event_archive', 'jce_after_event_archive');


/**
 * Output cal wrapper div around event archive
 */
add_action( 'jce/before_event_loop', 'jce_before_event_loop');

add_action( 'jce/after_event_loop', 'jce_after_event_loop');

/**
 * Display Filters Link
 */
add_action('jce/after_archive_heading', 'jce_show_archive_filter', 15);


/**
 * Display monthly title in archive only
 */
add_action('jce/widget/before_event_calendar', 'jce_output_monthly_archive_heading');
add_action('jce/before_event_calendar', 'jce_output_monthly_archive_heading');


add_action('jce/widget/before_event_calendar', 'jce_output_calendar_wrapper_open', 0);
add_action('jce/before_event_calendar', 'jce_output_calendar_wrapper_open', 0);


add_action('jce/widget/after_event_calendar', 'jce_output_calendar_wrapper_close', 999);
add_action('jce/after_event_calendar', 'jce_output_calendar_wrapper_close', 999);






/**
 * Do pagination
 */
add_action( 'jce/after_event_loop', 'jce_output_pagination' );


/**
 * Single Event content
 */

// add single event date to header
add_action( 'jce/single_event_header', 'jce_add_event_date', 20 );


add_action('jce/single_event_content', 'jce_add_single_event_content', 10);


add_action('jce/single_event_content', 'jce_add_single_event_image', 8);


add_action('jce/single_event_content', 'jce_add_single_event_venue', 15);


add_action('jce/single_event_content', 'jce_add_single_event_organiser', 16);


add_action('jce/single_event_content', 'jce_add_single_event_footer_meta', 20);


add_action('jce/before_event_loop', 'jce_add_single_back_btn');


add_action('jce/after_event_calendar', 'jce_output_daily_archive');


add_action('jce/widget/after_event_calendar', 'jce_output_widget_daily_archive');


// add_action('jce/widget/before_event_calendar', 'jce_output_event_filters', 11);
add_action('jce/before_event_calendar', 'jce_output_event_filters', 11);
add_action('jce/before_event_archive', 'jce_output_event_filters', 11);