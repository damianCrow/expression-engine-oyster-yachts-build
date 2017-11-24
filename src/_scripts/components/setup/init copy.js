'use strict';

// ADD Here any Libraries and Other things that need to be initialised first before loading anything else.

define(['foundation', 'typekit', 'googleMaps'], function (Foundation, typekit, googleMaps) {
  // initilise foundation
  $(document).foundation();

  // load typekit fonts
  try {
    Typekit.load({ async: true });
  } catch (e) {
    console.log(e);
  }

  // load googleMaps
  try {
    googleMaps.load({ async: true });
  } catch (e) {}

  // window.lazySizesConfig = window.lazySizesConfig || {};

  //add simple support for background images:
  // document.addEventListener('lazybeforeunveil', function(e){
  //  console.log('lazybeforeunveil');
  //  console.log('$(e.target)[0]', $(e.target)[0]);
  //     if ($(e.target)[0].attr('data-bg')) {
  //      console.log('yes has data-bg');
  //      var bg = e.target.getAttribute('data-bg');
  //      if(bg){
  //          e.target.style.backgroundImage = 'url(' + bg + ')';
  //      }     
  //     }
  // });
});
//# sourceMappingURL=init.js.map
