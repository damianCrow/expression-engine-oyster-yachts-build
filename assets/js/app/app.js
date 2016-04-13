'use strict';

define(['jquery', 'cycle', 'ScrollMagic', 'foundation', 'masonry', /*'jquery-bridget',*/'lightgallery', 'lightgalleryThumbs', 'select2', 'owlcarousel', 'components/header/header', './brokerage/brokerage', './index'], function ($, cycle, ScrollMagic, Foundation, Masonry /*, jQueryBridget*/) {

	// initilise foundation
	$(document).foundation();

	/*// make Masonry a jQuery plugin
    jQueryBridget( 'masonry', Masonry, $ );*/

	//  ---- BACK BUTTON ON HERO BANNERS FUNCTIONALITY -----  //
	$('#page-back-button').on('click', function (evt) {
		evt.preventDefault();
		history.back(1);
	});
	//  ---- *end* BACK BUTTON ON HERO BANNERS FUNCTIONALITY *end* -----  //

	//  ---- LOCAL SUB-NAVIGATION SCROLL ON CLICK FUNCTIONALITY -----  //
	// Yacht nav - scroll to section
	$('#yacht-nav').on('click', '.scroll', function (e) {
		e.preventDefault();

		$('html, body').animate({
			scrollTop: $($(this).attr('href')).position().top
		}, 700);
	});
	//  ---- *end* LOCAL SUB-NAVIGATION SCROLL ON CLICK FUNCTIONALITY *end* -----  //

	//  ---- GLOBAL TESTIMONIALS SLIDESHOW CYCLE -----  //
	$('#page-testimonials .testimonials').cycle({
		autoHeight: 'calc',
		pager: '> .pager',
		pagerTemplate: '<span><span></span></span>',
		slides: '> .testimonials-inner',
		timeout: 4000
	});
	//  ---- *end* GLOBAL TESTIMONIALS SLIDESHOW CYCLE *end* -----  //

	//  ---- GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON -----  //
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
	//  ---- *end* GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON *end* -----  //

	//  ---- GLOBAL FILTER SEARCH INSIDE HERO HEADER -----  //
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
	//  ---- *end* GLOBAL FILTER SEARCH INSIDE HERO HEADER *end* -----  //

	// ---- GLOBAL STICKY SIDEBAR ----
	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    _sideBarScene = function _sideBarScene(elem) {
		// if elem doesnt exist, this will prevent the error
		if (_.isEmpty(elem)) elem = { offset: $.noop, height: $.noop };
		if (_.isEmpty(elem.offset())) return { addTo: $.noop, enabled: $.noop, destroy: $.noop };

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

	// Watch for breakpoint changes
	$(window).on('changed.zf.mediaquery', function (event, newSize, oldSize) {
		// newSize is the name of the now-current breakpoint, oldSize is the previous breakpoint
		if (newSize !== "small" && newSize !== "medium") {
			$sideBarScene = _sideBarScene($elem);
			$sideBarScene.addTo(controller);
		} else {
			$sideBarScene.enabled(false);
			$sideBarScene.destroy(true);
		}
	});

	if (!_.isEmpty($elem) && Foundation.MediaQuery.atLeast("large")) $sideBarScene.addTo(controller);

	// opens and closes the the table when snapped on the header
	$('.sticky-sidebar-header .header').on('click', function (e) {
		$(this).next().toggle(0);
	});

	// Simple Scroll spy for the Local SubNAV
	$(window).scroll(function () {
		// Get container scroll position
		var fromTop = $(this).scrollTop() + $locaSubNavHeight;

		// Get id of current scroll item
		var cur = $localSubNavItemsMap.map(function () {
			if ($(this).offset().top < fromTop) return this;
		});

		// Get the id of the current element
		cur = cur[cur.length - 1];
		var id = cur && cur.length ? cur[0].id : "";

		if ($lastId !== id) {
			$lastId = id;

			// Set/remove active class
			$localSubNavItems.parent().removeClass("active").end().filter("[href='#" + id + "']").parent().addClass("active");
		}
	});

	// Yacht nav - scroll to section
	$locaSubNav.on('click', '.scroll', function (e) {
		e.preventDefault();

		$('html, body').animate({
			scrollTop: $($(this).attr('href')).position().top
		}, 700);
	});

	// ---- *end* GLOBAL STICKY SIDEBAR *end* ----	

	//  ---- GLOBAL FOOTER SIGN UP BOX -----  //
	$('.sign-up-btn').on('click', function () {
		var btn = this;
		var nope = false;

		$(':input[required]').each(function () {
			if (!$(this).val()) {
				nope = true;
				console.log('error');
				$(this).parent('.field').addClass('error');
				$('.new-sign-up .form-error').addClass('visible');
			} else {
				$(this).parent('.field').removeClass('error');
				$('.new-sign-up .form-error').removeClass('visible');
			}
		});

		if (!validateEmail($(':input[type="email"]').val())) {
			nope = true;

			$(':input[type="email"]').parent('.field').addClass('error');
			$('.new-sign-up .form-error').addClass('visible');
		} else {
			console.log('correct email:', validateEmail($(':input[type="email"]').val()));

			$(':input[type="email"]').parent('.field').removeClass('error');
			$('.new-sign-up .form-error').removeClass('visible');
		};

		console.log('nope = ', nope);
		if (!nope) {
			console.log('should be false, nope = ', nope);
			$('.sign-up-btn').addClass('is-loading');
			setTimeout(function () {
				$('.sign-up').addClass('is-complete');
			}, 3000);
		}
	});

	$('#signup-home-footer').submit(function (e) {
		e.preventDefault();
	});

	// $('.sign-up-btn').on('click', function() {
	//   $(':input[required]').each(function() {
	//     if (!$(this).val()) {
	//       return $(this).parent('.field').addClass('error');
	//     } else {
	//       $('.sign-up-btn').addClass('is-loading');
	//       return setTimeout((function() {
	//         return $('.sign-up').addClass('is-complete');
	//       }), 3000);
	//     }
	//   });
	//   console.log('going to return false');
	//   return false;
	// });

	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}

	//  ---- *end* GLOBAL FOOTER SIGN UP BOX *end* -----  //

	//  ---- VIEW GALLERY (lightgallery) POP UP -----  //
	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container'
	});

	// init gallery
	$('.gallery-content').lightGallery({
		thumbnail: true,
		thumbContHeight: 136,
		thumbWidth: 197,
		thumbMargin: 14,
		toogleThumb: false,
		showThumbByDefault: false,
		closable: false,
		backdropDuration: 0
	});

	// trigger first slide to open in gallery
	$('.gallery').on('click', function (e) {
		e.preventDefault();

		$('.gallery-content a:first').trigger('click');
	});
	// ---- *end* VIEW GALLERY (lightgallery) POP UP *end* ----	

	//  ---- MASONRY.JS CONFIGUARATION -----  //

	// World rally news page masonry config
	/*var $worldRallyNews = $(".news-panes");
 console.log($worldRallyNews);
 	var $msnry = new Masonry($worldRallyNews, {
 	// options
 	itemSelector: '.news-pane',
 	columnWidth: 200
 });*/
	// ---- *end* MASONRY.JS CONFIGUARATION *end* ----	
});
//# sourceMappingURL=app.js.map
