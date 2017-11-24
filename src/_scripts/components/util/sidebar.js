'use strict';

define(['jquery', 'ScrollMagic', 'foundation'], function ($, ScrollMagic, Foundation) {
	// ---- GLOBAL STICKY SIDEBAR ----

	var mainNavHeight = $('#main-nav').height();
	var sideBarToStick = 'section.about-yacht .sticky-sidebar';

	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    $lastId = null,
		$locaSubNav = $("[data-local-subnav]") || {},
	    $locaSubNavHeight = $locaSubNav.outerHeight(),
	    $localSubNavItems = $locaSubNav.find(".local-subnav a"),
	    $localSubNavItemsMap = $localSubNavItems.map(function () {
			var item = $(this).attr("href").trim();

			if (item.toString().substring(0, 1) == "#") {
				if (item.length) return item;
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
			scrollTop: $($(this).attr('href')).position().top
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

});