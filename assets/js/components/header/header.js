'use strict';

define(['jquery', 'ScrollMagic', 'validateForm', 'foundation', 'ownersAreaModal'], function ($, ScrollMagic, validateForm) {
	validateForm();

	// Global header
	var header = $("header.global-header"),
	    scrollToPoint = header.attr('data-snap-to-min-nav'),
	    snapOffset = parseInt(header.attr('data-snap-offset'), 10) || 0,
	   
	// local-nav
	localSubNav = $("[data-local-subnav]"),
	    headerClass = "global-header-mini",
	   
	// local-sidebar
	localSidebar = $("[data-local-sidebar]");

	// if the scroll point is a div id, get its index point
	if (isNaN(parseInt(scrollToPoint), 10) && scrollToPoint != "undefined") scrollToPoint = $(scrollToPoint).offset().top - snapOffset;

	$(window).bind('scroll', function () {
		var scroll = $(window).scrollTop();

		// if the subnav and the header exists, remove the box shadow
		if (localSubNav !== [] && header !== []) headerClass = "global-header-mini no-boxshadow";

		if (scroll >= parseInt(scrollToPoint, 10)) {
			header.addClass(headerClass);

			localSubNav.addClass('global-local-subnav-fixed').parent().css({
				position: 'static'
			});
		} else {
			header.removeClass(headerClass);

			localSubNav.removeClass('global-local-subnav-fixed').parent().css({
				position: 'relative'
			});
		}

		var sidebarElem = $(localSidebar.attr('data-sticky-sidebar-at-bottom')) || { offset: $.noop },
		    sidebarElemOffset = sidebarElem.offset() || {},
		    sidebarScrollToPoint = sidebarElemOffset.top + sidebarElem.height();

		// table sidenav sticky nav
		if (scroll >= sidebarScrollToPoint) {
			localSidebar.removeClass('hide').parent().addClass('adjust-height-width-sidebar');
		} else {
			localSidebar.addClass('hide').parent().removeClass('adjust-height-width-sidebar');
		}
	});

	// OVVERIDE MOBILE ACCORDION TEXT TO BE LINKS
	$('#global-navigation-modal a.accordion-title span').on('click', function (evt) {
		evt.preventDefault();
		location.href = $(this).parent().attr('href');
		return false;
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
	// site search open
	var $siteSearchOpen = $('.global-header-top-nav-large .site-search-open'),
		$siteSearchClose = $('.global-header-top-nav-large .site-search-close'),
		$siteSearchBar = $('.global-header-top-nav-large .search-bar');

	// site search open
	$siteSearchOpen.on('click', function(e) {
		e.preventDefault();

		$siteSearchBar.show().find('input').animate({right:0}, 300);
		$siteSearchOpen.fadeOut(300);
		$siteSearchClose.fadeIn(300);
	});

	// site search close
	$siteSearchClose.on('click', function(e) {
		e.preventDefault();

		$siteSearchBar.find('input').animate({right:'-100%'}, 300, function() {
			$siteSearchBar.hide();
		});

		$siteSearchOpen.fadeIn(300);
		$siteSearchClose.fadeOut(300);
	});
	//  ---- *end* GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON *end* -----  //




});
//# sourceMappingURL=header.js.map
