'use strict';

define(['jquery', 'lightgallery', 'lightgalleryThumbs', 'lightgalleryVideo', 'lightgalleryHash'], function ($) {
	// init gallery

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
			loadVimeoThumbnail: true,
	    	vimeoThumbSize: 'thumbnail_medium',
	    	vimeoPlayerParams: {
		        byline : 0,
		        portrait : 0,
		        color : '003145'
		    },
		    videoMaxWidth: '100%',
		    galleryId: (i+1)
		});
	});

	// trigger first slide to open in gallery
	$('.btn-gallery').on('click', function (e) {
		e.preventDefault();

		var gallery = $(this).attr('data-gallery');

		$('.gallery-content[data-gallery="'+gallery+'"] a:first').trigger('click');
	});
});