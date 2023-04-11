import type { Alpine } from 'alpinejs'
import EditorAlpinePlugin from './plugin.js'

export default (Alpine: Alpine) => {
  Alpine.plugin(EditorAlpinePlugin)
}
