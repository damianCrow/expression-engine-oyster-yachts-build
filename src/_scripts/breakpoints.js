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
    const wW = $(window).width()

    for (const key in this.breakPoints) {
      if (this.breakPoints.hasOwnProperty(key)) {
        if (wW > this.breakPoints[key]) {
          return this.breakPoints[key]
        }
      }
    }
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
    const atLeastBreakPoints = []

    for (const key in this.breakPoints) {
      if (this.breakPoints.hasOwnProperty(key)) {
        if (this.breakPoints[key] <= this.curentBreakPoint()) {
          // Add to atLeastBreakPoints array.
          atLeastBreakPoints.push(key)
        }
      }
    }

    return $.each(atLeastBreakPoints, (index, element) => (sizeQuery === element))
  }
}


//  var BreakPoints = (function () {
//    var breakPoints = 
//    var curentBreakPoint = function () {

//    };

//    var curentBreakPointString = function () {

//    };

//    var atLeast = function (sizeQuery) {

//    };
//    return {
//      atLeast: atLeast
//    };

//  })();

//  return BreakPoints;

// });

