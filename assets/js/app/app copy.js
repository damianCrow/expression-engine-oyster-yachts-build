'use strict';

define(['jquery', 'ScrollMagic', 'foundation', 'cycle', 'salvattore', 'lightgallery', 'lightgalleryThumbs', 'header', 'footer', 'map', 'select2', 'lazySizes'], function ($, ScrollMagic, Foundation) {

	//  ---- BACK BUTTON ON HERO BANNERS FUNCTIONALITY -----  //
	$('#page-back-button').on('click', function (evt) {
		evt.preventDefault();
		history.back(1);
	});
	//  ---- *end* BACK BUTTON ON HERO BANNERS FUNCTIONALITY *end* -----  //

	//  ---- LOCAL SUB-NAVIGATION SCROLL ON CLICK FUNCTIONALITY -----  //
	// Yacht nav - scroll to section
	// $('#yacht-nav').on('click', '.scroll', function (e) {
	// 	e.preventDefault();

	// 	$('html, body').animate({
	// 		scrollTop: $($(this).attr('href')).position().top
	// 	}, 700);
	// });
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

	var shareListBtn, shareListChosen, shareContainer;

	$('.share .share-icon').on('click', function () {
		shareListBtn = this;
		shareContainer = $(this).parent();
		shareListChosen = $(this).next('.share-list');
		if ($(shareListChosen).hasClass('share-list-visible')) {
			closeShareLists();
		} else {
			openShareList();
		}
	});

	$(document).on('click', function (event) {
		if (!$(event.target).closest('.share').length) {
			closeShareLists();
		}
	});

	function closeShareLists() {
		$('.share-list').removeClass('share-list-visible');
		setTimeout(function () {
			$('.share-list').addClass('hide');
			// Remove the tooltip while the share-list is open.
			$(shareListBtn).addClass('tooltip-left share-icon-tooltip');
		}, 250);
	};

	function openShareList() {
		// closeShareLists();
		$(shareListBtn).removeClass('tooltip-left share-icon-tooltip');
		$(shareListChosen).removeClass('hide');
		$(shareListChosen).addClass('share-list-visible');
	}

	//  ---- *end* SHARE BUTTON *end* -----  //


	//  ---- VIEW GALLERY (lightgallery) POP UP -----  //
if($('#layout-slider .cycle-slide').length > 1){
		var firstImage = $("#layout-slider .cycle-slide img:first");
		firstImage.on('load', function() {
		}).each(function() {
			// console.log('each fired');
			// console.log('each this: ', this.complete);
			if(this.complete){
				$(this).load();
				$('#layout-slider').cycle({
					slides: '> div',
					paused: true,
					pager: '.slider-pager',
					pagerTemplate: '',
					autoHeight: 'container',
					log: false
				});	
				$('#layout-slider').css('height', $(this).height());
			} 
		});
		firstImage.attr("src", firstImage.attr("src") + '?_=' + (new Date().getTime()));
		firstImage.load();
	}



	enableSelect2();
	function enableSelect2() {
		$('select').select2({
			minimumResultsForSearch: -1
		});
	};


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



	$('.slider-pager a').on('click', function(){
		var jumpToHash = $(this).attr('href');
		$(jumpToHash)[0].scrollIntoView();
	});


	var mainNavHeight = $('#main-nav').height();
	var sideBarToStick = 'section.about-yacht .sticky-sidebar';

	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    $lastId = null,
		$locaSubNav = $("[data-local-subnav]") || {},
	    $locaSubNavHeight = $locaSubNav.outerHeight(),
	    $localSubNavItems = $locaSubNav.find(".local-subnav a"),
	    $localSubNavItemsMap = $localSubNavItems.map(function () {
			if ($(this).attr("href")) {
				var item = $(this).attr("href").trim();

				if (item.toString().substring(0, 1) == "#") {
					if (item.length) return item;
				}
			}
		});

	sideBarStick();

	var resizeTimer;

	$(window).on('resize', function (e) {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			// Run code here, resizing has "stopped"
			sideBarStick();
			checkForTabs();
			enableSelect2();
			stackedBlocks();
			Foundation.Equalizer;
			yachtHeroSlideHeight();
		}, 250);
	});

	function sideBarStick() {
		if ($(sideBarToStick).length > 0) {
			var sideBarLocalPos = $(sideBarToStick).position().top;
			var sideBarHeight = $(sideBarToStick).height();
			$(window).on("scroll", function (e) {

				if ($(window).scrollTop() > $elem.offset().top + 15 && Foundation.MediaQuery.atLeast("large")) {
					snapBar('stick');
				} else {
					snapBar('unstick');
				}

				if ($(window).scrollTop() > $('.about-yacht').offset().top + $('.about-yacht').height() - (sideBarHeight + 55) && Foundation.MediaQuery.atLeast("large")) {

					if ($('.overview-download').data('closed-once') !== true) {
						$('.overview-download').addClass('overview-contents-hidden');
					}
				} else {
					$('.overview-download').removeClass('overview-contents-hidden');
				}
			});
		}
	}

	function snapBar(action) {
		if ($(sideBarToStick).hasClass('overview-stuck') && action == 'unstick') {
			$(sideBarToStick).css({ position: '', top: '', width: '' });
			$(sideBarToStick).removeClass('overview-stuck');
		} else if (action == 'stick' && !$(sideBarToStick).hasClass('overview-stuck')) {
			var fixedDistance = $(sideBarToStick).offset().top - $(window).scrollTop();
			var widthSide = $(sideBarToStick)[0].getBoundingClientRect().width;
			$(sideBarToStick).css({ position: 'fixed', top: '55px', width: widthSide });
			$(sideBarToStick).addClass('overview-stuck');
		}
	}

	$('.aside-header').on('click', function () {

		if ($('.overview-stuck').hasClass('overview-contents-hidden')) {
			$('.overview-download').data('closed-once', true);
			$('.overview-stuck').removeClass('overview-contents-hidden');
		} else {
			$('.overview-download').data('closed-once', false);
			$('.overview-stuck').addClass('overview-contents-hidden');
		}
	});

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
			scrollTop: ($($(this).attr('href')).position().top) -100
		}, 700);
	});

	// ---- *end* GLOBAL STICKY SIDEBAR *end* ----

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

	stackedBlocks();

	function stackedBlocks() {

		var stackedBlocks = '.heritage-blocks .stacked-blocks';
		

		if($(stackedBlocks).length > 0){
			if(Foundation.MediaQuery.atLeast("large")) {
				$(stackedBlocks + ' .stacked-block:nth-child(3n - 1)').addClass('container-col2');
				$(stackedBlocks + ' .stacked-block:nth-child(3n)').addClass('container-col3');

				$('.container-col2').appendTo(stackedBlocks).removeClass('container-col2');
				$('.container-col3').appendTo(stackedBlocks).removeClass('container-col3');
			}else if(Foundation.MediaQuery.current == 'medium') {
				$(stackedBlocks + ' .stacked-block:nth-child(even)').addClass('container-col2');
				// $(stackedBlocks + ' .stacked-block:nth-child(3n)').addClass('container-col3');

				$('.container-col2').appendTo(stackedBlocks).removeClass('container-col2');
				// $('.container-col3').appendTo(stackedBlocks).removeClass('container-col3');
			}

			$('.heritage-blocks .stacked-blocks').addClass('active');
		}
	}


	// --- Media Centre Gallery Select --- //

	$('.select-gallery-wrapper select').each(function (index, element) {
		$(element).on('change', function () {
			$(element).siblings('a').attr('href', this.value);
		});
	});

	// ---- *end* Media Centre Gallery Select *end* ----
	// 
	// ---- *end* salvattore.JS CONFIGUARATION *end* ----

	//
	yachtHeroSlideHeight();
	function yachtHeroSlideHeight() {
		$('.hero.full-screen').height($(window).height() - $('.global-header').height());
	}
	//

});
