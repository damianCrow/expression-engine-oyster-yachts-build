!(function($, global){
	"use strict";

	var User = window.User = window.User || {};

	// -------------------------------------
	//	private vars
	// -------------------------------------

	var channelFieldCache = {};

	// -------------------------------------
	//	private functions
	// -------------------------------------

	function fetchChannelFields(channel, callback)
	{
		if (_.has(channelFieldCache, channel))
		{
			return callback(channelFieldCache[channel]);
		}

		$.getJSON(User.channelAjaxUrl + channel, function(data){
			if ( ! _.has(data, 'success') || ! data.success)
			{
				return alert('There was an error fetching channel fields');
			}

			channelFieldCache[channel] = data.fields;

			callback(data.fields);
		});
	}

	// -------------------------------------
	//	Document ready
	// -------------------------------------

	$(function(){
		// -------------------------------------
		//	we need underscore to run templates
		//	<{= thing }> style because of stupid
		//	asp tags in PHP
		// -------------------------------------

		_.templateSettings = {
			evaluate	: /<\{([\s\S]+?)\}>/g,
			interpolate	: /<\{=([\s\S]+?)\}>/g,
			escape		: /<\{-([\s\S]+?)\}>/g
		};

		// -------------------------------------
		//	Templates
		// -------------------------------------

		var rowTemplate = _.template($('#new-row-template').html());

		// -------------------------------------
		//	fill table
		// -------------------------------------

		var $table			= $('.settings');
		var $mappingTable	= $('#mapping-table');
		var $mappingBody	= $mappingTable.find('tbody:first');
		var $channelSelect	= $('select[name="channel_sync_channel"]');
		User.channelAjaxUrl	= _.has(User, 'channelAjaxUrl') ?
								User.channelAjaxUrl :
								'';
		//remove &amp;
		User.channelAjaxUrl = User.channelAjaxUrl.replace(/\&amp\;/img, '&');

		// -------------------------------------
		//	incoming prefs?
		// -------------------------------------

		if (User.channelAjaxUrl &&
			_.has(User, 'incomingChannel') &&
			//just in case this is empty
			User.incomingPrefs &&
			_.keys(User.incomingPrefs).length
		)
		{
			fetchChannelFields(User.incomingChannel, function(fields){
				_.each(User.incomingPrefs, function(channelField, memberField){
					$mappingBody.append(rowTemplate({
						'memberField'	: memberField,
						'channelField'	: channelField,
						'channelFields' : fields,
						'num'			: $mappingBody.find('tr').length
					}));
				});
			});
			//END fetchChannelFields
		}
		//END if (User.channelAjaxUrl

		$channelSelect.on('change', function(e){
			$mappingBody.children().remove();
			var $that = $(this);

			//hide everything except the submit button and the channel
			//chooser if they choose to disable
			if ($that.val() == '0')
			{
				$table.find('fieldset[data-group="channel_sync"]').hide();
				$table.find('fieldset:first').addClass('last');
			}
			else
			{
				$table.find('fieldset[data-group="channel_sync"]').show();
				$table.find('fieldset:first').removeClass('last');
			}
		});

		//edit?
		if ($channelSelect.val() == '0')
		{
			//fire change so we can
			//hide the elements.
			//We don't want to do this if its
			//not 0 because it will clear out the mapping
			//and we don't want that on edit.
			$channelSelect.change();
		}

		// -------------------------------------
		//	add row for mapping fields
		//	fetches cached field info to build
		//	row
		// -------------------------------------

		$('#add-mapping-row').on('click', function(e){
			e.preventDefault();

			fetchChannelFields($channelSelect.val(), function(fields){
				$mappingBody.append(rowTemplate({
					'channelFields' : fields,
					'num'			: $mappingBody.find('tr').length
				}));
			});

			return false;
		});

		// -------------------------------------
		//	delete button rows
		// -------------------------------------

		$mappingTable.on('click', '.mapping-delete', function(e){
			e.preventDefault();

			$(this).closest('tr').remove();

			return false;
		});
	});
	//END $(function(){

}(jQuery, window));