import $ from 'jquery'

export default class Shortlist {
  constructor() {
    this.init()
  }

  init() {
    //  ---- SHORTLIST LOGIC -----  //

    let shortlistYachts = []

    function removeNowClosedYachts() {
      $.each(shortlistYachts, (key, yachtOnList) => {
        console.log($.inArray(yachtOnList.yachtid, currentYachts))
        if ($.inArray(yachtOnList.yachtid, currentYachts) === -1) {
          removeFromList(yachtOnList.yachtid)
        }
      })
    }

    // Pull the info, check it and put it in the object if it's not already
    function addToShortlist(item) {
      let yachtContainer = $(item).parents('[data-yachtid]')

      if (yachtContainer.length < 1) {
        yachtContainer = $('[data-yachtid]')
      }

      var yachtId = $(yachtContainer).data('yachtid'),
        yachtImage = yachtContainer[0].querySelector('.yacht-listing-photo'),
        yachtModal = $(yachtContainer).find('.yacht-list-modal').eq(0).text(),
        yachtName = $(yachtContainer).find('.yacht-list-name').eq(0).text(),
        yachtSection = $(yachtContainer).data('yachtsection'),
        yachtSpec = $(yachtContainer).data('spec')

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

    function checkIfYachtShortlist(yachtId, yachtImage, yachtModal, yachtName, yachtSection, yachtSpec) {
      // console.log('checkIfYachtShortlist, shortlistYachts: ' , shortlistYachts)

      // Check if this yacht is on the shortlist already
      const shortlistCheck = $.grep(shortlistYachts, e => e.yachtid === yachtId)

      if (shortlistCheck.length) {
        // This is already on the shortlist
      } else {
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


    function addYachtsToFreeform() {
      console.log(shortlistYachts)
      // alert('hello')
      // Remove all existing images from the shortlist form
      $('.ff_yacht_image,.ff_yacht_name,.ff_yacht_link,.ff_yacht_spec').remove()

      let shortListCounter = 0

      $.each(shortlistYachts, (key, yachtOnList) => {
        const yachtImage = yachtOnList.yachtImage.toString().replace('__blur', '')

        // Add to shortlist form
        $('<input>').attr({
          type: 'hidden',
          id: 'yacht_image_' + shortListCounter,
          name: 'yacht_image_' + shortListCounter,
          value: yachtImage,
          class: 'ff_yacht_image',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: 'yacht_name_' + shortListCounter,
          name: 'yacht_name_' + shortListCounter,
          value: yachtOnList.yachtmodal + ' // ' + yachtOnList.name,
          class: 'ff_yacht_name',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: 'yacht_link_' + shortListCounter,
          name: 'yacht_link_' + shortListCounter,
          value: 'http://' + window.location.hostname + '/brokerage/fleet/' + yachtOnList.yachtid,
          class: 'ff_yacht_link',
        }).appendTo('#spec-form')

        $('<input>').attr({
          type: 'hidden',
          id: 'yacht_spec_' + shortListCounter,
          name: 'yacht_spec_' + shortListCounter,
          value: yachtOnList.yachtSpec,
          class: 'ff_yacht_spec',
        }).appendTo('#spec-form')

        shortListCounter++
      })
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
      const yachtToRemove = $('.yachts-shortlist [data-yachtid="' + yachtToRemoveId + '"]')

      console.log('yachtToRemoveId', yachtToRemoveId)

      const refinedList = $.grep(shortlistYachts, e => e.yachtId !== yachtToRemoveId)

      console.log('removeFromList()')
      console.log('shortlistYachts', shortlistYachts)
      console.log('refinedList', refinedList)

      shortlistYachts = refinedList

      localStorage.setItem('localShortlist', JSON.stringify(shortlistYachts))

      toggleEmptyShortlistMessage(shortlistYachts)

      console.log('yachtToRemove', yachtToRemove)

      $(yachtToRemove).parent('.column').css('display', 'none')
      // $(yachtToRemove).parent().html('test')
      addYachtsToFreeform()
    }

    $('.add-to-shortlist').on('click', (e) => {
      const chosenYacht = e.currentTarget
      const localStorageSl = JSON.parse(localStorage.getItem('localShortlist'))
      // Is there a local storage list?

      if (localStorageSl != null && localStorageSl.length > 0) {
        shortlistYachts = localStorageSl

        if (typeof currentYachts !== 'undefined') {
          removeNowClosedYachts()
        }
      }

      addToShortlist(chosenYacht)

      if ($(shortlistYachts).length > 0) {
        localStorage.setItem('localShortlist', JSON.stringify(shortlistYachts))
      }
    })
  }
}
