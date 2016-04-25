/*jshint unused: vars */
require.config({
  paths: {
    typekit: 'https://use.typekit.net/htg3ydj',
    jquery: '/bower_components/jquery/dist/jquery',
    lodash: '/bower_components/lodash/dist/lodash.core.min',
    TweenMax: '/bower_components/gsap/src/minified/TweenMax.min',
    jquerygsap: '/bower_components/gsap/src/minified/jquery.gsap.min',
    foundation: '/bower_components/foundation-sites/dist/foundation.min',
    cycle: '/bower_components/jquery-cycle2/build/jquery.cycle2.min',
    lightgallery: '/assets/js/lib/lightgallery/lightgallery',
    lightgalleryThumbs: '/assets/js/lib/lightgallery/lg-thumbnail',
    ScrollMagic: '/bower_components/scrollmagic/scrollmagic/minified/ScrollMagic.min',
    jqueryValidation: '/bower_components/jquery-validation/dist/jquery.validate.min',
    owlcarousel: '/bower_components/owl.carousel/dist/owl.carousel.min',
    select2: '/bower_components/select2/dist/js/select2.full.min',
    simpleWeather: '/bower_components/simpleWeather/jquery.simpleWeather.min',
    googleMaps: 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC4Ctq_b0K3ygkut_DEJ4YFyuGkcWKvM68',
    salvattore: '/bower_components/salvattore/dist/salvattore.min',

    foundationInit: '/assets/js/components/setup/foundationInit',
    weather_icons: '/assets/js/components/util/weather-icons',
    
    oyster_global: '/assets/js/app/app',
    oyster_header: '/assets/js/components/header/header',
    oyster_home: '/assets/js/app/index',
    oyster_brokerage: '/assets/js/app/brokerage/brokerage',
    oyster_charter: '/assets/js/app/charter/charter',
    oyster_brokerage_filters: '/assets/js/components/util/brokerage-filters',
    oyster_charter_filters: '/assets/js/components/util/charter-filters',
    oyster_social_grid: '/assets/js/components/util/social-grid'
  },
  shim: {
    'jquery': {
      exports: '$'
    },
    'lodash': {
      exports: '_'
    },
    'TweenMax': [
      'jquery'
    ],
    'jquerygsap': [
      'jquery'
    ],
    'store': {
      exports: '__store'
    },
    'cycle': [
      'jquery'
    ],
    'lightgallery': [
      'jquery'
    ],
    'lightgalleryThumbs': [
      'jquery',
      'lightgallery'
    ],
    'select2': [
      'jquery'
    ],
    'jqueryValidation': [
      'jquery'
    ],
    'foundation': {
      deps: ['jquery'],
      exports: 'Foundation'
    },
    'ScrollMagic': {
      deps: ['jquery'],
      exports: 'ScrollMagic'
    },
    'owlcarousel': [
      'jquery'
    ],
    'simpleWeather': [
      'jquery'
    ],
    'salvattore': {
      deps: ['jquery'],
      exports: 'Salvattore'
    },
    'typekit': {
      exports: 'Typekit'
    }
  },
  priority: ['jquery', 'lodash']
});

//http://code.angularjs.org/1.2.1/docs/guide/bootstrap#overview_deferred-bootstrap
window.name = 'NG_DEFER_BOOTSTRAP!';

require([
  'jquery',
  'lodash',
  'typekit',
  'TweenMax',
  'jquerygsap',
  'foundation',
  'owlcarousel',
  'googleMaps',
  'foundationInit',
  'oyster_global'
], function($, _, Typekit ) {
  'use strict';

  // load typekit fonts
  try{Typekit.load({ async: true });}catch(e){console.log(e)}

  // load googleMaps
  try{googleMaps.load({ async: true });}catch(e){console.log(e)}
});
