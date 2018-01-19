import $ from 'jquery'

export default class Shortlist {
  constructor() {
    this.init()
  }

  init() {
    //  ---- SHORTLIST LOGIC -----  //

    let shortlistYachts = []

    function checkIfYachtShortlist(yachtId, yachtImage, yachtModal, yachtName, yachtSection, yachtSpec) {
      // console.log('checkIfYachtShortlist, shortlistYachts: ' , shortlistYachts)

      // Check if this yacht is on the shortlist already
      // const shortlistCheck = $.grep(shortlistYachts, e => e.yachtid === yachtId)
      const shortlistCheck = shortlistYachts.some(y => y.yachtId === yachtId)

      if (!shortlistCheck) {
        // Push the yacht details into the shortlist array.
        // Now push it to the dom.
        shortlistYachts.push({
          yachtId,
          yachtImage,
          yachtModal,
          yachtName,
          yachtSection,
          yachtSpec,
        })
      }
    }

    function ripBgUrl(container) {
      const bgCss = $(container).css('background-image')
      return bgCss.replace('url(', '').replace(')', '').replace(/['"]+/g, '')
    }

    // Pull the info, check it and put it in the object if it's not already
    function addToShortlist(item) {
      let yachtContainer = $(item).parents('[data-yachtid]')

      if (yachtContainer.length < 1) {
        yachtContainer = $('[data-yachtid]')
      }

      let yachtImage = yachtContainer[0].querySelector('.yacht-listing-photo')

      const yachtId = $(yachtContainer).data('yachtid')
      const yachtModal = $(yachtContainer).find('.yacht-list-modal').eq(0).text()
      const yachtName = $(yachtContainer).find('.yacht-list-name').eq(0).text()
      const yachtSection = $(yachtContainer).data('yachtsection')
      const yachtSpec = $(yachtContainer).data('spec')

      if ($(' > img', yachtImage).length > 0) {
        yachtImage = $(' > img', yachtImage)[0].currentSrc
      } else {
        yachtImage = ripBgUrl(yachtImage)
      }

      if (yachtImage === undefined) {
        yachtImage = yachtContainer[0].querySelector('.yacht-listing-photo img').src
      }

      checkIfYachtShortlist(yachtId, yachtImage, yachtModal, yachtName, yachtSection, yachtSpec)
      // Now add the items emptied from the DOM onto the list
      displayOnShortlist()

      // console.log('yachtImage src', yachtImage)
    }

    function addYachtsToFreeform() {
      console.log(shortlistYachts)
      // Remove all existing images from the shortlist form
      $('.ff_yacht_image,.ff_yacht_name,.ff_yacht_link,.ff_yacht_spec').remove()

      let shortListCounter = 0

      $.each(shortlistYachts, (key, yachtOnList) => {
        console.log('yachtOnList', yachtOnList)

        const yachtImage = yachtOnList.yachtImage.toString().replace('__blur', '')

        // Add to shortlist form
        $('<input>').attr({
          type: 'hidden',
          id: `yacht_image_${shortListCounter}`,
          name: `yacht_image_${shortListCounter}`,
          value: yachtImage,
          class: 'ff_yacht_image',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: `yacht_name_${shortListCounter}`,
          name: `yacht_name_${shortListCounter}`,
          value: `${yachtOnList.yachtModal} // ${yachtOnList.yachtName}`,
          class: 'ff_yacht_name',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: `yacht_link_${shortListCounter}`,
          name: `yacht_link_${shortListCounter}`,
          value: `http://${window.location.hostname}/brokerage/fleet/${yachtOnList.yachtId}`,
          class: 'ff_yacht_link',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: `yacht_spec_${shortListCounter}`,
          name: `yacht_spec_${shortListCounter}`,
          value: yachtOnList.yachtSpec,
          class: 'ff_yacht_spec',
        }).appendTo('#spec-form')

        shortListCounter += 1
      })
    }

    // Display the yachts saved in the shortlist array into a list in the DOM
    function displayOnShortlist() {
      // Empty what might be there
      $('#shortlistModal .yachts-shortlist').empty()

      // console.log('shortlistYachts', shortlistYachts)

      $.each(shortlistYachts, (key, yachtOnList) => {
        console.log('yachtOnList', yachtOnList)
        const { yachtId, yachtImage, yachtModal, yachtName, yachtSection } = yachtOnList
        // Check if the yacht image is defined, if not, remove the yacht..
        const yachtImage2 = yachtImage.toString().replace('__blur', '')
        const backgroundImage = `<div class="yacht-listing-photo" style="background-image: url(${yachtImage2})"></div>`
        const removeButton = '<button class="remove-button"></button>'
        const completeYachtList = '<li class="column medium-6 small-12"><div data-yachtid=' + yachtId + ' data-yachtsection=' + yachtSection + ' class="yacht-list-item"><a href="">' + backgroundImage + '<div class="yacht-listing-title"><span class="yacht-list-modal">' + yachtModal + '</span><span class="yacht-list-name double-slash">' + yachtName + '</span></div></a>' + removeButton + '</div></li>'
        $('#shortlistModal .yachts-shortlist').append(completeYachtList)
      })

      addYachtsToFreeform()
      toggleEmptyShortlistMessage(shortlistYachts)
      enableRemoveFromList()
      window.lazySizesConfig = window.lazySizesConfig || {}
    }


    function enableRemoveFromList() {
      $('.yachts-shortlist').on('click', '.remove-button', function(e) {
        e.preventDefault()
        const yachtToRemoveId = $(this).parents('[data-yachtid]').data('yachtid')
        removeFromList(yachtToRemoveId)
      })
    }

    function toggleEmptyShortlistMessage(shortlistYachts) {
      const shortlistCont = 'user-shortlist'
      const hiddenClass = '--hide-message'
      const el = document.getElementsByClassName(shortlistCont)[0]
      const requestBtn = document.querySelector('#shortlistModal .request-spec-button')

      if (shortlistYachts.length === 0) {
        el.classList.remove(shortlistCont + hiddenClass)

        requestBtn.setAttribute('disabled', 'true')
        requestBtn.style.opacity = 0.1
      } else {
        el.classList.add(shortlistCont + hiddenClass)

        requestBtn.removeAttribute('disabled')
        requestBtn.style.opacity = 1
      }
    }

    function removeFromList(yachtToRemoveId) {
      const yachtToRemove = $(`.yachts-shortlist [data-yachtid="${yachtToRemoveId}"]`)

      console.log('yachtToRemoveId', yachtToRemoveId)

      const refinedList = $.grep(shortlistYachts, e => e.yachtId !== yachtToRemoveId)

      console.log('removeFromList()')
      console.log('shortlistYachts', shortlistYachts)
      console.log('refinedList', refinedList)

      shortlistYachts = refinedList

      const date = new Date()
      console.log('date', date)
      localStorage.setItem('localShortlist', JSON.stringify({ date: date, list: shortlistYachts }))

      toggleEmptyShortlistMessage(shortlistYachts)

      console.log('yachtToRemove', yachtToRemove)

      $(yachtToRemove).parent('.column').css('display', 'none')
      addYachtsToFreeform()
    }


    // TODO: This needs to be fixed, check if a yacht which is on the localstorage
    // is still avaible or not.
    // currentYachts was defined with PHP, now not being used.
    function removeNowClosedYachts() {
      $.each(shortlistYachts, (key, yachtOnList) => {
        console.log($.inArray(yachtOnList.yachtId, currentYachts))
        if ($.inArray(yachtOnList.yachtId, currentYachts) === -1) {
          removeFromList(yachtOnList.yachtId)
        }
      })
    }

    $('.add-to-shortlist').on('click', (e) => {
      const chosenYacht = e.currentTarget
      const localStorageSl = JSON.parse(localStorage.getItem('localShortlist'))
      // Is there a local storage list?
      console.log('localStorageSl', localStorageSl)

      if (localStorageSl !== null) {
        console.log('localStorageSl', localStorageSl)

        if (Array.isArray(localStorageSl)) {
          console.log('this is an old shortlist, delete it.')
          localStorage.removeItem('localShortlist')
        } else {
          shortlistYachts = localStorageSl.list

          if (typeof currentYachts !== 'undefined') {
            removeNowClosedYachts()
          }
        }
      }

      addToShortlist(chosenYacht)

      if ($(shortlistYachts).length > 0) {
        const date = new Date()
        localStorage.setItem('localShortlist', JSON.stringify({ date, list: shortlistYachts }))
      }
    })
  }
}
