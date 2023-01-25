import type { Alpine } from 'alpinejs'
import EditorAlpinePlugin from './plugin'

export default (Alpine: Alpine) => {
  Alpine.plugin(EditorAlpinePlugin)
}
