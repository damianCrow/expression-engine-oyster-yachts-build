/*jshint unused: vars */
require.config({
  paths: {
    typekit: 'https://use.typekit.net/htg3ydj',
    jquery: '/bower_components/jquery/dist/jquery.min',
    underscore: '/bower_components/underscore/underscore-min',
    TweenLite: '/bower_components/gsap/src/minified/TweenLite.min',
    CSSPlugin: '/bower_components/gsap/src/minified/plugins/CSSPlugin.min',
    jquerygsap: '/bower_components/gsap/src/minified/jquery.gsap.min',
    foundation: '/bower_components/foundation-sites/dist/foundation.min',
    cycle: '/bower_components/jquery-cycle2/build/jquery.cycle2.min',
    lightgallery: '/assets/js/lib/lightgallery/lightgallery',
    lightgalleryThumbs: '/assets/js/lib/lightgallery/lg-thumbnail',
    ScrollMagic: '/bower_components/scrollmagic/scrollmagic/minified/ScrollMagic.min',
    jqueryValidation: '/bower_components/jquery-validation/dist/jquery.validate.min',
    owlcarousel: '/bower_components/owl.carousel/dist/owl.carousel.min',
    select2: '/bower_components/select2/dist/js/select2.full.min',
    salvattore: '/bower_components/salvattore/dist/salvattore.min',
    simpleWeather: '/bower_components/simpleWeather/jquery.simpleWeather.min',
    googleMaps: 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC4Ctq_b0K3ygkut_DEJ4YFyuGkcWKvM68',
    salvattore: '/bower_components/salvattore/dist/salvattore.min',

    weather_icons: '/assets/js/components/util/weather-icons',
    appInit: '/assets/js/components/setup/init',

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
    'underscore': {
      exports: '_'
    },
    'TweenLite': {
      exports: 'TweenLite'
    },
    'CSSPlugin': [
      'TweenLite'
    ],
    'jquerygsap': [
      'jquery', 'TweenLite', 'CSSPlugin'
    ],
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
    'salvattore': {
      deps: ['jquery'],
      exports: 'Salvattore'
    },
    'owlcarousel': [
      'jquery'
    ],
    'simpleWeather': [
      'jquery'
    ],
    'typekit': {
      exports: 'Typekit'
    }
  },
  priority: ['jquery', 'underscore']
});

require([
  'jquery',
  'underscore',
  'jquerygsap',
  'foundation',
  'owlcarousel',
  'googleMaps',

  'appInit',
  'oyster_global'
]);
