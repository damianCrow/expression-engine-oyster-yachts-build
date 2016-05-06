'use strict';

define(['jquery'], function ($) {
	//  ---- GLOBAL FOOTER SIGN UP BOX -----  //
	$('.sign-up-btn').on('click', function() {
		var btn = this;
		var nope = false;

		$(':input[required]').each(function() {
			if (!$(this).val()) {
				nope = true;

				$(this).parent('.field').addClass('error');
				$('.new-sign-up .form-error').addClass('visible');
			}else{
				$(this).parent('.field').removeClass('error');
				$('.new-sign-up .form-error').removeClass('visible');

			}
		});

		if (!validateEmail($(':input[type="email"]').val())) {
			nope = true;

			$(':input[type="email"]').parent('.field').addClass('error');
			$('.new-sign-up .form-error').addClass('visible');

		} else {
			$(':input[type="email"]').parent('.field').removeClass('error');
			$('.new-sign-up .form-error').removeClass('visible');
		};

		if (!nope) {
			$('.sign-up-btn').addClass('is-loading');
			setTimeout((function() {
				$('.sign-up').addClass('is-complete');
			}), 3000);
		}

	});

	$('#signup-home-footer').submit(function(e){
		e.preventDefault();
	});

	

	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}
});