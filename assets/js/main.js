jQuery(document).ready(function($){
	$('#event_start_date').datepicker({ 
		dateFormat: 'yy-mm-dd', 
		minDate: '0',
		onClose: function( selectedDate ) {
            $( "#event_end_date" ).datepicker( "option", "minDate", selectedDate );
        }
	});
	$('#event_end_date').datepicker({ 
		dateFormat: 'yy-mm-dd',
		minDate: '0'
	});


	$('.recurrence_specific').hide();
	if($('#wp-engine-meta-id_recurrence_type').val() == 0){
		$('#recurrence_fields').hide();
	}else{
		$('.recurrence_'+$('#wp-engine-meta-id_recurrence_type').val()).show();	
	}
	
	$('#wp-engine-meta-id_recurrence_type').change(function(){

		if($('#wp-engine-meta-id_recurrence_type').val() == 0){
			$('#recurrence_fields').hide();
		}else{
			$('#recurrence_fields').show();
		}

		$('.recurrence_specific').hide();
		if($('.recurrence_'+$(this).val()).length > 0){
			$('.recurrence_'+$(this).val()).show();	
		}
	});

	// manage admin cal checkboxes
	$('.simple_events #category-all .input.checkbox input').change(function(){
		$('.event-list').each(function(){
			$(this).hide();
		});

		$('.simple_events #category-all .input.checkbox input').each(function(){
			if($(this).prop('checked') == true ){
				$('.'+$(this).attr('name')+'.event-list').show();
			}
		});
	});

	$('select#calendar_list').change(function(){
		console.log($(this).val());
	});
});