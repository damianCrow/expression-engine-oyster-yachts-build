import 'babel-polyfill'

// Legacy imports
// TODO: Break these (and their code below) into modules.
import $ from 'jquery'
import ScrollMagic from 'scrollmagic'
import 'foundation-sites'
// import { Foundation } from 'foundation-sites/js/foundation.core'
// import 'foundation-sites/js/foundation.util.mediaQuery'
// import 'foundation-sites/js/foundation.equalizer'
// import 'foundation-sites/js/foundation.reveal'
import 'lazysizes'
import 'lazysizes/plugins/respimg/ls.respimg'
import 'lazysizes/plugins/optimumx/ls.optimumx'
import 'lazysizes/plugins/unveilhooks/ls.unveilhooks'

import GlobalHeader from '../_modules/header/header'
import GlobalFooter from '../_modules/footer/footer'
import Homepage from '../_modules/homepage/homepage'
import BrokerageFilters from '../_modules/brokerage/brokerage-filters'
import CharterFilters from '../_modules/charter/charter-filters'
import Shortlist from '../_modules/shortlist/shortlist'
import GalleryModal from '../_modules/gallery-modal/gallery-modal'
import Weather from '../_modules/weather/weather'
import OwnersArea from '../_modules/owners-area/owners-area-modal'

import { documentReady } from './helper-functions'
import { quoteTestimonials } from '../_modules/quote-testimonials/quote-testimonials'

class Main {
  constructor() {
    this.globalHeader = document.querySelector('.global-header-wrapper')
    this.globalFooter = document.querySelector('.global-footer')

    this.gallery = document.querySelector('.gallery-content')
    this.sideBarToStick = document.querySelector('section.about-yacht .sticky-sidebar')
    this.homeSlider = document.querySelector('.hero-home')
    this.brokerageGrid = document.querySelector('.brokerage-fleet #yacht-grid')
    this.charterGrid = document.querySelector('.charter-fleet #yacht-grid')
    this.shortlistModal = document.getElementById('shortlistModal')

    $(document).foundation()
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

    // this.maps()

    // Foundation utls

    quoteTestimonials()

    this.header = this.globalHeader && new GlobalHeader()
    this.footer = this.globalFooter && new GlobalFooter()
    this.gallery = this.galleryModal && new GalleryModal()
    this.homepage = this.homeSlider && new Homepage()
    this.brokerageFilters = this.brokerageGrid && new BrokerageFilters()
    this.charterFilters = this.charterGrid && new CharterFilters()
    this.shortlist = this.shortlistModal && new Shortlist()
    this.weather = new Weather()
    this.owenersArea = new OwnersArea()
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

    //  ---- VIEW GALLERY (lightgallery) POP UP -----  //
    if ($('#layout-slider .cycle-slide').length > 1) {
      const firstImage = $('#layout-slider .cycle-slide img:first')
      firstImage.on('load').each((e) => {
        const image = e.currentTarget
        if (image.complete) {
          $(image).load()

          $('#layout-slider').cycle({
            slides: '> div',
            paused: true,
            pager: '.slider-pager',
            pagerTemplate: '',
            autoHeight: 'container',
            log: false,
          })

          $('#layout-slider').css('height', $(this).height())
        }
      })

      firstImage.attr('src', firstImage.attr('src') + '?_=' + (new Date().getTime()))
      firstImage.load()
    }

    // ---- VIEW GALLERY END ----  //

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
      const jumpToHash = $(e.currentTarget).attr('href')
      $(jumpToHash)[0].scrollIntoView()
    })

    // --- Media Centre Gallery Select --- //

    $('.select-gallery-wrapper select').each((index, element) => {
      $(element).on('change', (e) => {
        $(element).siblings('a').attr('href', e.currentTarget.value)
      })
    })
  }

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

  scroller() {
    const controller = new ScrollMagic.Controller()
    let lastId = null

    const locaSubNav = $('[data-local-subnav]') || {}
    const locaSubNavHeight = locaSubNav.outerHeight()
    const localSubNavItems = locaSubNav.find('.local-subnav a')
    const localSubNavItemsMap = localSubNavItems.map(function () {
      if ($(this).attr('href')) {
        const item = $(this).attr('href').trim()

        if (item.toString().substring(0, 1) === '#') {
          if (item.length) return item
        }
      }
    })

    this.sideBarStick()

    // Simple Scroll spy for the Local SubNAV
    $(window).scroll(function () {
      // Get container scroll position
      const fromTop = $(this).scrollTop() + locaSubNavHeight

      // Get id of current scroll item
      let cur = localSubNavItemsMap.map(function () {
        if ($(this).offset().top < fromTop) return this
      })

      // Get the id of the current element
      cur = cur[cur.length - 1]
      const id = cur && cur.length ? cur[0].id : ''

      if (lastId !== id) {
        lastId = id

        // Set/remove active class
        localSubNavItems.parent().removeClass('active').end().filter("[href='#" + id + "']").parent().addClass('active')
      }
    })

    // Yacht nav - scroll to section
    locaSubNav.on('click', '.scroll', function (e) {
      e.preventDefault()

      $('html, body').animate({
        scrollTop: ($($(this).attr('href')).position().top) - 100,
      }, 700)
    })
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
    const tabWidth = 100 / numOfTabs + '%'
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
      const positionUnderTab = 100 * index + '%'
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

    $(tabs).on('click', function () {
      const moveHere = $(this).data('transform-pos')
      $(tabSlider).css({ transform: `translateX(${moveHere})` })
      $(this).data('transform-pos')
    })
  }


  enableSelect2() {
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
