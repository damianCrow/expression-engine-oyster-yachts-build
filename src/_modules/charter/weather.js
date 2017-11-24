'use strict';

define(['jquery', 'simpleWeather', 'weather_icons', 'average_climate_data'], function ($) {	
	$('.destination-todays-temp').each(function (index, element) {
		var weatherLocaiton = $(element).data('weather-location');
		$.simpleWeather({
			zipcode: '',
			woeid: '', //2357536
			location: weatherLocaiton,
			unit: 'c',
			success: function success(weather) {
				$('.destination-temp span', element).html(weather.temp);
				$('.destination-date-time time', element).html(weather.forecast[0].date);
				$('.destination-date-time time', element).data('cel', weather.temp);
				$('.destination-date-time time', element).data('fanren', weather.alt.temp);
				$('.weather-icon', element).html(weatherIconIds[weather.code]).promise().done(function () {
					$('.destination-temp').addClass('destination-temp-loaded');
				});
			}
		});
	});

	$('.temperature-setting-label').on('click', function () {
		var weatherBox = $(this).parents('.destination-todays-temp');
		if ($('input', this).prop("checked")) {
			var faran = $(weatherBox).find('.destination-date-time time').data('fanren');
			$('.destination-temp span', weatherBox).html(faran);
		} else {
			var cel = $(weatherBox).find('.destination-date-time time').data('cel');
			$('.destination-temp span', weatherBox).html(cel);
		}
	});

	// ---- *end* simpleWeather.js CONFIG *end* ----	

	//  ---- AVERAGE CLIMATE SLIDER -----  //
	if ($('[data-average-climate-hardcode]').length > 0) {

		$('.destination-average-climate').each(function (index, element) {
			var destination = $(element).data('average-climate-hardcode');

			var monthText = $('.destination-selected-month', element);

			// Set the first values (aka January)
			monthText.html(avgClimate(destination)[0].month);
			$('.destination-avg-rainfall span', element).html(avgClimate(destination)[0].value.Rainfall + 'mm');
			$('.destination-avg-temperature span', element).html(avgClimate(destination)[0].value.AvgTemp + '°C');

			$('.destination-avg-temperature-next', element).on('click', function () {

				var currentMonth = monthText.data('average-climate-month');

				var nextMonth = currentMonth + 1;

				if (avgClimate(destination)[nextMonth] === undefined) {
					nextMonth = 0;
				}

				monthText.html(avgClimate(destination)[nextMonth].month);
				$('.destination-avg-rainfall span', element).html(avgClimate(destination)[nextMonth].value.Rainfall + 'mm');
				$('.destination-avg-temperature span', element).html(avgClimate(destination)[nextMonth].value.AvgTemp + '°C');

				monthText.data('average-climate-month', nextMonth);
			});

			$('.destination-avg-temperature-prev', element).on('click', function () {

				var currentMonth = monthText.data('average-climate-month');

				var nextMonth = currentMonth - 1;

				if (avgClimate(destination)[nextMonth] === undefined) {
					nextMonth = 11;
					console.log('true null');
				}

				monthText.html(avgClimate(destination)[nextMonth].month);
				$('.destination-avg-rainfall span', element).html(avgClimate(destination)[nextMonth].value.Rainfall + 'mm');
				$('.destination-avg-temperature span', element).html(avgClimate(destination)[nextMonth].value.AvgTemp + '°C');

				monthText.data('average-climate-month', nextMonth);
			});
		});
	}

	function avgClimate(destination) {
		var output = [];

		function getDescendantProp(obj, desc) {
			var arr = desc.split(".");
			while (arr.length && (obj = obj[arr.shift()]));
			return obj;
		}

		for (var k in climateMonths) {
			var climateMonth = climateMonths[k];
			output.push({ month: climateMonth, value: getDescendantProp(averageClimate, destination.toString())[climateMonth] });
		}

		return output;
	}
});