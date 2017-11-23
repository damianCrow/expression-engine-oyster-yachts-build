/*jshint unused: vars */
require.config({
  urlArgs: "v=1.12",

  paths: {
    typekit: 'https://use.typekit.net/htg3ydj',
    jquery: '/bower_components/jquery/dist/jquery.min',
    underscore: '/bower_components/underscore/underscore-min',
    TweenMax: '/bower_components/gsap/src/minified/TweenMax.min',
    CSSPlugin: '/bower_components/gsap/src/minified/plugins/CSSPlugin.min',
    jquerygsap: '/bower_components/gsap/src/minified/jquery.gsap.min',
    foundation: '/bower_components/foundation-sites/dist/foundation.min',
    cycle: '/bower_components/jquery-cycle2/build/jquery.cycle2.min',
    lightgallery: '/assets/js/lib/lightgallery/lightgallery',
    lightgalleryThumbs: '/assets/js/lib/lightgallery/lg-thumbnail.min',
    lightgalleryHash: '/assets/js/lib/lightgallery/lg-hash',
    lightgalleryVideo: '/assets/js/lib/lightgallery/lg-video.min',
    ScrollMagic: '/bower_components/scrollmagic/scrollmagic/minified/ScrollMagic.min',
    jqueryValidation: '/bower_components/jquery-validation/dist/jquery.validate.min',
    owlcarousel: '/bower_components/owl.carousel/dist/owl.carousel.min',
    select2: '/bower_components/select2/dist/js/select2.full.min',
    salvattore: '/bower_components/salvattore/dist/salvattore.min',
    simpleWeather: '/bower_components/simpleWeather/jquery.simpleWeather.min',
    googleMaps: 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC4Ctq_b0K3ygkut_DEJ4YFyuGkcWKvM68',
    salvattore: '/bower_components/salvattore/dist/salvattore.min',
    froogaloop: 'https://f.vimeocdn.com/js/froogaloop2.min',
    lazySizesRespImg: '/bower_components/lazysizes/plugins/respimg/ls.respimg.min',
    lazySizesBgSet: '/bower_components/lazysizes/plugins/bgset/ls.bgset.min',
    lazySizesParentFit: '/bower_components/lazysizes/plugins/parent-fit/ls.parent-fit.min',
    lazySizesProg: '/bower_components/lazysizes/plugins/progressive/ls.progressive.min',
    lazySizes: '/bower_components/lazysizes/lazysizes.min',

    appInit: '/assets/js/components/setup/init',
    global: '/assets/js/app/app',
    header: '/assets/js/components/header/header',
    footer: '/assets/js/components/footer/footer',
    home: '/assets/js/app/index',
    brokerage: '/assets/js/app/brokerage',
    charter: '/assets/js/app/charter',
    yachts: '/assets/js/app/yachts',
    
    brokerage_filters: '/assets/js/components/util/brokerage-filters',
    charter_filters: '/assets/js/components/util/charter-filters',
    social_grid: '/assets/js/components/util/social-grid',
    breakpoints: '/assets/js/components/util/breakpoints',
    map: '/assets/js/components/util/map',
    gallery_fullscreen: '/assets/js/components/util/gallery-full',
    gallery_modal: '/assets/js/components/util/gallery-modal',
    shortlist: '/assets/js/components/util/shortlist',
    weather: '/assets/js/components/util/weather',
    validateForm: '/assets/js/components/util/validate-form',
    weather_icons: '/assets/js/components/util/weather-icons',
    ownersAreaModal: '/assets/js/components/util/owners-area-modal',
    average_climate_data: '/assets/js/components/util/average-climate-data'
  },
  shim: {
    'jquery': {
      exports: '$'
    },
    'underscore': {
      exports: '_'
    },
    'TweenMax': {
      exports: 'TweenMax'
    },
    'CSSPlugin': [
      'TweenMax'
    ],
    'jquerygsap': [
      'jquery', 'TweenMax', 'CSSPlugin'
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
    'lightgalleryHash': [
      'jquery',
      'lightgallery'
    ],
    'lightgalleryVideo': [
      'jquery',
      'lightgallery',
      'froogaloop'
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
  'lazySizesRespImg',
  'lazySizesBgSet',
  'lazySizesParentFit',
  'lazySizesProg',
  'lazySizes',
  'appInit',
  'global'
]);
