// import 'smoothscroll'

import { addClass, removeClass, getElemDistance } from '../../_scripts/helper-functions'

export default class SubBar {
  constructor(element, scrollTo) {
    this.scrollTo = scrollTo
    this.filterBar = element

    this.activeBar = false
    this.transition = false

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

        this.fixedHeight = filterBar.getBoundingClientRect().height
      }
      if (fixedTopValue !== this.topBarHeight) {
        filterBar.style.transform = `translateY(${fixedTopValue}px)`

        // Make sure the first time the page is launched the bar doesn't drop down.
        if (!this.transition) {
          filterBar.style.transition = 'none'
          this.transition = true
        } else {
          filterBar.style.transition = ''
        }

        this.topBarHeight = fixedTopValue
      } else if (fixedTopValue === 0) {
        this.topBarHeight = 0

        filterBar.style.transform = ''
      }
    }

    return this.fixedHeight
  }

  localScrollPosLinks() {
    if (this.scrollLinks) {
      for (let i = 0; i < this.scrollLinks.length; i += 1) {
        this.scrollLinks[i].addEventListener('click', (e) => {
          e.preventDefault()

          const destination = document.querySelector(this.scrollLinks[i].getAttribute('href'))

          this.scrollTo(destination)

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
