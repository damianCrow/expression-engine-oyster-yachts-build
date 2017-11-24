'use strict';

define(['jquery'], function ($) {
	//  ---- GLOBAL FOOTER SIGN UP BOX -----  //
	$('.sign-up-btn').on('click', function() {
		var btn = this;
		var nope = false;

		$('.new-sign-up :input[required]').each(function () {
			if (!$(this).val()) {
				nope = true;
				$(this).parent('.field').addClass('error');
				$('.new-sign-up .form-error').addClass('visible');
			} else {
				$(this).parent('.field').removeClass('error');
				$('.new-sign-up .form-error').removeClass('visible');
			}
		});

		if (!validateEmail($('.new-sign-up :input[type="email"]').val())) {
			nope = true;

			$('.new-sign-up :input[type="email"]').parent('.field').addClass('error');
			$('.new-sign-up .form-error').addClass('visible');
		} else {
			$('.new-sign-up :input[type="email"]').parent('.field').removeClass('error');
			$('.new-sign-up .form-error').removeClass('visible');
		};

		if (!nope) {
			$('.sign-up-btn').addClass('is-loading');
			
			var $form = $('#signup-home-footer');

			$.ajax({
				url: '/newsletter/signup.php',
				dataType: 'json',
				type: 'POST',
				data: {
					'email': $form.find('.newsletter-email').val(),
					'fname': $form.find('.newsletter-fname').val(),
					'sname': $form.find('.newsletter-sname').val(),
					'country': $form.find('.newsletter-country').val()
				}
			}).done(function( data ) {
				if (data.status === "success") {
					$('.sign-up').addClass('is-complete');
				}
			});
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