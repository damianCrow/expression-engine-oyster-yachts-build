'use strict';

define(['jquery', 'foundation', 'googleMaps'], function ($) {
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

// WORLD RALLY MAP

if($('#map').length > 0){
 init();
        
            function init() {
                // Basic options for a simple Google Map
                // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
                var mapOptions = {
                    // How zoomed in you want the map to start at (always required)
                    zoom: 4,
                    // mapTypeId: google.maps.MapTypeId.SATELLITE,
                    scrollwheel: false,

                    // The latitude and longitude to center the map (always required)
                    center: new google.maps.LatLng(17.074656, -61.817521), // New Yo

                    // How you would like to style the map. 
                    // This is where you would paste any style found on Snazzy Maps.
                    styles:  [{
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
  }]
                };

                // Get the HTML DOM element that will contain your map 
                // We are using a div with id="map" seen below in the <body>
                var mapElement = document.getElementById('map');

                // Create the Google Map using our element and options defined above
                var map = new google.maps.Map(mapElement, mapOptions);

  //  marker = icon: 'http://google-maps-icons.googlecode.com/files/sailboat-tourism.png'


                // Let's also add a marker while we're at it
             var ctaLayer = new google.maps.KmlLayer({ url: 'http://www.oysteryachts.com/route.kmz', map: map, preserveViewport: true });
    //                      google.maps.event.addDomListener(window, "resize", function() {
    //  var center = map.getCenter();
    //  google.maps.event.trigger(map, "resize");
    //  map.setCenter(center); 
    // });
            }	
}


	//  ---- GOOGLE MAPS EMBEDS -----  //
 
initDestinationMap();
 
function initDestinationMap() {
	var customMapType = new google.maps.StyledMapType(googleMapStyle);
	var customMapTypeId = 'custom_style';
 
	$('.single-destination-map-container').each(function(index, element) {
 
		var locationLat = +$(element).data('lat'),
		locationLng = +$(element).data('lng'),
		defaultZoom = 10;
 
		if ($(element).data('default-zoom')){
			defaultZoom = $(this).data('default-zoom');
			console.log('defaultZoom', defaultZoom);
		}
		
		var singleDestinationMap = new google.maps.Map(element, {
			zoom: defaultZoom,
			center: {lat: locationLat, lng: locationLng},  // British Virgin Islands
			streetViewControl: false,
			mapTypeControl: false,
			scrollwheel: false
		});
 
				var marker = [], infoWindows = [], openWindow, highlightedIcon = 1;

		console.log('processing destination: ', element);

		// Check to make sure that MediaQuery will report something.
		if(typeof Foundation !== "undefined") {
			if(Foundation.MediaQuery.current == 'small'){
				$('.single-destination .destination-list').cycle({
					autoHeight: 'calc',
					swipe: true,
					pager: '.nav-points',
					pagerActiveClass: 'active',
					pagerTemplate: '<div class="nav-point"></div>',
					slides: '> .destination-box',
					log: false,
					timeout: 6000
				})
			}
		}else {
			Foundation.MediaQuery;
			if(Foundation.MediaQuery.current == 'small'){
				$('.single-destination .destination-list').cycle({
					autoHeight: 'calc',
					swipe: true,
					pager: '.nav-points',
					pagerActiveClass: 'active',
					pagerTemplate: '<div class="nav-point"></div>',
					slides: '> .destination-box',
					log: false,
					timeout: 6000
				})
			}
		}


		$('.destination-box').each(function(index, element) {

			var iconImage = {
				url: '/assets/images/charter/destination-marker-' + (index + 1) + '.png',
				// This marker is 20 pixels wide by 32 pixels high.
				size: new google.maps.Size(30, 30),
				// The origin for this image is (0, 0).
				origin: new google.maps.Point(0, 0),
				// The anchor for this image is the base of the flagpole at (0, 32).
				anchor: new google.maps.Point(0, 32)
			};

			marker[index] = new google.maps.Marker({
				position: {lat: +$(element).data('lat'), lng: +$(element).data('lng')},
				map: singleDestinationMap,
				title: 'Day: ' + (index + 1).toString(),
				icon: iconImage
			});

			console.log('marker[index]', marker[index]);

			marker[index].addListener('click', function() {
				if(Foundation.MediaQuery.atLeast("medium")){

					if(openWindow){
						openWindow.close();
					}

					infoWindows[index].open(singleDestinationMap, marker[index]);
					openWindow = infoWindows[index];

					$('.destination-box').removeClass('destination-active');
					$('.destination-box').eq(index).addClass('destination-active');

				}else {
					$('.single-destination .destination-list').cycle('goto', index);
					// marker[index].setIcon('/assets/images/destination-marker-' + (index + 1) + '-cyan.png');
				
				}

				if(highlightedIcon) {
					highlightIcon(index, highlightedIcon);
				}else {
					highlightIcon(index);
				}
				highlightedIcon =  index;

			});

			$(element).on('click', function() {
				
				if(Foundation.MediaQuery.atLeast("medium")){

					if(openWindow){
						openWindow.close();
					}
					infoWindows[index].open(singleDestinationMap, marker[index]);
					// lazySizes.loader.unveil($('single-destination-map-container.destination-info-box-image'));
					openWindow = infoWindows[index];

				}		

				if(highlightedIcon) {
					highlightIcon(index, highlightedIcon);
				}else {
					highlightIcon(index);
				}

				highlightedIcon =  index;

				//
				$('.destination-box').removeClass('destination-active');
				$(element).addClass('destination-active');

			});

			// function onMarkerClick(destinationIndex, destinationElement){
			// 	if(openWindow){
			// 		openWindow.close();
			// 	}
			// 	infoWindows[destinationIndex].open(singleDestinationMap, marker[destinationIndex]);
			// 	// lazySizes.loader.unveil($('single-destination-map-container.destination-info-box-image'));
			// 	openWindow = infoWindows[destinationIndex];					
			// }

			if(Foundation.MediaQuery.atLeast("medium")){

				infoWindows[index] = new google.maps.InfoWindow({
					content: $('.destination-info-box-wrapper', element).html().toString(),
					maxWidth: 325
				});
			}

		});

		$('.single-destination .destination-list').on('cycle-after', function(event, optionHash) {
			if(highlightedIcon) {
				highlightIcon((optionHash.slideNum - 1), highlightedIcon);
			}else {
				highlightIcon((optionHash.slideNum - 1));
			}
			highlightedIcon =  (optionHash.slideNum - 1);
		});

		function highlightIcon(newIcon, oldIcon){
			// Change the old one back.

			if(!oldIcon){
				oldIcon = 0;
			}

			marker[oldIcon].setIcon({
				url: '/assets/images/charter/destination-marker-' + (oldIcon + 1) + '.png',
				// This marker is 20 pixels wide by 32 pixels high.
				size: new google.maps.Size(30, 30),
				// The origin for this image is (0, 0).
				origin: new google.maps.Point(0, 0),
				// The anchor for this image is the base of the flagpole at (0, 32).
				anchor: new google.maps.Point(0, 32)
			});
			// Highlight the new one.
			marker[newIcon].setIcon({
				url: '/assets/images/charter/destination-marker-' + (newIcon + 1) + '-cyan.png',
				// This marker is 20 pixels wide by 32 pixels high.
				size: new google.maps.Size(30, 30),
				// The origin for this image is (0, 0).
				origin: new google.maps.Point(0, 0),
				// The anchor for this image is the base of the flagpole at (0, 32).
				anchor: new google.maps.Point(0, 32)
			});
		}
		singleDestinationMap.mapTypes.set(customMapTypeId, customMapType);
		singleDestinationMap.setMapTypeId(customMapTypeId);
	});

	
}

// ---- *end* GOOGLE MAPS EMBEDS *end* ----	
});
