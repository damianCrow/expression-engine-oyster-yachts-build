import $ from 'jquery'
import 'jquery.cycle2'

export default function quoteTestimonials() {
  $('.quote-testimonials').cycle({
    autoHeight: 'calc',
    pager: '.nav-points',
    pagerActiveClass: 'active',
    pagerTemplate: '<div class="nav-point"></div>',
    slides: '> blockquote',
    log: false,
    timeout: 4000,
  })
}
