'use strict';

define(['jquery', 'googleMaps'], function ($) {
	var googleMapStyle = [{
		"featureType": "water",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#004363" }]
	}, {
		"featureType": "water",
		"elementType": "labels.text.fill",
		"stylers": [{ "color": "#000000" }]
	}, {
		"featureType": "landscape",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#5a7c8c" }]
	}, {
		"featureType": "landscape",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#5b7e8d" }]
	}, {
		"featureType": "poi",
		"elementType": "geometry.fill",
		"stylers": [{ "color": "#53636b" }]
	}];

	$(function() {
		initMap();
	});
	
	function initMap() {
		var customMapType = new google.maps.StyledMapType(googleMapStyle);
		var customMapTypeId = 'custom_style';

		$('.destination-map-container').each(function(index, element) {

			var locationLat = $(element).data('lat'),
			locationLng = $(element).data('lng'),
			defaultZoom = 10;

			if ($(element).data('default-zoom')){
				defaultZoom = $(this).data('default-zoom');
			}
			
			var map = new google.maps.Map(element, {
				zoom: defaultZoom,
				center: {lat: locationLat, lng: locationLng},  // British Virgin Islands
				streetViewControl: false,
				mapTypeControl: false,
				scrollwheel: false
			});

			map.mapTypes.set(customMapTypeId, customMapType);
			map.setMapTypeId(customMapTypeId);			
		});
	}
});