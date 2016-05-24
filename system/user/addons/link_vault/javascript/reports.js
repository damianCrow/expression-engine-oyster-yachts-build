$(document).ready(function() {

	show_hide_report_fields();
	$('#rpt_table').change(function() {
		show_hide_report_fields();
	});

	// Give the two date fields the JQuery UI datepicker functionality
	$('.rpt_date').datepicker({'dateFormat' : 'yy-mm-dd'});

	// Save the form criteria so it can be run easily later
	$('#rpt_save').click(function() {
		$.ajax({
			'url'		: $('#rpt_save_form').attr('action'),
			'dataType'	: 'json',
			'type'		: 'post',
			'data'		: {
				'report_title'		: $('#rpt_title').val(),
				'report_criteria'	: $('#rpt_form').formData(),
				'XID'				: $('#rpt_save_form input[name=XID]').val()
			},
			'success'	: function(data) {
				if (data['status'] == "1")
				{
					$('#rpt_save_success').show();
					setTimeout(function() {
						$('#rpt_save_success').fadeOut(500);
					}, 3000);
				}
				else
				{
					$('#rpt_save_error').show();
					setTimeout(function() {
						$('#rpt_save_error').fadeOut(500);
					}, 3000);
				}
			}
		});
		return false;
	});

});

function show_hide_report_fields()
{
	if ( $('#rpt_table').val() == 'link_clicks' ) {
		$('#rpt_directory_span').hide();
		$('#rpt_directory').val('');
		$('#rpt_pretty_url_id_span').show();
	}
	else {
		$('#rpt_directory_span').show();
		$('#rpt_pretty_url_id_span').hide();
		$('#rpt_pretty_url_id_span').val('');
	}
}

// This function refreshes the tbody element in the report results table
function refresh_report_results()
{
	// Refresh the XID hash.
	$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		var old_xid = EE.XID;

		jqXHR.setRequestHeader("X-EEXID", old_xid);

		jqXHR.complete(function(xhr) {
			var new_xid = xhr.getResponseHeader('X-EEXID');

			if (new_xid) {
				EE.XID = new_xid;
				$('input[name="XID"]').filter('[value="'+old_xid+'"]').val(new_xid);
			}
		});
	});

	$('#rpt_loader').css('visibility', 'visible');
	$.ajax({
		'url'      : $('#rpt_form').attr('action'),
		'dataType' : 'html',
		'type'     : 'get',
		'data'     : $('#rpt_form').formData(),
		'success'  : function(data) {
			$('#report_table_body').html( data );
			$('#rpt_loader').css('visibility', 'hidden');

			// Add
			$('#rpt_results_count').html( '('+ $('#report_table_body tr').length +')' );
		}
	});
}

// This function creates a JSON string from form data. Thanks, Stack Overflow!
$.fn.formData = function()
{
	var form_elements = {};
	var serialized_form_data = this.find(':input:not(.ignore)').serializeArray();
	$.each(serialized_form_data, function() {
		if (form_elements[this.name] !== undefined) {
			if (!form_elements[this.name].push) {
				form_elements[this.name] = [form_elements[this.name]];
			}
			form_elements[this.name].push(this.value || '');
		} else {
			form_elements[this.name] = this.value || '';
		}
	});
	return form_elements;
};

