import $ from 'jquery'
import GoogleMapsLoader from 'google-maps'

export default class Maps {
  constructor() {
    this.legacy()

    GoogleMapsLoader.KEY = 'AIzaSyC4Ctq_b0K3ygkut_DEJ4YFyuGkcWKvM68'

    this.mapStyle = [{
      featureType: 'water',
      elementType: 'geometry.fill',
      stylers: [{
        color: '#004363',
      }],
    }, {
      featureType: 'water',
      elementType: 'labels.text.fill',
      stylers: [{
        color: '#000000',
      }],
    }, {
      featureType: 'landscape',
      elementType: 'geometry.fill',
      stylers: [{
        color: '#5a7c8c',
      }],
    }, {
      featureType: 'landscape',
      elementType: 'geometry.fill',
      stylers: [{
        color: '#5b7e8d',
      }],
    }, {
      featureType: 'poi',
      elementType: 'geometry.fill',
      stylers: [{
        color: '#53636b',
      }],
    }]
  }

  legacy() {
    const customMapType = new google.maps.StyledMapType(this.mapStyle)
    const customMapTypeId = 'custom_style'
    console.log('hi')
    $('.destination-map-container').each((index, element) => {
      const lat = $(element).data('lat')
      const lng = $(element).data('lng')

      let defaultZoom = 10

      if ($(element).data('default-zoom')) {
        defaultZoom = $(element).data('default-zoom')
      }

      GoogleMapsLoader.load((google) => {
        const map = new google.maps.Map(element, {
          zoom: defaultZoom,
          center: { lat, lng },
          streetViewControl: false,
          mapTypeControl: false,
          scrollwheel: false,
        })

        map.mapTypes.set(customMapTypeId, customMapType)
        map.setMapTypeId(customMapTypeId)
      })
    })

    // WORLD RALLY MAP
    if ($('#map').length > 0) {
      // Basic options for a simple Google Map
      // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
      const mapOptions = {
        // How zoomed in you want the map to start at (always required)
        zoom: 4,
        // mapTypeId: google.maps.MapTypeId.SATELLITE,
        scrollwheel: false,

        // The latitude and longitude to center the map (always required)
        center: new google.maps.LatLng(17.074656, -61.817521), // New Yo

        // How you would like to style the map.
        // This is where you would paste any style found on Snazzy Maps.
        styles: [{
          featureType: 'water',
          elementType: 'geometry.fill',
          stylers: [{ color: '#004363' }],
        }, {
          featureType: 'water',
          elementType: 'labels.text.fill',
          stylers: [{ color: '#000000' }],
        }, {
          featureType: 'landscape',
          elementType: 'geometry.fill',
          stylers: [{ color: '#5a7c8c' }],
        }, {
          featureType: 'landscape',
          elementType: 'geometry.fill',
          stylers: [{ color: '#5b7e8d' }],
        }, {
          featureType: 'poi',
          elementType: 'geometry.fill',
          stylers: [{ color: '#53636b' }],
        }],
      }

      // Get the HTML DOM element that will contain your map
      // We are using a div with id="map" seen below in the <body>
      const mapElement = document.getElementById('map')

      // Create the Google Map using our element and options defined above
      const map = new google.maps.Map(mapElement, mapOptions)

      //  marker = icon: 'http://google-maps-icons.googlecode.com/files/sailboat-tourism.png'

      // Let's also add a marker while we're at it
      const ctaLayer = new google.maps.KmlLayer({ url: 'http://www.oysteryachts.com/route.kmz', map, preserveViewport: true })
    }

    //  ---- GOOGLE MAPS EMBEDS -----  //

    function initDestinationMap() {
      const customMapType = new google.maps.StyledMapType(this.mapStyle)
      const customMapTypeId = 'custom_style'

      $('.single-destination-map-container').each((index, element) => {
        const lat = +$(element).data('lat')
        const lng = +$(element).data('lng')

        let defaultZoom = 10

        if ($(element).data('default-zoom')) {
          defaultZoom = $(element).data('default-zoom')
        }

        let singleDestinationMap

        GoogleMapsLoader.load((google) => {
          singleDestinationMap = new google.maps.Map(element, {
            zoom: defaultZoom,
            center: { lat, lng },
            streetViewControl: false,
            mapTypeControl: false,
            scrollwheel: false,
          })
        })

        const marker = []
        const infoWindows = []

        let highlightedIcon = 1
        let openWindow

        // Check to make sure that MediaQuery will report something.
        if (typeof Foundation !== 'undefined') {
          if (Foundation.MediaQuery.current === 'small') {
            $('.single-destination .destination-list').cycle({
              autoHeight: 'calc',
              swipe: true,
              pager: '.nav-points',
              pagerActiveClass: 'active',
              pagerTemplate: '<div class="nav-point"></div>',
              slides: '> .destination-box',
              log: false,
              timeout: 6000,
            })
          }
        } else {
          if (Foundation.MediaQuery.current === 'small') {
            $('.single-destination .destination-list').cycle({
              autoHeight: 'calc',
              swipe: true,
              pager: '.nav-points',
              pagerActiveClass: 'active',
              pagerTemplate: '<div class="nav-point"></div>',
              slides: '> .destination-box',
              log: false,
              timeout: 6000,
            })
          }
        }


        $('.destination-box').each((index, element) => {

          const iconImage = {
            url: `/assets/images/charter/destination-marker-${(index + 1)}.png`,
            // This marker is 20 pixels wide by 32 pixels high.
            size: new google.maps.Size(30, 30),
            // The origin for this image is (0, 0).
            origin: new google.maps.Point(0, 0),
            // The anchor for this image is the base of the flagpole at (0, 32).
            anchor: new google.maps.Point(0, 32),
          }

          infoWindows[index] = new google.maps.InfoWindow({
            content: $('.destination-info-box-wrapper', element).html().toString(),
            maxWidth: 325,
          })

          marker[index] = new google.maps.Marker({
            position: { lat: +$(element).data('lat'), lng: +$(element).data('lng') },
            map: singleDestinationMap,
            title: `Day: ${(index + 1).toString()}`,
            icon: iconImage,
          })

          console.log('marker[index]', marker[index])

          marker[index].addListener('click', function() {
            if (Foundation.MediaQuery.atLeast("medium")){
              
              if (openWindow) {
                openWindow.close()
              }

              infoWindows[index].open(singleDestinationMap, marker[index])
              openWindow = infoWindows[index]

              $('.destination-box').removeClass('destination-active')
              $('.destination-box').eq(index).addClass('destination-active')
            } else {
              $('.single-destination .destination-list').cycle('goto', index)
              // marker[index].setIcon('/assets/images/destination-marker-' + (index + 1) + '-cyan.png');
            }

            if (highlightedIcon) {
              highlightIcon(index, highlightedIcon)
            } else {
              highlightIcon(index)
            }

            highlightedIcon =  index

          });

          $(element).on('click', function() {
            
            if (Foundation.MediaQuery.atLeast('medium')){

              if (openWindow) {
                openWindow.close()
              }
              infoWindows[index].open(singleDestinationMap, marker[index])
              // lazySizes.loader.unveil($('single-destination-map-container.destination-info-box-image'));
              openWindow = infoWindows[index]
            }

            if(highlightedIcon) {
              highlightIcon(index, highlightedIcon)
            }else {
              highlightIcon(index)
            }

            highlightedIcon =  index

            //
            $('.destination-box').removeClass('destination-active')
            $(element).addClass('destination-active')
          })
          // function onMarkerClick(destinationIndex, destinationElement){
          //  if(openWindow){
          //    openWindow.close();
          //  }
          //  infoWindows[destinationIndex].open(singleDestinationMap, marker[destinationIndex]);
          //  // lazySizes.loader.unveil($('single-destination-map-container.destination-info-box-image'));
          //  openWindow = infoWindows[destinationIndex];
          // }
        })

        $('.single-destination .destination-list').on('cycle-after', function(event, optionHash) {
          if (highlightedIcon) {
            highlightIcon((optionHash.slideNum - 1), highlightedIcon)
          } else {
            highlightIcon((optionHash.slideNum - 1))
          }
          highlightedIcon =  (optionHash.slideNum - 1)
        })

        function highlightIcon(newIcon, oldIcon){
          // Change the old one back.

          if (!oldIcon) {
            oldIcon = 0
          }

          marker[oldIcon].setIcon({
            url: `/assets/images/charter/destination-marker-${(oldIcon + 1)}.png`,
            // This marker is 20 pixels wide by 32 pixels high.
            size: new google.maps.Size(30, 30),
            // The origin for this image is (0, 0).
            origin: new google.maps.Point(0, 0),
            // The anchor for this image is the base of the flagpole at (0, 32).
            anchor: new google.maps.Point(0, 32),
          })

          // Highlight the new one.
          marker[newIcon].setIcon({
            url: `/assets/images/charter/destination-marker-${(newIcon + 1)}-cyan.png`,
            // This marker is 20 pixels wide by 32 pixels high.
            size: new google.maps.Size(30, 30),
            // The origin for this image is (0, 0).
            origin: new google.maps.Point(0, 0),
            // The anchor for this image is the base of the flagpole at (0, 32).
            anchor: new google.maps.Point(0, 32),
          })
        }

        singleDestinationMap.mapTypes.set(customMapTypeId, customMapType)
        singleDestinationMap.setMapTypeId(customMapTypeId)
      })
    }

    initDestinationMap()
  }
}

