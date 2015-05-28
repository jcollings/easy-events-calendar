// calendar ajax
jQuery(document).ready(function($) {
	
	// show and hide archive filters
	$('.jce-archive-filters').hide();

	$('.jce-calendar, .jce-event-archive').each(function(){

		var _cal = $(this);

		// toggle cal filters
		_cal.on('click', '.jce-show-filters', function(){
			_cal.find('.jce-archive-filters').slideToggle();
		});

		// next/prev month
		// _cal.on('click', '	.jce-month-link', function(){

		// 	_cal.find('.cal').html('<div class="jce-event-archive"><article class="jce-event"><p>Loading Events</p></article></div>');
		// 	_cal.find('#daily_ajax_response').html('');

		// 	var data = {
		// 		'action': 'get_cal_month',
		// 		'url': $(this).attr('href')
		// 	};

		// 	jQuery.post(ajax_object.ajax_url, data, function(response) {

		// 		// quickfix to remove ajax-links from url
		// 		response = response.replace('/wp-admin/admin-ajax.php', '');

		// 		_cal.html(response);
		// 		_cal.find('.jce-archive-filters').hide();
		// 	});

		// 	return false;
		// });

		// day tile
		_cal.on('click', '.cal .current-month', function(event){

			_cal.find('#daily_ajax_response').html('<div class="jce-event-archive"><article class="jce-event"><p>Loading Events</p></article></div>');

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

			// scroll down to ajax response
			$('html, body').animate({
                scrollTop: $("#daily_ajax_response").offset().top
            }, 2000);

			event.preventDefault();
		});

		// day tile link
		_cal.on('click', '.no-inline-events .current-month a', function(event){
			event.preventDefault();
		});
	});

});