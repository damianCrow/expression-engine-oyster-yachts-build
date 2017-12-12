import { addClass, removeClass, getElemDistance } from '../../_scripts/helper-functions'
import BreakPoints from '../../_scripts/breakpoints'

export default class Filters {
  constructor() {
    this.filterBar = document.getElementById('global-page-filters')

    this.activeBar = false
    this.stickyBar = false

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
      const { filterBar } = this
      const { lastKnownScrollPosition, fixedTopValue } = reactiveScrollProps

      const snapPoint = this.distFromTop - (fixedTopValue)

      if (lastKnownScrollPosition > snapPoint && !this.activeBar) {
        removeClass(filterBar, `${filterBar.className.split(' ')[0]}--unstuck`)

        this.activeBar = true
        this.fixedHeight = 0
      } else if (lastKnownScrollPosition < snapPoint && this.activeBar) {
        addClass(filterBar, `${filterBar.className.split(' ')[0]}--unstuck`)

        this.activeBar = false

        this.topBarHeight = 0
        filterBar.style.transform = ''

        this.fixedHeight = filterBar.getBoundingClientRect().height
      }

      if (fixedTopValue !== this.topBarHeight && this.activeBar) {
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
