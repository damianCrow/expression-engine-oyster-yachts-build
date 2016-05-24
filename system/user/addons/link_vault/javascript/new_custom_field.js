$(document).ready(function() {
	
	$('#cf_field_label').blur(function () {
		var cf_label = $(this).val();
		if (cf_label != '')
			$('#cf_field_name').val(cf_label.toLowerCase().replace(/ /g, '_'));
	});
	
	$('#cf_field_type').change(function() {
		set_field_type_description($(this).val());
	});

	set_field_type_description($('#cf_field_type').val());
	
});

function set_field_type_description(f_type)
{
	var description = '';
	switch(f_type)
	{
		case 'INT' :
			description = $('#desc_int').val();
			break;

		case 'FLOAT' :
			description = $('#desc_float').val();
			break;

		case 'VARCHAR' :
			description = $('#desc_varchar').val();
			break;

		default :
			description = 'Invalid field type';
	}
	$('#cf_field_type_description').html(description);
}
