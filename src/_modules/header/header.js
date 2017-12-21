import $ from 'jquery'

import { addClass, removeClass } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class GlobalHeader {
  constructor(rePosTopLevels) {
    this.breakpoints = new BreakPoints()

    this.rePosTopLevels = rePosTopLevels

    this.topBarHeight = 0
    this.fixedTopValue = 0
    this.fixedHeight = this.currentHeight()
    this.header = document.querySelector('.main-header')
    this.title = document.querySelector('.main-header__title')

    this.headerDependencies = [
      this.header,
      this.header.querySelector('.logo'),
      this.header.querySelector('.main-header__menu'),
    ]

    this.pastScrollPoint = true
    this.largeHeaderActive = true
  }

  currentHeight() {
    return this.breakpoints.atLeast('medium') ? 70 : 50
  }

  legacy() {
    // OVVERIDE MOBILE ACCORDION TEXT TO BE LINKS
    $('#global-navigation-modal a.accordion-title span').on('click', (evt) => {
      evt.preventDefault()
      location.href = $(evt.currentTarget).parent().attr('href')
      return false
    })

    $('.site-menu').on('click', (e) => {
      $(e.currentTarget).toggleClass('active-burger')
    })

    // Toggle follow oyster social buttons
    const navFooter = $('#global-navigation-modal .nav-footer-modal')
    const followHeader = $('.global-header .follow')

    $('.nav-footer-modal .follow-btn').on('click', () => {
      navFooter.addClass('follow-oyster-social-btns')
    })

    $('.nav-footer-modal .social-links .back-btn').on('click', () => {
      navFooter.removeClass('follow-oyster-social-btns')
    })

    $('.global-header .follow-oyster').on('click', () => {
      followHeader.toggleClass('follow-on')
    })

    $('.global-header .back-btn').on('click', () => {
      followHeader.removeClass('follow-on')
    })

    // close foundation modal
    $('.global-modals .close-button-wrapper a.site-search-close').on('click', () => {
      // if a modal is full screen and has the combination of ".close-button-wrapper a.site-search-close", manually close it
      $('.global-modals.full').foundation('close')
    })

    //  ---- GLOBAL MAIN FIXED HEADER SEARCH BAR TOGGLE SEARCH ICON -----  //
    const siteSearchOpen = $('.global-header-top-nav-large .site-search')

    // site search open
    siteSearchOpen.on('click', (e) => {
      e.preventDefault()

      $('.global-header-top-nav-large').toggleClass('search-bar-open')

      if ($('.global-header-top-nav-large').hasClass('search-bar-open')) {
        $('.search-bar input').focus()
      }
    })
  }

  snapPointCheck(reactiveScrollProps) {
    const { lastKnownScrollPosition, headerSnapPoint } = reactiveScrollProps

    this.fixedTopValue = reactiveScrollProps.fixedTopValue

    if (this.pastScrollPoint && (this.currentHeight() === 50 || lastKnownScrollPosition > headerSnapPoint)) {
      this.size()
      this.pastScrollPoint = false
    } else if (lastKnownScrollPosition < headerSnapPoint && !this.pastScrollPoint) {
      this.size(true)
      this.pastScrollPoint = true
    }

    this.topPosition()

    return this.fixedHeight
  }

  size(large) {
    if (large) {
      for (let i = 0; i < this.headerDependencies.length; i += 1) {
        if (this.headerDependencies[i]) {
          addClass(this.headerDependencies[i], `${this.headerDependencies[i].className.split(' ')[0]}--large`)
        }
      }
      this.topBarHeight = 0

      this.largeHeaderActive = true
      this.fixedHeight = this.currentHeight()
    } else {
      for (let i = 0; i < this.headerDependencies.length; i += 1) {
        if (this.headerDependencies[i]) {
          removeClass(this.headerDependencies[i], `${this.headerDependencies[i].className.split(' ')[0]}--large`)
        }
      }

      this.largeHeaderActive = false
      this.fixedHeight = 50
    }
  }

  topPosition() {
    if (this.fixedTopValue !== this.topBarHeight) {
      this.header.style.transform = `translateY(${this.fixedTopValue}px)`
      this.topBarHeight = this.fixedTopValue
    } else if (this.fixedTopValue === 0) {
      this.topBarHeight = 0
      this.header.style.transform = ''
    }
  }

  fullScreenMode(options) {
    const config = { on: true, ...options }

    if (config.on) {
      addClass(this.header, 'main-header--full-screen')
      this.size()
    } else {
      removeClass(this.header, 'main-header--full-screen')
      this.size(this.pastScrollPoint)
    }

    this.rePosTopLevels()
  }
}

