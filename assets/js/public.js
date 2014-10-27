// calendar ajax
jQuery(document).ready(function($) {
	
	// load currently selected days events
	$('.cal.no-inline-events .current-month').click(function(){

		$('#daily_ajax_response').html('<div class="jce-event-archive"><article class="jce-event"><p>Loading Events</p></article></div>');


		// console.log($(this).parentsUntil('.widget').html());

		var url = $(this).find('a').attr('href');
		// url = url.replace('#', '&widget=1#');

		var data = {
			'action': 'get_events',
			'url': url
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

	$('.jce-widget-calendar').each(function(){

		var _cal = $(this);

		// next/prev month
		_cal.on('click', '	.jce-month-link', function(){

			var data = {
				'action': 'get_cal_month',
				'url': $(this).attr('href')
			};

			jQuery.post(ajax_object.ajax_url, data, function(response) {

				// quickfix to remove ajax-links from url
				response = response.replace('/wp-admin/admin-ajax.php', '');

				_cal.html(response);
			});

			return false;
		});

		// day tile
		_cal.on('click', '.cal.no-inline-events .current-month', function(event){

			var url = $(this).find('a').attr('href');
			// url = url.replace('#', '&widget=1#');
			
			_cal.find('.current-month.active').removeClass('active');
			$(this).addClass('active');

			var data = {
				'action': 'get_events',
				'url': url
			};
			
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			jQuery.post(ajax_object.ajax_url, data, function(response) {

				_cal.find('#daily_ajax_response').html(response);
				// alert('Got this from the server: ' + response);
			});

			event.preventDefault();
		});

		// day tile link
		_cal.on('click', '.current-month a', function(event){
		// $('.cal.no-inline-events .current-month a').click(function(event){
			event.preventDefault();
		});

	});

});