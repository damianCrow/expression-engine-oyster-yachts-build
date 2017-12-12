import { addClass, removeClass, getElemDistance } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class SubBar {
  constructor(element) {
    this.filterBar = element

    this.activeBar = false

    this.topBarHeight = 0
    this.fixedHeight = 0

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
}
