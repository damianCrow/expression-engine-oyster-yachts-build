import { addClass, removeClass, hasClass } from '../../_scripts/helper-functions'

export default class Burger {
  constructor(button, navControls) {
    this.button = button
    this.burgerPiece = button.querySelector('.burger-container')
    this.navControls = navControls
    this.buttonTrans = 'close'

    this.events()
  }

  events() {
    this.button.addEventListener('click', () => {
      if (hasClass(this.burgerPiece, 'open')) {
        this.navControls.close()
        this.close()
      } else {
        this.navControls.open()
        this.open()
      }
    })

    this.burgerPiece.addEventListener('transitionend', () => {
      const action = this.buttonTrans === 'close' ? 'closing' : 'opening'
      if (hasClass(this.burgerPiece, action)) {
        removeClass(this.burgerPiece, action)
        addClass(this.burgerPiece, this.buttonTrans)
      }
    })
  }

  close() {
    this.buttonTrans = 'close'
    if (hasClass(this.burgerPiece, 'open')) {
      removeClass(this.burgerPiece, 'open')
      addClass(this.burgerPiece, 'closing')
    }
  }

  open() {
    this.buttonTrans = 'open'
    if (!hasClass(this.burgerPiece, 'open') && !hasClass(this.burgerPiece, 'opening') && !hasClass(this.burgerPiece, 'closing')) {
      removeClass(this.burgerPiece, 'close')
      addClass(this.burgerPiece, 'opening')
    }
  }
}
