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
    if($post && in_array($post->post_type, array('events','recurring_events'))) {
       return true;
    } else { 
       return false; 
    }
}

function eec_get_permalink($args = array()){
	if(get_option('permalink_structure')){
		if(empty($args))
			return site_url( '/events/' );
		elseif(isset($args['id']) && intval($args['id']) > 0){
			return get_permalink( $args['id'] );
		}
	}else{
		if(empty($args))
			return  add_query_arg('post_type' , 'events', site_url( '/' ));
	}
}

?>