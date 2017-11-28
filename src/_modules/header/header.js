import $ from 'jquery'

import BreakPoints from '../../_scripts/breakpoints'

export default class GlobalHeader {
  constructor() {
    this.header = $('.global-header')
    this.scrollToPoint = this.header.attr('data-snap-to-min-nav')
    this.snapOffset = parseInt(this.header.attr('data-snap-offset'), 10) || 0
    this.headerClass = 'global-header-mini'

    // local-sidebar
    this.localSidebar = $('[data-local-sidebar]')
    this.stickySidebar = $('.sticky-sidebar')
    this.expandHeaderBuffer = $(this.header).height() * 1.5

    this.yachtNav = $('[data-local-subnav]')
    this.localSubNav = $('[data-local-subnav]') ? $('[data-local-subnav]') : $('.global-local-subnav')

    this.breakpoints = new BreakPoints()

    this.init()
  }

  init() {
    // if ($('[data-local-subnav]')[0]) {
    //   this.localSubNav = $('[data-local-subnav]')
    // } else if ($('.global-local-subnav')[0]) {
    //   this.localSubNav = $('.global-local-subnav')
    // }

    // COOKIE HEADER MESSAGE

    const retrievedCookieMessage = JSON.parse(localStorage.getItem('oysterYachtsCookie'))

    // console.log(retrievedCookieMessage)

    // var oysterYachtsCookie = {value: "true", timestamp: new Date().getTime()}

    const oysterYachtsCookie = { value: JSON.stringify('true'), timestamp: new Date().getTime() + 31556926000 }

    if ($(retrievedCookieMessage).length > 0) {
      if (retrievedCookieMessage.timestamp < new Date().getTime()) {
        //expired
        // console.log('expired', retrievedCookieMessage.timestamp)
        // console.log('cureent date', new Date().getTime())
        $('body').addClass('fixed-message-showing')
      } else {
        // console.log('retrievedCookieMessage.timestamp', retrievedCookieMessage.timestamp)
        $('body').removeClass('fixed-message-showing')
      }
    } else {
      $('body').addClass('fixed-message-showing')
    }


    $('.accept-message').on('click', () => {
      $('body').removeClass('fixed-message-showing')
      // cookieMessage = 'true'
      localStorage.setItem('oysterYachtsCookie', JSON.stringify(oysterYachtsCookie))
    })


    // if the scroll point is a div id, get its index point
    if (isNaN(parseInt(this.scrollToPoint), 10) && this.scrollToPoint !== 'undefined') this.scrollToPoint = $(this.scrollToPoint).offset().top - this.snapOffset

    $(window).bind('scroll', () => {
      const scroll = $(window).scrollTop()
      // if the subnav and the header exists, remove the box shadow
      if (this.localSubNav && this.header.length !== 0) this.headerClass = 'global-header-mini no-boxshadow'

      if (scroll >= parseInt(this.scrollToPoint, 10)) {
        if ($(this.header).hasClass(this.headerClass)) {
          // $('.main-nav li ul').removeClass('hidden-animation')
        } else {
          $('.main-nav li ul').addClass('hidden-animation')
          this.header.addClass(this.headerClass)
        }

        if (this.localSubNav && this.breakpoints.atLeast('medium')) {
          this.localSubNav.addClass('global-local-subnav-mini').parent().css({ position: 'static' })
        }

        // A buffer zone for expanding the header.
      } else {
        this.yachtNav.removeClass('global-local-subnav-mini').parent().css({ position: 'relative' })
      }

      if (scroll <= parseInt(this.expandHeaderBuffer, 10)) {
        if ($(this.header).hasClass(this.headerClass)) {
          $('.main-nav li ul').addClass('hidden-animation')

          this.header.removeClass(this.headerClass)
        } else {
          // $('.main-nav li ul').removeClass('hidden-animation')
        }

        this.localSubNav && this.localSubNav.removeClass('global-local-subnav-mini')
      }
    })


    /* From Modernizr */
    function whichTransitionEvent() {
      let t
      const el = document.createElement('fakeelement')
      const transitions = {
        'transition': 'transitionend',
        'OTransition': 'oTransitionEnd',
        'MozTransition': 'transitionend',
        'WebkitTransition': 'webkitTransitionEnd',
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
}

