import 'babel-polyfill'

// Legacy imports
// TODO: Break these (and their code below) into modules.
import $ from 'jquery'
import select2 from 'select2'
import 'foundation-sites'
// import { Foundation } from 'foundation-sites/js/foundation.core'
// import 'foundation-sites/js/foundation.util.mediaQuery'
// import 'foundation-sites/js/foundation.equalizer'
// import 'foundation-sites/js/foundation.reveal'

import 'lazysizes/plugins/optimumx/ls.optimumx'
import 'lazysizes/plugins/respimg/ls.respimg'
import 'lazysizes/plugins/parent-fit/ls.parent-fit'
import 'lazysizes'
import 'lazysizes/plugins/object-fit/ls.object-fit'

import GlobalHeader from '../_modules/header/header'
import TopMessageBar from '../_modules/top-message-bar/top-message-bar'
import GlobalNav from '../_modules/global-nav/global-nav'
import SubBar from '../_modules/sub-bar/sub-bar'
import Burger from '../_modules/burger/burger'
import GlobalFooter from '../_modules/footer/footer'
import Homepage from '../_modules/homepage/homepage'
import Filters from '../_modules/filters/filters'
import BrokerageFilters from '../_modules/brokerage/brokerage-filters'
import CharterFilters from '../_modules/charter/charter-filters'
import Shortlist from '../_modules/shortlist/shortlist'
import GalleryModal from '../_modules/gallery-modal/gallery-modal'
import Weather from '../_modules/weather/weather'
import OwnersArea from '../_modules/owners-area/owners-area-modal'
import FormValidation from '../_modules/form-validation/form-validation'

import { documentReady, windowResize, getElemDistance } from './helper-functions'
import { quoteTestimonials } from '../_modules/quote-testimonials/quote-testimonials'

class Main {
  constructor() {
    const d = document
    this.body = d.body
    this.homepageHero = d.getElementById('homepage')

    this.globalLogo = d.querySelector('.logo')
    this.globalHeader = d.querySelector('.global-header-wrapper')
    this.topMessageBar = d.querySelector('.top-header')
    this.burgerBtn = d.querySelector('.main-header__menu')
    this.globalNav = d.querySelector('.global-nav')
    this.subBar = d.querySelector('.sub-bar')
    this.globalFooter = d.querySelector('.global-footer')
    this.filters = d.getElementById('global-page-filters')

    this.galleryModal = d.getElementById('galleries')
    this.sideBarToStick = d.querySelector('section.about-yacht .sticky-sidebar')
    this.homeSlider = d.querySelector('.hero-home')
    this.brokerageFiltersDom = d.querySelector('.filters--brokerage')
    this.charterGrid = d.querySelector('.charter-fleet #yacht-grid')
    this.shortlistModal = d.getElementById('shortlistModal')

    this.fixedTopValue = 10
    this.fixedTopValues = []
    this.headerSnapPoint = 450

    $(d).foundation()
    this.legacyCode()
    this.init()
  }

  init() {
    // appInit: '/assets/js/components/setup/init',
    // global: '/assets/js/app/app', ✓
    // header: '/assets/js/components/header/header', ✓
    // footer: '/assets/js/components/footer/footer', ✓
    // home: '/assets/js/app/index', ✓
    // brokerage: '/assets/js/app/brokerage', ✓
    // charter: '/assets/js/app/charter', ⤫
    // yachts: '/assets/js/app/yachts', ⤫

    // brokerage_filters: '/assets/js/components/util/brokerage-filters', ✓
    // charter_filters: '/assets/js/components/util/charter-filters', ✓
    // social_grid: '/assets/js/components/util/social-grid', ✓
    // breakpoints: '/assets/js/components/util/breakpoints', ✓
    // map: '/assets/js/components/util/map',
    // gallery_fullscreen: '/assets/js/components/util/gallery-full',
    // gallery_modal: '/assets/js/components/util/gallery-modal', ✓
    // shortlist: '/assets/js/components/util/shortlist', ✓
    // weather: '/assets/js/components/util/weather', ✓
    // validateForm: '/assets/js/components/util/validate-form', ✓
    // weather_icons: '/assets/js/components/util/weather-icons', ✓
    // ownersAreaModal: '/assets/js/components/util/owners-area-modal', ✓
    // average_climate_data: '/assets/js/components/util/average-climate-data' ✓

    this.maps()

    quoteTestimonials()

    const header = new GlobalHeader(this.positionElements.bind(this))

    // Cookie message, pass the posistionElements method to allow it to be dismissed.
    const topMessageBar = new TopMessageBar(this.topMessageBar, this.positionElements.bind(this))
    const nav = new GlobalNav(this.globalNav, this.burgerBtn, document.body)
    const subNav = new SubBar(this.subBar, this.scrollToBannerSection.bind(this))
    const filters = new Filters()
    const form = new FormValidation()
    const burger = new Burger(this.burgerBtn, nav)
    const footer = this.globalFooter && new GlobalFooter()
    const gallery = new GalleryModal(this.galleryModal, header, this.positionElements.bind(this))
    const homepage = this.homepageHero && new Homepage()
    const brokerageFilters = this.brokerageFiltersDom && new BrokerageFilters()
    const charterFilters = this.charterGrid && new CharterFilters()
    const shortlist = this.shortlistModal && new Shortlist()
    const weather = new Weather()
    const owenersArea = new OwnersArea()

    this.scrollReativeElements = [topMessageBar, header, gallery, nav, subNav, filters]

    this.scrollEvents()
    this.positionElements(window.scrollY)

    this.enableSelect2()

    this.windowReszing()
  }

  windowReszing() {
    let resizeTimer
    let windowWidth = window.innerWidth
    windowResize(window, 'resize', () => {
      clearTimeout(resizeTimer)
      resizeTimer = setTimeout(() => {
        // Run code here, resizing has "stopped"
        if (windowWidth !== window.innerWidth) {
          windowWidth = window.innerWidth
          this.positionElements(window.scrollY)
        }
      }, 250)
    }, true)
  }

  scrollEvents() {
    let lastKnownScrollPosition = window.scrollY
    let ticking = false

    document.addEventListener('scroll', () => {
      lastKnownScrollPosition = window.scrollY

      if (!ticking) {
        window.requestAnimationFrame(() => {
          this.positionElements(lastKnownScrollPosition)
          ticking = false
        })
        ticking = true
      }
    })
  }

  positionElements(lastKnownScrollPosition) {
    const topValues = [10]
    this.fixedTopValue = 10

    for (let i = 0; i < this.scrollReativeElements.length; i += 1) {
      const lastModuleHeight = this.scrollReativeElements[i].snapPointCheck({
        lastKnownScrollPosition,
        fixedTopValue: this.fixedTopValue,
        topValues,
        headerSnapPoint: this.headerSnapPoint,
      })

      topValues.push(lastModuleHeight)
      this.fixedTopValue += lastModuleHeight
    }
  }

  maps() {
    // load googleMaps
    try {
      googleMaps.load({ async: true })
    } catch (e) {
      console.log('google maps error: ', e)
    }
  }

  // TODO: Break this up into modules.
  legacyCode() {
    document.addEventListener('lazybeforeunveil', (e) => {
      const bg = e.target.getAttribute('data-bg')
      if (bg) {
        e.target.style.backgroundImage = `url(${bg})`
      }
    })

    //  ---- SHARE BUTTON -----  //

    let shareListBtn
    let shareListChosen

    function openShareList() {
      $(shareListBtn).removeClass('tooltip-left share-icon-tooltip')
      $(shareListChosen).removeClass('hide')
      $(shareListChosen).addClass('share-list-visible')
    }

    function closeShareLists() {
      $('.share-list').removeClass('share-list-visible')
      setTimeout(() => {
        $('.share-list').addClass('hide')
        // Remove the tooltip while the share-list is open.
        $(shareListBtn).addClass('tooltip-left share-icon-tooltip')
      }, 250)
    }

    $('.share .share-icon').on('click', (e) => {
      shareListBtn = e.currentTarget
      shareListChosen = $(shareListBtn).next('.share-list')
      if ($(shareListChosen).hasClass('share-list-visible')) {
        closeShareLists()
      } else {
        openShareList()
      }
    })

    $(document).on('click', (event) => {
      if (!$(event.target).closest('.share').length) {
        closeShareLists()
      }
    })

    //  ---- *end* SHARE BUTTON *end* -----  //

    $('.aside-header').on('click', () => {
      if ($('.overview-stuck').hasClass('overview-contents-hidden')) {
        $('.overview-download').data('closed-once', true)
        $('.overview-stuck').removeClass('overview-contents-hidden')
      } else {
        $('.overview-download').data('closed-once', false)
        $('.overview-stuck').addClass('overview-contents-hidden')
      }
    })

    // opens and closes the the table when snapped on the header
    $('.sticky-sidebar-header .header').on('click', () => {
      $(this).next().toggle(0)
    })

    $('.slider-pager a').on('click', (e) => {
      e.preventDefault()
      const jumpToHash = $(e.currentTarget).attr('href')
      this.scrollToBannerSection(document.querySelector(jumpToHash))
      // $(jumpToHash)[0].scrollIntoView()
    })

    //  ---- VIEW GALLERY POP UP -----  //

    if ($('#layout-slider .cycle-slide').length > 1) {
      const firstImage = $('#layout-slider .cycle-slide img:first')

      firstImage.on('load', () => {
        $('#layout-slider').cycle({
          slides: '> div',
          paused: true,
          pager: '.slider-pager',
          pagerTemplate: '',
          autoHeight: 'container',
          log: false,
        })

        console.log('height thing about to run')
        $('#layout-slider').css('height', $(firstImage).height())
      })

      firstImage.attr('src', `${firstImage.attr('src')}?_=${(new Date().getTime())}`)
    }

    // --- Media Centre Gallery Select --- //
    $('.select-gallery-wrapper select').each((index, element) => {
      $(element).on('change', (e) => {
        $(element).siblings('a').attr('href', e.currentTarget.value)
      })
    })
  }

  scrollToBannerSection(section) {
    // const destinationBannerHeight = section.querySelector('.banner').getBoundingClientRect().height
    const distance = getElemDistance(section)

    // Scroll to specific values
    window.scroll({
      top: distance - (this.fixedTopValue + 3),
      left: 0,
      behavior: 'smooth',
    })
  }


  // TODO: Move this over to the new method.
  windowResize() {
    let resizeTimer = {}

    $(window).on('resize', () => {
      clearTimeout(resizeTimer)
      resizeTimer = setTimeout(() => {
        // Run code here, resizing has "stopped"
        this.sideBarStick()
        this.checkForTabs()
        this.enableSelect2()
        this.stackedBlocks()
        Foundation.Equalizer
        this.yachtHeroSlideHeight()
      }, 250)
    })
  }

  sideBarStick() {
    const { sideBarToStick } = this

    function snapBar(action) {
      if ($(sideBarToStick).hasClass('overview-stuck') && action === 'unstick') {
        $(sideBarToStick).css({ position: '', top: '', width: '' })
        $(sideBarToStick).removeClass('overview-stuck')
      } else if (action === 'stick' && !$(sideBarToStick).hasClass('overview-stuck')) {
        // const fixedDistance = $(sideBarToStick).offset().top - $(window).scrollTop()
        const widthSide = $(sideBarToStick)[0].getBoundingClientRect().width
        $(sideBarToStick).css({ position: 'fixed', top: '55px', width: widthSide })
        $(sideBarToStick).addClass('overview-stuck')
      }
    }

    if ($(sideBarToStick).length > 0) {
      // const sideBarLocalPos = $(sideBarToStick).position().top
      const sideBarHeight = $(sideBarToStick).height()

      $(window).on('scroll', () => {
        if ($(window).scrollTop() > $('.about-yacht').offset().top + 15 && Foundation.MediaQuery.atLeast('large')) {
          snapBar('stick')
        } else {
          snapBar('unstick')
        }

        if ($(window).scrollTop() > $('.about-yacht').offset().top + $('.about-yacht').height() - (sideBarHeight + 55) && Foundation.MediaQuery.atLeast('large')) {

          if ($('.overview-download').data('closed-once') !== true) {
            $('.overview-download').addClass('overview-contents-hidden')
          }
        } else {
          $('.overview-download').removeClass('overview-contents-hidden')
        }
      })
    }
  }

  checkForTabs() {
    if ($('.sliding-tabs').length >= 1) {
      this.slidingTabs()
      $('.sliding-tabs').each((index, element) => { this.slidingTabs(element) })
    }
  }

  slidingTabs(tabSet) {
    const tabs = $(tabSet).find('li')
    const numOfTabs = $(tabs).length
    const tabWidth = `${100 / numOfTabs}%`
    let firstPositionUnderTab
    const tabContainer = $(tabSet).find('.tab-slider-container')
    const tabSlider = $(tabSet).find('.tab-slider')

    $(tabSlider).css({ width: '' })
    $(tabContainer).css({ width: '' })
    $(tabSet).find('ul').css({ width: '' })

    $(tabSlider).css({ width: tabWidth })
    $(tabContainer).css({ width: $(tabs).width() * $(tabs).length })
    $(tabSet).find('ul').css({ width: $(tabs).width() * $(tabs).length })

    $(tabs).each((index, element) => {
      const positionUnderTab = `${100 * index}%`
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
      const moveHere = $(e.target).data('transform-pos')
      $(tabSlider).css({ transform: `translateX(${moveHere})` })
      $(e.target).data('transform-pos')
    })
  }


  enableSelect2() {
    select2(window, $)
    $('select').select2({ minimumResultsForSearch: -1 })
  }

  stackedBlocks() {
    const stackedBlocks = '.heritage-blocks .stacked-blocks'

    if ($(stackedBlocks).length > 0) {
      if (Foundation.MediaQuery.atLeast('large')) {
        $(`${stackedBlocks} .stacked-block:nth-child(3n - 1)`).addClass('container-col2')
        $(`${stackedBlocks} .stacked-block:nth-child(3n)`).addClass('container-col3')

        $('.container-col2').appendTo(stackedBlocks).removeClass('container-col2')
        $('.container-col3').appendTo(stackedBlocks).removeClass('container-col3')
      } else if (Foundation.MediaQuery.current === 'medium') {
        $(`${stackedBlocks} .stacked-block:nth-child(even)`).addClass('container-col2')

        $('.container-col2').appendTo(stackedBlocks).removeClass('container-col2')
      }

      $('.heritage-blocks .stacked-blocks').addClass('active')
    }
  }

  yachtHeroSlideHeight() {
    $('.hero.full-screen').height($(window).height() - $('.global-header').height())
  }
}

documentReady().then(() => { new Main() })
