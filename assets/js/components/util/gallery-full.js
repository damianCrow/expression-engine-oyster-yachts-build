'use strict';

define(['jquery', 'lightgallery', 'lightgalleryThumbs', 'lightgalleryHash'], function ($) {
	$(function() {

		$('.gallery-content').each(function(i, el) {
			$(el).lightGallery({
				thumbnail: true,
				thumbContHeight: 136,
				thumbWidth: 197,
				thumbMargin: 14,
				toogleThumb: false,
				showThumbByDefault: false,
				closable: false,
				backdropDuration: 0,
				startOnClick: false,
				galleryId: (i+1)
			});
		});

		if (window.location.hash.indexOf('lg=') < 0) {
			$('.gallery-content a:first').trigger('click');
		}
	});
});