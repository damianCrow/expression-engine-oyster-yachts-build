import $ from 'jquery'
import 'lightgallery'
import 'lightgallery/modules/lg-zoom'
import 'lightgallery/modules/lg-pager'
import 'lightgallery/modules/lg-thumbnail'
import 'lightgallery/modules/lg-share'
import 'lightgallery/modules/lg-fullscreen'

import { addClass, removeClass } from '../../_scripts/helper-functions'

export default class GalleryModal {
  constructor(modal, globalHeader) {
    if (modal) {
      this.modal = modal
      this.header = this.modal.querySelector('.galleries__header')
      this.globalHeader = globalHeader
      this.close = this.header.querySelector('.galleries__close')
      console.log('this.close', this.close)
      this.init()
    }
  }

  init() {
    $('.gallery-content').each((i, el) => {
      $(el).lightGallery({
        thumbnail: true,
        thumbContHeight: 136,
        thumbWidth: 197,
        thumbMargin: 14,
        toogleThumb: false,
        showThumbByDefault: false,
        closable: false,
        backdropDuration: 0,
        loadVimeoThumbnail: true,
        vimeoThumbSize: 'thumbnail_medium',
        share: false,
        vimeoPlayerParams: {
          byline: 0,
          portrait: 0,
          color: '003145',
        },
        videoMaxWidth: '100%',
        galleryId: (i + 1),
      })

      this.setEvents($(el).data('lightGallery'))
    })

    // trigger first slide to open in gallery
    $('.btn-gallery').on('click', (e) => {
      e.preventDefault()
      const gallery = $(e.currentTarget).attr('data-gallery')

      this.open()

      $(`.gallery-content[data-gallery="${gallery}"] a:first`).trigger('click')
    })

    // ---- VIEW GALLERY END ----  //
  }

  close() {
    removeClass(document.body, 'locked')
    removeClass(this.modal, 'galleries--active')
    this.globalHeader.fullScreenMode()
  }

  open() {
    addClass(document.body, 'locked')
    addClass(this.modal, 'galleries--active')
    this.globalHeader.fullScreenMode(true)
  }

  setEvents(gallery) {
    this.close.addEventListener('click', () => {
      gallery.destroy()
      this.close()
      this.globalHeader.fullScreenMode(false)
    })
  }
}
