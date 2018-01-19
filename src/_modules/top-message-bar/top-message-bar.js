import $ from 'jquery'

import { addClass, removeClass, getElemDistance } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class TopMessageBar {
  constructor(element, reposistion) {
    this.filterBar = element

    this.activeBar = true
    this.stickyBar = false

    this.topBarHeight = 0
    this.fixedHeight = 0

    if (this.filterBar) {
      this.fetchSizes()
      this.distFromTop = getElemDistance(this.filterBar)
      this.cookieMessage(reposistion)
    }
  }

  fetchSizes() {
    this.fixedHeight = $(this.filterBar).outerHeight(true)
  }

  snapPointCheck(reactiveScrollProps) {
    if (this.filterBar) {
      const { filterBar } = this

      if (reactiveScrollProps.fixedTopValue !== this.topBarHeight && this.activeBar) {
        filterBar.style.transform = `translateY(${reactiveScrollProps.fixedTopValue}px)`
        this.topBarHeight = reactiveScrollProps.fixedTopValue
      } else if (reactiveScrollProps.fixedTopValue === 0) {
        this.topBarHeight = 0

        filterBar.style.transform = ''
      }
    }

    return this.activeBar ? this.fixedHeight : 0
  }

  cookieMessage(reposistion) {
    const retrievedCookieMessage = JSON.parse(localStorage.getItem('oysterYachtsCookie'))
    const oysterYachtsCookie = { value: JSON.stringify('true'), timestamp: new Date().getTime() + 31556926000 }

    if ($(retrievedCookieMessage).length > 0) {
      if (retrievedCookieMessage.timestamp < new Date().getTime()) {
        // expired
        this.activeBar = true
        $('body').addClass('fixed-message-showing')
      } else {
        this.activeBar = false
        $('body').removeClass('fixed-message-showing')
      }
    } else {
      this.activeBar = true
      $('body').addClass('fixed-message-showing')
    }


    $('.accept-message').on('click', () => {
      this.activeBar = false
      reposistion()
      $('body').removeClass('fixed-message-showing')
      // cookieMessage = 'true'
      localStorage.setItem('oysterYachtsCookie', JSON.stringify(oysterYachtsCookie))
    })
  }
}
