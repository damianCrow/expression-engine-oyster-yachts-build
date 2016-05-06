'use strict';

define(['jquery', 'jqueryValidation'], function ($) {
	function validateForm() {

		$('.address-fill-out').on('click', '.add-address-line', function () {
			var addressLineLength = $('.address-line').length;
			var newAddressLineNum = addressLineLength + 1;
			if (addressLineLength < 5) {
				var newClone = $('.last-address-line').clone();
				newClone.insertAfter('.last-address-line');
				if (addressLineLength > 3) {
					$('.last-address-line button').css('display', 'none');
				} else {
					// For whatever reason, the modal closes when removing the button, so hiding it instead.
					$('.last-address-line:eq(0) button').css('display', 'none');
					$('.last-address-line:eq(0)').removeClass('last-address-line');
				};

				$('.last-address-line:last label').attr({
					'for': 'addressline' + newAddressLineNum
				});

				$('.last-address-line:last input').attr({
					name: 'addressline' + newAddressLineNum,
					id: 'addressline' + newAddressLineNum,
					placeholder: 'Address Line ' + newAddressLineNum
				});
			}
		});

		$('.sign-up-modal-form').each(function (index, element) {
			$(element).validate({
				errorLabelContainer: ".sign-up-modal-form:eq(" + index + ") .form-error .error-messages",
				showErrors: function showErrors(errorMap, errorList) {
					if (this.numberOfInvalids() == 0) {
						$(".sign-up-modal-form:eq(" + index + ") .form-error").removeClass('visible');
						$(".sign-up-modal-form:eq(" + index + ") li").removeClass('error');

						// $(errorList).each(function(index, element) {
						// 	$(element.element).parent('li').addClass('error');
						// })
					} else {
							$(".sign-up-modal-form:eq(" + index + ") .form-error").addClass('visible');
							$(".sign-up-modal-form:eq(" + index + ") .form-error > span").html("Your form contains " + this.numberOfInvalids() + " errors, see highlighted fields below.");
							this.defaultShowErrors();

							console.log('errorList', errorList);
						}
				},
				highlight: function highlight(element, errorClass, validClass) {
					$(element).parent('li').addClass('error').removeClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").addClass(errorClass);
					console.log("highlight");
					console.log("$('#' + element.id).parent('li')", $('#' + element.id).parent('li'));
				},
				unhighlight: function unhighlight(element, errorClass, validClass) {
					$(element).parent('li').removeClass('error').addClass(validClass);
					$(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
					console.log("unhighlight");
					console.log("$('#' + element.id).parent('li')", $('#' + element.id).parent('li'));
				},
				submitHandler: function submitHandler(form) {
					// do other things for a valid form
					form.submit();
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
					}
				}
			});
		});
	};

	return validateForm;
});