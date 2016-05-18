!(function($, global){
	"use strict";
	
	
		
		var User									= global.User = global.User || {};
		User.lang									= User.lang || {};
		//it doesn't matter of these overwrite. Just langfile crud.
		User.lang.create_new_permission				= "<?=addslashes(lang('create_new_permission'))?>";
		User.lang.add_inclusive_role_requirement	= "<?=addslashes(lang('add_inclusive_role_requirement'))?>";
		User.lang.and_word							= "<?=addslashes(lang('and'))?>";

		User.listenersSet							= User.listenersSet || false;
		User.windowListenerSet						= User.windowListenerSet || false;


		// -------------------------------------
		//	remove used choices on select:change
		// -------------------------------------

		// --------------------------------------------------------------------

		/**
		 * Remove Used Choices From Select fields
		 *
		 * @access	private
		 * @param	{Object}	$context	jQuery object of select or parent
		 * @return	{Void}
		 */
		var removeUsedChoices = function($context)
		{
			var $set;

			if ($context.is('.permission-sub-section'))
			{
				$set = $context;
			}
			else
			{
				$set = $context.closest('.permission-sub-section');

				if ($set.length === 0)
				{
					$set = $context.parents().find('.permission-sub-section:first');
				}
			}

			var $selects = $set.find('.roles-select');

			//remove all disabled first so we can rebuild
			//disabled sets
			$selects.find('option').attr('disabled', false);

			$selects.each(function(i, item)
			{
				var $that = $(this);

				if ($.trim($that.val()) !== '')
				{
					//disable my choice on everything thats not me
					$selects.
						not($that).
						find('option[value="' + $that.val() + '"]').
						attr('disabled', 'disabled');
				}
			});

			//force update on chosen itself
			$selects.trigger("chosen:updated");
		};
		//ENd removeUsedChoices


		// --------------------------------------------------------------------

		/**
		 * Chosen select
		 *
		 * @access	public
		 * @param	{Object}	$element	element or query string for
		 *									elements to add chosen to
		 * @param	{Bool}		search		allow search UI
		 * @return	{Bool}					chosen fired on element
		 */

		User.addChosenSelect = User.addChosenSelect || function($element, search)
		{
			search = (typeof search !== 'undefined') ? search : false;

			if (typeof $ === 'undefined' ||
				typeof $.fn.chosen === 'undefined'
			)
			{
				return false;
			}

			if (typeof $element === 'string')
			{
				$element = $($element);
			}

			$element.chosen({disable_search:(search !== true)}).change(function(e){
				removeUsedChoices($(this));
			}).change(); //fire this as soon as one gets set out

			return true;
		};
		//END User.addChosenSelect


		// --------------------------------------------------------------------

		/**
		 * Quick Template
		 * Portable implimentation of UnderscoreJS Templates
		 *
		 * @access	public
		 * @param	{String}	text		template body
		 * @param	{Object}	data		optional data variables for instant call
		 * @param	{Object}	objectName	scope object
		 * @return	{Function}				compiled template as a function
		 */

		User.quickTemplate = User.quickTemplate || function(text, data, objectName)
		{
			'use strict';

			// By default, Underscore uses ERB-style template delimiters, change the
			// following template settings to use alternative delimiters.
			//
			// Swapped to use mustache style {{}} instead of ASP tags which
			// some implimentations of PHP _still_ have enabled. - gf
			var settings = {
				evaluate: /\{\{([\s\S]+?)\}\}/g,
				interpolate: /\{\{=([\s\S]+?)\}\}/g,
				escape: /\{\{-([\s\S]+?)\}\}/g
			};

			// When customizing `templateSettings`, if you don't want to define an
			// interpolation, evaluation or escaping regex, we need one that is
			// guaranteed not to match.
			var noMatch = /.^/;

			// Certain characters need to be escaped so that they can be put into a
			// string literal.
			var escapes = {
				'\\': '\\',
				"'": "'",
				'r': '\r',
				'n': '\n',
				't': '\t',
				'u2028': '\u2028',
				'u2029': '\u2029'
			};

			for (var p in escapes)
			{
				if (escapes.hasOwnProperty(p))
				{
					escapes[escapes[p]] = p;
				}
			}

			var escaper = /\\|'|\r|\n|\t|\u2028|\u2029/g;
			var unescaper = /\\(\\|'|r|n|t|u2028|u2029)/g;

			var tmpl = function (text, data, objectName)
			{
				settings.variable = objectName;

				// Compile the template source, taking care to escape characters that
				// cannot be included in a string literal and then unescape them in code
				// blocks.
				var source = "__p+='" + text
					.replace(escaper, function (match) {
						return '\\' + escapes[match];
					})
					.replace(settings.escape || noMatch, function (match, code)
					{
						return "'+\n_.escape(" + unescape(code) + ")+\n'";
					})
					.replace(settings.interpolate || noMatch, function (match, code)
					{
						return "'+\n(" + unescape(code) + ")+\n'";
					})
					.replace(settings.evaluate || noMatch, function (match, code)
					{
						return "';\n" + unescape(code) + "\n;__p+='";
					}) + "';\n";

				// If a variable is not specified, place data values in local scope.
				if (!settings.variable)
				{
					source = 'with(obj||{}){\n' + source + '}\n';
				}

				source = "var __p='';var print=function(){" +
						"__p+=Array.prototype.join.call(arguments, '')};\n" +
						source + "return __p;\n";

				var render = new Function(settings.variable || 'obj', source);  // jshint ignore:line

				if (data)
				{
					return render(data);
				}

				var template = function (data)
				{
					return render.call(this, data);
				};

				// Provide the compiled function source
				// as a convenience for build time
				// precompilation.
				template.source = 'function(' +
					(settings.variable || 'obj') +
					'){\n' + source + '}';

				return template;
			};

			return tmpl(text, data, objectName);
		};
		//END User.quickTemplate


		// --------------------------------------------------------------------

		/**
		 * Set Fieldtype listeners
		 *
		 * @access	public
		 * @param	{Object}	$	jQuery object
		 * @return	{Void}
		 */

		User.setFTListeners = User.setFTListeners || function($)
		{
			if (User.listenersSet === true)
			{
				return;
			}

			User.listenersSet = true;

			$('.user-permission-block').each(function(){

				var $context				= $(this);
				var hasData					= ($context.attr('data-has-data') == 'true');
				var fieldId					= $context.attr('data-field-id');

				var $restrictPostToRoles	= $('.restrict-post-to-roles', $context);
				var $addPermission			= $('.add-permission', $context);
				var rolesSelectTemplate		= User.quickTemplate(
					$('.roles-selection-template', $context).html()
				);
				var selectWrapperTemplate	= User.quickTemplate(
					$('.permissions-wrapper-template', $context).html()
				);

				//last-child is being a turd-bag in Firefox
				var fixLastElements = function()
				{
					var $parents = $context.find('.permission-sub-section');

					$parents.each(function(i, item){
						var $finds = $('.chosen-container', $(this));
						$finds.removeClass('last').last().addClass('last');
					});

					$parents.removeClass('last').last().addClass('last');
				};
				//end Fix Last elements


				// -------------------------------------
				//	remove permission group
				// -------------------------------------

				$context.on('click', '.remove-permission-group', function(e)
				{
					e.preventDefault();

					var $that = $(this);
					$that.parent().remove();

					//show main button again if this is the last one
					if ($context.find('.permission-sub-section').length <= 0)
					{
						$restrictPostToRoles.show();
						$addPermission.hide();
						fixLastElements();
					}
					else
					{
						$restrictPostToRoles.hide();
						$addPermission.show();
						fixLastElements();
					}

					return false;
				});
				//end remove permission group

				$context.on('click', '.chosen-container-single .chosen-single div b', function(e){
					e.preventDefault();

					var $that = $(this);

					var $chosen = $that.parents('[id$="_chosen"]:first');

					var $parent = $chosen.parents('.permission-sub-section:first');

					//find select element by chosen ID which is the same
					//but with _chosen appended
					$($chosen.attr('id').replace(/_chosen$/, '')).remove();

					//remove chosen select
					$chosen.remove();

					//if this is the last one, lets just remove the entire container
					if ($parent.find('[class*=chosen-container]').length === 0)
					{
						$parent.find('.remove-permission-group').click();
					}
					else
					{
						fixLastElements();
					}

					return false;
				});

				// -------------------------------------
				//	On edit
				// -------------------------------------

				//we are in edit mode. hide starting button
				if ($context.find('.permission-sub-section').length > 0)
				{
					$restrictPostToRoles.hide();
					$addPermission.show();
					//add chosen
					//eventhough we are already past window onload here
					//we have to run document ready for this to work?
					//I dont even know wtf...
					$(function(){
						$context.find('select').each(function(){
							var $that = $(this);
							User.addChosenSelect($that);
						});
						//this must be called here because this runs even
						//later than settimeouts
						fixLastElements();
					});
				}
				else
				{
					$addPermission.hide();
				}

				fixLastElements();

				// -------------------------------------
				//	get set id
				// -------------------------------------

				var findLastSetId = function($context)
				{
					var setId = 0;

					var $subs = $context ? $context : $('.permission-sub-section') ;

					if ($subs.length > 0)
					{
						var setIds = [];

						// -------------------------------------
						//	get the set id from each set of
						//	select sets, then sort them and
						//	return the highest one
						// -------------------------------------

						$subs.each(function(){
							var $that	= $(this);
							var $select = $that.find('.roles-select:last');

							if ($select.length === 0)
							{
								return;
							}

							var parts	= $select.attr('name').split('_');
							var lSetId	= parts[2];

							setIds.push(lSetId);
						});

						setIds.sort();

						if (setIds.length)
						{
							setId = setIds[setIds.length -1];
						}
					}

					return Number(setId);
				};

				// -------------------------------------
				//	Create new permission
				// -------------------------------------

				var createNewPermissionCB = function(e)
				{
					e.preventDefault();

					// -------------------------------------
					//	Set ids
					// -------------------------------------
					//	Set ids really don't matter except
					//	to separate items on submission.
					//	Actual set ids (which again only matter
					//	for pairing purposes) are set at time
					//	of data save in the fieldtypes PHP.
					// -------------------------------------

					var setId = findLastSetId($context);

					if (setId === 0)
					{
						setId = 1;
					}
					else
					{
						setId++;
					}

					var $block = $(selectWrapperTemplate({
						content: rolesSelectTemplate({
							"set_id"	: setId,
							"postfix"	: 1 //this is the first one in this set
						})
					}));

					$addPermission.before($block);

					User.addChosenSelect($block.find('select'));

					fixLastElements();

					return false;
				};
				//END createNewPermissionCB


				//Initial button
				$context.on('click', '.restrict-post-to-roles', function(e)
				{
					$restrictPostToRoles.hide();
					$addPermission.show();
					createNewPermissionCB(e);
				});

				$context.on('click', '.add-inclusive', function(e)
				{
					e.preventDefault();

					var $that	= $(this);
					var $parent = $that.parent();

					var setId	= findLastSetId($parent);

					var postfix = Number($parent.
									find('.roles-select').
									last().
									attr('name').
									split('_')[3]) + 1;

					var $output = $(rolesSelectTemplate({
						"set_id"	: setId,
						"postfix"	: postfix //this is the first one in this set
					}));

					$parent.find('.add-inclusive').before($output);

					var $out = $output;

					//for some strange reason
					//$output = $(rolesSelectTemplate(
					//is making this into an object set instead of
					//a parent set of HTML ? I suspect its because
					//it doesn't have a div wrapper. Either way, every
					//annoying.
					$output.each(function(){
						if ($(this).is('select'))
						{
							$out = $(this);
						}
					});

					User.addChosenSelect($out);
					fixLastElements();

					return false;
				});

				//section button
				$context.on('click', '.add-permission', createNewPermissionCB);

			});
			//END $('.user-permission-block').each
		};
		//END User.setFTListeners


		// --------------------------------------------------------------------

		/**
		 * Add Listener - cross browser event listener
		 *
		 * @access	public
		 * @param	{String}	event		event to listen for e.g. 'load'. Not 'onload'.
		 * @param	{Object}	element		element to listen for event on
		 * @param	{Function}	callback	callback function for event
		 * @return	{Object}				listener result
		 */

		User.addListener = User.addListener || function(event, element, callback)
		{
			// W3C DOM
			if (element.addEventListener)
			{
				return element.addEventListener(event,callback,false);
			}
			// IE DOM
			else if (element.attachEvent)
			{
				return element.attachEvent("on" + event, callback);
			}
		};
		//END addListener


		// --------------------------------------------------------------------

		/**
		 * Set Window Listener
		 *
		 * @access	public
		 * @return {Void}
		 */

		User.setWindowListener = User.setWindowListener || function()
		{
			if (User.windowListenerSet === true)
			{
				return;
			}

			User.windowListenerSet = true;

			var counter = 0;

			var callback = function()
			{
				if (typeof jQuery !== 'undefined')
				{
					return User.setFTListeners(jQuery);
				}
				//try for three seconds total
				else if (counter < 30)
				{
					counter++;
					setTimeout(callback, 100);
				}
				else if (typeof console !== 'undefined' &&
						typeof console.log === 'function')
				{
					console.log('User fieldtype requires jQuery and could not find it.');
				}
			};

			User.addListener('load', window, callback);
		};
		//END setWindowListener

		// -------------------------------------
		//	check if set
		// -------------------------------------

		if ( ! User.listenersSet && ! User.windowListenerSet)
		{
			if (typeof jQuery !== 'undefined')
			{
				User.setFTListeners(jQuery);
			}
			else
			{
				User.setWindowListener();
			}
		}

	$(function(){
		var $context = $('form.settings');

		// -------------------------------------
		//	autoshortname
		// -------------------------------------

		var $label		= $context.find('input[name$="_label"]');
		var $name		= $context.find('input[name$="_name"]');

		if (typeof User !== 'undefined' &&
			$label.length > 0 &&
			$name.length > 0)
		{
			User.autoGenerateShortname($label, $name);
		}
	});
	//END $(function(){

}(jQuery, window));