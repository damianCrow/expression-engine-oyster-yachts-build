$(document).foundation();

$(function() {

	// Yacht nav - scroll to section
	$('#yacht-nav').on('click', '.scroll', function(e) {
		e.preventDefault();

		$('html, body').animate({scrollTop:$($(this).attr('href')).position().top}, 700);
	});


	$('#layout-slider').cycle({
		slides: '> div',
		paused: true,
		pager: '.slider-pager',
		pagerTemplate: '',
		autoHeight: 'container'
	});

	/*$(window).scroll(function() {
		var pos = $(document).scrollTop()/2;
		$('#full-image').css({
			'-ms-transform': 'translateY('+(pos)+'px)',
			'-webkit-transform': 'translateY('+(pos)+'px)',
			'transform': 'translateY('+(pos)+'px)'
		});
	});*/

	// main nav hover
	$('#main-nav').on('mouseenter', '>li', function() {
		$(this).addClass('hover');
	}).on('mouseleave', '>li', function() {
		$(this).removeClass('hover');
	});

	/*var $interiorGallery = $("#interior-gallery");
	
	$interiorGallery.lightGallery({
		thumbnail: true,
		thumbContHeight: 136,
		thumbWidth: 197,
		thumbMargin: 14,
		toogleThumb: false,
		showThumbByDefault: false,
		closable: false,
		backdropDuration: 0
	});

	var $exteriorGallery = $("#exterior-gallery");

	$exteriorGallery.lightGallery({
		thumbnail: true,
		thumbContHeight: 136,
		thumbWidth: 197,
		thumbMargin: 14,
		toogleThumb: false,
		showThumbByDefault: false,
		closable: false,
		backdropDuration: 0
	});*/

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

	$('.gallery').on('click', function(e) {
		e.preventDefault();

		$('.gallery-content a:first').trigger('click');
	});

	/*$('.gallery-interior').on('click', function(e) {
		e.preventDefault();

		$exteriorGallery.data('lightGallery').destroy();

		console.log('close');
	});*/


	// site search open
	$('.site-search-open').on('click', function(e) {
		e.preventDefault();

		$('.search-bar').show().find('input').animate({right:0}, 300);
		$('.site-search-open').fadeOut(300);
		$('.site-search-close').fadeIn(300);
	});

	// site search close
	$('.site-search-close').on('click', function(e) {
		e.preventDefault();

		$('.search-bar').find('input').animate({right:'-100%'}, 300, function() {
			$('.search-bar').hide();
		});
		$('.site-search-open').fadeIn(300);
		$('.site-search-close').fadeOut(300);
	});


	$('#brokerage-filters').on('change', 'select', function() {
		//console.log($(this).val());

		var data = {};
		if ($('#filter-model').val() !== "") {
			data.model = $('#filter-model').val();
		}
		if ($('#filter-status').val() !== "") {
			data.status = $('#filter-status').val();
		}
		if ($('#filter-location').val() !== "") {
			data.location = $('#filter-location').val();
		}
		if ($('#filter-price').val() !== "") {
			var $selected = $('#filter-price option:selected');

			if ($selected[0].hasAttribute('data-price-min')) {
				data.price_min = $selected.attr('data-price-min');
			}

			if ($selected[0].hasAttribute('data-price-max')) {
				data.price_max = $selected.attr('data-price-max');
			}
		}

		console.log(data);

		$.ajax({
			method: 'GET',
			url: '/oyster/ajax/brokerage-filter',
			dataType: 'json',
			data: data
		}).done(function(data) {
			console.log(data);
		}).fail(function(error) {
			console.log('error', error);
		});
	});
});