'use strict';

define(['jquery', 'ScrollMagic', 'foundation'], function ($, ScrollMagic, validateForm) {

  console.log('main-nav js');

  // Find the nav background sprite
  let img = document.getElementsByClassName('main-nav-second__image')[0],
  style = window.getComputedStyle(img, ':after'),
  bi = style.backgroundImage.slice(4, -1);

  console.log('bi, ', bi);

  // Preload image
  let navImage = new Image();
  navImage.src = bi;

});
