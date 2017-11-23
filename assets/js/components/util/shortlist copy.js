'use strict';

define(['jquery'], function ($) {
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
		var yachtContainer = $(item).parents('[data-yachtid]');

		if (yachtContainer.length < 1) {
			yachtContainer = $('[data-yachtid]');
		}

		var yachtId = $(yachtContainer).data('yachtid'),
		    yachtImage = $(yachtContainer).find('.yacht-listing-photo'),
		    yachtModal = $(yachtContainer).find('.yacht-list-modal').eq(0).text(),
		    yachtName = $(yachtContainer).find('.yacht-list-name').eq(0).text(),
		    yachtSection = $(yachtContainer).data('yachtsection');

		if ($(' > img', yachtImage).length > 0) {
			yachtImage = $(' > img', yachtImage)[0].currentSrc;
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

		// Remove all existing images from the shortlist form
		$('.ff_yacht_image,.ff_yacht_name').remove();
		var shortListCounter = 0;
		$.each(shortlistYachts, function (key, yachtOnList) {

			var yachtImage = yachtOnList.image.toString().replace('__blur', '');
			var backgroundImage = '<div class="yacht-listing-photo" style="background-image: url(' + yachtImage + ')"></div>';
			var removeButton = '<button class="remove-button"></button>';
			var completeYachtList = '<li class="column medium-6 small-12"><div data-yachtid=' + yachtOnList.yachtid + ' data-yachtsection=' + yachtOnList.yachtSection + ' class="yacht-list-item"><a href="">' + backgroundImage + '<div class="yacht-listing-title"><span class="yacht-list-modal">' + yachtOnList.yachtmodal + '</span><span class="yacht-list-name double-slash">' + yachtOnList.name + '</span></div></a>' + removeButton + '</div></li>';

			// Add to shortlist form
			$('<input>').attr({
			    type: 'hidden',
			    id: 'yacht_image_'+shortListCounter,
			    name: 'yacht_image_'+shortListCounter,
			    value: yachtImage,
			    class: 'ff_yacht_image'
			}).appendTo('#spec-form');

			$('<input>').attr({
			    type: 'hidden',
			    id: 'yacht_name_'+shortListCounter,
			    name: 'yacht_name_'+shortListCounter,
			    value: yachtOnList.yachtmodal + ' // ' + yachtOnList.name,
			    class: 'ff_yacht_name'
			}).appendTo('#spec-form');

			$('#shortlistModal .yachts-shortlist').append(completeYachtList);

			shortListCounter++;
		});
		enableRemoveFromList();
		window.lazySizesConfig = window.lazySizesConfig || {};
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
});