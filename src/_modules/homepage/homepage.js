import $ from 'jquery'
import 'jquery.cycle2'
import 'owl.carousel2'

import BreakPoints from '../../_scripts/breakpoints'
import SocialGrid from '../social-grid/social-grid'


export default class Homepage {
  constructor() {
    this.breakpoints = new BreakPoints()
    this.legacyCode()
  }

  legacyCode() {
    // ----- HOME PAGE SOCIAL GRID ----
    new SocialGrid()

    $('.scroll-message').click(() => {
      $('html, body').animate({
        scrollTop: $('.yacht-feature').offset().top - $('.global-header').height(),
      }, 1000)
    })

    // Set the height of the hero slides on the homepage to height of the window, and on resize.
    // - CSS VH works great, but with jump on mobile (Chrome on Android for example)
    function heroSlideHeight() {
      $('.hero-home .hero-slide, .hero-home .hero-slides').height($(window).height() - $('.global-header').height())
    }
    heroSlideHeight()


    // Homepage cycle / sliders

    $('.hero-slides').on('initialized.owl.carousel', () => {
      setTimeout(() => {
        // Apply event before the carousel is initialized and add the zooming animation class to all.
        $('.hero-slide').find('.hero-image-container').addClass('zooming-in')
      }, 850)
    }).owlCarousel({
      loop: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      margin: 0,
      autoHeight: false,
      items: 1,
      dotsContainer: '.hero-controls',
      dotClass: 'nav-point',
      animateOut: 'fadeOut',
      animateIn: 'fadeIn',

    }).on('translate.owl.carousel', (e) => {
      // On each change make sure every carousel item is animating (with this class).
      setTimeout(() => {
        $('.hero-image-container').not(`.hero-slides .slide-${(e.page.index + 1)}`).removeClass('zooming-in')
      }, 850)

      $(`.hero-slides .slide-${(e.page.index + 1)}`).addClass('zooming-in')
    })

    $('.hero-controls .nav-point').click(() => {
      // With optional speed parameter
      // Parameters has to be in square bracket '[]'
      $('.hero-slides').trigger('play.owl.autoplay', [5000])
    })

    const newsPreviewCarousel = $('.news-preview .owl-carousel')

    newsPreviewCarousel.owlCarousel({
      loop: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      margin: 0,
      // autoWidth: true,
      items: 1,
      nav: false,
    })

    // Modernizr.on('videoautoplay', (result) => {
    //   if (result) {
    //     cycleFeaturedYachtSlider(BreakPoints.atLeast('large'))
    //   } else {
    //     console.log('Modernizr failed to run autoplay test')
    //   }
    // })

    function cycleFeaturedYachtSlider(secondarySlider) {
      const timerNewRow = 6500
      const timerStagger = 100
      const slideSubRows = []
      let currentSubRow = 0
      let timeout
      let currentRow

      // get how many subrows there as for each row
      $(slideIndex).each((i, subrow) => {
        slideSubRows[i] = Math.max.apply(Math, subrow)
      })

      function nextSlideTimer(i, c) {
        let index = 0

        for (let j = 0; j < currentRow; j += 1) {
          index += slideIndex[j][i]
        }

        index += currentSubRow

        setTimeout(() => {
          imageCycles.eq(i).cycle('goto', index)
        }, c * timerStagger)
      }

      function nextImageSlides() {
        let c = 0

        for (let i = 0; i < 4; i += 1) {
          if (currentSubRow === 0) {
            nextSlideTimer(i, c)

            c += 1
          } else if (slideIndex[currentRow][i] > 1) {
            nextSlideTimer(i, c)

            c += 1
          }
        }
      }

      function nextSlide() {
        timeout = setTimeout(() => {
          if (currentSubRow < slideSubRows[currentRow] - 1) {
            currentSubRow++

            nextImageSlides()
          } else {
            currentSubRow = 0
            currentRow++
            if (currentRow >= slideIndex.length) {
              currentRow = 0
            }

            overviewCycle.cycle('next')
            nextImageSlides()
          }

          nextSlide()
        }, timerNewRow)
      }

      nextSlide()

      function startAutopager() {
        nextSlide()
      }

      function stopAutopager() {
        clearTimeout(timeout)
      }

      // init Overview cycle
      const overviewCycle = $('.yacht-feature .details').on('cycle-pager-activated', (event, opts) => {
        currentRow = opts.currSlide

        clearTimeout(timeout)
        nextSlide()

        nextImageSlides()
      }).cycle({
        pager: '.nav-points',
        pagerTemplate: '<div class="nav-point"></div>',
        pagerActiveClass: 'active',
        slides: '> .slide',
        prev: '.yacht-left',
        next: '.yacht-right',
        fx: 'scrollHorz',
        swipe: true,
        speed: 500,
        log: false,
        paused: true,
      })

      // init image cycles
      const imageCycles = $('.yacht-feature-large-media, .yacht-feature-small-media').cycle({
        paused: true,
        speed: 500,
        fx: 'scrollHorz',
        swipe: true,
        slides: '> .slide',
      })

      window.addEventListener('focus', startAutopager)
      window.addEventListener('blur', stopAutopager)
    }

    let resizeTimer

    $(window).on('resize', () => {
      clearTimeout(resizeTimer)
      resizeTimer = setTimeout(() => {
        // Run code here, resizing has "stopped"
        heroSlideHeight()
        cycleFeaturedYachtSlider(this.breakpoints.atLeast('large'))
      }, 250)
    })
  }
}
