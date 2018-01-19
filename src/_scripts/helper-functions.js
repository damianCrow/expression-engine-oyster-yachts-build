/**
 * Returns a new element with a given class.
 *
 * @param {string} type of element.
 * @param {string} newClass is the class the new element will have.
 *
 * @return {string} The filename extracted from the path.
 */
export function iOS() {

  const iDevices = [
    'iPad Simulator',
    'iPhone Simulator',
    'iPod Simulator',
    'iPad',
    'iPhone',
    'iPod'
  ];

  if (!!navigator.platform) {
    while (iDevices.length) {
      if (navigator.platform === iDevices.pop()) {
        return true;
      }
    }
  }

  return false;
}

// Get an element's distance from the top of the page
export function getElemDistance(elem) {
  let location = 0
  if (elem.offsetParent) {
    do {
      location += elem.offsetTop
      elem = elem.offsetParent
    } while (elem)
  }
  return location >= 0 ? location : 0
}

export function findAncestor(el, cls) {
  while ((el = el.parentNode) && el.className.indexOf(cls) < 0)
  return el
}

/**
 * Returns a new element with a given class.
 *
 * @param {string} type of element.
 * @param {string} newClass is the class the new element will have.
 *
 * @return {string} The filename extracted from the path.
 */
export function newEl(type, newClass) {
  let element = document.createElement(type);
  element.className = newClass;
  return element;
}

/**
 * Returns a new element with a given class.
 *
 * @param {string} min of element.
 * @param {string} max is the class the new element will have.
 *
 * @return {string} The filename extracted from the path.
 */
export function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min);
}

/**
 * Returns a new element with a given class.
 *
 * @param {string} min of element.
 * @param {string} max is the class the new element will have.
 *
 * @return {string} The filename extracted from the path.
 */
export function addEventListener(el, eventName, handler) {
  if (el.addEventListener) {
    el.addEventListener(eventName, handler);
  } else {
    el.attachEvent('on' + eventName, function() {
      handler.call(el);
    });
  }
}

/**
 * Returns a new element with a given class.
 *
 * @param {string} object of element.
 * @param {string} type is the class the new element will have.
 * @param {string} callback is the class the new element will have.
 *
 */
export function windowResize(object, type, callback) {
  if (object == null || typeof(object) == 'undefined') return;
  if (object.addEventListener) {
      object.addEventListener(type, callback, false);
  } else if (object.attachEvent) {
      object.attachEvent('on' + type, callback);
  } else {
      object['on' + type] = callback;
  }
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function setAttributes(el, attrs) {
  for (let key in attrs) {
    el.setAttribute(key, attrs[key]);
  }
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function toggleClass(el, className) {
  if (el.classList) {
    el.classList.toggle(className);
  } else {
      let classes = el.className.split(' ');
      let existingIndex = -1;
      for (let i = classes.length; i--;) {
        if (classes[i] === className)
          existingIndex = i;
      }

      if (existingIndex >= 0)
        classes.splice(existingIndex, 1);
      else
        classes.push(className);

    el.className = classes.join(' ');
  };
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function removeClass(el, cls) {
  if (el.className.indexOf(cls) === -1)
    return
  var s = el.className.split(/\s+/),
    newClass = '',
    i = 0
  for (; i < s.length; i++) {
    if (s[i] && s[i] != cls) {
      if (i > 0) newClass += ' '
      newClass += s[i]
    }
  }
  el.className = newClass
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function addClass(el, className) {
  if (el.classList) {
    el.classList.add(className);
  }else{
    el.className += ' ' + className;
  }
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function hasClass(el, className) {
  if (el.classList) {
    return el.classList.contains(className);
  }else{
    return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
  }
}

/**
 * Returns a new element with a given class.
 *
 * @param {node} el of element.
 * @param {object} attrs is the class the new element will have.
 *
 */
export function removeHash() {
  let scrollV = 0;
  let scrollH = 0;
  const loc = window.location;
  if ('pushState' in history) {
      history.pushState('', document.title, loc.pathname + loc.search);
  } else {
      // Prevent scrolling by storing the page's current scroll offset
      scrollV = document.body.scrollTop;
      scrollH = document.body.scrollLeft;

      loc.hash = '';

      // Restore the scroll offset, should be flicker free
      document.body.scrollTop = scrollV;
      document.body.scrollLeft = scrollH;
  }
}


/**
 * Returns a new element with a given class.
 *
 * @param {array} array of element.
 *
 * @return {string} The filename extracted from the path.
 */
export function shuffleArray(array) {

  for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      const temp = array[i];
      array[i] = array[j];
      array[j] = temp;
  }

  return array;

}


/**
 * Returns a new element with a given class.
 *
 * @param {object} obj of element.
 *
 * @return {string} The filename extracted from the path.
 */
export function toArray(obj) {
  let array = [];
  // iterate backwards ensuring that length is an UInt32
  for (let i = obj.length >>> 0; i--;) {
    array[i] = obj[i];
  }
  return array;
}

/**
 * Returns a new element with a given class.
 *
 * @param {object} obj of element.
 *
 * @return {string} The filename extracted from the path.
 */
export function documentReady() {
  return new Promise((resolve) => {
    if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
      resolve()
    } else {
      document.addEventListener('DOMContentLoaded', () => resolve())
    }
  })
}

