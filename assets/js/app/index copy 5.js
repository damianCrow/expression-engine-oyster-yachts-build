'use strict';

define(['jquery', 'foundation', 'social_grid', 'breakpoints', 'owlcarousel', 'cycle'], function ($, Foundation, SocialGrid, BreakPoints) {

	// ----- HOME PAGE SOCIAL GRID ----
	new SocialGrid();

	$('.scroll-message').click(function () {
		$('html, body').animate({
			scrollTop: $(".yacht-feature").offset().top - $('.global-header').height()
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
	var resizeTimer;

	$(window).on('resize', function(e) {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			// Run code here, resizing has "stopped"
			heroSlideHeight();
			cycleFeaturedYachtSlider(BreakPoints.atLeast("large"));
		}, 250);
	});
	// Homepage cycle / sliders

	var prevActiveClasses;
	$('.hero-slides').on('initialized.owl.carousel', function (e) {
		setTimeout(function(){
			// Apply event before the carousel is initialized and add the zooming animation class to all.
			$('.hero-slide').find('.hero-image-container').addClass('zooming-in');
		}, 850);
	}).owlCarousel({
		loop: true,
		autoplay: true,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		margin: 0,
		autoHeight: false,
		items: 1,
		dotsContainer: '.hero-controls',
		dotClass: 'nav-point',
		animateOut: 'fadeOut',
		animateIn: 'fadeIn'

	}).on('translate.owl.carousel', function (e, info) {
		// On each change make sure every carousel item is animating (with this class).
		setTimeout(function(){
				$('.hero-image-container').not('.hero-slides .slide-' + ( (e.page.index) + 1)).removeClass('zooming-in');
			}, 850);

			$('.hero-slides .slide-' + ( (e.page.index) + 1)).addClass('zooming-in');
	});
$('.hero-controls .nav-point').click(function() {
// With optional speed parameter
// Parameters has to be in square bracket '[]'
	$('.hero-slides').trigger('play.owl.autoplay', [5000]);
})
	

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

	Modernizr.on('videoautoplay', function( result ) {
		if (result) {
			cycleFeaturedYachtSlider(BreakPoints.atLeast("large"));
		}else{
			console.log('Modernizr failed to run autoplay test');
		}
	})

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

	function cycleFeaturedYachtSlider(secondarySlider) {

		if ($('.landing-yacht-feature').length > 0) {
			var firstSliders = function firstSliders() {

				$('.yacht-feature .details').data('cycle-pause-on-hover', '.yacht-feature').data('cycle-initialized', true);

				var timer = 100;
				var slideTimeout = 6500;

				$('.yacht-feature-large-media, .yacht-feature-small-media').on('cycle-initialized', function () {
					// console.log("$('.yacht-feature-large-media .cycle-slide-active').find('img')", $('.yacht-feature-large-media .cycle-slide-active').find('img'));

					$('.yacht-feature-large-media, .yacht-feature-small-media').data('cycle-initialized', true);

					var lgActiveSlideImg = $('.yacht-feature-large-media .cycle-slide-active').find('img');
					var smActiveSlideImg = $('.yacht-feature-small-media .cycle-slide-active').find('img');

					// unveil the first slides
					if (lgActiveSlideImg.length > 0) {
						lazySizes.loader.unveil(lgActiveSlideImg[0]);
					}

					if (BreakPoints.atLeast("large")) {
						playFirstVideos();
					}

					function playFirstVideos() {

						var firstLargeVideo = $('.yacht-feature-large-media .slide').eq(0).find('video');
						var firstSmallVideo = $('.yacht-feature-small-media .slide').eq(0).find('video');

						$('.yacht-feature-small-media').each(function (index, element) {
							if ($('.slide', element).eq(0).find('video').length > 0) {
								// console.log('slide element', $(element).eq(0).find('video'));
								$(element).eq(0).find('video').get(0).play();
							}
						});

						if (firstLargeVideo.length > 0) {
							$(firstLargeVideo).each(function (index, element) {
								$(element).get(0).play();
							});
						}

						if (smActiveSlideImg.length > 0) {
							// lazySizes.loader.unveil(smActiveSlideImg[0]);
							smActiveSlideImg.each(function (index, element) {
								// console.log(element);
								lazySizes.loader.unveil(smActiveSlideImg[index]);
							});
						}
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
					swipe: true,
					speed: 500,
					log: false,
					// hoverPaused: true,
					timeout: slideTimeout
				}).on('cycle-before', function (event, optionHash) {

					var nextLargeImg = $('.yacht-feature-large-media .cycle-slide-active').next().find('img'),
					    nextLargeVideo = $('.yacht-feature-large-media .slide').eq(optionHash.slideNum).find('video');

					Modernizr.on('videoautoplay', function (result) {

						if (nextLargeVideo.length > 0) {
							$('.yacht-feature-large-media').find('video').not(nextLargeVideo).each(function (index, element) {
								var videoFound = element;
								// $(this).get(0).play();
								$(videoFound).get(0).pause();
							});
							$(nextLargeVideo).get(0).play();
						}
					});

					if (nextLargeImg.length > 0) {
						lazySizes.loader.unveil(nextLargeImg[0]);
					}

					// Top row image
					setTimeout(function () {
						$('.yacht-feature-large-media').cycle('goto', optionHash.slideNum - 1);
						// $('.yacht-feature-large-media').cycle('next');
						// console.log($('.yacht-feature-large-media').cycle('next'));
						// console.log('optionHash', optionHash);		
					}, timer);
				});

				$('.yacht-feature-large-media').cycle(secondarySliders);
			};

			var secondarySliders = {
				paused: true,
				speed: 500,
				fx: 'scrollHorz',
				swipe: true,
				slides: '> .slide',
				log: false
			},
			    timer = 100,
			    slideTimeout = 6500;

			if (!$(".yacht-feature .details").data('cycle-initialized')) {
				firstSliders();
			}

			if (secondarySlider == true) {
				// If it doesn't exist, create it

				var smallerSliders = function smallerSliders() {

					$(".yacht-feature .details").on('cycle-initialized', function () {});

					$(".yacht-feature .details").on('cycle-before', function (event, optionHash) {

						var nextSmallVideo = [],
						    nextSmallImg = $('.yacht-feature-small-media .cycle-slide-active').next().find('img');

						$('.yacht-feature-small-media').find('video').each(function (index, element) {
							var videoFound = element;
							// $(this).get(0).play();
							$(videoFound).get(0).pause();
						});

						$('.yacht-feature-small-media').each(function (index, element) {
							if ($('.slide', element).eq(optionHash.slideNum).find('video').length > 0) {
								// console.log('slide element', $(element).eq(0).find('video'));
								$('.slide', element).eq(optionHash.slideNum).find('video').each(function (index2, videoToPlay) {
									$(videoToPlay).get(0).play();
								});
							}
						});	

						// An each loop because there are multiple small image carousels.
						if (nextSmallImg.length > 0) {
							nextSmallImg.each(function (index, element) {
								lazySizes.loader.unveil(nextSmallImg[index]);
							});
						}

						// Bottom row image 1
						setTimeout(function () {
							$('.yacht-feature-small-media:eq(0)').cycle('goto', optionHash.slideNum - 1);
							// $('.yacht-feature-small-media:eq(0)').cycle('next');
						}, timer * 2);

						// Bottom row image 2
						setTimeout(function () {
							$('.yacht-feature-small-media:eq(1)').cycle('goto', optionHash.slideNum - 1);
							// $('.yacht-feature-small-media:eq(1)').cycle('next');
						}, timer * 3);

						// Bottom row image 3
						setTimeout(function () {
							$('.yacht-feature-small-media:eq(2)').cycle('goto', optionHash.slideNum - 1);
							// $('.yacht-feature-small-media:eq(2)').cycle('next');
						}, timer * 4);
					});

					$('.yacht-feature-small-media').cycle(secondarySliders);
				};

				smallerSliders();
			} else if ($(".yacht-feature-small-media").data('cycle-initialized')) {
				//If it exists, destory it.
				$('.yacht-feature-small-media').cycle('destroy');
			}

			// Autoplay all videos in carousels, else delete it.
			$('.yacht-feature').find('video').each(function () {
				var videoFound = this;

				Modernizr.on('videoautoplay', function (result) {
					if (result) {
						// console.log('removing image from video slide?');
						$(videoFound).siblings('img, .lazyload, div').css('display', 'none');
						$(videoFound).css('display', 'block');
					} else {
						$(videoFound).siblings('img, .lazyload, div').css('display', 'block');
						$(videoFound).css('display', 'none');
					}
				});
			});

			// Make sure the other (not details slider) sliders sync when they are swiped
			$('.yacht-feature-large-media, .yacht-feature-small-media').on('cycle-before', function(event, optionHash){
				$('.yacht-feature .details').cycle('goto', optionHash.slideNum - 1);
			});


		}
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
