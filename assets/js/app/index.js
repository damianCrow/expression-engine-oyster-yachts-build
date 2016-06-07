'use strict';

define(['jquery', 'foundation', 'social_grid', 'owlcarousel', 'cycle'], function ($, Foundation, SocialGrid) {

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

	/*$(window).resize(function () {
		heroSlideHeight();
	});*/

	// Homepage cycle / sliders

	var prevActiveClasses;
	$('.hero-slides').on('initialized.owl.carousel', function (e) {
		// Apply event before the carousel is initialized and add the zooming animation class to all.
		$('.hero-slide').find('.hero-image').addClass('zooming-in');
	}).owlCarousel({
		loop: true,
		autoplay: true,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		margin: 0,
		autoHeight: true,
		items: 1,
		dotsContainer: '.hero-controls',
		dotClass: 'nav-point',
		animateOut: 'fadeOut',
		animateIn: 'fadeIn'

	}).on('translate.owl.carousel', function (e, info) {
		// On each change make sure every carousel item is animating (with this class).
		$('.hero-slide').find('.hero-image').addClass('zooming-in');

		// Remove the class from the last active item (to allow the animation to reset next time round)
		$(prevActiveClasses).removeClass('zooming-in');

		// We need to pull each slide's unique class number (from slide-[i]), as Owl duplicates slides to allow for a smooth loop.
		var classesOfActive = $('.hero-slides .active .hero-image').attr('class').match(/slide-(\d+)/)[1];

		// Use the slides number to select it and it's dublicates.
		prevActiveClasses = $('.hero-slides .slide-' + classesOfActive);
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

	if ($('.landing-yacht-feature').length > 0) {
		// gsapFeaturedYachtSlider();
		cycleFeaturedYachtSlider();
	}

	function gsapFeaturedYachtSlider() {

		var smallSlides = $(".yacht-feature-small-media .small-slides");
		var detailsSlides = $(".details");

		var activeSlide = 0;
		var totalSlides = $('.slide', detailsSlides).length;

		var detailsSlideWidth = $('.slide', detailsSlides)[0].getBoundingClientRect().width;
		var smallSlideWidth = $(".slide", smallSlides)[0].getBoundingClientRect().width;

		var yachtSlider = new TimelineMax({ onComplete: nextSlideOrNo, autoRemoveChildren: false });

		var autoplayTimeDelay = 2000;
		var autoplayTimeout;
		var sliderStarted = false;
		var firstSlide = true;

		var stopAfterActiveTrans = false;

		addSliderDots();
		nextSlideOrNo();

		function addSliderDots() {
			$('.slide', detailsSlides).each(function (index, element) {
				$('.yacht-feature-nav-points .nav-points').append('<div class="nav-point"></div>');

				$(this).data('slide-number', index);
			});
			$('.nav-points .nav-point:eq(0)').addClass('active');
			$('.yacht-feature-nav-points .nav-points .nav-point').on('click', function () {
				jumpSlides($(this).index());
			});

			// $('.yacht-feature-nav-points .nav-points .nav-point:eq(0)').addClass('active');
		}

		function nextSlide() {

			var lgSlidesXVal = 0;
			var smSlidesXVal = 0;

			if (sliderStarted == true && activeSlide > 1 && firstSlide == false) {
				lgSlidesXVal = $('.details-slides')[0]._gsTransform.x;
				smSlidesXVal = smallSlides[0]._gsTransform.x;
			}

			var largeMoveTo = 0 - detailsSlideWidth * activeSlide;
			var smallMoveTo = 0 - smallSlideWidth * activeSlide;

			$('.nav-points .nav-point').removeClass('active');

			if (activeSlide < totalSlides) {
				console.log('activeSlide', activeSlide);
				// $('.nav-points .nav-point').removeClass('active');
				$('.nav-points .nav-point:eq(' + activeSlide + ')').addClass('active');
				firstSlide = false;
			} else {
				$('.nav-points .nav-point:eq(0)').addClass('active');
				activeSlide = 0;
				largeMoveTo = 0;
				smallMoveTo = 0;
				firstSlide = true;
			}

			yachtSlider.add('details-slides-large-media');
			yachtSlider.staggerTo($(".details-slides, .yacht-feature-large-media-slides"), 2, { x: largeMoveTo, ease: Expo.easeInOut }, 0.25);
			yachtSlider.add('yacht-feature-small-media-slides', '-=2');
			yachtSlider.staggerTo(smallSlides, 2, { x: smallMoveTo, ease: Expo.easeInOut }, 0.25, 'yacht-feature-small-media-slides');

			activeSlide++;
			sliderStarted = true;
		}

		function jumpSlides(clickedSlideIndex) {
			// yachtSlider.pause(); //Go back to the start (true is to suppress events)
			clearTimeout(autoplayTimeout);
			stopAfterActiveTrans = true;
		}



		function nextSlideOrNo() {
			// This just checks if a slide dot was clicked during an active animation
			if (!stopAfterActiveTrans) {
				// This timeout will need to be cleared.
				autoplayTimeout = setTimeout(function () {
					nextSlide();
				}, autoplayTimeDelay);
			}
		}
	}

	function cycleFeaturedYachtSlider() {
		$('.landing-yacht-feature').removeClass('gsap-slider');

		var timer = 100;

		$('.yacht-feature-large-media, .yacht-feature-small-media').on('cycle-initialized', function () {
			
			var lgActiveSlideImg = $('.yacht-feature-large-media .cycle-slide-active').find('img');
			var smActiveSlideImg = $('.yacht-feature-small-media .cycle-slide-active').find('img');

			// unveil the first slides
			if (lgActiveSlideImg.length > 0) {
				lazySizes.loader.unveil(lgActiveSlideImg[0]);
			}

			if (smActiveSlideImg.length > 0) {
				// lazySizes.loader.unveil(smActiveSlideImg[0]);
				smActiveSlideImg.each(function (index, element) {
					// console.log(element);
					lazySizes.loader.unveil(smActiveSlideImg[index]);
				});
			}
		});

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
		}).on('cycle-before', function (event, optionHash) {

			var nextLargeImg = $('.yacht-feature-large-media .cycle-slide-active').next().find('img'),
			    nextSmallImg = $('.yacht-feature-small-media .cycle-slide-active').next().find('img');

			if (nextLargeImg.length > 0) {
				lazySizes.loader.unveil(nextLargeImg[0]);
			}

			if (nextSmallImg.length > 0) {
				nextSmallImg.each(function (index, element) {
					lazySizes.loader.unveil(nextSmallImg[index]);
				});
			}

			// Top row image
			setTimeout(function () {
				$('.yacht-feature-large-media').cycle('next');
				// console.log($('.yacht-feature-large-media').cycle('next'));
				// console.log('optionHash', optionHash);		
			}, timer);

			// Bottom row image 1
			setTimeout(function () {
				$('.yacht-feature-small-media:eq(0)').cycle('next');
			}, timer * 2);

			// Bottom row image 2
			setTimeout(function () {
				$('.yacht-feature-small-media:eq(1)').cycle('next');
			}, timer * 3);

			// Bottom row image 3
			setTimeout(function () {
				$('.yacht-feature-small-media:eq(2)').cycle('next');
			}, timer * 4);
		});

		$('.yacht-feature-large-media, .yacht-feature-small-media').cycle({
			paused: true,
			speed: 500,
			fx: 'scrollHorz',
			slides: '> .slide',
			log: false
		});

		// Autoplay all videos in carousels, else delete it.
		$('.yacht-feature').find('video').each(function () {
			var videoFound = this;
			// $(this).get(0).play();

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
	}


	$('.news-preview .cheveron-nav-left').on('click', function () {
		newsPreviewCarousel.trigger('prev.owl.carousel');
	});

	$('.news-preview .cheveron-nav-right').on('click', function () {
		newsPreviewCarousel.trigger('next.owl.carousel');
	});

	// newsPreviewCarousel
});
//# sourceMappingURL=index.js.map
