'use strict';

define(['jquery', 'lightgallery', 'lightgalleryThumbs'], function ($) {
	$(function() {
		$('.gallery-content').lightGallery({
			thumbnail: true,
			thumbContHeight: 136,
			thumbWidth: 197,
			thumbMargin: 14,
			toogleThumb: false,
			showThumbByDefault: false,
			closable: false,
			backdropDuration: 0,
			startOnClick: false
		});

		$('.gallery-content a:first').trigger('click');
	});
});