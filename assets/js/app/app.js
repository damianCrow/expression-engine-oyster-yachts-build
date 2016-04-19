'use strict';

define(['jquery', 'cycle', 'ScrollMagic', 'foundation', 'salvattore', 'lightgallery', 'lightgalleryThumbs', 'select2', 'jqueryValidation', 'owlcarousel', 'oyster_header'], function ($, cycle, ScrollMagic, Foundation, Salvattore) {

	// initilise foundation
	$(document).foundation();

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
	$('#page-testimonials .testimonials').cycle({
		autoHeight: 'calc',
		pager: '> .pager',
		pagerTemplate: '<span><span></span></span>',
		slides: '> .testimonials-inner',
		timeout: 4000
	});
	//  ---- *end* GLOBAL TESTIMONIALS SLIDESHOW CYCLE *end* -----  //

	//  ---- GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON -----  //
	// site search open
	$('.site-search-open').on('click', function (e) {
		e.preventDefault();

		$('.search-bar').show().find('input').animate({ right: 0 }, 300);
		$('.site-search-open').fadeOut(300);
		$('.site-search-close').fadeIn(300);
	});

	// site search close
	$('.site-search-close').on('click', function (e) {
		e.preventDefault();

		$('.search-bar').find('input').animate({ right: '-100%' }, 300, function () {
			$('.search-bar').hide();
		});
		$('.site-search-open').fadeIn(300);
		$('.site-search-close').fadeOut(300);
	});
	//  ---- *end* GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON *end* -----  //


	$('select').select2({
		minimumResultsForSearch: -1
	});
	//  ---- *end* GLOBAL FILTER SEARCH INSIDE HERO HEADER *end* -----  //

	// ---- GLOBAL STICKY SIDEBAR ----
	var controller = new ScrollMagic.Controller(),
	    $elem = $('.about-yacht'),
	    _sideBarScene = function _sideBarScene(elem) {
		// if elem doesnt exist, this will prevent the error
		if (_.isEmpty(elem)) elem = { offset: $.noop, height: $.noop };
		if (_.isEmpty(elem.offset())) return { addTo: $.noop, enabled: $.noop, destroy: $.noop };

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
	    $localSubNavItemsMap = $localSubNavItems.map(function () {
		var item = $($(this).attr("href"));

		if (item.length) return item;
	});

	// Watch for breakpoint changes
	$(window).on('changed.zf.mediaquery', function (event, newSize, oldSize) {
		// newSize is the name of the now-current breakpoint, oldSize is the previous breakpoint
		if (newSize !== "small" && newSize !== "medium") {
			$sideBarScene = _sideBarScene($elem);
			$sideBarScene.addTo(controller);
		} else {
			$sideBarScene.enabled(false);
			$sideBarScene.destroy(true);
		}
	});

	if (!_.isEmpty($elem) && Foundation.MediaQuery.atLeast("large")) $sideBarScene.addTo(controller);

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

	//  ---- GLOBAL FOOTER SIGN UP BOX -----  //
	$('.sign-up-btn').on('click', function () {
		var btn = this;
		var nope = false;

		$(':input[required]').each(function () {
			if (!$(this).val()) {
				nope = true;
				console.log('error');
				$(this).parent('.field').addClass('error');
				$('.new-sign-up .form-error').addClass('visible');
			} else {
				$(this).parent('.field').removeClass('error');
				$('.new-sign-up .form-error').removeClass('visible');
			}
		});

		if (!validateEmail($(':input[type="email"]').val())) {
			nope = true;

			$(':input[type="email"]').parent('.field').addClass('error');
			$('.new-sign-up .form-error').addClass('visible');
		} else {
			console.log('correct email:', validateEmail($(':input[type="email"]').val()));

			$(':input[type="email"]').parent('.field').removeClass('error');
			$('.new-sign-up .form-error').removeClass('visible');
		};

		console.log('nope = ', nope);
		if (!nope) {
			console.log('should be false, nope = ', nope);
			$('.sign-up-btn').addClass('is-loading');
			setTimeout(function () {
				$('.sign-up').addClass('is-complete');
			}, 3000);
		}
	});

	$('#signup-home-footer').submit(function (e) {
		e.preventDefault();
	});

	// $('.sign-up-btn').on('click', function() {
	//   $(':input[required]').each(function() {
	//     if (!$(this).val()) {
	//       return $(this).parent('.field').addClass('error');
	//     } else {
	//       $('.sign-up-btn').addClass('is-loading');
	//       return setTimeout((function() {
	//         return $('.sign-up').addClass('is-complete');
	//       }), 3000);
	//     }
	//   });
	//   console.log('going to return false');
	//   return false;
	// });

	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}

	//  ---- *end* GLOBAL FOOTER SIGN UP BOX *end* -----  //

	//  ---- YACHTS REGISTER FORM -----  //

	$('.address-fill-out').on('click', '.add-address-line', function () {
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

	$('#spec-form').validate({
		errorLabelContainer: "#spec-form .form-error .error-messages",
		showErrors: function showErrors(errorMap, errorList) {
			if (this.numberOfInvalids() == 0) {
				$("#spec-form .form-error").removeClass('visible');
			} else {
				$("#spec-form .form-error").addClass('visible');
				$("#spec-form .form-error > span").html("Your form contains " + this.numberOfInvalids() + " errors, see highlighted fields below.");
				this.defaultShowErrors();
			}
		},
		highlight: function highlight(element, errorClass, validClass) {
			$(element).parent('li').addClass('error').removeClass(validClass);
			$(element.form).find("label[for=" + element.id + "]").addClass(errorClass);
		},
		unhighlight: function unhighlight(element, errorClass, validClass) {
			$(element).parent('li').removeClass('error').addClass(validClass);
			$(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
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
		console.log('localStorageSl', localStorageSl);
		// Is there a local storage list?

		// if (localStorageSl != null){
		if (localStorageSl != null && localStorageSl.length > 0) {
			console.log('localStorageSl is not null');
			console.log('pulling local storage list: ', localStorageSl);
			shortlistYachts = localStorageSl;
		}
		// };

		console.log("Checking new item with list");
		addToShortlist(this);

		console.log('Saving new list to localStorage');
		// if($(shortlistYachts).length > 0){
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

		console.log('yachtImage.length', yachtImage.length);

		if (yachtImage.length == 0) {
			console.log('yes hero');
			yachtImage = ripBgUrl(yachtContainer);
		} else {
			yachtImage = ripBgUrl(yachtImage);
		}

		// yachtImage = ripBgUrl($(yachtContainer).find('.yacht-listing-photo')),

		console.log('yachtName', yachtName);
		console.log('yachtModal', yachtModal);

		// Check if this yacht is on the shortlist already
		var shortlistCheck = $.grep(shortlistYachts, function (e) {
			console.log(e);
			return e.yachtid == yachtId;
		});

		if (shortlistCheck.length) {
			// This is already on the shortlist
			console.log('this item is already on the list, not adding', shortlistCheck.length);
			// console.log('shortlistCheck', shortlistCheck);
		} else {
				var ripYachtDetails = new yachtDetails(yachtId, yachtImage, yachtModal, yachtName, yachtSection);

				console.log('this item is new, add it to the list.');
				// Push the yacht details into the shortlist array.
				shortlistYachts.push(ripYachtDetails);
				// Now push it to the dom.
			}

		// Now add the items emptied from the DOM onto the list
		displayOnShortlist();

		console.log('Saved yachts', shortlistYachts);
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

		console.log('each loop of shortlistYachts');
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

			console.log('yachtToRemoveId = ', yachtToRemoveId);

			var refinedList = $.grep(shortlistYachts, function (e) {
				console.log('e', e);
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
		autoHeight: 'container'
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
	$('.gallery').on('click', function (e) {
		e.preventDefault();

		$('.gallery-content a:first').trigger('click');
	});
	// ---- *end* VIEW GALLERY (lightgallery) POP UP *end* ----	

	//  ---- salvattore.JS CONFIGUARATION -----  //
	// ---- *end* salvattore.JS CONFIGUARATION *end* ----	
});
//# sourceMappingURL=app.js.map
