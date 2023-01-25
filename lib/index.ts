import type { IInertiaTyped, InertiaTypedOptions } from '@/modules/vue-plugin-inertia-typed'
import InertiaTyped from '@/modules/vue-plugin-inertia-typed'
import type { StewardOptions } from '@/modules/vite-plugin'
import { Steward } from '@/modules/vite-plugin'
// import EditorAlpinePlugin from '@/modules/editor'
// import './css/tiptap.css'

const log = (message: string) => console.log(`[vite-plugin-laravel-steward] ${message}`)

export type { IInertiaTyped, InertiaTypedOptions, StewardOptions }
export {
  log,
  Steward,
  InertiaTyped,
  // EditorAlpinePlugin,
}
