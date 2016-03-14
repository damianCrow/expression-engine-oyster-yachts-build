/**
 * Low Reorder JavaScript
 *
 * @package        low_reorder
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/addons/low-reorder
 * @copyright      Copyright (c) 2016, Low
 */

// Make sure LOW namespace is valid
if (typeof LOW == 'undefined') var LOW = new Object;

// Anonymous wrapper
(function($){

// Settings for Low Reorder
LOW.CategoryOptions = function(){

	var $el = $('#low-category-options');

	if ( ! $el.length) return;

	$el.find('select[name="cat_option"]').on('change', function(){
		var val = $(this).val(),
			id = '#low-category-'+val;

		$el.find('.low-category-option').hide();
		$(id).show();

	});

};

$(LOW.CategoryOptions);

// ------------------------------------------
// Shortcut Parameters
// ------------------------------------------

LOW.Params = function(){
	var $el   = $('.low-reorder-search-fields'),
		$tmpl = $el.find('div'),
		$add  = $el.find('.add'),
		params = $el.data('params');

	var addFilter = function(event, key, val) {
		// Clone the filter template and remove the id
		var $newFilter = $tmpl.clone().hide();

		// If a key is given, set it
		if (key) $newFilter.find('select').val(key);

		// If a val is given, set it
		if (val) $newFilter.find('input').val(val);

		// Add it just above the add-button
		$add.before($newFilter);

		// If it's a click event, slide down the new filter,
		// Otherwise just show it
		if (event) {
			event.preventDefault();
			$newFilter.slideDown(100);
		} else {
			$newFilter.show();
		}

		if (event) $newFilter.find('select').focus();
	};

	// If we have reorder fields pre-defined, add them to the list
	if (typeof params == 'object') {

		// Remove template from DOM
		$tmpl.remove();

		for (var i in params) {
			addFilter(null, i, params[i]);
		}

	}

	// Enable the add-button
	$add.click(addFilter);

	// Enable all future remove-buttons
	$el.delegate('button.remove', 'click', function(event){
		event.preventDefault();
		$(this).parent().remove();
	});
};

$(LOW.Params);

// ------------------------------------------
// Jump to category
// ------------------------------------------

LOW.Jump = function(){

	var $el = $('.low-reorder-select-cat');

	if ( ! $el.length) return;

	var $select = $el.find('select'),
		url = $el.find('input[name="url"]').val() + '/';

	$select.on('change', function(){
		var id = $select.val();
		$select.prop('disabled', true);
		location.href = url + id;
	});

};

$(LOW.Jump);

// ------------------------------------------
// Reorder entries
// ------------------------------------------

LOW.Reorder = function(){

	var $el = $('.low-reorder');

	if ( ! $el.length) return;

	var $ul = $el.find('ul');

	$ul.sortable({
		axis: 'y',
		containment: $el,
		items: 'li'
	});

	$ul.find('li').mousedown(function(){
		$(this).addClass('grabbing');
	}).mouseup(function(){
		$(this).removeClass('grabbing');
	});

};

$(LOW.Reorder);


})(jQuery);
