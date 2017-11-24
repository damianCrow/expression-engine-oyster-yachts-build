'use strict';

define(['jquery', 'jqueryValidation'], function ($) {
	function validateForm() {

		$('.address-fill-out').on('click', '.add-address-line', function () {
			var addressLineLength = $('.address-line').length;
			var newAddressLineNum = addressLineLength + 1;

			if (addressLineLength < 5) {
				var $lastLine = $('.address-line:last');

				var $newClone = $lastLine.clone();
				$newClone.find('label').attr({
					'for': 'member_address_line_' + newAddressLineNum
				});
				$newClone.find('input').attr({
					name: 'member_address_line_' + newAddressLineNum,
					id: 'member_address_line_' + newAddressLineNum,
					placeholder: 'Address Line ' + newAddressLineNum,
					value: ''
				});

				if (addressLineLength  === 4) {
					$newClone.find('button').hide();
				}

				$newClone.insertAfter($lastLine);

				$lastLine.find('button').hide();
			}
		});

		$('.sign-up-modal-form, .request-spec, .brokerage-modal form').each(function (index, element) {
			// $(this).parent().parent().find('[type="submit"]').click(function () {
			// 	console.log('submit btn clicked');
			// 	$(element).submit();
			// });

			// var errorContainer = $('.form-error .error-messages', element)[0];

			var formId = $(element).attr('id');
			var currentForm = $(element);

			if($('[type="submit"][data-form-id="'+formId+'"]').length){
				console.log('found submit btn with data');
				$('[type="submit"][data-form-id="'+formId+'"]').on('click', function(e) {
					// e.preventDefault()
					console.log('data jquery - submit btn clicked');
					$(element).submit();
				});
			}else{
				$(currentForm).parent().parent().find('[type="submit"]').not("[data-form-id]").click( function(e) {
					console.log('parent jquery - submit btn clicked');
					$(element).submit();
				});
			}

			if($('.form-error[data-form-id="'+formId+'"]').length) {
				var errorContainer = $('.form-error[data-form-id="'+formId+'"]');
			}else{
				var errorContainer = $('.form-error', element)[0];
			}
			
			$(element).validate({
				errorLabelContainer: $('.error-messages', errorContainer),
				showErrors: function(errorMap, errorList) {
					if(this.numberOfInvalids() == 0){
						$(".form-error", currentForm).removeClass('visible');
						$("li", currentForm).removeClass('error');

						// $(errorList).each(function(index, element) {
						// 	$(element.element).parent('li').addClass('error');
						// })

					}else{
						$(".form-error", currentForm).addClass('visible');
						$(".form-error > span", currentForm).html("Your form contains "
							+ this.numberOfInvalids()
							+ " errors, see highlighted fields below.");
						this.defaultShowErrors();


						console.log('errorList', errorList);
					}
				},
				highlight: function(element, errorClass, validClass) {
					var addErrorClassTo;
					if($(element).parents('.required-input').length >= 1) {
						addErrorClassTo = $(element).parents('.required-input');
					}else if($(element).parent('li').length >= 1){
						addErrorClassTo = $(element).parent('li');
					}
					addErrorClassTo.addClass('error').removeClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").not('.checkbox-label').addClass(errorClass);
					console.log('cahce test');
				},
				unhighlight: function(element, errorClass, validClass) {
					var addErrorClassTo;
					if($(element).parents('.required-input').length >= 1) {
						addErrorClassTo = $(element).parents('.required-input');
					}else if($(element).parent('li').length >= 1){
						addErrorClassTo = $(element).parent('li');
					}

					addErrorClassTo.removeClass('error').addClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
				},
				submitHandler: function submitHandler(form) {

					if($(form).is('form')) {
						form.submit();
					}else{
						form = $(form).find('form');
						form.submit();
					}

					// do other things for a valid form
					// form.submit();
				},
				rules: {
					rules: {
						maxlength: 800
					},
					email: {
						required: true,
						email: true
					},
					tel: {
						maxlength: 15
					},
					'current-yacht-make': {
						maxlength: 300
					},
					'current-yacht-model': {
						maxlength: 300
					},
					postcode: {
						maxlength: 100
					},
					'oyster-model[]': {
						required: true
					},
					'models_interested_in[]': {
						required: true
					}
				}
			});
			$('select', element).change(function () {
				if($(this).find(':selected').data('available-times')) {
					console.log($(this).find(':selected').data('available-times'));
					limitTimesBasedOnDay($(this).find(':selected').data('available-times'))
				}
				$(this).valid();
			});


			function limitTimesBasedOnDay(selectedTimes) {
				$('#viewing-time').empty();
				$("<option value=''>Select</option>").appendTo('#viewing-time');
				$.each(selectedTimes, function (key, element) {
					var addAsOption = "<option value='" + element + "'>" + element + "</option>"
					$(addAsOption).appendTo('#viewing-time');
				});
			}
		});
	};

	return validateForm;
});