'use strict';

define(['jquery', 'ScrollMagic', 'validateForm', 'foundation'], function ($, ScrollMagic, validateForm) {
	validateForm();

	// Global header
	var header = $("header.global-header"),
	    scrollToPoint = header.attr('data-snap-to-min-nav'),
	    snapOffset = parseInt(header.attr('data-snap-offset'), 10) || 0,
	   
	// local-nav

	headerClass = "global-header-mini",
	   
	// local-sidebar
	localSidebar = $("[data-local-sidebar]"),
	    stickySidebar = $('.sticky-sidebar'),
	    expandHeaderBuffer = $(header).height() * 1.5;

	var yachtNav = $("[data-local-subnav]"),
	    localSubNav = $(".global-local-subnav");

	if ($("[data-local-subnav]")[0]) {
		var localSubNav = $("[data-local-subnav]");
	} else if ($(".global-local-subnav")[0]) {
		var localSubNav = $(".global-local-subnav");
	}


	// if the scroll point is a div id, get its index point
	if (isNaN(parseInt(scrollToPoint), 10) && scrollToPoint != "undefined") scrollToPoint = $(scrollToPoint).offset().top - snapOffset;

	$(window).bind('scroll', function () {
		var scroll = $(window).scrollTop();

		// if the subnav and the header exists, remove the box shadow
		if (localSubNav.length !== 0 && header.length !== 0) headerClass = "global-header-mini no-boxshadow";

		if (scroll >= parseInt(scrollToPoint, 10)) {

			if ($(header).hasClass(headerClass)) {
				// $('.main-nav li ul').removeClass('hidden-animation');
			} else {
					$('.main-nav li ul').addClass('hidden-animation');
					header.addClass(headerClass);
				}

			localSubNav.addClass('global-local-subnav-mini').parent().css({
				position: 'static'
			});

			// A buffer zone for expanding the header.
		} else {

				yachtNav.removeClass('global-local-subnav-mini').parent().css({
					position: 'relative'
				});
			}

		if (scroll <= parseInt(expandHeaderBuffer, 10)) {

			if ($(header).hasClass(headerClass)) {
				$('.main-nav li ul').addClass('hidden-animation');

				header.removeClass(headerClass);
			} else {
				// $('.main-nav li ul').removeClass('hidden-animation');
			}

			localSubNav.removeClass('global-local-subnav-mini');
		}
	});


	/* From Modernizr */
	function whichTransitionEvent() {
		var t;
		var el = document.createElement('fakeelement');
		var transitions = {
			'transition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'MozTransition': 'transitionend',
			'WebkitTransition': 'webkitTransitionEnd'
		};

		for (t in transitions) {
			if (el.style[t] !== undefined) {
				return transitions[t];
			}
		}
	}

	/* Listen for a transition! */
	var e = document.getElementsByClassName('logo-o')[0];

	var transitionEvent = whichTransitionEvent();
	transitionEvent && e.addEventListener(transitionEvent, function () {
		$('.main-nav li ul').removeClass('hidden-animation');
	});


	// OVVERIDE MOBILE ACCORDION TEXT TO BE LINKS
	$('#global-navigation-modal a.accordion-title span').on('click', function (evt) {
		evt.preventDefault();
		location.href = $(this).parent().attr('href');
		return false;
	});

	$('.site-menu').on('click', function () {
		$(this).toggleClass('active-burger');
	});

	// Toggle follow oyster social buttons
	var navFooter = $('#global-navigation-modal .nav-footer-modal'),
	    followHeader = $(".global-header .follow");

	$('.nav-footer-modal .follow-btn').on('click', function () {
		navFooter.addClass('follow-oyster-social-btns');
	});
	$('.nav-footer-modal .social-links .back-btn').on('click', function () {
		navFooter.removeClass('follow-oyster-social-btns');
	});

	$('.global-header .follow-oyster').on('click', function () {
		followHeader.toggleClass('follow-on');
	});
	$('.global-header .back-btn').on('click', function () {
		followHeader.removeClass('follow-on');
	});

	// close foundation modal
	$('.global-modals .close-button-wrapper a.site-search-close').on('click', function (evnt) {
		// if a modal is full screen and has the combination of ".close-button-wrapper a.site-search-close", manually close it
		$('.global-modals.full').foundation('close');
	});

	//  ---- GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON -----  //
	var $siteSearchOpen = $('.global-header-top-nav-large .site-search'),
	    $siteSearchBar = $('.global-header-top-nav-large .search-bar');

	// site search open
	$siteSearchOpen.on('click', function (e) {
		e.preventDefault();

		$('.global-header-top-nav-large').toggleClass('search-bar-open');

		if ($('.global-header-top-nav-large').hasClass('search-bar-open')) {
			$('.search-bar input').focus();
		}
	});

});
//# sourceMappingURL=header.js.map
