'use strict';

define(['jquery', 'cycle', 'lightgallery', 'components/header/header', './brokerage/brokerage'], function ($, cycle) {

	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container'
	});

	/*$(window).scroll(function() {
 	var pos = $(document).scrollTop()/2;
 	$('#full-image').css({
 		'-ms-transform': 'translateY('+(pos)+'px)',
 		'-webkit-transform': 'translateY('+(pos)+'px)',
 		'transform': 'translateY('+(pos)+'px)'
 	});
 });*/

	$("#lightgallery").lightGallery({
		thumbnail: true,
		thumbContHeight: 136,
		thumbWidth: 197,
		thumbMargin: 14,
		toogleThumb: false,
		showThumbByDefault: false,
		closable: false
	});

	$('.gallery').on('click', function (e) {
		e.preventDefault();

		$("#lightgallery a:first").trigger('click');
	});

	// site search open
	$('.site-search-open').on('click', function (e) {
		e.preventDefault();

		$('.search-bar').show().find('input').animate({ right: 0 }, 300);
		$('.site-search-open').fadeOut(300);
		$('.site-search-close').fadeIn(300);
	});

	// site search close
	$('.site-search-close').on('click', function (e) {
		e.preventDefault();

		$('.search-bar').find('input').animate({ right: '-100%' }, 300, function () {
			$('.search-bar').hide();
		});
		$('.site-search-open').fadeIn(300);
		$('.site-search-close').fadeOut(300);
	});
});
//# sourceMappingURL=app.js.map
