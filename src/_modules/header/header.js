import $ from 'jquery'

import { addClass, removeClass, hasClass } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class GlobalHeader {
  constructor() {
    this.breakpoints = new BreakPoints()

    this.topBarHeight = 0
    this.fixedTopValue = 0
    this.fixedHeight = this.breakpoints.atLeast('medium') ? 90 : 60
    this.header = document.querySelector('.main-header')
    this.title = document.querySelector('.main-header__title')

    this.headerDependencies = [
      this.header,
      this.header.querySelector('.logo'),
      this.header.querySelector('.main-header__menu'),
    ]

    this.largeHeaderActive = true


    // this.legacy()
    // this.snapPointCheck()
  }

  legacy() {

    /* From Modernizr */
    function whichTransitionEvent() {
      let t
      const el = document.createElement('fakeelement')
      const transitions = {
        transition: 'transitionend',
        OTransition: 'oTransitionEnd',
        MozTransition: 'transitionend',
        WebkitTransition: 'webkitTransitionEnd',
      }

      for (t in transitions) {
        if (el.style[t] !== undefined) {
          return transitions[t]
        }
      }
    }

    /* Listen for a transition! */
    const e = document.getElementsByClassName('logo-o')[0]

    const transitionEvent = whichTransitionEvent()
    transitionEvent && e.addEventListener(transitionEvent, () => {
      $('.main-nav li ul').removeClass('hidden-animation')
    })

    // Hack to make sure SVG transisions work correctly inside an anchor tag
    if (navigator.userAgent.indexOf('Safari') !== -1 && navigator.userAgent.indexOf('Chrome') === -1) {
      const logoHref = $('.global-header .logo').attr('href')
      $('.global-header .logo').removeAttr('href')
      $('.global-header .logo').append('<a class="safari-logo-link" href=' + logoHref + '></a>')
    }


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

  // snapPointCheck(lastKnownScrollPosition = window.scrollY) {
  //   return this.styleExpanded(lastKnownScrollPosition)
  // }

  snapPointCheck(reactiveScrollProps) {
    const { lastKnownScrollPosition, headerSnapPoint } = reactiveScrollProps

    this.fixedTopValue = reactiveScrollProps.fixedTopValue

    if (lastKnownScrollPosition > headerSnapPoint && this.largeHeaderActive) {
      this.size()
    } else if (lastKnownScrollPosition < headerSnapPoint && !this.largeHeaderActive) {
      this.size(true)
    }

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
      this.header.style.transform = ''

      this.largeHeaderActive = true

      this.fixedHeight = this.breakpoints.atLeast('medium') ? 90 : 60
    } else {
      for (let i = 0; i < this.headerDependencies.length; i += 1) {
        if (this.headerDependencies[i]) {
          removeClass(this.headerDependencies[i], `${this.headerDependencies[i].className.split(' ')[0]}--large`)
        }
      }

      // this.topBarHeight = fixedTopValue
      this.largeHeaderActive = false

      this.fixedHeight = 60
    }
    this.topPosition()
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

  fullScreenMode(visble) {
    if (visble) {
      addClass(this.header, 'main-header--full-screen')
      this.size()
    } else {
      removeClass(this.header, 'main-header--full-screen')
      this.size(true)
    }
  }
}

