$(document).ready(function () {

	$("select[name='status_toggler']").bind('change', function () {
		var theSelection = $(this).val();
		$("select.status_dropdown").each(function () {
			var theDropdown = $(this);
			var optionArray = $('option', theDropdown);
			
			optionArray.each(function () {
				if($(this).val() == theSelection)
				{
					theDropdown.val(theSelection);
				}
			});
		});
	});

	/**
     * Datepicker
     */
    $("body").delegate(".datepicker input, input.datepicker", "focus", function(e) {
		var datepickerElem = $(this);
		var theDateTime    = $(this).val();
		var theTime        = $(this).val().split(' ')[1];

        $(this).datepicker({
            dateFormat: 'yy-mm-dd ' + theTime,
        });
    });
	
});