'use strict';

define(['jquery', 'ScrollMagic', 'jquerygsap', 'cycle', 'foundation', 'salvattore', 'lightgallery', 'lightgalleryThumbs', 'select2', 'jqueryValidation', 'owlcarousel', 'googleMaps', 'simpleWeather', 'weather_icons', 'oyster_header'], function ($, ScrollMagic) {

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


	enableSelect2();
	function enableSelect2() {
		$('select').select2({
			minimumResultsForSearch: -1
		});
	};

	// ---- GLOBAL STICKY SIDEBAR ----
	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    _sideBarScene = function(elem) {
			// if elem doesnt exist, this will prevent the error
			if ( _.isEmpty(elem) ) elem = { offset: $.noop, height: $.noop };
			if ( _.isEmpty(elem.offset()) ) return { addTo: $.noop, enabled: $.noop, destroy: $.noop };
			
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
		$localSubNavItems = $locaSubNav.find(".local-subnav a"),

		$localSubNavItemsMap =  $localSubNavItems.map(function(){
			var item = $( $(this).attr("href") );

			if (item.length) return item;
		});

	// Watch for breakpoint changes
	$(window).on('changed.zf.mediaquery', function(event, newSize, oldSize) {
		// newSize is the name of the now-current breakpoint, oldSize is the previous breakpoint
		if (newSize !== "small" && newSize !== "medium") {
			$sideBarScene = _sideBarScene($elem);
			$sideBarScene.addTo(controller);
		} else {
			$sideBarScene.enabled(false);
			$sideBarScene.destroy(true);
		}
	});

	if ( !_.isEmpty($elem) && Foundation.MediaQuery.atLeast("large") ) $sideBarScene.addTo(controller);

	// opens and closes the the table when snapped on the header
	$('.sticky-sidebar-header .header').on('click', function(e) {
		$(this).next().toggle(0);
	});

	// Simple Scroll spy for the Local SubNAV
	$(window).scroll(function(){
		// Get container scroll position
		var fromTop = $(this).scrollTop() + $locaSubNavHeight;

		// Get id of current scroll item
		var cur = $localSubNavItemsMap.map(function() {
			if ( $(this).offset().top < fromTop ) return this;
		});

		// Get the id of the current element
		cur = cur[ cur.length - 1 ];
		var id = cur && cur.length ? cur[0].id : "";

		if ($lastId !== id) {
			$lastId = id;

			// Set/remove active class
			$localSubNavItems
				.parent()
				.removeClass("active")
				.end()

				.filter("[href='#" + id + "']")
				.parent()
				.addClass("active");
		}
	});

	// Yacht nav - scroll to section
	$locaSubNav.on('click', '.scroll', function(e) {
		e.preventDefault();

		$('html, body').animate({
			scrollTop: $( $(this).attr('href') ).position().top
		}, 700);
	});

	// ---- *end* GLOBAL STICKY SIDEBAR *end* ----	

	//  ---- GLOBAL FOOTER SIGN UP BOX -----  //
	$('.sign-up-btn').on('click', function() {
		var btn = this;
		var nope = false;

		$(':input[required]').each(function() {
			if (!$(this).val()) {
				nope = true;

				$(this).parent('.field').addClass('error');
				$('.new-sign-up .form-error').addClass('visible');
			}else{
				$(this).parent('.field').removeClass('error');
				$('.new-sign-up .form-error').removeClass('visible');

			}
		});

		if (!validateEmail($(':input[type="email"]').val())) {
			nope = true;

			$(':input[type="email"]').parent('.field').addClass('error');
			$('.new-sign-up .form-error').addClass('visible');

		} else {
			$(':input[type="email"]').parent('.field').removeClass('error');
			$('.new-sign-up .form-error').removeClass('visible');
		};

		if (!nope) {
			$('.sign-up-btn').addClass('is-loading');
			setTimeout((function() {
				$('.sign-up').addClass('is-complete');
			}), 3000);
		}

	});

	$('#signup-home-footer').submit(function(e){
		e.preventDefault();
	});

	

	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}

	//  ---- *end* GLOBAL FOOTER SIGN UP BOX *end* -----  //

	//  ---- YACHTS REGISTER FORM -----  //

	validateForm();
	// This will also get called from ajax modals, so will need to be called again.
	function validateForm() {

		$('.address-fill-out').on('click', '.add-address-line', function () {
			console.log('clicked');
			var addressLineLength = $('.address-line').length;
			var newAddressLineNum = addressLineLength + 1;
			if (addressLineLength < 5) {
				var newClone = $('.last-address-line').clone();
				newClone.insertAfter('.last-address-line');
				if (addressLineLength > 3) {
					$('.last-address-line button').css('display', 'none');
				} else {
					// For whatever reason, the modal closes when removing the button, so hiding it instead.
					$('.last-address-line:eq(0) button').css('display', 'none');
					$('.last-address-line:eq(0)').removeClass('last-address-line');
				};

				$('.last-address-line:last label').attr({
					'for': 'addressline' + newAddressLineNum
				});

				$('.last-address-line:last input').attr({
					name: 'addressline' + newAddressLineNum,
					id: 'addressline' + newAddressLineNum,
					placeholder: 'Address Line ' + newAddressLineNum
				});
			}
		});

		$('.sign-up-modal-form').each(function (index, element) {
			$(element).validate({
				errorLabelContainer: ".sign-up-modal-form:eq(" + index + ") .form-error .error-messages",
				showErrors: function showErrors(errorMap, errorList) {
					if (this.numberOfInvalids() == 0) {
						$(".sign-up-modal-form:eq(" + index + ") .form-error").removeClass('visible');
						$(".sign-up-modal-form:eq(" + index + ") li").removeClass('error');

						// $(errorList).each(function(index, element) {
						// 	$(element.element).parent('li').addClass('error');
						// })
					} else {
							$(".sign-up-modal-form:eq(" + index + ") .form-error").addClass('visible');
							$(".sign-up-modal-form:eq(" + index + ") .form-error > span").html("Your form contains " + this.numberOfInvalids() + " errors, see highlighted fields below.");
							this.defaultShowErrors();

							console.log('errorList', errorList);
						}
				},
				highlight: function highlight(element, errorClass, validClass) {
					$(element).parent('li').addClass('error').removeClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").addClass(errorClass);
					console.log("highlight");
					console.log("$('#' + element.id).parent('li')", $('#' + element.id).parent('li'));
				},
				unhighlight: function unhighlight(element, errorClass, validClass) {
					$(element).parent('li').removeClass('error').addClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
					console.log("unhighlight");
					console.log("$('#' + element.id).parent('li')", $('#' + element.id).parent('li'));
				},
				submitHandler: function submitHandler(form) {
					// do other things for a valid form
					form.submit();
				},
				rules: {
					rules: {
						maxlength: 800
					},
					email: {
						required: true,
						email: true
					},
					tel: {
						maxlength: 15
					},
					'current-yacht-make': {
						maxlength: 300
					},
					'current-yacht-model': {
						maxlength: 300
					},
					postcode: {
						maxlength: 100
					}
				}
			});
		});
	};
	//  ---- *end* YACHTS REGISTER FORM *end* -----  //

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

	//  ---- SHORTLIST LOGIC -----  //

	var shortlistYachts = [];

	$('.add-to-shortlist').on('click', function () {

		var localStorageSl = JSON.parse(localStorage.getItem("localShortlist"));
		// Is there a local storage list?

		if (localStorageSl != null && localStorageSl.length > 0) {
			shortlistYachts = localStorageSl;
		}

		addToShortlist(this);

		if ($(shortlistYachts).length > 0) {
			localStorage.setItem("localShortlist", JSON.stringify(shortlistYachts));
		};
	});

	// Pull the info, check it and put it in the object if it's not already
	function addToShortlist(item) {
		var yachtContainer = $(item).parents('[data-yachtid]'),
		    yachtId = $(yachtContainer).data('yachtid'),
		    yachtImage = $(yachtContainer).find('.yacht-listing-photo'),
		    yachtModal = $(yachtContainer).find('.yacht-list-modal').text(),
		    yachtName = $(yachtContainer).find('.yacht-list-name').text(),
		    yachtSection = $(yachtContainer).data('yachtsection');

		if (yachtImage.length == 0) {
			yachtImage = ripBgUrl(yachtContainer);
		} else {
			yachtImage = ripBgUrl(yachtImage);
		}

		// Check if this yacht is on the shortlist already
		var shortlistCheck = $.grep(shortlistYachts, function (e) {
			return e.yachtid == yachtId;
		});

		if (shortlistCheck.length) {
			// This is already on the shortlist
		} else {
				var ripYachtDetails = new yachtDetails(yachtId, yachtImage, yachtModal, yachtName, yachtSection);
				// Push the yacht details into the shortlist array.
				shortlistYachts.push(ripYachtDetails);
				// Now push it to the dom.
			}

		// Now add the items emptied from the DOM onto the list
		displayOnShortlist();
	}

	function yachtDetails(yachtid, image, yachtmodal, name, yachtSection) {
		this.yachtid = yachtid;
		this.image = image;
		this.yachtmodal = yachtmodal;
		this.name = name;
		this.yachtSection = yachtSection;
	};

	function ripBgUrl(container) {
		var bgCss = $(container).css('background-image');
		return bgCss.replace('url(', '').replace(')', '').replace(/['"]+/g, '');
	}

	// Display the yachts saved in the shortlist array into a list in the DOM
	function displayOnShortlist() {

		// Empty what might be there
		$('#shortlistModal .yachts-shortlist').empty();

		$.each(shortlistYachts, function (key, yachtOnList) {

			var backgroundImage = '<div class="yacht-listing-photo" style="background-image: url(' + yachtOnList.image + ')"></div>';
			var removeButton = '<button class="remove-button"></button>';
			var completeYachtList = '<li class="column medium-6 small-12"><div data-yachtid=' + yachtOnList.yachtid + ' data-yachtsection=' + yachtOnList.yachtSection + ' class="yacht-list-item"><a href="">' + backgroundImage + '<div class="yacht-listing-title"><span class="yacht-list-modal">' + yachtOnList.yachtmodal + '</span><span class="yacht-list-name double-slash">' + yachtOnList.name + '</span></div></a>' + removeButton + '</div></li>';

			$('#shortlistModal .yachts-shortlist').append(completeYachtList);
		});
		enableRemoveFromList();
	}

	function enableRemoveFromList() {
		$('.yachts-shortlist').on('click', '.remove-button', function (e) {
			e.preventDefault();

			var yachtToRemove = $(this).parents('[data-yachtid]');
			var yachtToRemoveId = $(yachtToRemove).data('yachtid');

			var refinedList = $.grep(shortlistYachts, function (e) {
				return e.yachtid != yachtToRemoveId;
			});

			shortlistYachts = refinedList;

			localStorage.setItem("localShortlist", JSON.stringify(shortlistYachts));

			$(yachtToRemove).parent('.column').css('display', 'none');
			// $(yachtToRemove).parent().html('test');
		});
	}

	//  ---- *end* SHORTLIST LOGIC *end* -----  //

	//  ---- VIEW GALLERY (lightgallery) POP UP -----  //
	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container',
		log: false
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
	$('.button-view-gallery').on('click', function (e) {
		e.preventDefault();

		var gallery = $(this).attr('data-gallery');

		$('.gallery-content[data-gallery="'+gallery+'"] a:first').trigger('click');
	});
	// ---- *end* VIEW GALLERY (lightgallery) POP UP *end* ----	

	//  ---- LISTING STAGGERED FADE IN -----  //

	// This is done in CSS, this JS is only for the staggered effect delay times.
	var staggerTime = 0;

	$('.list-entrance > li').each(function (index, element) {
		staggerTime = staggerTime + 200;
		$(element).css('animation-delay', staggerTime + 'ms');
	});

	$('.list-entrance > li').addClass('list-entrance-animations');

	// ---- *end* LISTING STAGGERED FADE IN *end* ----	

	//  ---- GOOGLE MAPS EMBEDS -----  //

	var googleMapStyle = [{
		"featureType": "water",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#004363" }]
	}, {
		"featureType": "water",
		"elementType": "labels.text.fill",
		"stylers": [{ "color": "#000000" }]
	}, {
		"featureType": "landscape",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#5a7c8c" }]
	}, {
		"featureType": "landscape",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#5b7e8d" }]
	}, {
		"featureType": "poi",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#53636b" }]
	}];

	initMap();

	function initMap() {
		var customMapType = new google.maps.StyledMapType(googleMapStyle);
		var customMapTypeId = 'custom_style';

		$('.destination-map-container').each(function(index, element) {

			var locationLat = $(element).data('lat'),
			locationLng = $(element).data('lng'),
			defaultZoom = 10;

			if ($(element).data('default-zoom')){
				defaultZoom = $(this).data('default-zoom');
			}
			
			var map = new google.maps.Map(element, {
				zoom: defaultZoom,
				center: {lat: locationLat, lng: locationLng},  // British Virgin Islands
				streetViewControl: false,
				mapTypeControl: false,
				scrollwheel: false
			});

			map.mapTypes.set(customMapTypeId, customMapType);
			map.setMapTypeId(customMapTypeId);			
		});
	}

// ---- *end* GOOGLE MAPS EMBEDS *end* ----	

//  ---- simpleWeather.js CONFIG -----  //

	$('.destination-todays-temp').each(function(index, element) {
		var weatherLocaiton = $(element).data('weather-location');
		$.simpleWeather({
				zipcode: '',
				woeid: '', //2357536
				location: weatherLocaiton,
				unit: 'c',
				success: function(weather) {
					$('.destination-temp span', element).html(weather.temp);
					$('.destination-date-time time', element).html(weather.forecast[0].date);
					$('.destination-date-time time', element).data('cel', weather.temp);
					$('.destination-date-time time', element).data('fanren', weather.alt.temp);
					$('.weather-icon', element).html(weatherIconIds[weather.code]).promise().done(function(){
						$('.destination-temp').addClass('destination-temp-loaded');
					});
				},
				error: function(error) {
				}
			});
	});

	$('.temperature-setting-label').on('click', function(){
		var weatherBox = $(this).parents('.destination-todays-temp');
		if($('input', this).prop("checked")) {
			var faran = $(weatherBox).find('.destination-date-time time').data('fanren');
			$('.destination-temp span', weatherBox).html(faran);
		} else {
			var cel = $(weatherBox).find('.destination-date-time time').data('cel');			
			$('.destination-temp span', weatherBox).html(cel);

		}
	});

	$('.owners-area-text').on('click', function (event) {
		event.preventDefault();
		// var popup = new Foundation.Reveal($('#popup-modal'));

		if ($('#login-page').length !== 0) {
			console.log($('#login-page').length);
			$('#login-page').foundation('open');
		} else {
			$.ajax('/owners-area/login').done(function (resp) {
				$('body').prepend(resp);
				$('#login-page').foundation();
				enableSelect2();
				validateForm();
				// console.log($('#login-page'));

				$('#login-page').foundation('open');
				// $('#login-page').open();

				slidingTabs();

				$('#login-page .close-button, #login-page [data-close="data-close"]').on('click', function () {
					$('#login-page').foundation('close');
				});
				// modalLogin.prepend(resp)
			});
		}
	});

	//  ---- SLIDING TABS -----  //
	if ($('.sliding-tabs').length >= 1) {
		slidingTabs();
	}

	function slidingTabs() {
		var tabs = $('.sliding-tabs li'),
		    numOfTabs = $(tabs).length,
		    tabWidth = 100 / numOfTabs + 25 + '%',
		    positionUnderTab,
		    firstPositionUnderTab;

		$('.sliding-tabs .tab-slider').css({ width: tabWidth });

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

		$('.sliding-tabs .tab-slider').css({ transform: 'translateX(' + firstPositionUnderTab + ')' });

		$(tabs).on('click', function () {
			var moveHere = $(this).data('transform-pos');
			$('.sliding-tabs .tab-slider').css({ transform: 'translateX(' + moveHere + ')' });
			$(this).data('transform-pos');
		});
	}

// ---- *end* simpleWeather.js CONFIG *end* ----	

});
//# sourceMappingURL=app.js.map
