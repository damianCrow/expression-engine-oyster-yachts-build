'use strict';

define(['jquery', 'jqueryValidation'], function($) {
  function validateForm() {

    $.validator.addMethod("phoneno", function(phone_number, element) {
      phone_number = phone_number.replace(/\s+/g, "");
      return this.optional(element) || phone_number.length > 9 && phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
    }, "Please specify a valid phone number");

    $('.address-fill-out').on('click', '.add-address-line', function() {
      var thisAddressContainer = $(this).parents('.address-fill-out');
      var addressLineLength = $('.address-line', thisAddressContainer).length;
      var newAddressLineNum = addressLineLength + 1;

      console.log('this', this);

      console.log('newAddressLineNum', newAddressLineNum);

      if (addressLineLength < 5) {
        var $lastLine = $('.address-line:last', thisAddressContainer);

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

        if (addressLineLength === 4) {
          $newClone.find('button').hide();
        }

        $newClone.insertAfter($lastLine);

        $lastLine.find('button').hide();
      }
    });

    // signup-home-footer is handled differently.
    $('form').not('#signup-home-footer').each(function(index, element) {

      var formId = $(element).attr('id');
      var currentForm = $(element);

      if ($('[type="submit"][data-form-id="' + formId + '"]').length) {

        $('[type="submit"][data-form-id="' + formId + '"]').on('click', function(e) {
          $(element).submit();
        });

      } else {
        $(currentForm).parent().parent().find('[type="submit"]').not("[data-form-id]").click(function(e) {
          $(element).submit();
        });
      }

      if ($('.form-error[data-form-id="' + formId + '"]').length) {
        var errorContainer = $('.form-error[data-form-id="' + formId + '"]');
      } else {
        var errorContainer = $('.form-error', element)[0];
      }

      $(element).validate({
        errorLabelContainer: $('.error-messages', errorContainer),
        ignore: ".ignore",
        showErrors: function(errorMap, errorList) {
          if (this.numberOfInvalids() == 0) {
            $(".form-error", currentForm).removeClass('visible');
            $("li", currentForm).removeClass('error');

          } else {
            $(".form-error", currentForm).addClass('visible');
            $(".form-error > span", currentForm).html("Your form contains " + this.numberOfInvalids() + " errors, see highlighted fields below.");
            this.defaultShowErrors();

            // console.log('errorList', errorList);
          }
        },
        highlight: function(element, errorClass, validClass) {
          var addErrorClassTo;

          if ($(element).parents('.required-input').length >= 1) {
            addErrorClassTo = $(element).parents('.required-input');
          } else if ($(element).parent('li').length >= 1) {
            addErrorClassTo = $(element).parent('li');
          }

          addErrorClassTo.addClass('error').removeClass(validClass);
          $(element.form).find("label[for=" + element.id + "]").not('.checkbox-label').addClass(errorClass);
        },
        unhighlight: function(element, errorClass, validClass) {
          var addErrorClassTo;

          if ($(element).parents('.required-input').length >= 1) {
            addErrorClassTo = $(element).parents('.required-input');
          } else if ($(element).parent('li').length >= 1) {
            addErrorClassTo = $(element).parent('li');
          }

          addErrorClassTo.removeClass('error').addClass(validClass);
          $(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
        },
        submitHandler: function submitHandler(form) {

          if ($(form).is('form')) {
            form.submit();
          } else {
            form = $(form).find('form');
            form.submit();
          }

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
            maxlength: 15,
            phoneno: true
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
          },
          captcha: {
            required: true
          },
          hiddenRecaptcha: {
            required: function() {
              if (grecaptcha.getResponse() == '') {
                return true;
              } else {
                return false;
              }
            }
          }
        }
      });

      $('select', element).change(function() {
        if ($(this).find(':selected').data('available-times')) {
          console.log($(this).find(':selected').data('available-times'));
          limitTimesBasedOnDay($(this).find(':selected').data('available-times'))
        }
        $(this).valid();
      });

      function limitTimesBasedOnDay(selectedTimes) {
        $('#viewing-time').empty();
        $("<option value=''>Select</option>").appendTo('#viewing-time');
        $.each(selectedTimes, function(key, element) {
          var addAsOption = "<option value='" + element + "'>" + element + "</option>"
          $(addAsOption).appendTo('#viewing-time');
        });
      }
    });
  };

  window.recaptchaCallback = function () {
    $('#hiddenRecaptcha').valid();
  };

  return validateForm;
});
