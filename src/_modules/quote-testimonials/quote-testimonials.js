import $ from 'jquery'
import 'jquery.cycle2'

export function quoteTestimonials() {
  // if ($('.quote-testimonials blockquote').length > 1) {
    $('.quote-testimonials').cycle({
      autoHeight: 'calc',
      pager: '.nav-points',
      pagerActiveClass: 'active',
      pagerTemplate: '<div class="nav-point"></div>',
      slides: '> blockquote',
      log: false,
      timeout: 4000,
    })
  // }
}
