/**
 * Slide Over store
 */
const SlideOver = {
  showLayer: false,
  showOverlay: false,
  isOpen: false,

  toggle() {
    if (this.isOpen)
      this.close()

    else
      this.open()
  },
  open() {
    this.showLayer = true
    setTimeout(() => {
      this.showOverlay = true
      this.isOpen = true
    }, 150)
  },
  close() {
    this.showOverlay = false
    this.isOpen = false
    setTimeout(() => {
      this.showLayer = false
    }, 150)
  },
}

export {
  SlideOver,
}
