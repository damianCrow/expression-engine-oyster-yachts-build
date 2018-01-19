import $ from 'jquery'

import FormValidation from '../form-validation/form-validation'

export default class OwnersArea {
  constructor() {
    this.init()
    this.checkForTabs()
  }

  init() {
    $('.owners-area-text').on('click', (event) => {
      event.preventDefault()
      if ($('#login-page').length !== 0) {
        $('#login-page').foundation('open')
      } else {
        $.ajax('/owners-area/login').done((resp) => {
          $('body').prepend(resp)
          $('#login-page').foundation()
          $('select').select2({ minimumResultsForSearch: -1 })
          new FormValidation()
          $('#login-page').foundation('open')

          this.slidingTabs()

          $('#login-page .close-button, #login-page [data-close="data-close"]').on('click', () => {
            $('#login-page').foundation('close')
          })
        })
      }
    })
  }

  checkForTabs() {
    if ($('.sliding-tabs').length >= 1) {
      this.slidingTabs()
      $('.sliding-tabs').each((index, element) => {
        this.slidingTabs(element)
      })
    }
  }

  slidingTabs(tabSet) {
    const tabs = $(tabSet).find('li')
    const numOfTabs = $(tabs).length
    const tabWidth = 100 / numOfTabs + '%'
    const tabContainer = $(tabSet).find('.tab-slider-container')
    const tabSlider = $(tabSet).find('.tab-slider')

    let positionUnderTab
    let firstPositionUnderTab

    $(tabSlider).css({ width: '' })
    $(tabContainer).css({ width: '' })
    $(tabSet).find('ul').css({ width: '' })

    $(tabSlider).css({ width: tabWidth })
    $(tabContainer).css({ width: $(tabs).width() * $(tabs).length })
    $(tabSet).find('ul').css({ width: $(tabs).width() * $(tabs).length })

    $(tabs).each((index, element) => {
      positionUnderTab = `${100 * index}%`
      $(element).data('transform-pos', positionUnderTab)
      if ($(element).hasClass('is-active')) {
        if (index !== 0) {
          firstPositionUnderTab = positionUnderTab
        } else {
          firstPositionUnderTab = '0%'
        }
      }
    })

    $(tabSlider).css({ transform: `translateX(${firstPositionUnderTab})` })

    $(tabs).on('click', (e) => {
      const clickedTab = e.currentTarget
      const moveHere = $(clickedTab).data('transform-pos')
      $(tabSlider).css({ transform: `translateX(${moveHere})` })
      $(clickedTab).data('transform-pos')
    })
  }
}
