import $ from 'jquery'

export default class BreakPoints {
  constructor() {
    this.breakPoints = {
      xxlarge: 1440,
      xlarge: 1240,
      large: 1024,
      medium: 768,
      small: 0,
    }
  }

  curentBreakPoint() {
    // Get Breakpoint - Breakpoints will need to be updated from CSS
    return Object.keys(this.breakPoints).find(key => ($(window).width() > this.breakPoints[key]))
  }

  curentBreakPointString() {
    // Get Breakpoint - Breakpoints will need to be updated from CSS
    const wW = $(window).width()

    for (const key in this.breakPoints) {
      if (this.breakPoints.hasOwnProperty(key)) {
        if (wW > this.breakPoints[key]) {
          return key
        }
      }
    }
  }

  atLeast(sizeQuery) {
    return (this.breakPoints[this.curentBreakPoint()] >= this.breakPoints[sizeQuery])
    // return Object.keys(this.breakPoints).find(key => ((this.breakPoints[key] <= this.breakPoints[this.curentBreakPoint()])))
  }
}
