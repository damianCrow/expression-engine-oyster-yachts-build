// import 'smoothscroll'

import { addClass, removeClass, getElemDistance } from '../../_scripts/helper-functions'

export default class SubBar {
  constructor(element) {
    this.filterBar = element

    this.activeBar = false

    this.topBarHeight = 0
    this.fixedHeight = 0

    this.scrollLinks = document.querySelectorAll('[data-local-scroll-pos]')
    this.localScrollPosLinks()

    if (this.filterBar) {
      this.fetchSizes()
      this.distFromTop = getElemDistance(this.filterBar)
    }
  }

  fetchSizes() {
    this.fixedHeight = this.filterBar.getBoundingClientRect().height
  }

  snapPointCheck(reactiveScrollProps) {
    if (this.filterBar) {
      const {
        lastKnownScrollPosition,
        fixedTopValue,
        headerSnapPoint,
      } = reactiveScrollProps

      const { filterBar } = this

      if (lastKnownScrollPosition > headerSnapPoint && !this.activeBar) {
        removeClass(filterBar, `${filterBar.className.split(' ')[0]}--large`)

        this.activeBar = true
      } else if (lastKnownScrollPosition < headerSnapPoint && this.activeBar) {
        addClass(filterBar, `${filterBar.className.split(' ')[0]}--large`)

        this.activeBar = false

        // this.topBarHeight = 0
        // filterBar.style.transform = ''

        this.fixedHeight = filterBar.getBoundingClientRect().height
      }
      if (fixedTopValue !== this.topBarHeight) {
        filterBar.style.transform = `translateY(${fixedTopValue}px)`
        this.topBarHeight = fixedTopValue
      } else if (fixedTopValue === 0) {
        this.topBarHeight = 0

        filterBar.style.transform = ''
      }
    }


    return this.fixedHeight
  }

  localScrollPosLinks() {
    console.log('hi')

    if (this.scrollLinks) {
      for (let i = 0; i < this.scrollLinks.length; i += 1) {
        console.log("this.scrollLinks[i].getAttribute('href')", this.scrollLinks[i].getAttribute('href'))

        this.scrollLinks[i].addEventListener('click', (e) => {
          e.preventDefault()
          const destination = document.querySelector(this.scrollLinks[i].getAttribute('href'))
          const destinationBannerHeight = destination.querySelector('.banner').getBoundingClientRect().height
          const distance = getElemDistance(destination)

          // console.log('destination.getBoundingClientRect().top', destination.getBoundingClientRect().top)
          console.log('this.topBarHeight', this.topBarHeight)
          console.log('distance', distance)

          // Scroll to specific values
          // scrollTo is the same
          console.log
          window.scroll({
            top: distance - (this.topBarHeight + destinationBannerHeight),
            left: 0,
            behavior: 'smooth',
          })

          // // Scroll certain amounts from current position
          // window.scrollBy({
          //   top: 100, // could be negative value
          //   left: 0,
          //   behavior: 'smooth',
          // })

          // // Scroll to a certain element
          // document.querySelector('.hello').scrollIntoView({
          //   behavior: 'smooth',
          // })

          // destination.scrollIntoView({
          //   behavior: 'smooth',
          // })
          // window.scrollBy(0, -10) // Adjust scrolling with a negative value here
        })
      }
    }
  }
}
