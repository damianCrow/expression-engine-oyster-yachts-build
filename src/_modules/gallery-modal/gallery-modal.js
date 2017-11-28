import $ from 'jquery'
import 'lightgallery'
import 'lightgallery/modules/lg-zoom'
import 'lightgallery/modules/lg-pager'
import 'lightgallery/modules/lg-thumbnail'
import 'lightgallery/modules/lg-share'
import 'lightgallery/modules/lg-fullscreen'

/**
* Social Grid object
*/
export default class GalleryModal {
  /**
  * Get a SocialItem which is not active
  * @return {int} index of new slide
  */
  constructor() {
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
        vimeoPlayerParams: {
          byline: 0,
          portrait: 0,
          color: '003145',
        },
        videoMaxWidth: '100%',
        galleryId: (i + 1),
      })
    })

    // trigger first slide to open in gallery
    $('.btn-gallery').on('click', (e) => {
      e.preventDefault()

      const gallery = $(e.currentTarget).attr('data-gallery')

      $(`.gallery-content[data-gallery="${gallery}"] a:first`).trigger('click')
    })
  }
}
