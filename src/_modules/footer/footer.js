import $ from 'jquery'

function validateEmail(email) {
  const re = /\S+@\S+\.\S+/
  return re.test(email)
}

export default class GlobalFooter {
  constructor() {
    this.init()
  }

  init() {
    $('.sign-up-btn').on('click', () => {
      let nope = false

      $('.new-sign-up :input[required]').each((input) => {
        if (!$(input).val()) {
          nope = true
          $(input).parent('.field').addClass('error')
          $('.new-sign-up .form-error').addClass('visible')
        } else {
          $(input).parent('.field').removeClass('error')
          $('.new-sign-up .form-error').removeClass('visible')
        }
      })

      if (!validateEmail($('.new-sign-up :input[type="email"]').val())) {
        nope = true

        $('.new-sign-up :input[type="email"]').parent('.field').addClass('error')
        $('.new-sign-up .form-error').addClass('visible')
      } else {
        $('.new-sign-up :input[type="email"]').parent('.field').removeClass('error')
        $('.new-sign-up .form-error').removeClass('visible')
      }

      if (!nope) {
        $('.sign-up-btn').addClass('is-loading')
        const $form = $('#signup-home-footer')

        $.ajax({
          url: '/newsletter/signup.php',
          dataType: 'json',
          type: 'POST',
          data: {
            email: $form.find('.newsletter-email').val(),
            fname: $form.find('.newsletter-fname').val(),
            sname: $form.find('.newsletter-sname').val(),
            country: $form.find('.newsletter-country').val(),
          },
        }).done((data) => {
          if (data.status === 'success') {
            $('.sign-up').addClass('is-complete')
          }
        })
      }
    })

    $('#signup-home-footer').submit((e) => {
      e.preventDefault()
    })
  }
}
