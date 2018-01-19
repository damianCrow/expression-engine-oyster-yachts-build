!(function($, global){
	"use strict";
	
	var $ms = $("#member_search");
	var url = $ms.attr('data-autocomplete-uri');
	$ms.autocomplete({
		source: function (request, response)
		{
			$.getJSON(
				url,
				{
					member_keywords: request.term,
					output:"json"
				},
				function (data)
				{
					var result = [];

					if (data && typeof data.members !== 'undefined')
					{
						$.each(data.members, function(i, item){
							result.push(item);
						});
					}

					// assuming data is a JavaScript array such as
					// ["one@abc.de", "onf@abc.de","ong@abc.de"]
					// and not a string
					response(result);
				}
			);
		},
		minLength: 2,
		appendTo: $ms.parent()
	});

	var $memberOrGroup = $('select[name="member_or_group"]');

	$memberOrGroup.on('change', function(){
		var $that = $(this);

		if ($that.val() == 'group')
		{
			$('#member_search_form').hide();
			$('#group_select').show();
		}
		else
		{
			$('#member_search_form').show();
			$('#group_select').hide();
		}
	});

	//Firing once to make sure it hides correctly but
	//chosen has a chance to get the width of the dropdown first
	//because some browsers seem to report a 0 width on hidden items.
	//Boooooo.
	//The timeout is to make sure it runs after chosen's had a second.
	setTimeout(function(){
		$memberOrGroup.change();
	},10);
	//END $(function(){

}(jQuery, window));