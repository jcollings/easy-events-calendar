// calendar ajax
jQuery(document).ready(function($) {
	
	

	$('.cal.no-inline-events .current-month').click(function(){

		$('#daily_ajax_response').html('<div class="jce-event-archive"><article class="jce-event"><p>Loading Events</p></article></div>');

		var data = {
			'action': 'get_events',
			'url': $(this).find('a').attr('href')
			// 'whatever': ajax_object.we_value      // We pass php values differently!
		};
		
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajax_object.ajax_url, data, function(response) {

			$('#daily_ajax_response').html(response);
			// alert('Got this from the server: ' + response);
		});

		return false;
	});

	// show and hide archive filters
	$('.jce-archive-filters').hide();
	$('#jce-show-filters').click(function(){
		$('.jce-archive-filters').slideToggle();
	});
});