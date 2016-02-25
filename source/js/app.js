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
});