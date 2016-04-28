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
});
