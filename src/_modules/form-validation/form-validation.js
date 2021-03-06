import $ from 'jquery'
import 'jquery-validation'

export default class FormValidation {
  constructor() {
    this.init()
  }

  init() {
    $.validator.addMethod('phoneno', function(phone_number, element) {
      phone_number = phone_number.replace(/\s+/g, '')
      return this.optional(element) || phone_number.length > 9 && phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/)
    }, 'Please specify a valid phone number')

    $('.address-fill-out').on('click', '.add-address-line', (e) => {
      const thisAddressContainer = $(e.target).parents('.address-fill-out')
      const addressLineLength = $('.address-line', thisAddressContainer).length
      const newAddressLineNum = addressLineLength + 1

      if (addressLineLength < 5) {
        const $lastLine = $('.address-line:last', thisAddressContainer)

        const newClone = $lastLine.clone()

        newClone.find('label').attr({ for: `member_address_line_${newAddressLineNum}` })

        newClone.find('input').attr({
          name: `member_address_line_${newAddressLineNum}`,
          id: `member_address_line_${newAddressLineNum}`,
          placeholder: `Address Line ${newAddressLineNum}`,
          value: '',
        })

        if (addressLineLength === 4) {
          newClone.find('button').hide()
        }

        newClone.insertAfter($lastLine)

        $lastLine.find('button').hide()
      }
    })

    // signup-home-footer is handled differently.
    $('form').not('#signup-home-footer').each((index, element) => {
      const formId = $(element).attr('id')
      const currentForm = $(element)

      if ($(`[type="submit"][data-form-id="${formId}"]`).length) {
        $(`[type="submit"][data-form-id="${formId}"]`).on('click', () => {
          $(element).submit()
        })
      } else {
        $(currentForm).parent().parent().find('[type="submit"]')
          .not('[data-form-id]')
          .click(() => {
            $(element).submit()
          })
      }

      let errorContainer
      if ($(`.form-error[data-form-id="${formId}"]`).length) {
        errorContainer = $(`.form-error[data-form-id="${formId}"]`)
      } else {
        errorContainer = $('.form-error', element)[0]
      }

      $(element).validate({
        errorLabelContainer: $('.error-messages', errorContainer),
        ignore: '.ignore',
        showErrors: function(errorMap, errorList) {
          if (this.numberOfInvalids() === 0) {
            $('.form-error', currentForm).removeClass('visible')
            $('li', currentForm).removeClass('error')
          } else {
            $('.form-error', currentForm).addClass('visible')
            $('.form-error > span', currentForm).html(`Your form contains ${this.numberOfInvalids()} errors, see highlighted fields below.`)
            this.defaultShowErrors()

            // console.log('errorList', errorList)
          }
        },
        highlight: (element, errorClass, validClass) => {
          let addErrorClassTo

          if ($(element).parents('.required-input').length >= 1) {
            addErrorClassTo = $(element).parents('.required-input')
          } else if ($(element).parent('li').length >= 1) {
            addErrorClassTo = $(element).parent('li')
          }

          addErrorClassTo.addClass('error').removeClass(validClass)
          $(element.form).find(`label[for=${element.id}]`).not('.checkbox-label').addClass(errorClass)
        },
        unhighlight: (element, errorClass, validClass) => {
          let addErrorClassTo

          if ($(element).parents('.required-input').length >= 1) {
            addErrorClassTo = $(element).parents('.required-input')
          } else if ($(element).parent('li').length >= 1) {
            addErrorClassTo = $(element).parent('li')
          }

          addErrorClassTo.removeClass('error').addClass(validClass)
          $(element.form).find(`label[for=${element.id}]`).removeClass(errorClass)
        },
        submitHandler: function submitHandler(form) {
          if ($(form).is('form')) {
            form.submit()
          } else {
            form = $(form).find('form')
            form.submit()
          }
        },
        rules: {
          rules: {
            maxlength: 800,
          },
          email: {
            required: true,
            email: true,
          },
          tel: {
            maxlength: 15,
            phoneno: true,
          },
          'current-yacht-make': {
            maxlength: 300,
          },
          'current-yacht-model': {
            maxlength: 300,
          },
          postcode: {
            maxlength: 100,
          },
          'oyster-model[]': {
            required: true,
          },
          'models_interested_in[]': {
            required: true,
          },
          captcha: {
            required: true,
          },
          hiddenRecaptcha: {
            required: () => {
              if (grecaptcha.getResponse() === '') {
                return true
              } else {
                return false
              }
            },
          },
        },
      })

      function limitTimesBasedOnDay(selectedTimes) {
        $('#viewing-time').empty()
        $("<option value=''>Select</option>").appendTo('#viewing-time')
        $.each(selectedTimes, (key, element) => {
          const addAsOption = `<option value='${element}'>${element}</option>`
          $(addAsOption).appendTo('#viewing-time')
        })
      }

      $('select', element).change((e) => {
        if ($(e.target).find(':selected').data('available-times')) {
          limitTimesBasedOnDay($(e.target).find(':selected').data('available-times'))
        }
        $(e.target).valid()
      })
    })
  }
}
