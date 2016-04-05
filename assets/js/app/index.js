'use strict';

define(['jquery', 'owlcarousel', 'foundation', 'ScrollMagic'], function ($) {

	$('.scroll-message').click(function () {
		$('html, body').animate({
			scrollTop: $(".yacht-feature").offset().top - $('header').height()
		}, 1000);
	});

	// Set the height of the hero slides on the homepage to height of the window, and on resize.
	// - CSS VH works great, but with jump on mobile (Chrome on Android for example)
	heroSlideHeight();
	function heroSlideHeight() {
		$('.hero-home .hero-slide').height($(window).height() - $('.global-header').height());
	}

	$(window).resize(function () {
		heroSlideHeight();
	});

	// Homepage cycle / sliders

	var prevActiveClasses;
	$('.hero-slides').on('initialized.owl.carousel', function (e) {
		// Apply event before the carousel is initialized and add the zooming animation class to all.
		$('.owl-item').find('.hero-image').addClass('zooming-in');
	}).owlCarousel({
		loop: true,
		autoplay: true,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		margin: 0,
		autoHeight: true,
		items: 1,
		dotsContainer: '.hero-controls',
		animateOut: 'fadeOut',
		animateIn: 'fadeIn'

	}).on('translate.owl.carousel', function (e, info) {
		// On each change make sure every carousel item is animating (with this class).
		$('.owl-item').find('.hero-image').addClass('zooming-in');

		// Remove the class from the last active item (to allow the animation to reset next time round)
		$(prevActiveClasses).removeClass('zooming-in');

		// We need to pull each slide's unique class number (from slide-[i]), as Owl duplicates slides to allow for a smooth loop.
		var classesOfActive = $('.hero-slides .active .hero-image').attr('class').match(/slide-(\d+)/)[1];

		// Use the slides number to select it and it's dublicates.
		prevActiveClasses = $('.hero-slides .slide-' + classesOfActive);
	});

	// Featured yachts sliding boxes
	var $yachtDet = $(".yacht-feature .details"),
	    $mediaCaro = $(".yacht-feature-large-media, .yacht-feature-small-media"),
	    flag = false,
	    duration = 300;

	// Speed is controlled within CSS (.owl-item)
	$yachtDet.owlCarousel({
		loop: true,
		dots: true,
		dotsContainer: '.yacht-feature-nav-points .nav-points',
		autoplay: true,
		autoplayTimeout: 10000,
		autoplayHoverPause: true,
		margin: 0,
		autoHeight: true,
		items: 1
	}).on('changed.owl.carousel', function (e) {
		// console.log('change1, flag = ', flag);
		if (!flag) {
			//console.log('change1-inside, flag = ', flag);
			flag = true;
			// console.log('yachtDet e.item.index', e.item);

			// Remove the -1 to support sync'd none looping.
			var slideIndex = e.item.index - 1;

			// Each loop
			$mediaCaro.each(function (index, element) {
				setTimeout(function () {
					$(element).trigger('to.owl.carousel', [slideIndex, duration, true]);
				}, (index + 1) * 150);
			});

			flag = false;
		}
	}).on('drag.owl.carousel, click.owl.carousel', function (e) {
		//Kill autoplay on drag.
		$yachtDet.trigger('autoplay.stop.owl');
	});

	$mediaCaro.each(function (index, element) {
		// Speed is controlled with CSS animation on (.owl-item)
		$(element).owlCarousel({
			loop: true,
			mouseDrag: false,
			touchDrag: false,
			pullDrag: false,
			freeDrag: false,
			margin: 0,
			autoHeight: true,
			items: 1,
			animateOut: 'slideOutLeft',
			animateIn: 'slideInRight'
		}).on('changed.owl.carousel', function (e) {
			// console.log('change2, flag = ', flag);
			// console.log('sync2 e.item.index', e.item);

			if (!flag) {
				flag = true;
				var slideIndex = e.item.index;
				$yachtDet.trigger('to.owl.carousel', [slideIndex, duration, true]);

				// Sync properties for syncing control via the media slides.
				// $mediaCaro.trigger('to.owl.carousel', [slideIndex, duration, true]);

				// $mediaCaro.each(function(index, element) {
				// 	setTimeout(function(){
				// 		console.log('element', element);
				// 		$(element).trigger('to.owl.carousel', [slideIndex, duration, true]);
				// 	}, (index + 1) * 250);
				// })
				flag = false;
			}
		});
	});

	// Autoplay all videos in carousels, else delete it.
	$('.owl-carousel').find('video').each(function () {
		var videoFound = this;
		$(this).get(0).play();
		if (Foundation.MediaQuery.atLeast('medium')) {
			$(videoFound).get(0).play();
			$(videoFound).get(0).onplay = function () {
				console.log('playing video');
				$(videoFound).siblings('.slide-image').remove();
			};
		} else {
			$(videoFound).remove();
		}
	});

	$(".social-slider").each(function (index, element) {
		// console.log(element);

		var randomTime = Math.floor(Math.random() * 16000) + 10000;

		// $(element).addClass('owl-carousel');

		$(element).owlCarousel({
			loop: true,
			mouseDrag: false,
			touchDrag: false,
			pullDrag: false,
			freeDrag: false,
			nav: false,
			dots: false,
			autoplay: true,
			autoplayTimeout: randomTime,
			autoplaySpeed: randomTime,
			autoplayHoverPause: true,
			margin: 0,
			items: 1,
			animateOut: 'fadeOut',
			animateIn: 'fadeIn'
		});
	});

	var newsPreviewCarousel = $('.news-preview .owl-carousel');

	newsPreviewCarousel.owlCarousel({
		loop: true,
		autoplay: true,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		margin: 0,
		// autoWidth: true,
		items: 1,
		nav: false
	});

	// animateOut: 'fadeOut',
	// animateIn: 'fadeIn'
	$('.news-preview .cheveron-nav-left').on('click', function () {
		newsPreviewCarousel.trigger('prev.owl.carousel');
	});

	$('.news-preview .cheveron-nav-right').on('click', function () {
		newsPreviewCarousel.trigger('next.owl.carousel');
	});

	// newsPreviewCarousel
});
//# sourceMappingURL=index.js.map
