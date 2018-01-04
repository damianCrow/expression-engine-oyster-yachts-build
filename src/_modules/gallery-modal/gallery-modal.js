import $ from 'jquery'
import 'lightgallery'
import 'lightgallery/modules/lg-zoom'
import 'lightgallery/modules/lg-pager'
import 'lightgallery/modules/lg-thumbnail'
// import 'lightgallery/modules/lg-share'
import 'lightgallery/modules/lg-fullscreen'
import 'lightgallery/modules/lg-hash'
import 'lightgallery/modules/lg-video'

import { addClass, removeClass } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class GalleryModal {
  constructor(modal) {
    if (modal) {
      this.breakpoints = new BreakPoints()

      this.modal = modal
      this.header = this.modal.querySelector('.galleries__header')
      this.closeBtn = this.header.querySelector('.galleries__close') ? this.header.querySelector('.galleries__close') : 0
      this.footer = this.modal.querySelector('.galleries__footer')
      this.thumbnailBar = this.modal.querySelector('.galleries__footer')
      this.gallerySwitchers = this.modal.querySelector('.galleries__nav')
      this.index = this.modal.querySelector('.galleries__index')
      this.galleryBtns = this.gallerySwitchers.querySelectorAll('button[data-gallery]')

      this.activeBar = document.querySelector('.galleries__header')

      this.activeThumbnailBar = false
      this.switchingGaleries = false
      this.currentGallery = {}
      this.currentGalleryName = ''

      this.topBarHeight = 0

      this.autoOpen = false
    }
  }

  init(globalHeader) {
    this.globalHeader = globalHeader

    $('.gallery-content').each((i, el) => {
      $('.btn-gallery').on('click', (e) => {
        e.preventDefault()
        const gallery = $(e.currentTarget).attr('data-gallery')
        this.currentGalleryName = gallery
        $(`.gallery-content[data-gallery="${gallery}"] a:first`).trigger('click')
      })

      $(el)
        // .on('onBeforeOpen.lg', () => this.open())
        .on('onAfterOpen.lg', () => {
          this.open($(el))
          this.currentGallery = $(el)
        })
        .on('onBeforeClose.lg', () => this.close())
        .on('onCloseAfter.lg', () => this.afterClose())

      $(el).lightGallery({
        appendCounterTo: '.galleries__index',
        backdropDuration: 0,
        closable: false,
        galleryId: (i + 1),
        hash: true,
        loadVimeoThumbnail: true,
        share: false,
        showThumbByDefault: false,
        thumbContHeight: 136,
        thumbMargin: 14,
        thumbnail: true,
        thumbWidth: 197,
        toogleThumb: false,
        videoMaxWidth: '100%',
        download: false,
        vimeoThumbSize: 'thumbnail_medium',
        vimeoPlayerParams: {
          byline: 0,
          portrait: 0,
          color: '003145',
        },
      })

      if ($(el).is('[data-auto-open]') && this.autoOpen === false) {
        this.autoOpen = true
        console.log('auto opening gallery')
        const gallery = $(el).attr('data-gallery')
        this.currentGalleryName = gallery
        $(`.gallery-content[data-gallery="${gallery}"] a:first`).trigger('click')
      }
    })

    if (this.modal) {
      this.globalGalleryEvents()
      this.removeGalleryBtn()
    }
  }

  removeGalleryBtn() {
    // TODO: Temp fix to remove just one button.
    if ($('.galleries__nav-item').length < 2) {
      $('.button-group').remove()
    }
  }

  snapPointCheck(reactiveScrollProps) {
    if (this.activeBar) {
      const { activeBar } = this

      const lastModuleHeight = [...reactiveScrollProps.topValues].pop()
      const fixedTopValue = reactiveScrollProps.fixedTopValue - (this.breakpoints.atLeast('medium') ? lastModuleHeight : 0)

      if (fixedTopValue !== this.topBarHeight && this.activeBar) {
        activeBar.style.transform = `translateY(${fixedTopValue}px)`

        if (this.closeBtn) {
          this.closeBtn.style.transform = !this.breakpoints.atLeast('medium') ? `translateY(-${lastModuleHeight}px)` : ''
        }

        this.topBarHeight = fixedTopValue
      } else if (fixedTopValue === 0) {
        this.topBarHeight = 0

        if (this.closeBtn) {
          this.closeBtn.style.transform = ''
        }

        activeBar.style.transform = ''
      }
    }

    // return this.fixedHeight
    // So nothing can go under this
    return 0
  }

  globalGalleryEvents() {
    if (this.closeBtn) {
      this.closeBtn.addEventListener('click', () => {
        this.currentGallery.data('lightGallery').destroy()
      })
    }

    console.log('this.modal', this.modal)

    this.footer.addEventListener('click', () => {
      // This gets created via the plugin later, so needs to be addressed here.
      const thumbnailContainer = document.querySelector('.lg-thumb-outer')

      if (thumbnailContainer) {
        if (this.activeThumbnailBar) {
          removeClass(thumbnailContainer, 'active')
          removeClass(this.thumbnailBar, 'galleries__footer--active')
          this.activeThumbnailBar = false
        } else {
          addClass(thumbnailContainer, 'active')
          addClass(this.thumbnailBar, 'galleries__footer--active')
          this.activeThumbnailBar = true
        }
      }
    })

    $('button[data-gallery]').on('click', (e) => {
      this.switchingGaleries = true
      this.currentGalleryName = $(e.target).attr('data-gallery')
      this.currentGallery.data('lightGallery').destroy()
    })
  }

  // setEvents($gallery) {
  //   const galleryType = $gallery.attr('data-gallery')
  //   // const galleryTrigger = this.gallerySwitchers.querySelector(`button[data-gallery="${galleryType}"]`)

  //   // trigger first slide to open in gallery
  // }

  close() {
    $(this.index).empty()

    if (!this.switchingGaleries) {
      this.currentGallery = {}
      console.log('GalleryModal, close')
      removeClass(document.body, 'locked--gallery')
      removeClass(this.modal, 'galleries--active')
      this.globalHeader.fullScreenMode({ on: false })
    }
  }

  afterClose() {
    if (this.switchingGaleries) {
      $(`.gallery-content[data-gallery="${this.currentGalleryName}"] a:first-child`).click()
      this.switchingGaleries = false
    }
  }

  open($gallery) {
    const galleryType = $gallery.attr('data-gallery')
    const activeGalleryBtn = this.gallerySwitchers.querySelector(`button[data-gallery="${galleryType}"]`)

    $(this.galleryBtns).removeClass('button--clear')
    $(activeGalleryBtn).addClass('button--clear')

    console.log('GalleryModal, open, galleryType: ', galleryType)
    console.log('$gallery', $gallery)

    $(document.body).addClass('locked--gallery')
    $(this.modal).addClass('galleries--active')
    console.log('this.globalHeader.fullScreenMode', this.globalHeader.fullScreenMode)
    this.globalHeader.fullScreenMode()
  }
}
