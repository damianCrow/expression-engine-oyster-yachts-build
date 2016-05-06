'use strict';

define(['jquery', 'simpleWeather', 'weather_icons'], function ($) {	
	$('.destination-todays-temp').each(function(index, element) {
		var weatherLocaiton = $(element).data('weather-location');
		$.simpleWeather({
				zipcode: '',
				woeid: '', //2357536
				location: weatherLocaiton,
				unit: 'c',
				success: function(weather) {
					$('.destination-temp span', element).html(weather.temp);
					$('.destination-date-time time', element).html(weather.forecast[0].date);
					$('.destination-date-time time', element).data('cel', weather.temp);
					$('.destination-date-time time', element).data('fanren', weather.alt.temp);
					$('.weather-icon', element).html(weatherIconIds[weather.code]).promise().done(function(){
						$('.destination-temp').addClass('destination-temp-loaded');
					});
				},
				error: function(error) {
				}
			});
	});

	$('.temperature-setting-label').on('click', function(){
		var weatherBox = $(this).parents('.destination-todays-temp');
		if($('input', this).prop("checked")) {
			var faran = $(weatherBox).find('.destination-date-time time').data('fanren');
			$('.destination-temp span', weatherBox).html(faran);
		} else {
			var cel = $(weatherBox).find('.destination-date-time time').data('cel');			
			$('.destination-temp span', weatherBox).html(cel);

		}
	});
});