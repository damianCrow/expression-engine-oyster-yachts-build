import { addClass, removeClass } from '../../_scripts/helper-functions'

export default class GlobalNav {
  constructor(nav, button, body) {
    this.body = body
    this.button = button
    this.nav = nav
    this.topBarHeight = 0
  }

  open() {
    addClass(this.nav, 'open')
    addClass(this.body, 'locked')
    this.nav.style.transform = `translateY(${this.topBarHeight}px)`
  }

  close() {
    removeClass(this.nav, 'open')
    removeClass(this.body, 'locked')
    this.nav.style.transform = ''
  }

  snapPointCheck(reactiveScrollProps) {
    if (this.nav) {
      const { fixedTopValue } = reactiveScrollProps
      const { nav } = this

      if (fixedTopValue !== this.topBarHeight) {
        this.topBarHeight = fixedTopValue
      } else if (fixedTopValue === 0) {
        nav.style.transform = ''
      }
    }

    return 0
  }
}
