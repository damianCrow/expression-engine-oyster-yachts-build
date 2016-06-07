'use strict';

define(['jquery', 'cycle', 'salvattore', 'lightgallery', 'lightgalleryThumbs', 'header', 'footer', 'map', 'sidebar', 'select2'], function ($) {

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


	//  ---- IMAGER.JS (RETINA IMAGES / LAZY LOADING / IMAGE LOADING) -----  //
	document.addEventListener('lazybeforeunveil', function (e) {
		var bg = e.target.getAttribute('data-bg');
		if (bg) {
			e.target.style.backgroundImage = 'url(' + bg + ')';
		}
	});
	//  ---- *end* IMAGER.JS (RETINA IMAGES / LAZY LOADING / IMAGE LOADING)  *end* -----  //

	//  ---- GLOBAL TESTIMONIALS SLIDESHOW CYCLE -----  //
	$('.quote-testimonials').cycle({
		autoHeight: 'calc',
		pager: '.nav-points',
		pagerActiveClass: 'active',
		pagerTemplate: '<div class="nav-point"></div>',
		slides: '> blockquote',
		log: false,
		timeout: 4000
	});
	//  ---- *end* GLOBAL TESTIMONIALS SLIDESHOW CYCLE *end* -----  //

	//  ---- SHARE BUTTON -----  //

	$('.share .share-icon').on('click', function () {
		var shareListBtn = this;
		var shareListChosen = $(this).next('.share-list');
		if ($(shareListChosen).hasClass('share-list-visible')) {
			$(shareListChosen).removeClass('share-list-visible');
			setTimeout(function () {
				$(shareListChosen).addClass('hide');
				// Remove the tooltip while the share-list is open.
				$(shareListBtn).addClass('tooltip-left share-icon-tooltip');
			}, 250);
		} else {
			$(shareListBtn).removeClass('tooltip-left share-icon-tooltip');
			$(shareListChosen).removeClass('hide');
			$(shareListChosen).addClass('share-list-visible');
		}
	});

	//  ---- *end* SHARE BUTTON *end* -----  //


	//  ---- VIEW GALLERY (lightgallery) POP UP -----  //
	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container',
		log: false
	});


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


	enableSelect2();
	function enableSelect2() {
		$('select').select2({
			minimumResultsForSearch: -1
		});
	};


	//  ---- SLIDING TABS -----  //
	checkForTabs();
	function checkForTabs() {
		if ($('.sliding-tabs').length >= 1) {

			slidingTabs();
			$('.sliding-tabs').each(function (index, element) {
				slidingTabs(element);
			});
		}
	}

	function slidingTabs(tabSet) {

		var tabs = $(tabSet).find('li'),
		    numOfTabs = $(tabs).length,
		    tabWidth = 100 / numOfTabs + '%',
		    positionUnderTab,
		    firstPositionUnderTab,
		    tabContainer = $(tabSet).find('.tab-slider-container'),
		    tabSlider = $(tabSet).find('.tab-slider');

		$(tabSlider).css({ width: '' });
		$(tabContainer).css({ width: '' });
		$(tabSet).find('ul').css({ width: '' });

		$(tabSlider).css({ width: tabWidth });
		$(tabContainer).css({ width: $(tabs).width() * $(tabs).length });
		$(tabSet).find('ul').css({ width: $(tabs).width() * $(tabs).length });

		$(tabs).each(function (index, element) {
			positionUnderTab = 100 * index + '%';
			$(element).data('transform-pos', positionUnderTab);
			if ($(element).hasClass('is-active')) {
				if (index !== 0) {
					firstPositionUnderTab = positionUnderTab;
				} else {
					firstPositionUnderTab = 0 + '%';
				}
			}
		});

		$(tabSlider).css({ transform: 'translateX(' + firstPositionUnderTab + ')' });

		$(tabs).on('click', function () {
			var moveHere = $(this).data('transform-pos');
			$(tabSlider).css({ transform: 'translateX(' + moveHere + ')' });
			$(this).data('transform-pos');
		});
	}
});
