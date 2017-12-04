import { addClass, removeClass, hasClass } from '../../_scripts/helper-functions'

export default class Burger {
  constructor(button, navControls) {
    this.button = button
    this.navControls = navControls
    this.buttonTrans = 'close'

    this.events()
  }

  events() {
    this.button.addEventListener('click', () => {
      if (hasClass(this.button, 'open')) {
        this.navControls.close()
        this.close()
      } else {
        this.navControls.open()
        this.open()
      }
    })

    this.button.addEventListener('transitionend', (e) => {
      const action = this.buttonTrans === 'close' ? 'closing' : 'opening'
      console.log('e.currentTarget.classList', e.currentTarget.classList)
      console.log('`${this.buttonTrans}ing`', action)
      if (hasClass(e.currentTarget, action)) {
        removeClass(this.button, action)
        addClass(this.button, this.buttonTrans)
      }
    })
  }

  close() {
    this.buttonTrans = 'close'
    if (hasClass(this.button, 'open')) {
      removeClass(this.button, 'open')
      addClass(this.button, 'closing')
    }
  }

  open() {
    this.buttonTrans = 'open'
    if (!hasClass(this.button, 'open')) {
      removeClass(this.button, 'close')
      addClass(this.button, 'opening')
    }
  }
}
