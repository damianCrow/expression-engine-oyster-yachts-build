'use strict';

define(['jquery', 'cycle', 'ScrollMagic', 'foundation', 'lightgallery', 'select2', 'owlcarousel', 'components/header/header', './brokerage/brokerage', './index'], function ($, cycle, ScrollMagic) {

	// initilise foundation
	$(document).foundation();

	// Yacht nav - scroll to section
	$('#yacht-nav').on('click', '.scroll', function (e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: $($(this).attr('href')).position().top }, 700);
	});

	$('.scroll-message').click(function () {
		$('html, body').animate({
			scrollTop: $(".yacht-feature").offset().top - $('header').height()
		}, 1000);
	});

	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container'
	});

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

	var $grid = $('#yacht-grid');

	$('#submit').on('click', function (e) {
		e.preventDefault();

		$grid.find('li').show();

		if ($('#filter-model').val() !== "") {
			$grid.find("li[data-model!='" + $('#filter-model').val() + "']").hide();
		}
		if ($('#filter-status').val() !== "") {
			$grid.find("li[data-status!='" + $('#filter-status').val() + "']").hide();
		}
		if ($('#filter-location').val() !== "") {
			$grid.find("li[data-location!='" + $('#filter-location').val() + "']").hide();
		}
		if ($('#filter-price').val() !== "") {
			var $selected = $('#filter-price option:selected');

			if ($selected[0].hasAttribute('data-price-min')) {
				$grid.find('li').filter(function () {
					return $(this).data('price') < $selected.attr('data-price-min');
				}).hide();
			}

			if ($selected[0].hasAttribute('data-price-max')) {
				$grid.find('li').filter(function () {
					return $(this).data('price') > $selected.attr('data-price-max');
				}).hide();
			}
		}
	});

	$('select').select2({
		minimumResultsForSearch: -1
	});

	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    _sideBarScene = function _sideBarScene(elem) {
		// if elem doesnt exist, this will prevent the error
		if (_.isEmpty(elem)) elem = { offset: $.noop, height: $.noop };
		if (_.isEmpty(elem.offset())) return {};

		var _scene = new ScrollMagic.Scene({
			duration: elem.height(),
			offset: elem.offset().top - 50
		});

		_scene.setPin('section.about-yacht .sticky-sidebar');
		return _scene;
	},
	    $lastId = null,
	    $sideBarScene = _sideBarScene($elem),
	    $locaSubNav = $("[data-local-subnav]") || {},
	    $locaSubNavHeight = $locaSubNav.outerHeight(),
	    $localSubNavItems = $locaSubNav.find("a"),
	    $localSubNavItemsMap = $localSubNavItems.map(function () {
		var item = $($(this).attr("href"));

		if (item.length) return item;
	});

	
});
//# sourceMappingURL=app.js.map
