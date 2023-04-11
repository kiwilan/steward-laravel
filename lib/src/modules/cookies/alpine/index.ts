import type { Alpine } from 'alpinejs'
import CookiesAlpinePlugin from './plugin.js'

export default (Alpine: Alpine) => {
  Alpine.plugin(CookiesAlpinePlugin)
}
