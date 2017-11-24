'use strict';

define(['jquery'], function($) {

	var BreakPoints = (function () {
		var breakPoints = {xxlarge: 1440, xlarge: 1240, large: 1024, medium: 768, small: 0};
		var curentBreakPoint = function () {
			// Get Breakpoint - Breakpoints will need to be updated from CSS
			var wW = $(window).width();

			for (var key in breakPoints) {
			  if (breakPoints.hasOwnProperty(key)) {
				if(wW > breakPoints[key]) {
				  return breakPoints[key]
				}
			  }
			}
		};

		var curentBreakPointString = function () {
			// Get Breakpoint - Breakpoints will need to be updated from CSS
			var wW = $(window).width();

			for (var key in breakPoints) {
			  if (breakPoints.hasOwnProperty(key)) {
				if(wW > breakPoints[key]) {
				  return key
				}
			  }
			}
		};

		var atLeast = function (sizeQuery) {
			var atLeastBreakPoints = [];
			for (var key in breakPoints) {
				if (breakPoints.hasOwnProperty(key)) {
					if(breakPoints[key] <= curentBreakPoint()) {
						// Add to atLeastBreakPoints array.
						atLeastBreakPoints.push(key);
					}
				}
			}
			var toReturn;
			$.each(atLeastBreakPoints, function(index, element) {
				if(sizeQuery == element) {
					toReturn = true;
					return false;
				}
			});

			return toReturn;
		};
		return {
			atLeast: atLeast
		};

	})();

	return BreakPoints;

});