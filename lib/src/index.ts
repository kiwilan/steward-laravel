import GdprAlpinePlugin from './modules/gdpr/alpine.js'
import GdprSetup from './modules/gdpr/setup.js'
import { SlideOver } from './modules/alpine/slide-over.js'
import { Copy } from './modules/alpine/copy.js'

const Gdpr = {
  alpine: GdprAlpinePlugin,
  setup: GdprSetup,
}

export {
  Gdpr,
  SlideOver,
  Copy,
}
