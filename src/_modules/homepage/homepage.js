import $ from 'jquery'
import BreakPoints from '../../_scripts/breakpoints'

// PAGINATION BUTTONS CLICK HANDLER FOR SECTIONS 3 AND 4. \\

let nextSlide = 0
let previousSlide = null
let timeoutName = null
const caroselLength = $('.carosel__link').length

const timeoutManager = (flag, func, time) => {
  if (flag) {
    timeoutName = setTimeout(func, time)
  } else {
    clearTimeout(timeoutName)
  }
}

const automateSlidshow = () => {
  timeoutManager(true, () => {
    $('.carosel__link')[nextSlide].click()
  }, 7000)
}

const handlePaninationButtonClick = (e) => {
// GET THE INDEX OF THE SLIDE RELATED TO THE PAGINATION LINK CLICKED \\
  const idx = parseInt($(e.currentTarget).attr('data-index'), 10)
// ADD THE reverse-dash CSS ANIMATION TO THE CURRENTLY ACTIVE PAGINATION BUTTON \\
  $('.carosel__link.active').addClass('reverse-dash')
// DEACTIVATE THE CURRENTLY ACTIVATED SLIDE AND TEXT \\
  $('.carosel__image-wrap, .text-wrapper').removeClass('active')
// ACTIVATE THE NEW SLIDE \\
  $(`.carosel__image-wrap[data-index="${idx}"]`).addClass('active')
// WAIT 0.8 SECONDS FOR THE reverse-dash ANIMATION TO COMPLETE \\
  setTimeout(() => {
// REMOVE THE ALL ANAMATION CLASSES FROM THE DEACTIVATED PAGINATION LINK \\
    $('.carosel__link').removeClass('active reverse-dash')
// ACTIVATE THE TEXT IN THE CURRENT SLIDE \\
    $(`.carosel__image-wrap[data-index="${idx}"]`).find('.text-wrapper').addClass('active')
// ACTIVATE THE CURRENT PAGINATION BUTTON AND ADD THE reverse-dash CSS ANIMATION AFTER THE FIRST ANIMATION COMPLETES \\
    $(`.carosel__link[data-index="${idx}"]`).addClass('active').on('webkitAnimationEnd animationend oanimationend MSAnimationEnd', (el) => {
      $(el.currentTarget).addClass('reverse-dash')
    })
  }, 800)
}

// SWIPE EVENTS DETECTOR FUNCTION \\
const detectswipe = (el, func) => {
  let swipe_det = {}
  swipe_det.sX = 0
  swipe_det.sY = 0
  swipe_det.eX = 0
  swipe_det.eY = 0
  let min_x = 30
  let max_x = 30
  let min_y = 50
  let max_y = 60
  let direc = ''
  let ele = document.getElementById(el)
  ele.addEventListener('touchstart', (e) => {
    let t = e.touches[0]
    swipe_det.sX = t.screenX
    swipe_det.sY = t.screenY
  }, false)
  ele.addEventListener('touchmove', (e) => {
    e.preventDefault()
    let t = e.touches[0]
    swipe_det.eX = t.screenX
    swipe_det.eY = t.screenY
  }, false)
  ele.addEventListener('touchend', (e) => {
// HORIZONTAL DETECTION \\
    if ((((swipe_det.eX - min_x > swipe_det.sX) || (swipe_det.eX + min_x < swipe_det.sX)) && ((swipe_det.eY < swipe_det.sY + max_y) && (swipe_det.sY > swipe_det.eY - max_y) && (swipe_det.eX > 0)))) {
      if (swipe_det.eX > swipe_det.sX) direc = 'r'
      else direc = 'l'
    }
// VERTICAL DETECTION \\
    else if ((((swipe_det.eY - min_y > swipe_det.sY) || (swipe_det.eY + min_y < swipe_det.sY)) && ((swipe_det.eX < swipe_det.sX + max_x) && (swipe_det.sX > swipe_det.eX - max_x) && (swipe_det.eY > 0)))) {
      if (swipe_det.eY > swipe_det.sY) direc = 'd'
      else direc = 'u'
    }

    if (direc !== '') {
      if (typeof func === 'function') func(direc)
    }

    swipe_det.sX = 0
    swipe_det.sY = 0
    swipe_det.eX = 0
    swipe_det.eY = 0
  }, false)
}

// CLICK THE RELEVANT SLIDE PAGINATION BUTTON ON SWIPE \\

const swipeController = (direction) => {
  if (direction === 'l') {
    $('.carosel__link')[previousSlide].click()
  }
  if (direction === 'r') {
    $('.carosel__link')[nextSlide].click()
  }
}

export default class Homepage {
  constructor() {
    this.breakpoints = new BreakPoints()
// ADD click  EVENT LISTENER TO THE PAGINATION BUTTONS \\
    $('.carosel__link').click((e) => {
      if (!$(e.currentTarget).hasClass('active')) {
        const currentSlide = parseInt($(e.currentTarget).attr('data-index'), 10)
// SET THE nextSlide TO 0 IF currentSlide IS THE LAST SLIDE \\
        if (currentSlide === caroselLength - 1) {
          nextSlide = 0
          previousSlide = currentSlide - 1
        }
// INCREMENT NEXT nextSlide IF currentSlide IS NOT THE LAST SLIDE \\
        if (currentSlide < caroselLength - 1) {
          nextSlide = currentSlide + 1

          if (currentSlide === 0) {
            previousSlide = caroselLength - 1
          } else {
            previousSlide = currentSlide - 1
          }
        }
// CLEAR ANY EXISTING TIMEOUTS \\
        timeoutManager(false)
// AUTOMATE THE CAROSEL AFTER 7 SECONDS (ONCE THE FIRST SLIDE ANIMATION COMPLETES) \\
        automateSlidshow()
// CALL THE CLICK HANDLER \\
        handlePaninationButtonClick(e)
      }
    })
// click() THE FIRST PAGINATION BUTTON ON LOAD TO SHOW THE FIRST SLIDE \\
    $('.carosel__link')[nextSlide].click()
// INITIATE SWIPE DETECTION ON HOMEPAGE \\
    detectswipe('homepage', swipeController)
  }
}
