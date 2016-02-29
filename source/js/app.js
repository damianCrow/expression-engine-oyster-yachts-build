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
});