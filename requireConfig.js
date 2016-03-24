/*jshint unused: vars */
require.config({
  paths: {
    typekit: 'https://use.typekit.net/htg3ydj',
    jquery: 'bower_components/jquery/dist/jquery',
    store: 'bower_components/store2/dist/store2.min',
    lodash: 'bower_components/lodash/dist/lodash.compat',
    cycle: 'bower_components/jquery-cycle2/build/jquery.cycle2.min',
    foundation: 'bower_components/foundation-sites/dist/foundation.min',
    lightgallery: 'bower_components/lightgallery/dist/js/lightgallery-all.min',
    ScrollMagic: 'bower_components/scrollmagic/scrollmagic/minified/ScrollMagic.min'
  },
  shim: {
    'jquery': {
      exports: '$'
    },
    'lodash': {
      exports: '_'
    },
    'lodash': {
      exports: '_'
    },
    'store': {
      exports: '__store'
    },
    'cycle': [
      'jquery'
    ],
    'lightgallery': [
      'jquery'
    ],
    'foundation': {
      deps: ['jquery'],
      exports: 'Foundation'
    },
    'typekit': {
      exports: 'Typekit'
    }
  },
  priority: ['jquery', 'lodash'],
  packages: []
});

//http://code.angularjs.org/1.2.1/docs/guide/bootstrap#overview_deferred-bootstrap
window.name = 'NG_DEFER_BOOTSTRAP!';

require([
  'jquery',
  'lodash',
  'typekit',
  'foundation',
  'ScrollMagic',
  'app/app',
  'store'
], function($, _, Typekit ) {
  'use strict';

  // load typekit fonts
  try{Typekit.load({ async: true });}catch(e){}
});
