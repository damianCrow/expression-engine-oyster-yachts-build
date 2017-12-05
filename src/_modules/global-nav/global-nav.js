import { addClass, removeClass } from '../../_scripts/helper-functions'

export default class GlobalNav {
  constructor(nav, button, body) {
    this.body = body
    this.button = button
    this.nav = nav
  }

  open() {
    addClass(this.nav, 'open')
    addClass(this.body, 'locked')
  }

  close() {
    removeClass(this.nav, 'open')
    removeClass(this.body, 'locked')
  }
}
