'use strict';

define(['jquery', 'gallery_modal'], function ($) {
	//  ---- LISTING STAGGERED FADE IN -----  //

	// This is done in CSS, this JS is only for the staggered effect delay times.
	var staggerTime = 0;

	$('.list-entrance > li').each(function (index, element) {
		//$(element).css('animation-delay', staggerTime + 'ms');
		
		fadeEl($(element), staggerTime);

		staggerTime = staggerTime + 300;
	});

	function fadeEl($el, staggerTime) {
		setTimeout(function() {
			$el.addClass('fadein');
		}, staggerTime);
	}

	//$('.list-entrance > li').addClass('list-entrance-animations');

	// ---- *end* LISTING STAGGERED FADE IN *end* ----	
});
