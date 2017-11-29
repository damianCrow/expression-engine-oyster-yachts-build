'use strict';

define(['jquery', 'ScrollMagic', 'foundation'], function ($, ScrollMagic, validateForm) {

  // Find the nav background sprite
  let img = document.getElementsByClassName('main-nav-second__image')[0],
  style = window.getComputedStyle(img, ':after'),
  bi = style.backgroundImage.slice(4, -1);

  // console.log('bi, ', bi);

  // Preload image
  let navImage = new Image();
  navImage.src = bi;

  const mainNav = document.querySelector('.main-nav-first');
  const allFirstNavs = document.querySelectorAll('.main-nav-first > .main-nav-first__option');

  Array.prototype.forEach.call(allFirstNavs, function(el, i){
    el.addEventListener('mouseover', showThisMenu(el));
  });

  Array.prototype.forEach.call(allFirstNavs, function(el, i){
    el.addEventListener('mouseleave', hideThisMenu(el));
  });

  function showThisMenu(menuTitle) {

    const menuToShow = menuTitle.querySelector('.main-nav-second')
    if (menuToShow.classList){
      menuToShow.classList.add('main-nav-second--active');
    } else {
      menuToShow.className += ' ' + 'main-nav-second--active';
    }
  }

  function hideThisMenu(menuTitle) {
    const menuToHide = menuTitle.querySelector('.main-nav-second')
    if (menuToHide.classList){
      menuToHide.classList.remove('main-nav-second--active');
    } else {
      menuToHide.className = menuToHide.className.replace(new RegExp('(^|\\b)' + 'main-nav-second--active'.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }
  }


});
