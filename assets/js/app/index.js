'use strict';

define(['jquery', 'foundation', 'oyster_social_grid', 'owlcarousel', 'cycle'], function ($, Foundation, SocialGrid) {

	// ----- HOME PAGE SOCIAL GRID ----
	new SocialGrid();

	$('.scroll-message').click(function () {
		$('html, body').animate({
			scrollTop: $(".yacht-feature").offset().top - $('header').height()
		}, 1000);
	});

	// Set the height of the hero slides on the homepage to height of the window, and on resize.
	// - CSS VH works great, but with jump on mobile (Chrome on Android for example)
	heroSlideHeight();
	function heroSlideHeight() {
		$('.hero-home .hero-slide, .hero-home .hero-slides').height($(window).height() - $('.global-header').height());
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

	
	// Yacht slideshows
	
	var timer = 100;

	$(".yacht-feature .details").cycle({
		pager: '.nav-points',
		pagerTemplate: '<div class="nav-point"></div>',
		pagerActiveClass: 'active',
		slides: '> .slide',
		prev: '.yacht-left',
		next: '.yacht-right',
		fx: 'scrollHorz',
		speed: 500,
		log: false
	}).on('cycle-before', function() {
		// Top row image
		setTimeout(function() {
			$('.yacht-feature-large-media').cycle('next');
		}, timer);
		
		// Bottom row image 1
		setTimeout(function() {
			$('.yacht-feature-small-media:eq(0)').cycle('next');
		}, timer*2);

		// Bottom row image 2
		setTimeout(function() {
			$('.yacht-feature-small-media:eq(1)').cycle('next');
		}, timer*3);

		// Bottom row image 3
		setTimeout(function() {
			$('.yacht-feature-small-media:eq(2)').cycle('next');
		}, timer*4);
	});

	$('.yacht-feature-large-media, .yacht-feature-small-media').cycle({
		paused: true,
		speed: 500,
		fx: 'scrollHorz',
		slides: '> .slide',
		log: false
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


	$('.news-preview .cheveron-nav-left').on('click', function () {
		newsPreviewCarousel.trigger('prev.owl.carousel');
	});

	$('.news-preview .cheveron-nav-right').on('click', function () {
		newsPreviewCarousel.trigger('next.owl.carousel');
	});

	// newsPreviewCarousel
});
//# sourceMappingURL=index.js.map
