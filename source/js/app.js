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

	var $grid = $('#yacht-grid');

	$('#submit').on('click', function(e) {
		e.preventDefault();

		$grid.find('li').show();

		if ($('#filter-model').val() !== "") {
			$grid.find("li[data-model!='"+$('#filter-model').val()+"']").hide();
		}
		if ($('#filter-status').val() !== "") {
			$grid.find("li[data-status!='"+$('#filter-status').val()+"']").hide();
		}
		if ($('#filter-location').val() !== "") {
			$grid.find("li[data-location!='"+$('#filter-location').val()+"']").hide();
		}
		if ($('#filter-price').val() !== "") {
			var $selected = $('#filter-price option:selected');

			if ($selected[0].hasAttribute('data-price-min')) {
				$grid.find('li').filter(function() {
				    return $(this).data('price') < $selected.attr('data-price-min');
				}).hide();
			}

			if ($selected[0].hasAttribute('data-price-max')) {
				$grid.find('li').filter(function() {
				    return $(this).data('price') > $selected.attr('data-price-max');
				}).hide();
			}
		}
	});

});

$('select').select2({
	minimumResultsForSearch: -1
});