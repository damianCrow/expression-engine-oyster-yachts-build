import $ from 'jquery'

/**
* Social Item object
* @param {String} platform Social media platform ("youtube", "instagram", "twitter")
* @param {[type]} data     Social media item data
*/
class SocialItem {
  constructor(platform, data) {
    // social platform
    this.platform = platform

    // social data for this item
    this.data = data

    // is this slide on the page
    this.active = false

    // jQuery object of the slide
    this.$el = this.buildElement(data)
  }

  buildElement() {
    // youtube
    if (this.platform === 'youtube') {
      return $('<a target="_blank" href="' + this.data.url + '" style="background-image:url(' + this.data.image + ')" class="social-slide social-slide-youtube"><i class="fa fa-youtube-play"></i></a>')
      // instagram
    } else if (this.platform === 'instagram') {
      return $('<a target="_blank" href="' + this.data.url + '" style="background-image:url(' + this.data.image + ')" class="social-slide social-slide-instagram"><i class="fa fa-instagram"></i></a>')
      // twitter
    } else if (this.platform === 'twitter') {
      return $('<a target="_blank" href="' + this.data.url + '" class="social-slide twitter social-slide-twitter"><i class="fa fa-twitter"></i><p>' + this.data.tweet + '</p></a>')
    }
  }
}

/**
* Social Grid object
*/
export default class SocialGrid {
  constructor() {
    // if no social items then bail
    if (typeof socialItems === 'undefined') return

    // eslint-disable-next-line no-undef
    this.socialItems = socialItems

    // select all slide blocks from the DOM
    this.$el = $('.social-slider')

    // list of social items
    this.items = []

    // populate the list of social items with SocialItem objects
    $.each(this.socialItems, (platform, platformItems) => {
      $.each(platformItems, (i, platformItem) => {
        this.items.push(new SocialItem(platform, platformItem))
      })
    })

    // iterate over all DOM slide blocks, adding initial slides
    $.each(this.$el, (i, $block) => {
      // get a new SocialItem index which isn't active
      const index = this.getNewIndex()

      // append slide to DOM
      this.items[index].$el.appendTo($block)

      // start loop
      this.newItem($block, index)
    })
  }

  /**
  * Get a SocialItem which is not active
  * @return {int} index of new slide
  */
  getNewIndex() {
    // get random number from 0 to this.items.length-1
    const index = Math.floor(Math.random() * this.items.length)

    // if the slide at the new index is active then get another index
    if (this.items[index].active === true) return this.getNewIndex()

    // set SocialItem at new index to active
    this.items[index].active = true

    return index
  }

  /**
  * Add new slide to block
  * @param  {jQuery object} $block       DOM block we're dealing with
  * @param  {int} oldItemIndex old SocialItem index of the block
  */
  newItem($block, oldItemIndex) {
    // get random delay
    const delay = Math.floor(Math.random() * 16000) + 10000

    // get new slide after random time is up
    setTimeout(() => {
      // get a new SocialItem index which isn't active
      const newItemIndex = this.getNewIndex()

      // add class to old item => adds z-index above new slide and fades opacity to 0
      this.items[oldItemIndex].$el.addClass('fade-block')

      // add new item to block
      this.items[newItemIndex].$el.appendTo($block)

      // give time for old slide to animate out
      setTimeout(() => {
        // detach the old slide and remove the animation class
        this.items[oldItemIndex].$el.removeClass('fade-block').detach()

        // set old SocialItem to inactive
        this.items[oldItemIndex].active = false

        // recursively get new item
        this.newItem($block, newItemIndex)
      }, 1000)
    }, delay)
  }
}
