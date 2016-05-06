'use strict';

define(['jquery', 'validateForm', 'select2'], function ($, validateForm) {

	enableSelect2();
	function enableSelect2() {
		$('select').select2({
			minimumResultsForSearch: -1
		});
	};

	$('.owners-area-text').on('click', function (event) {
		event.preventDefault();
		// var popup = new Foundation.Reveal($('#popup-modal'));

		if ($('#login-page').length !== 0) {
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