import $ from 'jquery'
import BreakPoints from '../../_scripts/breakpoints'

// PAGINATION BUTTONS CLICK HANDLER FOR SECTIONS 3 AND 4. \\

let nextSlide = 0
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
        }
// INCREMENT NEXT nextSlide IF currentSlide IS NOT THE LAST SLIDE \\
        if (currentSlide < caroselLength - 1) {
          nextSlide = currentSlide + 1
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
  }
}
