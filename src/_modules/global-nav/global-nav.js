import { addClass, removeClass } from '../../_scripts/helper-functions'

export default class GlobalNav {
  constructor(nav, button, body) {
    this.body = body
    this.button = button
    this.nav = nav
  }

  // events() {
  //   this.button.addEventListener('click', () => hasClass(this.nav, 'open') ? this.close() : this.open())
  // }

  open() {
    addClass(this.nav, 'open')
    addClass(this.body, 'locked')
  }

  close() {
    removeClass(this.nav, 'open')
    removeClass(this.body, 'locked')
  }
}
