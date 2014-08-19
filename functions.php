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
			$permalink = site_url( '/events/' );
		elseif(isset($args['id']) && intval($args['id']) > 0){
			$permalink = get_permalink( $args['id'] );
		}
	}else{
		if(empty($args))
			$permalink = add_query_arg('post_type' , 'events', site_url( '/' ));
		elseif(isset($args['id']) && intval($args['id']) > 0){
			$permalink = get_permalink( $args['id'] );
		}
	}

	$date = $args['date'];
	if($date){
		$permalink = add_query_arg(array(
			'xday' => date('d', strtotime($date)),
			'xmonth' => date('m', strtotime($date)),
			'xyear' => date('Y', strtotime($date)),
		), $permalink);
	}

	return $permalink;
}

function eec_pagination($total_posts = 0, $posts_per_page = false){

	if($total_posts > $posts_per_page){

		// time for some pagination
		$page_count = ceil($total_posts / $posts_per_page);
		for($x = 1; $x <= $page_count; $x++){
			echo '<a href="'.add_query_arg('paged', $x).'">'.$x.'</a>';
		}
	}
}

?>