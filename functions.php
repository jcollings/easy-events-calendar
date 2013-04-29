<?php
/**
 * Event Check
 *
 * Return true if current post is an event
 * @param  int  $page_id 
 * @return boolean
 */
function is_event($page_id) { 
    $post = get_post( $page_id);
    if( in_array($post->post_type, array('events','recurring_events'))) {
       return true;
    } else { 
       return false; 
    }
}
?>