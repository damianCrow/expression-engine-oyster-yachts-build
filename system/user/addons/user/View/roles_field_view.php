<style type="text/css">
	.user-permission-block {
		margin: 0 0 10px 0;
	}

	.user-permission-block .chosen-container {
		display: block;
		float: left;
		margin-top: 5px;
		margin-bottom: 5px;
		margin-right: 27px;
	}

	.user-permission-block .chosen-container:after {
		content: "\0026";
		font-size: 12px;
		color: #808080;
		display: inline;
		position: absolute;
		top: 5px;
		right: -18px;
	}

	.user-permission-block .chosen-container.last:after {
		content: ' ';
	}

	.user-permission-block .chosen-container.last {
		margin-right: 3px;
	}

	.user-permission-block hr {
		border: 0;
		height: 0;
		border-top: 1px solid rgba(0, 0, 0, 0.1);
		border-bottom: 1px solid rgba(255, 255, 255, 0.3);
		margin: 0 0 10px 0;
	}

	.user-permission-block a:hover {
		text-decoration: none;
		color: inherit;
	}

	.user-permission-block button.add-inclusive {
		top: 2px;
		position: relative;
	}

	.permission-sub-section {
		position: relative;
		display: block;
		margin-bottom: 25px;
		padding: 0 30px 10px 0;
		width: 95%;
		border-bottom: 1px solid #CCC;
	}

	.permission-sub-section:before {
		content: "<?=addslashes(lang('or'))?>";
		font-size: 12px;
		color: #bfbfbf;
		position: absolute;
		bottom: -22px;
		left: 50%;
		margin-left: -10px;
	}

	.permission-sub-section.last:before {
		content: ' ';
	}

	.permission-sub-section.last {
		margin-bottom: 10px;
	}

	.permission-sub-section .roles-select {
		width: 140px;
	}

	.permission-sub-section button.add-inclusive,
	.permission-sub-section button.remove-permission-group {
		background-color: #fff;
		border: solid 1px #cdcdcd;
		cursor: pointer;
		font-size: 12px;
		font-weight: 400;
		padding: 6px 8px 5px;
		text-decoration: none;
		font-family: 'solspace-fa';
		-moz-box-shadow: 0 1px 0 0 rgba(0, 0, 0, .05);
		-webkit-box-shadow: 0 1px 0 0 rgba(0, 0, 0, .05);
		box-shadow: 0 1px 0 0 rgba(0, 0, 0, .05);
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border-radius: 5px
	}

	.permission-sub-section button.add-inclusive {
		display: block;
		float: left;
		margin: 3px 0 0 5px;
	}

	.permission-sub-section button.remove-permission-group {
		position: absolute;
		top: 3px;
		right: 0;
	}

	.permission-sub-section .add-inclusive:before {
		color: #7baf55;
		content: '\e803'
	}

	.permission-sub-section .remove-permission-group:before {
		color: #bc4848;
		content: '\e809';
	}

	.permission-sub-section .chosen-container-single .chosen-single:hover div b {
		background: transparent url("<?=$addon_theme_url?>images/close_button.png") no-repeat scroll 2px 7px !important;
	}

	.clearfix:after {
		content:"";
		display:table;
		clear:both;
	}
</style>

<input type="hidden" name="<?=$field_name?>" value="<?=$field_id?>" />
<div class="user-permission-block" data-field-id="<?=$field_id?>">
	<button class="restrict-post-to-roles btn action">
		<?=lang('add_a_rule')?>
	</button>

	<div class="permission-section">
<?php if ( ! empty($field_permissions)):?>
	<?php
		$current_set	= 0;
		$first			= true;
		$counter		= 0;
		foreach ($field_permissions as $field_permission):
			//each set needs its own button to remove and add inclusives
			if ($current_set != $field_permission['set_id']):
				$current_set = $field_permission['set_id'];
		?>

			<?php if ($first): $first = false; else: ?>
				<button class="add-inclusive">
					<span class="glyphicons glyphicons-plus"></span>
				</button>
			</div>
			<?php endif;/*if first*/?>

			<div class="permission-sub-section clearfix">
				<button class="remove-permission-group"></button>
		<?php endif;/*if current set*/?>

			<select
				class="roles-select"
				name="roles_<?=$field_id?>_<?=$current_set?>_<?=($counter++)?>"
				id="roles_<?=$field_id?>_<?=$current_set?>_<?=($counter++)?>"
				>
				<option value=""><?=lang('please_choose_role')?></option>
		<?php foreach ($roles as $role_id => $role_data):?>
				<option value="<?=$role_id?>"
			<?php if ($field_permission['role_id'] == $role_id):?>
					selected="selected"
			<?php endif;/*if field permission*/?>
					>
					<?=$role_data['role_label']?>
				</option>
		<?php endforeach; /*roles*/?>
			</select>
	<?php endforeach; /*foreach ($field_permissions*/ /* this last item closes the sets of items*/?>

				<button class="add-inclusive">
					<span class="glyphicons glyphicons-plus"></span>
				</button>
			</div>
<?php endif; /*if ! empty field_permissions*/?>
		<button class="add-permission btn action">
			<?=lang('add_another_rule')?>
		</button>
	</div>

	<script class="roles-selection-template" type="text/html">
		<select
			class="roles-select"
			name="roles_<?=$field_id?>_{{= set_id }}_{{= postfix }}"
			id="roles_<?=$field_id?>_{{= set_id }}_{{= postfix }}">
			<option value=""><?=lang('please_choose_role')?></option>
	<?php foreach ($roles as $role_id => $role_data):?>
			<option value="<?=$role_id?>">
				<?=$role_data['role_label']?>
			</option>
	<?php endforeach;?>
		</select>
	</script>

	<script class='permissions-wrapper-template' type="text/html">
		<div class="permission-sub-section clearfix">
            <button class="remove-permission-group"></button>
			{{= content }}
			<button class="add-inclusive"></button>
		</div>
	</script>

</div>

<script type="text/javascript">
	// -------------------------------------
	//	The following should show you how
	//	difficult it is to write addons
	//	that have unpredicable environments.
	// -------------------------------------

	(function(global){
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

					var id = $chosen.attr('id').replace(/_chosen$/, '');

					//find select element by chosen ID which is the same
					//but with _chosen appended
					$("select#" + id).remove();

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

					//roles_<?=$field_id?>_{{set_id}}_{{postfix}}
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
	}(window));
	//END (function(global){

</script>
